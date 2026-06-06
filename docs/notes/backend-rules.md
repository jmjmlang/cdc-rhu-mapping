# Backend Rules

Read before touching controllers, services, models, validation, routes, or Eloquent queries.

## Core Rules

| Rule | Standard |
|---|---|
| Thin controllers | Validate, call a service/model method, return view/redirect/JSON. |
| Service layer | Domain/business logic belongs in `app/Services/`, currently `DssService.php`; create a new service only for real domain separation. |
| Validation | Use validated data only. Inline `$request->validate()` is fine for simple one-off validation; use FormRequest for reusable/complex validation. |
| Mass assignment | Define explicit `$fillable`; never use `$guarded = []`; never call `Model::create($request->all())`. |
| N+1 prevention | Before passing collections to Blade loops, eager-load every relationship the view uses. |
| Scopes | Use named scopes like `approved()`, `pending()`, `withinDays(30)` instead of repeated raw filters. |
| Filtering | Filter in SQL before data reaches Blade/JS. Never send all reports for client filtering. |
| Role checks | Enforce with middleware; unauthorized role access must `abort(403)`. |
| API JSON | Use `response()->json($data)` and expose the minimum required fields. |

## Good Patterns

```php
$validated = $request->validate([
    'barangay_id' => ['required', 'exists:barangays,id'],
    'health_category_id' => ['required', 'exists:health_categories,id'],
    'number_of_cases' => ['required', 'integer', 'min:1'],
    'report_date' => ['required', 'date', 'before_or_equal:today'],
]);

CaseReport::create($validated + [
    'user_id' => auth()->id(),
    'status' => 'pending',
]);
```

```php
$reports = CaseReport::pending()
    ->with(['user', 'barangay', 'healthCategory'])
    ->latest()
    ->paginate(15);
```

```php
$data = CaseReport::approved()
    ->withinDays(30)
    ->when($request->filled('health_category_id'), fn ($query) =>
        $query->where('health_category_id', $request->integer('health_category_id'))
    )
    ->with(['barangay', 'healthCategory'])
    ->get();
```

## Anti-Patterns

| Avoid | Why |
|---|---|
| `$request->all()` in writes | Mass-assignment and unvalidated data risk. |
| Relationship access in Blade loops without `with()` | Causes N+1 queries. |
| Business logic in controllers or Blade | Hard to test and duplicate-prone. |
| Inline role checks in controllers | Middleware should own access control. |
| Raw repeated `where('status', 'approved')` | Use `CaseReport::approved()`. |
| Raw repeated date windows | Use `CaseReport::withinDays(30)`. |
| Caching Eloquent model collections | WAMP/Windows file cache can return strings; cache scalars/plain arrays only. |

## Project Query Recipes

| Use case | Query |
|---|---|
| Pending admin review | `CaseReport::pending()->with(['user','barangay','healthCategory'])->latest()->paginate(15)` |
| Approved reports table | `CaseReport::approved()->with(['barangay','healthCategory'])->latest()->paginate(...)` |
| Citizen's own reports | `$user->caseReports()->with(['barangay','healthCategory'])->latest()->paginate(10)` |
| Map/DSS data | `CaseReport::approved()->withinDays(30)->with([...])` |

## Map API Contract

`/api/map-data` is auth-gated and must:

- validate filter inputs;
- use `approved()->withinDays(30)`;
- eager-load needed relations;
- aggregate by barangay/category as needed;
- return coordinates, barangay/category labels, totals, and no PII.

## File Placement

| Concern | Location |
|---|---|
| Controllers | `app/Http/Controllers/{Admin,Citizen}/` or root for shared controllers |
| Validation classes | `app/Http/Requests/` when reusable/complex |
| Domain logic | `app/Services/` |
| Scopes/relations/casts | `app/Models/` |
| Role enforcement | `app/Http/Middleware/EnsureRole.php` |
| Routes | `routes/web.php` |
