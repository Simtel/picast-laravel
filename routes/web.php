<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


use App\Context\Domains\Infrastructure\Controller\DomainsController;
use App\Context\Domains\Infrastructure\Controller\WhoisController;
use App\Context\Youtube\Infrastructure\Controller\YouTubeVideoController;
use App\Http\Controllers\Personal\ImagesController;
use App\Http\Controllers\Personal\IndexController;
use App\Http\Controllers\Personal\InviteController;
use App\Http\Controllers\Personal\SettingsController;
use App\Http\Controllers\Personal\UsersController;

Auth::routes();
Route::get('/', static function () {
    if (Auth::check()) {
        return redirect()->route('personal');
    }
    return view('auth.login');
})->name('home');



Route::get('logout', '\App\Http\Controllers\Auth\LoginController@logout');


//личный кабинет
Route::group(['middleware' => 'auth', 'prefix' => 'personal'], routes: static function () {
    Route::get('/', [IndexController::class, 'index'])->name('personal');

    Route::get('/settings', [SettingsController::class, 'index'])->name('settings');
    Route::post('/settings/password', [SettingsController::class, 'password'])->name('settings.password');
    Route::post('/settings/profile', [SettingsController::class, 'updateProfile'])->name('settings.profile');
    Route::post('/settings/token', [SettingsController::class, 'token'])->name('settings.token');

    Route::get('/invite', [InviteController::class, 'index'])->name('invite');
    Route::post('/invite', [InviteController::class, 'invite'])->name('invite.user');

    Route::group(['prefix' => 'user', 'middleware' => ['can:edit user']], static function () {
        Route::get('/edit/{user}', [UsersController::class, 'edit'])->name('user.edit');
        Route::post('/edit/{user}', [UsersController::class, 'update'])->name('user.update');
    });

    Route::group(['prefix' => 'images', 'middleware' => ['can:edit images']], static function () {
        Route::get('/', [ImagesController::class, 'index'])->name('images.index');
        Route::get('/show/{image}', [ImagesController::class, 'show'])->name('images.show');
        Route::get('/new', [ImagesController::class, 'create'])->name('images.create');
        Route::post('/store', [ImagesController::class, 'store'])->name('images.store');
    });

    Route::resource('domains', DomainsController::class)->middleware('permission:domains');

    Route::group(['prefix' => 'youtube','middleware' => ['permission:edit youtube']], static function () {
        Route::get('/', [YouTubeVideoController::class, 'index'])->name('youtube.index');
        Route::delete('/{video}', [YouTubeVideoController::class, 'destroy'])->name('youtube.destroy');
        Route::get('/create', [YouTubeVideoController::class, 'create'])->name('youtube.create');
        Route::post('/store', [YouTubeVideoController::class, 'store'])->name('youtube.store');
        Route::post('/{video}/refresh-formats', [YouTubeVideoController::class, 'refreshFormats'])->name('youtube.refresh_formats');
        Route::post('/{video}/queue-download', [YouTubeVideoController::class, 'queueDownload'])->name('youtube.queue-download');
    });

    Route::post(
        '/domain/{id}/delete-old-whois',
        [WhoisController::class, 'deleteOldWhois']
    )->name('domains.delete_old_whois');

    // ChadGPT feature
    Route::group(['prefix' => 'chadgpt'], static function () {
        Route::get('/', [\App\Context\ChadGPT\Infrastructure\Controller\ChadGptController::class, 'index'])->name('chadgpt.index');
        Route::post('/send-message', [\App\Context\ChadGPT\Infrastructure\Controller\ChadGptController::class, 'sendMessage'])->name('chadgpt.send-message');
    });

    // Temporary debugging route
    Route::get('/debug-chadgpt', function () {
        return response()->json([
            'status' => 'ok',
            'message' => 'Debug route working',
            'csrf_token' => csrf_token()
        ]);
    });
});
