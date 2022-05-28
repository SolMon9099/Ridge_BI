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
    Route::get('/authority_group', 'TopController@permission_group')->name('admin.top.permission_group');
    Route::post('/permission_store', 'TopController@permission_store')->name('admin.top.permission_store');

    Route::group(['prefix' => 'account'], function () {
        Route::get('/', 'AccountController@index')->name('admin.account');
        Route::get('/create', 'AccountController@create')->name('admin.account.create');
        Route::post('/store', 'AccountController@store')->name('admin.account.store');
        Route::get('/edit/{admin}', 'AccountController@edit')->name('admin.account.edit');
        Route::put('/update/{admin}', 'AccountController@update')->name('admin.account.update');
        Route::delete('/delete/{admin}', 'AccountController@delete')->name('admin.account.delete');
    });

    Route::group(['prefix' => 'notification'], function () {
        Route::get('/', 'NotificationController@index')->name('admin.notification');
        Route::get('/create', 'NotificationController@create')->name('admin.notification.create');
        Route::post('/store', 'NotificationController@store')->name('admin.notification.store');
        Route::get('/edit/{group}', 'NotificationController@edit')->name('admin.notification.edit');
        Route::put('/update/{group}', 'NotificationController@update')->name('admin.notification.update');
        Route::delete('/delete/{group}', 'NotificationController@delete')->name('admin.notification.delete');
        Route::get('/create_msg', 'NotificationController@create_msg')->name('admin.notification.create_msg');
        Route::post('/store_msg', 'NotificationController@store_msg')->name('admin.notification.store_msg');
        Route::get('/edit_msg/{msg}', 'NotificationController@edit_msg')->name('admin.notification.edit_msg');
        Route::put('/update_msg/{msg}', 'NotificationController@update_msg')->name('admin.notification.update_msg');
        Route::delete('/delete_msg/{msg}', 'NotificationController@delete_msg')->name('admin.notification.delete_msg');
    });

    Route::group(['prefix' => 'location'], function () {
        Route::get('/', 'LocationController@index')->name('admin.location');
        Route::get('/create', 'LocationController@create')->name('admin.location.create');
        Route::post('/store', 'LocationController@store')->name('admin.location.store');
        Route::get('/edit/{location}', 'LocationController@edit')->name('admin.location.edit');
        Route::put('/update/{location}', 'LocationController@update')->name('admin.location.update');
        Route::delete('/delete/{location}', 'LocationController@delete')->name('admin.location.delete');
    });

    Route::group(['prefix' => 'camera'], function () {
        Route::get('/', 'CameraController@index')->name('admin.camera');
        Route::get('/create', 'CameraController@create')->name('admin.camera.create');
        Route::post('/store', 'CameraController@store')->name('admin.camera.store');
        Route::get('/edit/{camera}', 'CameraController@edit')->name('admin.camera.edit');
        Route::put('/update/{camera}', 'CameraController@update')->name('admin.camera.update');
        Route::delete('/delete/{camera}', 'CameraController@delete')->name('admin.camera.delete');
        Route::get('/mapping', 'CameraController@mapping')->name('admin.camera.mapping');
        Route::post('/mapping/store', 'CameraController@store_mapping')->name('admin.camera.mapping.store');
        Route::get('/create_drawing', 'CameraController@create_drawing')->name('admin.camera.create_drawing');
        Route::post('/store_drawing', 'CameraController@store_drawing')->name('admin.camera.store_drawing');
        Route::get('/edit_drawing/{drawing}', 'CameraController@edit_drawing')->name('admin.camera.edit_drawing');
        Route::put('/update_drawing/{drawing}', 'CameraController@update_drawing')->name('admin.camera.update_drawing');
        Route::delete('/delete_drawing/{drawing}', 'CameraController@delete_drawing')->name('admin.camera.delete_drawing');

        Route::get('/mapping_detail/{drawing}', 'CameraController@mappingDetail')->name('admin.camera.mapping.detail');

        Route::post('/ajaxUploadFile', 'CameraController@ajaxUploadFile')->name('admin.camera.ajaxUploadFile');
    });

    Route::group(['prefix' => 'danger'], function () {
        Route::get('/', 'DangerController@index')->name('admin.danger');
        Route::get('/edit', 'DangerController@edit')->name('admin.danger.edit');
        Route::post('/edit2', 'DangerController@edit2')->name('admin.danger.edit2');
        Route::get('/create', 'DangerController@create')->name('admin.danger.create');
        Route::post('/create2', 'DangerController@create2')->name('admin.danger.create2');
        Route::get('/list', 'DangerController@list')->name('admin.danger.list');
        Route::get('/list2', 'DangerController@list2')->name('admin.danger.list2');
    });

    Route::group(['prefix' => 'shelf'], function () {
        Route::get('/', 'ShelfController@index')->name('admin.shelf');
        Route::get('/edit', 'ShelfController@edit')->name('admin.shelf.edit');
        Route::post('/edit2', 'ShelfController@edit2')->name('admin.shelf.edit2');
        Route::get('/create', 'ShelfController@create')->name('admin.shelf.create');
        Route::post('/create2', 'ShelfController@create2')->name('admin.shelf.create2');
        Route::get('/list', 'ShelfController@list')->name('admin.shelf.list');
        Route::get('/list2', 'ShelfController@list2')->name('admin.shelf.list2');
    });

    Route::group(['prefix' => 'meter'], function () {
        Route::get('/', 'MeterController@index')->name('admin.meter');
        Route::get('/create', 'MeterController@create')->name('admin.meter.create');
        Route::post('/create2', 'MeterController@create2')->name('admin.meter.create2');
        Route::get('/edit', 'MeterController@edit')->name('admin.meter.edit');
        Route::post('/edit2', 'MeterController@edit2')->name('admin.meter.edit2');
        Route::get('/list', 'MeterController@list')->name('admin.meter.list');
        Route::get('/list2', 'MeterController@list2')->name('admin.meter.list2');
    });

    Route::group(['prefix' => 'analyze'], function () {
        Route::get('/', 'AnalyzeController@index')->name('admin.analyze');
        Route::get('/now_list', 'AnalyzeController@now_list')->name('admin.analyze.now_list');
        Route::get('/finish_list', 'AnalyzeController@finish_list')->name('admin.analyze.finish_list');
    });

    Route::post('logout', 'Auth\LoginController@logout')->name('admin.logout');
});
