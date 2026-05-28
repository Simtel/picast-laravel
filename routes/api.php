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

use App\Context\ChadGPT\Infrastructure\Http\Controllers\Api\ChatsController;
use App\Context\Domains\Infrastructure\Http\Controllers\Api\DomainsController;
use App\Context\Tournaments\Infrastructure\Http\Controllers\Api\TournamentsController;
use App\Context\Youtube\Infrastructure\Http\Controllers\Api\VideoController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::fallback(static function () {
    return response()->json(['message' => 'Page Not Found'], 404);
});

Route::group(['middleware' => 'auth:api', 'prefix' => 'v1'], static function () {
    // Текущий пользователь
    Route::get('/user/current', static function () {
        return Auth::user();
    })->name('api.user.current');

    // Домены
    Route::apiResource('domains', DomainsController::class)->names('api.domains');

    // YouTube видео
    Route::apiResource('videos', VideoController::class)->names('api.videos');

    // Турниры (только чтение)
    Route::get('tournaments', [TournamentsController::class, 'index'])->name('api.tournaments.index');
    Route::get('tournaments/{id}', [TournamentsController::class, 'show'])->name('api.tournaments.show');

    // ChadGPT чаты
    Route::apiResource('chats', ChatsController::class)->only(['index']);
    Route::post('/chats', [ChatsController::class, 'sendMessage'])->name('api.chats.send');
    Route::delete('/chats', [ChatsController::class, 'clearHistory'])->name('api.chats.clear');
});
