[← Configuration](configuration.md) · [Back to README](../README.md) · [Deployment →](deployment.md)

# Testing

The test suite uses PHPUnit 13 configured in `phpunit.xml` and requires the `picast_test` database.

## Running Tests

```bash
make test              # php artisan test (default env)
make test-coverage     # HTML coverage report to tests/reports/coverage
php artisan test --filter DomainTest   # run a single test class
php artisan test --env=github          # GitHub Actions env
```

## Structure

```
tests/
├── bootstrap.php           # Bootstraps app, ensures user #1 has an API token
├── TestCase.php            # Base class (DatabaseTransactions)
├── Feature/                # Integration tests
│   ├── Api/                # API endpoint tests
│   ├── Auth/               # Authentication tests
│   ├── ChadGPT/            # ChadGPT feature tests
│   ├── Command/            # Artisan command tests
│   ├── Common/             # Common feature tests
│   ├── Domain/             # Domain feature tests
│   ├── Tools/              # Tools (barcode) feature tests
│   ├── Tournaments/        # Tournament feature tests
│   └── YouTube/            # YouTube feature tests
└── Unit/                   # Isolated unit tests
    ├── Common/             # CommandBus tests
    └── Context/            # Per-context unit tests (ChadGPT, Domains, Youtube, Tournaments, User)
```

## Base TestCase Helpers

`Tests\TestCase` uses the `DatabaseTransactions` trait, so each test is wrapped in a transaction. Helpers available in feature tests:

| Helper                                          | Purpose                                   |
|-------------------------------------------------|-------------------------------------------|
| `loginAdmin()`                                  | Log in as user ID 1                       |
| `authUserWithPermissions($attributes, $permissions)` | Create user with role+permissions and log in |
| `createUserWithPermissions($attributes, $permissions)` | Create user without logging in         |
| `getAuthUser()`                                 | Current authenticated user                |
| `getAdminUser()`                                | User ID 1                                 |

## Testing Environment

PHPUnit reads `.env.testing`. In CI (`.env.github`) the flow is:

1. Start MySQL 8.0 service container
2. Create the test database
3. Run `php artisan migrate`
4. Seed `YouTubeVideoStatusSeeder`
5. Run `php artisan test --env=github`

## See Also

- [Getting Started](getting-started.md) — setting up the test database locally
- [Contributing](contributing.md) — code quality gates used with tests
- [Deployment](deployment.md) — CI/CD pipeline running the suite
