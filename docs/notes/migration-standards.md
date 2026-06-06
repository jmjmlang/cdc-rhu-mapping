# Migration Standards

Read before writing, editing, running, or deploying migrations/seeders.

## Non-Negotiables

- `Schema::defaultStringLength(191)` must exist in `App\Providers\AppServiceProvider::boot()`.
- Every schema change is a new migration. Do not squash migrations on Laravel Cloud.
- Confirm before destructive DB operations such as `migrate:fresh`, dropping tables, or production rollback.
- Parent migrations must run before child migrations.
- Seeders must be idempotent via `updateOrCreate`.

## Required MySQL Setup

```php
use Illuminate\Support\Facades\Schema;

public function boot(): void
{
    Schema::defaultStringLength(191);
}
```

Prevents indexed `utf8mb4` strings from hitting MySQL key-length errors on WAMP/Laravel Cloud.

## Foreign Keys

Use FK shorthand and explicit delete behavior:

```php
$table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
$table->foreignId('barangay_id')->constrained()->restrictOnDelete();
$table->foreignId('health_category_id')->constrained('health_categories')->restrictOnDelete();
```

Avoid manual `unsignedBigInteger` + `foreign()` unless there is a documented reason.

| Method | Effect | Use |
|---|---|---|
| `cascadeOnDelete()` | Deletes child rows | True ownership only. |
| `restrictOnDelete()` | Blocks parent deletion | Reference data/report history. |
| `nullOnDelete()` | Keeps child, clears FK | Users/reviewers where reports should remain. |
| `noActionOnDelete()` | DB default | Avoid unless required. |

## FK Safety Guards

Use guards when creating/dropping FK-heavy tables:

```php
public function up(): void
{
    Schema::disableForeignKeyConstraints();
    Schema::create('case_reports', function (Blueprint $table) {
        // columns...
    });
    Schema::enableForeignKeyConstraints();
}

public function down(): void
{
    Schema::disableForeignKeyConstraints();
    Schema::dropIfExists('case_reports');
    Schema::enableForeignKeyConstraints();
}
```

Every `down()` must reverse `up()` exactly.

## Dependency Order

Current domain order:

```text
users -> municipalities -> barangays -> health_categories -> case_reports
```

`case_reports` references `users`, `barangays`, and `health_categories`, so it must run after them. Timestamp prefix controls order. Wrong order usually surfaces as `SQLSTATE[HY000]: 1215 Cannot add foreign key constraint`.

## Column Standards

- Indexed/unique strings: use length `191` or rely on default string length.
- Coordinates: `decimal(10, 7)`.
- Enums: keep migration values, model casts, validation, and `match()` defaults aligned.
- Never use `fake()` in migrations; factories/seeders only.

## Seeder Standards

```php
Barangay::updateOrCreate(
    ['name' => $row['name']],
    $row
);
```

- No bare `create`.
- Coordinates live only in `BarangaySeeder`.
- Seeded credentials must match `AGENTS.md` / `CLAUDE.md`.

## Deployment Checklist

1. Review migration order and FK behavior locally.
2. Confirm `AppServiceProvider` string length rule.
3. Run `php artisan migrate:status`.
4. Ask before destructive or production-impacting DB actions.
5. Run migration, then verify route/page/data behavior.

## Useful Commands

```powershell
php artisan migrate
php artisan migrate:status
php artisan migrate:rollback
php artisan make:migration add_column_to_table --table=table_name
php artisan make:seeder ExampleSeeder
```

`php artisan migrate:fresh --seed` is local-only and still requires confirmation.
