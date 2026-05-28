<?php

declare(strict_types=1);

/**
 * @OA\Info(
 *     title="Picast Laravel API",
 *     version="1.0.0",
 *     description="API для управления доменами, YouTube видео, турнирами и ChadGPT чатами",
 *     contact={
 *         "name": "API Support",
 *         "email": "support@picast.com"
 *     }
 * )
 * @OA\Server(
 *     url="http://localhost",
 *     description="Локальный сервер разработки"
 * )
 * @OA\SecurityScheme(
 *     securityScheme="sanctum",
 *     type="apiKey",
 *     in="header",
 *     name="Authorization",
 *     description="Bearer token для аутентификации"
 * )
 */
