# AI Decision Tree Bootstrap Guide

Use this to create the same compact AI documentation system for another project.

## Generated File Set

```text
AGENTS.md
CLAUDE.md
docs/agent-planning-mode.md
docs/agent-edit-mode.md
docs/agent-debug-mode.md
docs/dev-traits/learn.md
docs/dev-traits/traits.md
docs/dev-traits/skills.md
docs/notes/migration-standards.md
docs/notes/backend-rules.md
docs/notes/ui-components.md
```

## Project Inputs

Fill these before asking an AI to generate the docs:

```text
PROJECT_NAME:
ONE_LINE_PURPOSE:
BACKEND_STACK:
FRONTEND_STACK:
AUTH_SYSTEM:
ROLES:
DATABASE:
DEPLOYMENT_TARGET:
KEY_THIRD_PARTY:
COMPONENT_SYSTEM:
TEST_CREDENTIALS:
KNOWN_CONSTRAINTS:
```

## Bootstrap Prompt

```text
You are setting up compact AI working docs for a software project.

PROJECT
Name: [PROJECT_NAME]
Purpose: [ONE_LINE_PURPOSE]
Backend: [BACKEND_STACK]
Frontend: [FRONTEND_STACK]
Auth: [AUTH_SYSTEM]
Roles: [ROLES]
Database: [DATABASE]
Deployment: [DEPLOYMENT_TARGET]
Third-party: [KEY_THIRD_PARTY]
Components: [COMPONENT_SYSTEM]
Test credentials: [TEST_CREDENTIALS]
Constraints: [KNOWN_CONSTRAINTS]

Generate the file set below. Keep docs comprehensive but concise: no repeated explanations, no tutorial padding, and no stale code snippets unless they are essential examples.

1. AGENTS.md
- Full agent contract.
- Include mode inference, hard rules, stack, component inventory, security/data rules, references, current route map, credentials, and session report template.

2. CLAUDE.md
- Auto-loaded quick context.
- Include project definition, mode table, hard rules, reference map, current routes, known inconsistencies, credentials, future notes, and report template.

3. docs/agent-planning-mode.md
- Entry criteria, scope questions, audit checklist, stack map, security checklist, plan output format, and confirmation triggers.

4. docs/agent-edit-mode.md
- Pre-flight checklist and decision tree for CRUD, UI, routes, migrations, seeders, forms, backend bugs, auth, and third-party integrations.

5. docs/agent-debug-mode.md
- Stop rule, symptom collection, layer identification, root-cause note, targeted fix, verification, and common error table.

6. docs/dev-traits/learn.md
- Living bug log with format template and initial entries for DB index length, FK order, auth/middleware, frontend/build, and key third-party gotchas.

7. docs/dev-traits/traits.md
- Naming, structure, route, component, backend, frontend, JS, database, and security conventions.

8. docs/dev-traits/skills.md
- Can do, must not do, confirm first, and known gaps.

9. docs/notes/migration-standards.md
- Robust migration rules for the database engine: engine setup, FK shorthand, delete behavior, dependency order, rollback/down methods, seeders, deployment checklist.

10. docs/notes/backend-rules.md
- Thin controllers, service layer, validation, mass assignment, N+1 prevention, scopes/named filters, middleware permissions, API data limits, and file placement.

11. docs/notes/ui-components.md
- No repeated markup, component inventory, authoring rules, naming conventions, layout/script rules, and examples adapted to the component system.

After generating, print a summary table with file paths and approximate line counts.
```

## Maintenance Rules

- `CLAUDE.md` stays short because it is auto-loaded.
- `AGENTS.md` is the binding contract.
- `docs/agent-*.md` are workflow protocols.
- `docs/dev-traits/*` are project memory and conventions.
- `docs/notes/*` are area references read only when relevant.
- Update route maps and component inventory immediately when code changes.
- Add every repeatable production/debug lesson to `learn.md`.

## Why This Works

```text
CLAUDE.md / AGENTS.md
  -> infer mode
     -> planning, edit, or debug protocol
        -> read only relevant notes
           -> implement/verify/report
```

The goal is low context cost with high rule retention: short docs, clear routing, no duplicate lectures.
