# 4-Week Build Roadmap

## Project Vision
Build a Laravel 12 web application that starts as a simple multi-tenant CRM with authentication and role-based access control and is designed to evolve into an API-driven platform for a future mobile application.

## Tech Stack
- Laravel 12
- Inertia.js + React
- Vite
- PHPUnit
- GitHub Actions (CI)
- Single database (tenant + location scoping)
- Local file storage (avatars)

---

## Week 1 — Foundation: Inertia React + Auth + CI

### Goal
App boots cleanly, auth works, CI runs on every push/PR.

### Build
- Install Laravel 12 + Inertia React starter kit
- Auth flows:
  - Register
  - Login
  - Forgot/Reset Password
- Protected `/dashboard`
- Basic app layout (Inertia + React)

### CI
- GitHub Actions pipeline:
  - `composer install`
  - `php artisan test`
  - Node install + `npm run build`

### Tests
- User can register
- User can login/logout
- Dashboard requires authentication

### Done Criteria
Fresh clone → migrate → login → dashboard, CI green.

---

## Week 2 — Tenancy: Tenant → Locations + Context

### Goal
Users belong to multiple locations and must select/switch current location.

### Build
- Models & migrations:
  - `Tenant`
  - `Location` (belongs to Tenant)
  - `LocationMembership` (user ↔ location)
- Location selection logic:
  - 1 location → auto-select
  - Multiple locations → select screen
- Store `current_location_id` in session
- Middleware: `EnsureLocationSelected`
- UI:
  - Select Location page
  - Dashboard shows Tenant + Location
  - Location switcher

### Tests
- User with 1 location auto-selects
- User with multiple locations must select
- Location switch updates session
- Cannot select a location user doesn’t belong to

### Done Criteria
All app routes consistently enforce location context.

---

## Week 3 — Authorization: Roles, Permissions, Impersonation

### Goal
Location-scoped access works; admins define roles; superadmin impersonates.

### Build
- Role & permission system:
  - `roles` (location-scoped)
  - `permissions`
  - `role_permissions`
  - `LocationMembership.role_id`
- Permission-based editors (Editor1/2/3 via role configs)
- Policies enforce access per location
- Superadmin impersonation:
  - Start/stop impersonation via session
- Admin UI:
  - Manage roles
  - Assign permissions
  - Assign roles to users per location

### Initial Permissions
- `posts.view`
- `posts.create`
- `posts.edit.own`
- `posts.edit.any`
- `users.manage`
- `roles.manage`

### Tests
- Same user has different access in different locations
- Admin can manage roles in location only
- Editors limited by permission set
- Superadmin can impersonate and revert

### Done Criteria
Access = (user + location + role permissions), proven by tests.

---

## Week 4 — Posts, Comments, Avatars, API-Ready Structure

### Goal
First real feature set with correct scoping and future API support.

### Build
- Posts (single table, flexible):
  - `location_id`, `author_id`
  - `type`, `title`, `content`
  - `settings` (JSON)
  - soft deletes
- Comments (belongs to posts)
- Policies: view/create/edit (own vs any)
- Inertia React pages:
  - Posts index
  - Create/Edit
  - Post detail + comments
- Avatars:
  - Local storage `users.avatar_path`
- Move business logic into services (API-ready)

### Tests
- Posts scoped to location
- Cannot access posts from another location
- Permission-restricted create/update
- Comments scoped via post
- Avatar upload stores file + updates user

### Done Criteria
Admins manage posts/users; editors limited by role; viewers read-only.

---

## Core Domain Concepts

### Tenancy Model
- **Tenant (Company)**: Owns one or more Locations
- **Location**: Primary data boundary for posts, comments, users, and permissions
- **User**: One global login identity; can belong to multiple locations across different tenants with different roles per location

Example:
`zamy@email.com` can be Viewer in Location A (Tenant X) and Admin in Location B (Tenant Y).

### Authentication Strategy (MVP-Friendly)
Start with Laravel 12 starter auth:
- Register
- Login
- Forgot/Reset Password

Use Inertia.js with React (or Vue).

No API required in Phase 1, but architecture must support adding APIs later without rewriting business logic.

### Roles & Permissions (Location-Scoped)

#### Global roles
- `superadmin` (global access, impersonation)
- `owner`
- `admin`
- `editor`
- `viewer`

#### Location role assignments
Roles are assigned per location, not globally.

#### Permission-driven editors
Use configurable permission sets instead of hard-coded editor types:
- Editor 1: edit any post
- Editor 2: create posts + edit own posts
- Editor 3: manage users

`owner`, `admin`, and `superadmin` can:
- Create custom editor roles
- Assign permissions to roles
- Assign roles to users per location

Implementation guidance:
- Use database-driven role + permission model
- JSON permissions acceptable early; normalized preferred long-term
- Enforce with Laravel Policies

---

## MVP Screens (Phase 1)
- Register
- Login
- Forgot Password
- Dashboard (with tenant + location context and switching)
- Optional if time allows: simple posts list/create/edit inside a location

---

## Content System (Posts – Designed for Growth)

### Recommended single-table post strategy
`posts` table fields:
- `id`
- `location_id`
- `author_id`
- `type` (`post`, `image`, `video`, `form`, `poll`)
- `title`
- `content`
- `settings` (JSON)
- timestamps
- soft deletes

Benefits:
- One migration for all tenants
- Easier testing
- Smooth evolution into specialized post types

### Comments
- Comments belong to posts
- Posts belong to locations
- Access enforced via location scoping

### File uploads
- Local storage initially (MVP: avatars)
- Design for future S3 migration with minimal refactor

---

## Impersonation
- `superadmin` can impersonate any user
- Must be explicit and reversible
- Must respect location context
- Use session-based impersonation

---

## Web Now, API Later

### Phase 1
- Inertia web app
- Controllers + policies
- Clean domain logic

### Phase 2
- Add `/api` routes
- Reuse services and policies
- Introduce Laravel Sanctum

### Phase 3
- Mobile app consumes API
- Same auth + permission model
- Location selection via API

---

## Testing & Maintainability
- Single database with `tenant_id` and `location_id` scoping
- Avoid per-tenant databases/migrations
- Feature tests for:
  - Auth flows
  - Location scoping
  - Role/permission enforcement
  - Superadmin impersonation
- Prefer explicit policies over magic middleware

---

## CI/CD Summary

### CI (Required)
GitHub Actions on push + PR:
- Backend tests: `php artisan test`
- Frontend build: `npm run build`

### CD (Optional)
- Deploy on `main` or tagged releases
- Run migrations + cache config
- Ready for Forge/VPS/SaaS hosting

---

## Target Outcome After 4 Weeks
- Multi-tenant CRM (Tenant → Locations)
- Users can belong to multiple locations/tenants
- Location-scoped roles & permissions
- Superadmin impersonation
- Flexible post system (future forms/polls)
- Clear path to API + mobile app
