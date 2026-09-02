[← Architecture](architecture.md) · [Back to README](../README.md) · [Configuration →](configuration.md)

# API Reference

The application exposes a REST API for external integrations and automation. Interactive docs can be generated with Scribe and served locally.

## Base URL

```
https://your-domain.com/api/v1
```

Local: `http://localhost/api/v1`.

## Authentication

API routes use the `auth:api` guard, which relies on **Sanctum personal access tokens**.

1. Log in to the web app.
2. Open **Settings** (`/personal/settings`), create an API token and copy the shown plain-text value (it is displayed only once).
3. Send it as a Bearer token:

```
Authorization: Bearer YOUR_API_TOKEN
```

## Error Format

Unknown routes return a JSON 404:

```json
{ "message": "Page Not Found" }
```

Validation and application errors return an HTTP status code with a `message`, and `errors` (field → messages) when applicable.

## Endpoints

### Current user

| Method | Path            | Description                    |
|--------|-----------------|--------------------------------|
| GET    | `/api/v1/user/current` | Get the authenticated user |

### Domains

Resource for managing domains and their WHOIS data. Response payloads are `DomainResource` JSON objects.

| Method | Path                    | Description                          |
|--------|-------------------------|--------------------------------------|
| GET    | `/api/v1/domains`       | List all user domains                |
| POST   | `/api/v1/domains`       | Create a domain                      |
| GET    | `/api/v1/domains/{id}`  | Get domain details with WHOIS        |
| PUT    | `/api/v1/domains/{id}`  | Update a domain                      |
| DELETE | `/api/v1/domains/{id}`  | Delete a domain                      |

Example — create a domain:

```bash
curl -X POST https://your-domain.com/api/v1/domains \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"name": "example.com"}'
```

### Videos

Resource for managing YouTube videos.

| Method | Path                   | Description                     |
|--------|------------------------|---------------------------------|
| GET    | `/api/v1/videos`       | List videos                     |
| POST   | `/api/v1/videos`       | Create/queue a video by URL     |
| GET    | `/api/v1/videos/{id}`  | Get video details               |
| PUT    | `/api/v1/videos/{id}`  | Update a video                  |
| DELETE | `/api/v1/videos/{id}`  | Delete a video                  |

Example — queue a video:

```bash
curl -X POST https://your-domain.com/api/v1/videos \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"url": "https://youtube.com/watch?v=VIDEO_ID"}'
```

### Tournaments

Read-only endpoints for tournaments and their groups.

| Method | Path                          | Description        |
|--------|-------------------------------|--------------------|
| GET    | `/api/v1/tournaments`         | List tournaments   |
| GET    | `/api/v1/tournaments/{id}`    | Tournament details |

### Chats (ChadGPT)

| Method | Path                | Description                        |
|--------|---------------------|------------------------------------|
| GET    | `/api/v1/chats`     | List conversations                 |
| POST   | `/api/v1/chats`     | Send a message to the chat         |
| DELETE | `/api/v1/chats`     | Clear chat history                 |

## Route Naming

Resource routes are registered with `->names('api.domains')`, `->names('api.videos')` etc., so route names like `api.domains.show` are available.

## Interactive Documentation

The project uses Scribe (`knuckleswtf/scribe`). Regenerate the docs with:

```bash
make scribe-generate
```

## See Also

- [Configuration](configuration.md) — required API keys and env vars
- [Getting Started](getting-started.md) — run the app locally
- [Architecture](architecture.md) — how API controllers are organized by context
