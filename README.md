# Picast Laravel

> Domain & YouTube management system with WHOIS tracking, video processing, tournaments, AI chat and role-based access.

Picast is a web application built on Laravel for managing domains with WHOIS monitoring, processing YouTube videos, tracking dance tournaments, and chatting with the ChadGPT AI — all behind role-based user access. The codebase follows Domain-Driven Design with context-based architecture.

## Features

- **Domain Management** — full CRUD with automated WHOIS updates and expiry monitoring
- **YouTube Processing** — queue-based video downloads with format management
- **Tournaments** — daily-synced tournament and group listings
- **ChadGPT Chat** — AI conversations with history and word statistics
- **Image Gallery** — responsive image management
- **Barcode Generator** — generate test barcodes (Code 128, EAN-13 and more)
- **Users & Roles** — invite-based onboarding with Spatie permissions
- **REST API** — `/api/v1` with Sanctum token auth for integrations
- **Quality Gates** — PHPStan level max, Pint PSR-12, PHPUnit suite, GitHub Actions CI/CD

## Quick Start

Requires Docker, Make, Git and Node.js.

```bash
git clone https://github.com/Simtel/picast-laravel.git
cd picast-laravel
cp .env.example .env
make up
make composer-install
docker exec -it picast_php php artisan app:create-database picast_test
make migrate
make seed
```

Frontend assets: `npm run build` (or `make npm-build`). Open http://localhost.

## Example

Track a domain via the REST API (create a token in Settings first):

```bash
curl -X POST https://your-domain.com/api/v1/domains \
  -H "Authorization: Bearer YOUR_API_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"name": "example.com"}'
```

WHOIS data is fetched automatically by the background queue.

## Documentation

| Guide | Description |
|-------|-------------|
| [Getting Started](docs/getting-started.md) | Installation, services, Make targets |
| [Architecture](docs/architecture.md) | DDD contexts and project structure |
| [API Reference](docs/api.md) | Endpoints, authentication, examples |
| [Configuration](docs/configuration.md) | Environment variables and config files |
| [Testing](docs/testing.md) | Running the test suite |
| [Deployment](docs/deployment.md) | Production setup and CI/CD |
| [Contributing](docs/contributing.md) | Code style and quality gates |

## Technology Stack

- **Backend**: Laravel 13, PHP 8.4+, MySQL 8.3, Memcached, Sanctum, Spatie Permissions
- **Frontend**: Vite, Tailwind CSS 4, Bootstrap 5, jQuery, Blade
- **Infrastructure**: Docker Compose (PHP-FPM, Nginx, Adminer, MailHog), Supervisor queue workers, GitHub Actions

## License

MIT
