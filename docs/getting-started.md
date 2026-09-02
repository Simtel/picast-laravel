[Back to README](../README.md) · [Architecture →](architecture.md)

# Getting Started

Picast runs in Docker Compose: MySQL, PHP-FPM, Nginx, Adminer, Memcached and MailHog are managed with `make` targets.

## Prerequisites

- Docker & Docker Compose
- GNU Make
- Git
- Node.js 20+ (for building frontend assets on the host, or use `make npm-install` / `make npm-build` inside the container)

## Quick Start

```bash
# 1. Clone the repository
git clone https://github.com/Simtel/picast-laravel.git
cd picast-laravel

# 2. Copy the environment template and adjust it if needed
cp .env.example .env

# 3. Build and start the containers
make up

# 4. Install PHP dependencies (inside the php container)
make composer-install

# 5. Create the databases (picast and picast_test)
docker exec -it picast_php php artisan app:create-database picast
docker exec -it picast_php php artisan app:create-database picast_test

# 6. Run migrations and seed initial data
make migrate
make seed
```

Open [http://localhost](http://localhost) — you should see the login page. The default seeded admin account comes from `.env`:

```env
DEFAULT_USER_NAME=Admin
DEFAULT_USER_EMAIL=admin@picast.lc
DEFAULT_USER_PASSWORD=123456
```

The databases can also be created via Adminer at http://localhost:8080 (server: `db`, user: `root`, password: `example`). The Makefile's `migrate`/`seed` targets run against both the default and the `testing` environment.

## Frontend Assets

```bash
# Development server with HMR (host, or `make npm-dev` in the container)
npm run dev

# Production build
npm run build
```

`npm run build` also runs `copy-static-assets.js`, which copies Font Awesome webfonts into `public/webfonts/`.

## Services

Once started, these services are available:

| Service         | URL                              | Description                          |
|-----------------|----------------------------------|--------------------------------------|
| Application     | http://localhost                 | Main web application (Nginx)         |
| Adminer         | http://localhost:8080            | Database administration              |
| MailHog UI      | http://localhost:8025            | Incoming email testing               |
| PHP container   | `make cli`                       | Shell into `picast_php` as `www-data`|
| MySQL console   | `make mysql-console`             | MySQL CLI as `root`                  |

## Make Targets

Run `make help` to list all targets. The most common ones:

```bash
make up              # Start containers (docker compose up -d --remove-orphans)
make down            # Stop containers
make build           # Rebuild images
make restart         # Stop then start
make cli             # Shell into the PHP container (www-data)
make mysql-console   # MySQL console (root)
make composer-install
make migrate         # Migrations on default + testing envs
make seed            # Seed on default + testing envs
make npm-install     # npm install inside the container
make npm-build       # Vite production build inside the container
make npm-dev         # Vite dev server inside the container
make test            # Run PHPUnit
make phpstan         # Static analysis (level max)
make pint            # Fix code style (PSR-12)
make worker          # Start Supervisor queue worker
make scribe-generate # Regenerate Scribe API docs
```

## Queue Worker

Jobs (video downloads, WHOIS updates, notifications) are processed from the `database` queue. Start a worker manually with `make worker`, or rely on the Supervisor config shipped at `.docker/supervisor/conf/laravel-worker.conf`.

## See Also

- [Architecture](architecture.md) — project structure and DDD conventions
- [Configuration](configuration.md) — environment variables reference
- [Testing](testing.md) — how to run the test suite
