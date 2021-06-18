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
Route::group(['middleware' => 'auth', 'prefix' => 'personal'], function () {
    Route::get('/', 'Personal\IndexController@index')->name('personal');
    Route::get('/settings', 'Personal\SettingsController@index')->name('settings');
    Route::post('/settings/password', 'Personal\SettingsController@password')->name('settings.password');
    Route::post('/settings/token', 'Personal\SettingsController@token')->name('settings.token');
    Route::get('/invite', 'Personal\InviteController@index')->name('invite');
    Route::post('/invite', 'Personal\InviteController@invite')->name('invite.user');
    Route::resource('domains', '\App\Http\Controllers\Personal\DomainsController');
    Route::post('/domain/{id}/delete_old_whois', [WhoisController::class, 'deleteOldWhois'])->name('domains.delete_old_whois');
});


