[← Deployment](deployment.md) · [Back to README](../README.md)

# Contributing

## Development Workflow

1. Fork the repository.
2. Create a feature branch:

   ```bash
   git checkout -b feature/new-feature
   ```

3. Make your changes following the conventions below.
4. Run the quality gates:

   ```bash
   make test
   make phpstan
   make pint
   ```

5. Commit your changes and push to your branch.
6. Open a Pull Request against `master`.

## Code Style

The project uses Laravel Pint with PSR-12 plus custom rules configured in `pint.json`:

- `declare(strict_types=1)` at the top of every PHP file
- No unused imports
- Closures marked `static` when they don't bind `$this`

```bash
make pint        # fix style
```

CI checks style with `vendor/bin/pint --test`.

## Static Analysis

PHPStan runs at **level max** with Larastan, PHPStan-Mockery and Carbon extensions (`phpstan.neon`). Analyzed paths: `app`, `tests`, `database/migrations`.

```bash
make phpstan     # ./vendor/bin/phpstan analyse --memory-limit=2G
```

## Class Conventions

- All classes use the `final` keyword
- Models expose explicit getters (`getId()`, `getName()`) rather than direct property access
- Eloquent relationships use PHPDoc generics (`@return HasMany<VideoFormats, $this>`)
- Properties are documented with `@property` PHPDoc tags
- Controllers, handlers and commands are `final class`

## Testing Guidelines

- Write tests under `tests/Feature/<Context>/` or `tests/Unit/Context/<Context>/`
- Use helpers from `Tests\TestCase` (`loginAdmin()`, `authUserWithPermissions()`, etc.)
- See [Testing](testing.md) for details.

## Git Hooks

Set up the pre-commit hook locally to run PHPStan before every commit:

```bash
make set-githooks
```

The hook lives at `.hooks/pre-commit` and blocks commits when analysis fails.

## Commit Messages

Use [Conventional Commits](https://www.conventionalcommits.org/) style, e.g. `feat(barcode): add barcode generation section`.

## See Also

- [Testing](testing.md) — running the test suite
- [Architecture](architecture.md) — structure and DDD conventions
- [Deployment](deployment.md) — CI/CD gates
