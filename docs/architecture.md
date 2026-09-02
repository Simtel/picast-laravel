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

## See Also

- [Getting Started](getting-started.md) — local environment setup
- [API Reference](api.md) — available REST endpoints
- [Configuration](configuration.md) — environment variables and config files
