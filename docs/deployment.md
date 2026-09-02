[← Testing](testing.md) · [Back to README](../README.md) · [Contributing →](contributing.md)

# Deployment

## Production Requirements

- PHP >= 8.4 (with GD extension, since the Docker image runs 8.5)
- MySQL 8.0+ (local dev uses 8.3)
- Nginx or Apache
- Composer
- Node.js (one-time asset build)
- Supervisor for queue workers
- SSL certificate for HTTPS
- Memcached (recommended cache driver)

## Manual Deployment Steps

1. Clone the repository onto the production server.
2. Install production dependencies:

   ```bash
   composer install --no-dev --optimize-autoloader
   ```

3. Create `.env` from `.env.example` and set production values:

   ```env
   APP_ENV=production
   APP_DEBUG=false
   APP_URL=https://your-domain.com
   CACHE_STORE=memcached
   QUEUE_CONNECTION=database
   ```

   See [Configuration](configuration.md) for the full variable reference.
4. Run migrations and seed data:

   ```bash
   php artisan migrate --force
   php artisan db:seed
   ```

5. Build frontend assets:

   ```bash
   npm run build
   ```

6. Start queue workers with Supervisor (config shipped at `.docker/supervisor/conf/laravel-worker.conf`):

   ```bash
   php artisan queue:work --sleep=3 --tries=3
   ```

7. Point the web server at `public/` (see `.docker/nginx/nginx.conf` for the Nginx + PHP-FPM reference) and configure SSL.
8. Set up scheduled tasks (`php artisan schedule:work` or cron). See below.

## Scheduled Tasks

Defined in `app/Console/Kernel.php`:

| Command / Job          | Cadence | Purpose                              |
|------------------------|---------|--------------------------------------|
| `domains:whois`        | daily   | Refresh WHOIS records                |
| `CheckExpireDomains`   | daily   | Check domains expiring soon          |
| `tournaments:clean`    | daily   | Remove expired tournaments           |
| `tournaments:fetch`    | daily   | Fetch tournaments (runs `tournaments:groups:fetch` afterwards, in background) |

## CI/CD Pipeline

GitHub Actions (`.github/workflows/laravel.yml`) runs on push to `master`:

1. **Lints** — PHPStan (level max) + Laravel Pint `--test`
2. **Tests** — MySQL 8.0 service container, migrate + seed, `php artisan test --env=github`
3. **Publish** (after lints + tests pass) — SSH to the production server:
   - `git pull`
   - `composer install --no-dev`
   - `php artisan migrate --force`
   - `php artisan db:seed`
   - `php artisan queue:restart`

A pre-commit hook (`.hooks/pre-commit`, enabled via `make set-githooks`) runs PHPStan in Docker and blocks commits with errors.

## Environment Files

| File           | Purpose                                  |
|----------------|------------------------------------------|
| `.env.example` | Template with defaults                   |
| `.env.testing` | PHPUnit local tests (`APP_ENV=testing`)  |
| `.env.github`  | GitHub Actions CI                        |

## See Also

- [Configuration](configuration.md) — production environment variables
- [Testing](testing.md) — test suite used by CI
- [Contributing](contributing.md) — code quality gates
