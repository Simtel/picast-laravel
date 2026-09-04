[← Getting Started](getting-started.md) · [Back to README](../README.md) · [API Reference →](api.md)

# Architecture

Picast follows **Domain-Driven Design** with context-based separation. Business logic is grouped into contexts under `app/Context/`, and each context is split into three layers.

## Top-Level Layout

```
app/
├── Common/                # Shared infrastructure (CommandBus, interfaces)
├── Console/               # Artisan kernel + swagger annotations
├── Context/
│   ├── ChadGPT/           # AI chat bot
│   ├── Common/            # Shared domain models (Images, InviteCode) + commands
│   ├── Domains/           # Domain WHOIS management
│   ├── Tools/             # Utilities (barcode generator)
│   ├── Tournaments/       # Tournament listings
│   ├── User/              # User profile & invitations
│   └── Youtube/           # YouTube video processing
├── Exceptions/            # Exception handler
├── Http/                  # Legacy controllers (Auth)
└── Providers/             # Service providers
```

## Context Internal Structure

Each context follows a three-layer architecture:

| Layer            | Directory            | Purpose                                                         |
|------------------|----------------------|-----------------------------------------------------------------|
| **Domain**       | `Domain/`            | Eloquent models, events, factories, observers, resources        |
| **Application**  | `Application/`       | Services, policies, contracts, DTOs, query handlers             |
| **Infrastructure** | `Infrastructure/`  | HTTP controllers (Web + API), artisan commands, jobs, mail, repositories, request validators |

For example, `Domains` contains `Domain/` (models, events), `Application/` (services, contracts, policies) and `Infrastructure/` (controllers, requests, jobs).

## Command Bus (CQRS-light)

A simple `CommandBus` (`App\Context\Common\Infrastructure\CommandBus`) is registered in `AppServiceProvider`. Commands implement `CommandInterface`, handlers implement `CommandHandlerInterface`, and both are mapped in `AppServiceProvider::register()`:

```php
$bus->register(CreateChatConversationCommand::class, CreateChatConversationHandler::class);
$bus->register(ListDomainsQuery::class, ListDomainsQueryHandler::class);
```

## Thin Controllers & Shared Application Services

Controllers (web and API) are thin: they handle HTTP (validation, route model binding, view/redirect/JSON) and delegate business logic to **services** and **listing queries** in the `Application/` layer. Where a web and an API controller expose the same action (e.g. Domains `store`/`destroy`, ChadGPT chat, YouTube video queueing), both delegate to one shared service so behaviour stays in one place.

| Context     | Service / Query (in `Application/`)                    | Responsibility                                  |
|-------------|--------------------------------------------------------|-------------------------------------------------|
| User        | `Service\ImageUploadService`                           | S3-upload + `Images::create`                    |
| User        | `Service\InviteUserService`                            | Code generation + invite mail                   |
| User        | `Service\ProfileUpdateService`                         | Shared name/email/birth_date update             |
| User        | `Service\ApiTokenService`                              | Create/delete Sanctum tokens                    |
| User        | `Service\ChangePasswordService`                        | Hash + remember-token rotation                  |
| User        | `Service\RoleService`                                  | Role CRUD, section permissions, guards          |
| User        | `Query\ImageListingQuery` / `Query\UserListingQuery`   | Filtered, paginated listings                    |
| Domains     | `Service\DomainService`                                | Domain create/delete, WHOIS refresh wrapper     |
| Tournaments | `Query\GetTournamentDetailQuery::fromRequest()`        | Shared web/API detail-query construction        |
| ChadGPT     | `Service\ChatService`                                  | Chat data, send message, clear history (web+API)|
| Youtube     | `Service\VideoActionService`                           | Video create + queue download w/ ownership check|
| Youtube     | `Query\VideoListingQuery`                              | Video listing (paginated web / plain API)       |
| Tools       | `Service\BarcodeService`                               | Barcode render + sample-data generators         |

Validation is enforced with `FormRequest` classes; domain lookups use repository/query objects or the `CommandBus` where one exists.

## Domain Events & Queue

Cross-context work is decoupled with events + listeners, and long-running work goes through the queue:

- **Domains**: `DomainCreated` → `GetWhoisDomain` listener; `DomainObserver` triggers WHOIS refresh
- **Youtube**: `YouTubeVideoCreated` → `YouTubeVideoCreateListener`; downloads are queued via `VideoDownloadQueue`
- **Tournaments**: `tournaments:fetch` daily command pulls data from the `simtel/dancemanager-scraper` package

Queue worker runs via Supervisor and processes the `database` driver.

## Data Flow (Example: WHOIS Refresh)

1. A `Domain` is created/updated in the web or API controller (`Infrastructure/`)
2. The `DomainObserver` or a listener dispatches a queued job
3. The job calls a `WhoisService` implementation (`Application/Contract/`)
4. The `Whois` record is stored and exposed through `DomainResource`

## Conventions

- All domain classes are `final`
- Eloquent models expose explicit getters (`getId()`, `getName()`) instead of raw property access
- Relationships and properties are documented with PHPDoc generics / `@property`
- Every PHP file starts with `declare(strict_types=1);`

## Routing

- **Web** (`routes/web.php`) — session auth under `auth` middleware; `/personal/*` section for authenticated users
- **API** (`routes/api.php`) — prefix `/api/v1`, `auth:api` (Sanctum token) middleware, JSON 404 fallback

## Role-based Section Access

Every section of the personal area is mapped to a Spatie permission:

| Section | Permission | Managed via |
|---------|------------|-------------|
| Dashboard («Участники») | `view dashboard` | `/personal/roles` |
| Domains | `domains` | `/personal/roles` |
| Images | `edit images` | `/personal/roles` |
| YouTube Videos | `edit youtube` | `/personal/roles` |
| ChadGPT Chat | `view chadgpt` | `/personal/roles` |
| Tournaments | `view tournaments` | `/personal/roles` |
| Tools | `view tools` | `/personal/roles` |
| Settings | `view settings` | `/personal/roles` |

- The catalog lives in `config/sections.php` and is consumed by the sidebar (`sidebar.blade.php`), the role editor and helper functions in `bootstrap/functions.php` (`sections_list()`, `section_permission()`).
- A section is visible in the menu and reachable via its route only if the user's role holds the corresponding permission; otherwise the route returns `403`.
- Admins manage roles and section access under `/personal/roles` (`RoleController`), which is guarded by the `edit user` permission. Default grants (roles `admin`/`member`) are applied in migration `2026_09_02_000000_add_section_permissions`.
- The dashboard route is not hard-gated: `IndexController` shows the user list only when the current user has `view dashboard` and redirects to `domains.index` otherwise.

## See Also

- [Getting Started](getting-started.md) — local environment setup
- [API Reference](api.md) — available REST endpoints
- [Configuration](configuration.md) — environment variables and config files
