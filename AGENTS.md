# AGENTS.md

## Cursor Cloud specific instructions

### Overview

RealBrick Dealer/Client CRM Portal — a Laravel 12 application with two role-based portals (Admin and Dealer). Uses SQLite (file-based), Vite + Tailwind CSS 4 for frontend, and optionally integrates with Bitrix24 REST API for product catalog.

### System prerequisites

- **PHP 8.2+** with extensions: cli, common, curl, mbstring, xml, zip, sqlite3, bcmath, intl, gd (installed from `ppa:ondrej/php`)
- **Composer** (installed globally at `/usr/local/bin/composer`)
- **Node.js 22+** and **npm** (pre-installed in the VM)

### First-time setup (after update script)

If `.env` does not exist:
```bash
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate --force
php artisan db:seed --force
```

### Running services

- **Laravel dev server**: `php artisan serve --host=0.0.0.0 --port=8000`
- **Vite dev server**: `npx vite --host 0.0.0.0 --port 5173`
- **Combined dev** (uses concurrently): `composer dev` — starts Laravel server, queue worker, log tail, and Vite simultaneously

### Key commands

| Task | Command |
|------|---------|
| Lint | `./vendor/bin/pint --test` |
| Lint fix | `./vendor/bin/pint` |
| Tests | `php artisan test` |
| Build frontend | `npm run build` |
| Migrations | `php artisan migrate --force` |

### Default credentials

The database seeder creates an admin user:
- Email: `admin@example.com`
- Password: `password`

### Gotchas

- The root route `/` redirects to login (302) — this is expected behavior, not an error. The `ExampleTest` feature test fails because it expects 200 from `/` but gets 302.
- Bitrix24 API integration is optional. Without valid webhook credentials in `.env`, product catalog pages will gracefully degrade (empty results).
- The app uses `DB_CONNECTION=sqlite` by default — no external database server needed. The SQLite file lives at `database/database.sqlite`.
- Session, cache, and queue all use the `database` driver by default — all backed by SQLite.
