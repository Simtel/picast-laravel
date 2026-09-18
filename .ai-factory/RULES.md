# Project Rules

> Short, actionable rules and conventions for this project. Loaded automatically by /aif-implement.

## Rules

- Не использовать `private const array` с вложенными метками/данными (например `['v1' => ['label' => '...']]`); для фиксированных наборов значений использовать PHP Enum (например `UuidVersion`) с методом `label()` и `values()`.