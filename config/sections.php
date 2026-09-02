<?php

declare(strict_types=1);

return [
    'dashboard' => [
        'label' => 'Участники',
        'permission' => 'view dashboard',
        'route' => 'personal',
        'icon' => 'fa-home',
    ],
    'domains' => [
        'label' => 'Домены',
        'permission' => 'domains',
        'route' => 'domains.index',
        'icon' => 'fa-globe',
    ],
    'images' => [
        'label' => 'Изображения',
        'permission' => 'edit images',
        'route' => 'images.index',
        'icon' => 'fa-image',
    ],
    'youtube' => [
        'label' => 'YouTube Videos',
        'permission' => 'edit youtube',
        'route' => 'youtube.index',
        'icon' => 'fa-youtube',
    ],
    'chadgpt' => [
        'label' => 'ChadGPT Chat',
        'permission' => 'view chadgpt',
        'route' => 'chadgpt.index',
        'icon' => 'fa-comments',
    ],
    'tournaments' => [
        'label' => 'Турниры',
        'permission' => 'view tournaments',
        'route' => 'tournaments.index',
        'icon' => 'fa-trophy',
    ],
    'tools' => [
        'label' => 'Инструменты',
        'permission' => 'view tools',
        'route' => 'tools.index',
        'icon' => 'fa-toolbox',
    ],
    'settings' => [
        'label' => 'Настройки',
        'permission' => 'view settings',
        'route' => 'settings',
        'icon' => 'fa-cog',
    ],
];
