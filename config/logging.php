<?php

declare(strict_types=1);

return [

    'channels' => [
        'console' => [
            'driver' => 'monolog',
            'handler' => Monolog\Handler\StreamHandler::class,
            'with' => [
                'stream' => 'php://stdout',
            ],
        ],
        // ...
    ],

];
