<?php

declare(strict_types=1);

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


use App\Context\Domains\Infrastructure\Controller\Api\DomainsController;

Route::fallback(static function () {
    return response()->json(['message' => 'Page Not Found'], 404);
});

Route::group(['middleware' => 'auth:api', 'prefix' => 'v1'], static function () {
    Route::get('/user/current', static function () {
        return Auth::user();
    })->name('api.user.current');

    Route::resource('domains', DomainsController::class)->names('api.domains');
});
