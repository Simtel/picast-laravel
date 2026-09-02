<?php

declare(strict_types=1);

/**
 * Возвращает каталог разделов сайта (key → метаданные раздела).
 *
 * @return array<string, array{label: string, permission: string, route: string, icon: string}>
 */
function sections_list(): array
{
    $sections = config('sections');
    if (!is_array($sections)) {
        return [];
    }

    return $sections;
}

/**
 * Возвращает пермишен, соответствующий разделу сайта.
 */
function section_permission(string $key): ?string
{
    $section = config("sections.{$key}");
    if (!is_array($section)) {
        return null;
    }

    return $section['permission'] ?? null;
}
