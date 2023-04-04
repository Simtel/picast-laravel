<?php

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


use App\Http\Controllers\Personal\DomainsController;
use App\Http\Controllers\Personal\ImagesController;
use App\Http\Controllers\Personal\IndexController;
use App\Http\Controllers\Personal\InviteController;
use App\Http\Controllers\Personal\PricesController;
use App\Http\Controllers\Personal\ProductsController;
use App\Http\Controllers\Personal\SettingsController;
use App\Http\Controllers\Personal\UsersController;
use App\Http\Controllers\Personal\WhoisController;

Route::get('/', static function () {
    if (Auth::check()) {
        return redirect()->route('personal');
    }
    return view('auth.login');
})->name('home');

Auth::routes();

Route::get('logout', '\App\Http\Controllers\Auth\LoginController@logout');


//личный кабинет
Route::group(['middleware' => 'auth', 'prefix' => 'personal'], routes: static function () {
    Route::get('/', [IndexController::class, 'index'])->name('personal');

    Route::get('/telegram-test', [IndexController::class, 'telegramTest'])->name('test.telegram');

    Route::get('/settings', [SettingsController::class, 'index'])->name('settings');
    Route::post('/settings/password', [SettingsController::class, 'password'])->name('settings.password');
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

    Route::resource('domains', DomainsController::class);

    Route::group(['prefix' => 'prices', 'middleware' => ['can:edit prices']], static function () {
        Route::get('/', [PricesController::class, 'index'])->name('prices.index');
        Route::get('/prices/{product}', [PricesController::class, 'show'])->name('prices.show');
        Route::get('/product/add', [ProductsController::class, 'create'])->name('prices.product.create');
        Route::post('/product/store', [ProductsController::class, 'store'])->name('prices.product.store');
        Route::post('/product/{product}/delete', [ProductsController::class, 'store'])->name('prices.product.destroy');
        Route::get('/product/{product}/edit', [ProductsController::class, 'edit'])->name('prices.product.edit');
        Route::post('/product/{product}/update', [ProductsController::class, 'update'])->name('prices.product.update');
    });

    Route::post(
        '/domain/{id}/delete_old_whois',
        [WhoisController::class, 'deleteOldWhois']
    )->name('domains.delete_old_whois');
});
