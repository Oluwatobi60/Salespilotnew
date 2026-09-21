# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Overview

SalesPilot (branded "VigCore" in-app) is a Laravel 12 multi-tenant SaaS point-of-sale / inventory platform. Each "business" signs up, gets a subscription plan, and can add managers, staff and branches under it. There is also a BRM (Business Relationship Manager) sales/commission layer and a Superadmin back office.

## Commands

```bash
# Install & environment setup (also runs migrate + npm build)
composer setup

# Local dev: PHP server + queue worker + Vite, concurrently
composer dev

# Run the test suite (Pest, backed by PHPUnit)
composer test
# or directly:
php artisan test
php artisan test --filter=CategoryPermissionTest   # single test file
php artisan test tests/Feature/CategoryPermissionTest.php

# Lint / format PHP
vendor/bin/pint
vendor/bin/pint --test        # check only, no writes

# Frontend
npm run dev                   # Vite dev server
npm run build                 # production assets

# Subscription lifecycle (also scheduled, see routes/console.php)
php artisan subscriptions:check-expired
php artisan subscriptions:process-renewals

# Backups (app-level; separate from the shell scripts in repo root)
php artisan backup:database   # writes to storage/app/backups/, 10-backup retention
```

Tests use `sqlite :memory:` and `RefreshDatabase` (configured in `tests/Pest.php` / `phpunit.xml`) — no external DB needed to run the suite.

## Architecture

### Four separate auth guards, one `role` column

`config/auth.php` defines four independent session guards, each with its own Eloquent model and password-reset table: `web` → `App\Models\User`, `staff` → `App\Models\Staffs`, `superadmin` → `App\Models\SuperAdmin`, `brms` → `App\Models\Brm`. Within the `web` guard, `User.role` further distinguishes `manager` (business owner/creator or an added manager) vs other values. The `rolemanager:<role>` middleware alias (`App\Http\Middleware\RoleManager`) checks `Auth::user()->role` and redirects to the right home route per role — it does not change guards. `bootstrap/app.php` centrally wires per-guard login redirects (`redirectGuestsTo`) and post-login home routes (`redirectUsersTo`), including the subscription check baked into the default redirect.

### Business hierarchy is encoded in `users.addby`, not a separate table

There is no `businesses` table. `User.addby`:
- `null` (or equal to the user's own email/id) → this user is the business creator/owner (`isBusinessCreator()`).
- set to the creator's email → this is a manager added by that creator, and inherits the creator's `business_name`, subscription and feature access.

Tenant-scoped data (categories, suppliers, units, items, staff, discounts, etc.) is scoped by the `business_name` string column (added managers share their creator's `business_name`), and some staff-related resources are scoped by `manager_email` instead. `App\Traits\AuthorizesBusinessResources` is the standard way to enforce this scoping — use `findAndAuthorize()`/`scopedQuery()`/`authorizeByManagerEmail()`/`authorizeStaff()` in any new controller that touches tenant data. This trait exists specifically because IDOR vulnerabilities were found and fixed across ~7 controllers by adding this scoping; skipping it on new CRUD endpoints reintroduces that class of bug.

### Subscriptions and feature gating

`SubscriptionPlan` has a JSON `features` array of feature slugs; `SubscriptionFeature` is the catalog of all possible slugs, prefixed by who they apply to (no prefix = business creator, `manager_*`, `staff_*`). `UserSubscription` links a `User` to a plan with `status` (`active`/`pending`/`expired`/`cancelled`) and `end_date`. Key points:
- `CheckSubscriptionStatus` middleware (aliased `check.subscription`, applied to manager routes) redirects to `plan_pricing` when there's no valid active subscription, and self-heals stale `active` rows whose `end_date` has passed.
- Managers created via `addby` don't have their own subscription — `user_has_feature()` / `user_subscription_features()` (in `app/Helpers/SettingsHelper.php`) fall back to the creator's subscription.
- Only one active subscription per user at a time: upgrading/downgrading (`SignupController::cancelExistingSubscriptions()`) cancels the old one and activates the new one immediately — there's no proration or overlap handling.
- Feature checks today are enforced in the UI (menu visibility via `user_has_feature('slug')`) and in some controllers directly (e.g. category create/update deny with a flash `error` message) — when adding a new gated feature, don't assume hiding the nav link is sufficient; add the same check in the controller.
- `subscriptions:check-expired` (daily) and `subscriptions:process-renewals` (daily at 08:00, handles auto-renewal + expiry reminder emails) are the scheduled jobs in `routes/console.php`; there's no queue worker requirement for these (they're `Schedule::command`, not queued jobs), but `composer dev` does run `queue:listen` for other queued work (e.g. mail).

### App-wide settings via `AppSetting`, never queried directly

`app/Helpers/SettingsHelper.php` (autoloaded as a Composer `files` entry, so its functions are globally available with no `use`/import) wraps the `AppSetting` model: `setting()`, `settings()`, `update_setting()`, plus typed helpers like `app_name()`, `currency_symbol()`, `default_timezone()`, `format_date()`, `max_upload_size()`. These are backed by Superadmin-editable settings with an internal cache that's invalidated on write. Always go through these helpers rather than querying `AppSetting` directly. `App\Http\Middleware\ApplySystemPreferences` (applied globally to all web requests) and `AppServiceProvider` push things like timezone/pagination/currency into the request and into every view.

### File uploads: two storage layouts coexist

New uploads go through `Storage::disk('public')->store(...)` (validated with `image|mimes:...|max:...`), landing under `storage/app/public/...` and served via the `public/storage` symlink (`php artisan storage:link` is a required one-time step on a fresh environment). Older data still has paths under `public/uploads/...`. Views/helpers that resolve an image path must handle both forms (check for a literal `uploads/` prefix) — don't assume everything lives under the newer `storage/app/public` layout.

### Frontend: Vite/Tailwind/Alpine for scaffolding, plain JS modules for manager UI widgets

Livewire is a Composer dependency but the actual interactive manager-panel widgets (category "quick add" panel, supplier panel, password strength validator, loading-button behavior) are hand-written vanilla JS classes under `public/manager_asset/js/...`, wired up with plain `<script>` tags (no bundling), each paired with a Blade component for markup and talking to JSON endpoints via `fetch`/AJAX — not Livewire components. The manager area also has a custom AJAX navigation layer (`public/manager_asset/js/ajax-navigation.js`) that intercepts sidebar clicks and swaps the `.content-wrapper` content instead of doing full page loads. Any page-specific JS for manager views must (re-)initialize on the custom `ajaxContentInitialized` DOM event, not `DOMContentLoaded`, or it will silently fail to run when the page is reached via sidebar navigation instead of a hard reload.

### AI features

`app/Services/GeminiService.php` calls the Gemini API for text generation (category suggestions, product descriptions, price recommendations, and a manager-facing "AI copilot" chat). The API key is resolved from the `AppSetting` (`gemini_api_key`, set via Superadmin) with a fallback to `GEMINI_API_KEY` in `.env`. Controllers: `App\Http\Controllers\Manager\AIInventoryController` and `AICopilotController`.

### Deploy/ops tooling (repo root)

Production deploys via Docker Compose (`docker-compose.yml`: `app`, `mysql`, `nginx` services) using `deploy.sh`/`deploy-simple.sh`, which assume a fixed remote path and container names. There are two independent, non-overlapping backup mechanisms: the in-app `php artisan backup:database` command (see above) and the shell-level `backup.sh`/`backup_enhanced.sh` (`docker exec ... mysqldump` to a host `/backup` directory, 7-day retention) — don't assume one covers the other. `git-untrack-uploads.sh`/`.ps1` are one-off remediation scripts for retroactively untracking upload directories, not part of normal workflow. Note: `docker-compose.yml` and the backup/deploy shell scripts currently contain a hardcoded MySQL root password checked into source control — treat this as a known, pre-existing issue rather than something to silently change; flag it if asked to touch those files.

## Testing conventions

Tests are Pest (`tests/Feature`, `tests/Unit`), using `RefreshDatabase` against sqlite in-memory. Feature tests that exercise tenant scoping/permissions (e.g. `tests/Feature/CategoryPermissionTest.php`) construct a "creator" `User` (`addby => null`) and an "added manager" `User` (`addby => creator's email`, same `business_name`) to assert that the `addby` hierarchy and feature gating behave correctly — follow this same two-user pattern when testing any business-scoped or feature-gated behavior.
