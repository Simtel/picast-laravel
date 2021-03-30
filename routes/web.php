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
    Route::get('/domains', 'Personal\DomainsController@index')->name('personal\domains');
    Route::resource('domains', '\App\Http\Controllers\Personal\DomainsController');
});


