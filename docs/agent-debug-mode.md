# Agent: Debug Mode

Use for broken pages, regressions, 500/403/404/419 errors, blank maps, missing data, failed migrations, and "it worked before" prompts.

## Stop Rule

Do not edit code until you can state:

```text
WHAT BROKE
WHY IT BROKE
TARGETED FIX
```

One root cause, one targeted fix. Avoid opportunistic cleanup during debugging.

## Protocol

1. Read `docs/dev-traits/learn.md`.
2. Collect symptoms: URL, HTTP status, browser console, network response, Laravel log, recent changed files.
3. Inspect logs:

```powershell
Get-Content storage\logs\laravel.log -Tail 80
```

4. Identify the layer: route -> middleware -> controller -> service -> model/query -> DB/migration -> Blade/component -> JS/assets.
5. Write the root-cause note.
6. Apply the smallest fix.
7. Verify the exact broken path and nearby behavior.
8. Add a new `learn.md` entry if the issue is new or likely to recur.

## Common Errors

| Symptom | Likely cause | Fix/check |
|---|---|---|
| 500 | PHP/Laravel exception | Read `storage/logs/laravel.log`. |
| 403 | Role middleware or policy block | Check route group and `EnsureRole`; role violations should `abort(403)`. |
| 404 | Missing/wrong route | Run `php artisan route:list`; check route name/path. |
| 419 | Missing CSRF/session issue | Check `@csrf`, method spoofing, session table. |
| Redirect loop | Role redirect mismatch | Check `AuthenticatedSessionController` and `/dashboard` dispatch. |
| Table/column not found | Migration not run | Check `php artisan migrate:status`; run migrate only when safe. |
| FK constraint failure | Migration order or parent row missing | Parent tables before child tables; verify seed data. |
| `UnhandledMatchError` | `match()` lacks default | Add a default arm or fallback threshold. |
| View/component not found | Blade path/tag mismatch | Dots map to folders: `<x-ui.badge>` -> `components/ui/badge.blade.php`. |
| Tailwind class missing | Assets not rebuilt | Run `npm run build`. |
| Gray map | Missing explicit height | Add `style="height: 560px;"` to `#phc-map`. |
| `L is not defined` | Leaflet JS loaded after map code | Load CDN before `@stack('scripts')`. |
| Empty map/DSS | No approved reports within 30 days | Approve/seed test data or verify scopes. |

## Verification Commands

```powershell
php artisan route:list
php artisan migrate:status
npm run build
php artisan route:clear
php artisan view:clear
```

Only run commands relevant to the fix. Confirm before destructive DB commands.
