<?php

use Illuminate\Support\Facades\Route;

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

Route::get('login', 'Auth\LoginController@showLoginForm')->name('admin.login');

Route::post('login', 'Auth\LoginController@login');

Route::group(['middleware' => 'auth:admin'], function () {
    Route::get('/', 'TopController@index')->name('admin.top');
    Route::post('logout', 'Auth\LoginController@logout')->name('admin.logout');
});
