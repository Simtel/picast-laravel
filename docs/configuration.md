[← API Reference](api.md) · [Back to README](../README.md) · [Testing →](testing.md)

# Configuration

All configuration lives in environment files. Start from the template and override values per environment:

| File             | Purpose                                              |
|------------------|------------------------------------------------------|
| `.env`           | Local development (gitignored)                       |
| `.env.example`   | Template with defaults for local dev                 |
| `.env.testing`   | Used by PHPUnit (`APP_ENV=testing`)                  |
| `.env.github`    | Used by GitHub Actions CI                            |

## Application

| Variable              | Default                    | Description                               |
|-----------------------|----------------------------|-------------------------------------------|
| `APP_NAME`            | `Picast`                   | Application name                          |
| `APP_ENV`             | `local`                    | Environment (`local`, `testing`, `production`, `github`) |
| `APP_KEY`             | *(random)*                 | Laravel encryption key (`php artisan key:generate`) |
| `APP_DEBUG`           | `true`                     | Show error details                        |
| `APP_LOG_LEVEL`       | `debug`                    | Minimum log level                         |
| `APP_URL`             | `https://picast.lc`        | Application base URL                      |
| `VITE_USE_BUILD`      | `true`                     | Use built Vite assets vs dev server       |
| `APP_FILES_URL`       | *(empty)*                  | Base URL for public files                 |
| `TMP_FILE_UPLOADS`    | `storage/upload/images/`   | Upload directory for images               |
| `THUMB_WIDTH`         | `170`                      | Image thumbnail width                     |
| `THUMB_HEIGHT`        | `130`                      | Image thumbnail height                    |

## Database

| Variable       | Default   | Description                        |
|----------------|-----------|------------------------------------|
| `DB_CONNECTION`| `mysql`   | Connection driver                  |
| `DB_HOST`      | `db`      | MySQL host (Docker service)        |
| `DB_PORT`      | `3306`    | MySQL port                         |
| `DB_DATABASE`  | `picast`  | Main database name                 |
| `DB_USERNAME`  | `root`    | Database user                      |
| `DB_PASSWORD`  | `example` | Database password                  |

A second database `picast_test` is used by the test suite.

## Cache, Session, Queue

| Variable           | Default    | Description                    |
|--------------------|-----------|--------------------------------|
| `CACHE_STORE`      | `file`     | Cache driver (use `memcached` in prod) |
| `SESSION_DRIVER`   | `file`     | Session driver                |
| `SESSION_LIFETIME` | `120`      | Session lifetime in minutes   |
| `QUEUE_CONNECTION` | `database` | Queue driver                  |
| `BROADCAST_DRIVER` | `log`      | Broadcasting driver           |
| `LOG_CHANNEL`      | `stack`    | Logging channel               |
| `REDIS_*`          | localhost  | Redis connection (optional)   |

## Mail & Notifications

| Variable        | Default    | Description                    |
|-----------------|-----------|--------------------------------|
| `MAIL_MAILER`   | `smtp`     | Mail driver                    |
| `MAIL_HOST`     | `mailhog`  | SMTP host (MailHog in dev)     |
| `MAIL_PORT`     | `1025`     | SMTP port                      |
| `MAIL_USERNAME` / `MAIL_PASSWORD` / `MAIL_ENCRYPTION` | `null` | SMTP credentials |

## ChadGPT

| Variable                    | Default          | Description                    |
|-----------------------------|------------------|--------------------------------|
| `CHADGPT_API_KEY`           | *(empty)*        | API key for `ask.chadgpt.ru`   |
| `CHADGPT_DEFAULT_MODEL`     | `gpt-5.6-terra`  | Default chat model             |
| `CHADGPT_MODELS_CACHE_TTL`  | `86400`          | Models list cache TTL (s)      |

## YouTube

| Variable          | Default | Description                      |
|-------------------|---------|----------------------------------|
| `YOUTUBE_API_KEY` | *(empty)* | YouTube Data API v3 key         |

## Object Storage (S3 / Selectel)

| Variable                | Description                     |
|-------------------------|---------------------------------|
| `AWS_ENDPOINT`          | S3-compatible endpoint          |
| `AWS_ACCESS_KEY_ID`     | Access key                      |
| `AWS_SECRET_ACCESS_KEY` | Secret key                      |
| `AWS_DEFAULT_REGION`    | Region                          |
| `AWS_BUCKET`            | Bucket name                     |
| `SELECTEL_PUBLIC`       | Public URL for Selectel storage |

## Pusher / Realtime

| Variable              | Description                 |
|-----------------------|-----------------------------|
| `PUSHER_APP_ID`       | Pusher app id               |
| `PUSHER_APP_KEY`      | Pusher app key              |
| `PUSHER_APP_SECRET`   | Pusher app secret           |
| `PUSHER_APP_CLUSTER`  | Pusher cluster (default `mt1`) |

## Default Seeded User

| Variable                  | Default           | Description                |
|---------------------------|-------------------|----------------------------|
| `DEFAULT_USER_NAME`       | `Admin`           | Seeded admin name          |
| `DEFAULT_USER_EMAIL`      | `admin@picast.lc` | Seeded admin email         |
| `DEFAULT_USER_PASSWORD`   | `123456`          | Seeded admin password      |

## Config Files

Most settings are plain Laravel config files under `config/`. Notable application config: `config/chadgpt.php` (ChadGPT API key/base URL) and config options used by services (WHOIS, thumbnails) that read the env vars above.

## See Also

- [Deployment](deployment.md) — production environment checklist
- [API Reference](api.md) — using API keys
- [Getting Started](getting-started.md) — local setup
