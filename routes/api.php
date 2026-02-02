<?php

declare(strict_types=1);

use OpenApi\Attributes as OA;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/**
 * @OA\Info(
 *     title="Picast Laravel API",
 *     version="1.0.0",
 *     description="API для управления доменами и YouTube видео",
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


use App\Context\Domains\Infrastructure\Controller\Api\DomainsController;
use App\Context\Youtube\Infrastructure\Controller\ApiVideoController;
use App\Context\Webcams\Infrastructure\Controllers\WebcamController;

Route::fallback(static function () {
    return response()->json(['message' => 'Page Not Found'], 404);
});

Route::group(['middleware' => 'auth:api', 'prefix' => 'v1'], static function () {
    Route::get('/user/current', static function () {
        return Auth::user();
    })->name('api.user.current');

    Route::resource('domains', DomainsController::class)->names('api.domains');

    Route::resource('video', ApiVideoController::class)->names('api.videos');

    Route::resource('webcams', WebcamController::class)->names('api.webcams');
    Route::get('webcams/by-location', [WebcamController::class, 'byLocation'])->name('api.webcams.by-location');
});
