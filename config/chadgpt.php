<?php

declare(strict_types=1);

return [
    'api_key' => env('CHADGPT_API_KEY', 'YOUR_API_KEY'),
    'url' => 'https://ask.chadgpt.ru/api/v1/',
    'default_model' => env('CHADGPT_DEFAULT_MODEL', 'gpt-5.6-terra'),
    'models_cache_ttl' => (int) env('CHADGPT_MODELS_CACHE_TTL', 86400),
];
