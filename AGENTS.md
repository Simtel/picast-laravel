# Picast Laravel — Project Overview for AI Coding Agents

## Project Identity

**Picast** is a Laravel 13 web application for domain WHOIS monitoring, YouTube video management, tournament tracking, ChadGPT AI chat integration, and user/role management. It follows Domain-Driven Design (DDD) with a context-based architecture.

- **License:** MIT
- **Language:** Russian (`ru` locale), with fallback to `en`
- **Timezone:** `Europe/Moscow`
- **PHP requirement:** `>= 8.4` (runs on 8.5 in Docker)

---

## Technology Stack

### Backend
| Category         | Technology                     |
|------------------|--------------------------------|
| Framework        | Laravel 13                     |
| PHP              | 8.5 (Alpine-based Docker image) |
| Database         | MySQL 8.3.0                    |
| Cache            | Memcached (`memcached:latest`) |
| Queue            | Laravel Queue (database driver) with Supervisor |
| Authentication   | Session-based (web) + token-based (`auth:api` guard) |
| Authorization    | `spatie/laravel-permission` ^7.0 |
| Image processing | `intervention/image` ^4.0 (GD driver) |
| WHOIS            | `io-developer/php-whois` ^4.0   |
| YouTube download | yt-dlp binary (`/usr/local/bin/youtube-dl`) wrapped by `norkunas/youtube-dl-php` |
| YouTube API      | `alaouy/youtube` ^2.2           |
| HTTP client      | `guzzlehttp/guzzle` ^7.0        |
| API docs         | `darkaonline/l5-swagger` ^11.0 + `knuckleswtf/scribe` ^5.0 |
| IDE helpers      | `barryvdh/laravel-ide-helper` ^3.1 |

### Frontend
| Category     | Technology                            |
|-------------|---------------------------------------|
| Build tool   | Vite 7 (`laravel-vite-plugin` ^2.0)   |
| CSS          | Tailwind CSS 4, Bootstrap 5.3         |
| JS           | jQuery 4, Axios, Marked (markdown)    |
| Icons        | Font Awesome Free 7                   |
| Templates    | Laravel Blade                         |

### Infrastructure (Docker Compose)
| Service   | Image / Technology          | Ports      |
|-----------|-----------------------------|------------|
| `db`      | MySQL 8.3.0                 | 3306:3306  |
| `php`     | Custom PHP 8.5-fpm-alpine   | —          |
| `nginx`   | nginx:1.17                  | 80:80      |
| `adminer` | Adminer (DB management)     | 8080:8080  |
| `memcached` | memcached:latest          | —          |
| `mailhog` | MailHog (email testing)     | 1025, 8025 |

Data volumes: MySQL data stored at `.docker/db/`, mounted configs at `.docker/conf/mysql/` and `.docker/php/`.

---

## Build & Run Commands

All commands are executed inside Docker containers unless noted otherwise. A `Makefile` wraps common operations.

### Quick Reference

```bash
# Start/stop environment
make up              # docker compose up -d --remove-orphans
make down            # docker compose down
make build           # docker compose build (rebuild images)
make restart         # stop + up

# Access services
make cli             # shell into picast_php as www-data
make mysql-console   # MySQL CLI as root

# Dependencies
make composer-install  # composer install (inside container)
npm install            # run on host (Node needed) or via `make npm-install`

# Database
make migrate         # php artisan migrate (both default + testing env)
make seed            # php artisan db:seed (both default + testing env)

# Frontend
npm run dev          # Vite dev server with HMR
npm run build        # Vite production build + copy-static-assets.js
make npm-build       # same, inside container

# Testing
make test            # php artisan test
make test-coverage   # php artisan test --coverage-html tests/reports/coverage

# Code quality
make phpstan         # ./vendor/bin/phpstan analyse --memory-limit=2G
make pint            # ./vendor/bin/pint --parallel

# Queue worker
make worker          # supervisord with laravel-worker.conf
```

### Database Setup

The `.env.example` file contains defaults:
```
DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=picast
DB_USERNAME=root
DB_PASSWORD=example
```

A test database `picast_test` should also exist (the Makefile runs migrations on both).

---

## Architecture: Context-Based DDD

The application code under `app/` follows **Domain-Driven Design** with contexts:

```
app/
├── Common/           # Shared infrastructure (CommandBus, interfaces)
├── Console/          # Artisan kernel + swagger annotations
├── Context/
│   ├── ChadGPT/      # AI chat bot
│   ├── Common/       # Shared domain models (Images, InviteCode) + commands
│   ├── Domains/      # Domain WHOIS management
│   ├── Tournaments/  # Tournament listings
│   ├── User/         # User profile & invitation
│   └── Youtube/      # YouTube video processing
├── Exceptions/       # Exception handler
├── Http/             # Legacy controllers (Auth)
├── Mail/             # (empty — mails live in Contexts)
├── Notifications/    # (empty)
└── Providers/        # Service providers
```

### Context Internal Structure

Each context follows a three-layer architecture:

| Layer          | Directory              | Purpose                                                    |
|---------------|------------------------|------------------------------------------------------------|
| **Domain**     | `Domain/`              | Eloquent Models, Events, Factories, Observers, Resources, Exceptions, Commands/Queries |
| **Application**| `Application/`         | Services, Policies, Contracts (interfaces), DTOs/Data objects, QueryHandlers |
| **Infrastructure** | `Infrastructure/`  | HTTP Controllers (Web + API), Artisan Commands, Jobs, Mail, Notifications, Repositories, Request validators, Handlers, Facades, Event Listeners |

### Command Bus (CQRS-light)

A simple `CommandBus` (`App\Context\Common\Infrastructure\CommandBus`) is registered in `AppServiceProvider`. Commands implement `CommandInterface`, handlers implement `CommandHandlerInterface`. Handlers are mapped in `AppServiceProvider::register()`:

```php
$bus->register(CreateChatConversationCommand::class, CreateChatConversationHandler::class);
$bus->register(ListDomainsQuery::class, ListDomainsQueryHandler::class);
```

### Domain Contexts Detail

#### ChadGPT
- **Models:** `ChadGptConversation`, `ChadGptConversationWordStat`
- **Web routes:** `GET /personal/chadgpt`, `POST /personal/chadgpt/send-message`, `DELETE /personal/chadgpt/clear-history`
- **API routes:** `GET/POST/DELETE /api/v1/chats`
- **Config:** `config/chadgpt.php` — API key & base URL for `ask.chadgpt.ru`

#### Domains
- **Models:** `Domain`, `Whois`
- **Web routes:** `Route::resource('domains', ...)` + custom routes for WHOIS
- **API routes:** `Route::apiResource('domains', ...)`
- **Policies:** `DomainPolicy`
- **Events/Listeners:** `DomainCreated` → `GetWhoisDomain`
- **Scheduled:** `domains:whois` daily, `CheckExpireDomains` job daily
- **Observers:** `DomainObserver`
- **Contracts:** `WhoisService`, `WhoisUpdater` (interfaces in `Application/Contract/`)

#### Youtube
- **Models:** `Video`, `VideoFormats`, `VideoFile`, `VideoDownloadQueue`, `VideoStatus`
- **Web routes:** CRUD under `/personal/youtube` (permission: `edit youtube`)
- **API routes:** `Route::apiResource('videos', ...)`
- **Policies:** `YouTubeVideoPolicy`
- **Events/Listeners:** `YouTubeVideoCreated` → `YouTubeVideoCreateListener`
- **Observers:** `YouTubeVideoObserver`
- **Artisan commands:** `youtube:download` (in `Infrastructure/Commands/`)
- **Download tool:** yt-dlp binary at `/usr/local/bin/youtube-dl`

#### Tournaments
- **Models:** `Tournament`, `TournamentGroup`
- **Web routes:** `GET /personal/tournaments`, `GET /personal/tournaments/{id}`
- **API routes:** `GET /api/v1/tournaments`, `GET /api/v1/tournaments/{id}`
- **Scheduled:** `tournaments:fetch` daily → then `tournaments:groups:fetch`
- **Source:** Data from `simtel/dancemanager-scraper` package

#### User
- **Model:** `User` (extends `Authenticatable`, uses `HasRoles`, `SoftDeletes`, `Notifiable`)
- **Web routes:** `/personal/settings`, `/personal/invite`, `/personal/user/edit/{user}`
- **API:** `GET /api/v1/user/current`
- **Related:** `Images` gallery under `/personal/images` (permission: `edit images`)

#### Common (shared)
- **Models:** `Images`, `InviteCode`
- **Artisan commands:** `app:create-database`, `process:queue-jobs`

---

## Routing

### Web Routes (`routes/web.php`)
- `Auth::routes()` provides standard Laravel auth endpoints
- `GET /` → redirects to `/personal` if logged in, else shows login view
- `/personal/*` — all authenticated routes grouped with `auth` middleware
- Route names follow Laravel conventions (e.g. `personal`, `settings`, `domains.index`, `youtube.index`)

### API Routes (`routes/api.php`)
- Prefix: `/api/v1`
- Middleware: `auth:api` (token-based)
- Fallback returns 404 JSON
- Resource names: `api.domains`, `api.videos`, `api.tournaments`, `api.chats`

### Route Service Provider
- Located at `app/Providers/RouteServiceProvider.php`
- Maps `routes/web.php` (middleware: `web`) and `routes/api.php` (prefix: `api`, middleware: `api`)

---

## Code Style Guidelines

Configured in `pint.json`:
- **Preset:** PSR-12
- **Additional rules:**
  - `declare_strict_types: true` — every PHP file must start with `declare(strict_types=1);`
  - `no_unused_imports: true` — no unused `use` statements
  - `static_lambda: true` — closures should be `static` when no `$this` binding needed
- **Excluded paths:** `.github`, `.docker`, `.hooks`, `storage`

Run `make pint` to auto-fix. CI checks with `vendor/bin/pint --test`.

**Observed conventions:**
- All classes use `final` keyword
- Models have explicit getter methods (e.g. `getId()`, `getName()`, `getEmail()`) rather than accessing properties directly
- Generics PHPDoc is used extensively for Eloquent relationships (e.g. `@return HasMany<VideoFormats, $this>`)
- Properties are documented with `@property` PHPDoc tags for IDE support
- Controllers, handlers, and commands use `final class`
- Blade templates are under `resources/views/` with Russian-named directories (`personal/`, `auth/`, `mail/`)

---

## Testing

### Framework
- **PHPUnit 13** with `phpunit.xml` in project root
- **Bootstrap:** `tests/bootstrap.php` — sets up app, ensures user #1 has an API token
- **Base TestCase:** `tests/TestCase.php`

### TestCase Features
`Tests\TestCase` extends Laravel's `TestCase` and uses:
- `DatabaseTransactions` trait — wraps each test in a transaction
- Helper methods:
  - `loginAdmin()` — logs in as user ID 1
  - `authUserWithPermissions(array $attributes, array $permissions)` — creates user with role+permissions and logs in
  - `createUserWithPermissions(array $attributes, array $permissions)` — creates without logging in
  - `getAuthUser()` — returns current authenticated user
  - `getAdminUser()` — fetches user ID 1

### Test Structure
```
tests/
├── Feature/          # Integration tests
│   ├── Api/          # API endpoint tests
│   ├── Auth/         # Authentication tests
│   ├── ChadGPT/      # ChadGPT feature tests
│   ├── Command/      # Artisan command tests
│   ├── Common/       # Common feature tests
│   ├── Domain/       # Domain feature tests
│   └── YouTube/      # YouTube feature tests
└── Unit/             # Isolated unit tests
    ├── Common/       # CommandBus tests
    └── Context/      # Per-context unit tests (ChadGPT, Domains, Youtube, Tournaments, User)
```

### Running Tests
```bash
make test                     # All tests
php artisan test --env=github  # With GitHub CI env
php artisan test --filter DomainTest  # Specific test
```

### CI Testing
In GitHub Actions, tests run with:
- MySQL 8.0 service container
- `--env=github` flag using `.env.github` config
- Steps: create database → migrate → seed `YouTubeVideoStatusSeeder` → run tests

---

## Static Analysis

**PHPStan** configured in `phpstan.neon`:
- **Level:** `max`
- **Extensions:** Larastan, PHPStan-Mockery, Carbon
- **Paths analyzed:** `app`, `tests`, `database/migrations`
- **Ignored errors:** `method.nonObject`, `argument.type`, `property.nonObject`, `binaryOp.invalid`
- Run: `make phpstan`

---

## CI/CD Pipeline

Defined in `.github/workflows/laravel.yml`, triggered on push to `master`:

1. **Lints** (job: `lints`)
   - PHP 8.5, `composer install`
   - `vendor/bin/phpstan analyse`
   - `vendor/bin/pint --test`

2. **Tests** (job: `tests`)
   - PHP 8.5 with GD extension
   - MySQL 8.0 service container (port 33306)
   - Create DB → migrate → seed → `php artisan test --env=github`

3. **Publish** (job: `publish`, needs lints+tests)
   - SSH to production server
   - `git pull` → `composer install --no-dev` → `php artisan migrate --force` → `db:seed` → `queue:restart`

### Pre-commit Hook
Located at `.hooks/pre-commit` — runs PHPStan inside the Docker container. Block commits with errors. Set up with `make set-githooks`.

---

## Environment Files

| File              | Purpose                                           |
|-------------------|---------------------------------------------------|
| `.env`            | Local development (gitignored)                    |
| `.env.example`    | Template with defaults for local dev              |
| `.env.testing`    | Used by PHPUnit (`APP_ENV=testing`)               |
| `.env.github`     | Used by GitHub Actions CI                         |

Key env variables (see `.env.example` for full list):
- `APP_URL=https://picast.lc`
- `DB_HOST=db`, `DB_DATABASE=picast`, `DB_USERNAME=root`, `DB_PASSWORD=example`
- `QUEUE_DRIVER=database`
- `CHADGPT_API_KEY`, `YOUTUBE_API_KEY`
- `AWS_ENDPOINT`, `AWS_ACCESS_KEY_ID`, `AWS_SECRET_ACCESS_KEY`, `AWS_BUCKET` (S3-compatible storage)
- `MAIL_HOST=mailhog`, `MAIL_PORT=1025`
- `DEFAULT_USER_NAME`, `DEFAULT_USER_EMAIL`, `DEFAULT_USER_PASSWORD` (for seeding)

---

## Scheduled Tasks

Defined in `app/Console/Kernel.php`:
- `domains:whois` — daily WHOIS updates
- `CheckExpireDomains` job — daily expiry checks
- `tournaments:fetch` → `tournaments:groups:fetch` — daily, runs in background

Queue workers run via Supervisor (config at `.docker/supervisor/conf/laravel-worker.conf`):
- Command: `php artisan queue:work -v --sleep=3 --tries=3`
- Single process, auto-restart

---

## Storage & File Handling

- Default disk: `local`
- S3-compatible cloud disk configured via `AWS_*` env vars (targets Selectel)
- Image uploads go to `storage/upload/images/` (config: `TMP_FILE_UPLOADS`)
- Thumbnail dimensions: 170×130 (config: `THUMB_WIDTH`, `THUMB_HEIGHT`)
- Static assets built by Vite to `public/build/`
- Font Awesome webfonts copied to `public/webfonts/` by `copy-static-assets.js`

---

## Key Dependencies Summary

### Production (`require`)
- `laravel/framework` ^13.0
- `spatie/laravel-permission` ^7.0 — roles and permissions
- `spatie/laravel-data` ^4.17 — DTO/data objects
- `spatie/laravel-html` * — HTML builder
- `intervention/image` ^4.0 + `intervention/image-laravel` ^4.0 — image processing
- `io-developer/php-whois` ^4.0 — WHOIS queries
- `alaouy/youtube` ^2.2 — YouTube API
- `norkunas/youtube-dl-php` dev-master — yt-dlp wrapper
- `pusher/pusher-php-server` ^7.0 — real-time events
- `league/flysystem-aws-s3-v3` ^3.0 — S3 filesystem
- `guzzlehttp/guzzle` ^7.0 — HTTP client
- `doctrine/dbal` ^4.0 — DB schema management
- `simtel/dancemanager-scraper` ^v3.0 — tournament data source

### Dev (`require-dev`)
- `larastan/larastan` ^3.0 — PHPStan for Laravel
- `laravel/pint` ^1.2 — code style
- `phpunit/phpunit` ^13.0 — testing
- `phpstan/phpstan-mockery` ^2.0 — Mockery PHPStan extension
- `barryvdh/laravel-debugbar` ^4.0 — debugging toolbar
- `knuckleswtf/scribe` ^5.0 — API doc generation
- `roave/security-advisories` dev-latest — security vulnerability checks

---

## Authentication & Authorization

- **Web guard:** session-based (`auth` middleware)
- **API guard:** token-based (`auth:api` middleware), token stored in `api_token` column on `users` table
- **User model:** `App\Context\User\Domain\Model\User`
- **Roles/Permissions:** `spatie/laravel-permission` with admin role seeded via migrations
- **Policies:** `DomainPolicy`, `YouTubeVideoPolicy` — registered in `AuthServiceProvider`
- **Middleware in routes:** `can:edit user`, `can:edit images`, `permission:domains`, `permission:edit youtube`

---

## Migrations & Database

50 migration files in `database/migrations/`, covering:
- Users (with soft deletes, API tokens, birth date)
- Password resets
- Permission tables (spatie)
- Domains + WHOIS (with foreign keys)
- YouTube videos, formats, files, statuses, download queue
- ChadGPT conversations + word stats
- Tournaments + tournament groups
- Images, invite codes, jobs, failed jobs

**Seeders:**
- `DatabaseSeeder` — main seeder
- `UsersTableSeeder` — creates default admin user
- `YouTubeVideoStatusSeeder` — populates video statuses
- `DomainTableSeeder`

---

## Key Files Reference

| File                          | Purpose                                          |
|-------------------------------|--------------------------------------------------|
| `composer.json`               | PHP dependencies & autoload (PSR-4: `App\` → `app/`, `Tests\` → `tests/`) |
| `package.json`                | Frontend dependencies (type: module)             |
| `vite.config.js`              | Vite config: inputs `resources/assets/sass/app.scss` + `resources/assets/js/app.js` |
| `docker-compose.yml`          | 6 services: db, php, nginx, adminer, memcached, mailhog |
| `.docker/php.Dockerfile`      | PHP 8.5 Alpine with xdebug, gd, composer, yt-dlp, supervisor |
| `.docker/nginx/nginx.conf`    | Nginx config: PHP-FPM via `fastcgi_pass php:9000` |
| `.docker/supervisor/conf/laravel-worker.conf` | Queue worker process |
| `pint.json`                   | PSR-12 + strict_types + no_unused_imports + static_lambda |
| `phpstan.neon`                | Level max, Larastan + Mockery + Carbon extensions |
| `phpunit.xml`                 | Two testsuites (Feature/Unit), env vars for testing |
| `Makefile`                    | Convenience commands for Docker operations |
| `bootstrap/app.php`           | Laravel app bootstrap, binds kernel + exception handler |
| `bootstrap/functions.php`     | Custom helper functions (currently minimal) |
| `copy-static-assets.js`       | Copies Font Awesome webfonts to public dir after Vite build |
| `.hooks/pre-commit`           | Git hook: runs PHPStan in Docker, blocks on errors |
