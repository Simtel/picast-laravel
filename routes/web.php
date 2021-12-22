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
use App\Http\Controllers\Personal\IndexController;
use App\Http\Controllers\Personal\InviteController;
use App\Http\Controllers\Personal\SettingsController;
use App\Http\Controllers\Personal\UsersController;
use App\Http\Controllers\Personal\WhoisController;


Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('personal');
    }
    return view('auth.login');
})->name('home');

Auth::routes();

Route::get('logout', '\App\Http\Controllers\Auth\LoginController@logout');


//личный кабинет
Route::group(['middleware' => 'auth', 'prefix' => 'personal'], static function () {
    Route::get('/', [IndexController::class, 'index'])->name('personal');
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings');
    Route::post('/settings/password', [SettingsController::class, 'password'])->name('settings.password');
    Route::post('/settings/token', [SettingsController::class, 'token'])->name('settings.token');
    Route::get('/invite', [InviteController::class, 'index'])->name('invite');
    Route::group(['prefix' => 'user'], static function () {
        Route::get('/edit/{user}', [UsersController::class, 'edit'])->name('user.edit');
        Route::post('/edit/{user}', [UsersController::class, 'update'])->name('user.update');
    });
    Route::post('/invite', [InviteController::class, 'invite'])->name('invite.user');
    Route::resource('domains', DomainsController::class);
    Route::post('/domain/{id}/delete_old_whois',
        [WhoisController::class, 'deleteOldWhois'])->name('domains.delete_old_whois');
});


