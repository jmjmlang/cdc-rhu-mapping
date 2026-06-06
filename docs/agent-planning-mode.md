# Agent: Planning Mode

Use for architecture, schema design, feature planning, redesigns, audits, and "how should we build X?" prompts. Do not edit code until the plan is written and any required confirmation is handled.

## Entry Criteria

Planning mode applies to prompts like:

- "How should we build/export/add/design..."
- "Should we add a table/column/role..."
- "Plan the admin dashboard/DSS/map feature..."
- "What is the best approach..."
- "Review the architecture..."

## Protocol

1. Read context:
   - Always: `docs/dev-plan-main.md`
   - Schema: `docs/notes/migration-standards.md`
   - Backend/query: `docs/notes/backend-rules.md`
   - Blade/UI: `docs/notes/blade-components.md`
   - Map: `docs/notes/leaflet-osm-guide.md`
   - Past issues: `docs/dev-traits/learn.md`

2. Define scope:
   - Which roles use it: `admin`, `rhu`, `citizen`, or authenticated users?
   - What data is created/read/updated/deleted?
   - Does it touch reports, map, DSS, activity log, verification, auth, or profile?
   - Does it expose health data or PII?

3. Audit existing code before proposing new files:

| Need | Check |
|---|---|
| Components | `resources/views/components/` |
| Routes | `routes/web.php` |
| Controllers | `app/Http/Controllers/` |
| Models/scopes | `app/Models/` |
| Services | `app/Services/` |
| Migrations/schema | `database/migrations/` |
| Seeders | `database/seeders/` |
| DSS thresholds | `app/Services/DssService.php` |

4. Map the stack in dependency order:

```text
Migration/Seeder -> Model/scopes/relations -> Service -> Controller -> Route/middleware -> Blade/components -> JS via @push('scripts') if needed
```

5. Security checklist:

| Question | Action |
|---|---|
| Needs login? | Add `auth` middleware. |
| Needs role restriction? | Add `role:admin`, `role:rhu`, or `role:citizen`; role failures use `abort(403)`. |
| Accepts input? | Validate before writes. |
| Writes DB? | Use validated data only; confirm destructive operations. |
| Exposes report/map/DSS data? | Apply `approved()->withinDays(30)` unless explicitly confirmed otherwise. |
| Adds role/schema/threshold/coordinates? | Confirm before implementation. |

6. Write the plan:

```text
WHAT EXISTS
  [relevant current files/behavior]

WHAT CHANGES
  [ordered migration -> model -> service -> controller -> route -> view/component/test/cache steps]

SECURITY AND DATA RULES
  [middleware, validation, scopes, PII limits]

CONFIRMATION NEEDED
  [destructive DB, schema, role, thresholds, coordinates, architecture]
```

After confirmation, switch to `docs/agent-edit-mode.md`.
