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

// Route::get('login', 'Auth\LoginController@showLoginForm')->name('admin.login');

// Route::post('login', 'Auth\LoginController@login');

Route::group(['middleware' => 'prevent-back-history'], function () {
    // Auth::routes();
    Route::get('login', 'Auth\LoginController@showLoginForm')->name('admin.login');
    Route::post('login', 'Auth\LoginController@login');
    Route::get('error', 'TopController@error')->name('admin.error');

    Route::group(['middleware' => 'auth:admin'], function () {
        Route::get('/', 'TopController@index')->name('admin.top');
        Route::get('/authority_group', 'TopController@permission_group')->name('admin.top.permission_group');
        Route::post('/permission_store', 'TopController@permission_store')->name('admin.top.permission_store');
        Route::post('/save_block', 'TopController@save_block')->name('admin.top.save_block');
        Route::post('/update', 'TopController@update')->name('admin.top.update');
        Route::post('/AjaxUpdate', 'TopController@AjaxUpdate')->name('admin.top.AjaxUpdate');
        Route::post('/AjaxDelete', 'TopController@AjaxDelete')->name('admin.top.AjaxDelete');
        Route::delete('/delete/{top}', 'TopController@delete')->name('admin.top.delete');
        Route::post('/save_search_option', 'TopController@save_search_option')->name('admin.top.save_search_option');
        Route::post('/CheckDetectData', 'TopController@CheckDetectData')->name('admin.top.CheckDetectData');

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
            Route::post('/getHeatmapData', 'CameraController@getHeatmapData')->name('admin.camera.getHeatmapData');
            Route::post('AjaxRefreshImg', 'CameraController@AjaxRefreshImg')->name('admin.camera.AjaxRefreshImg');
            Route::post('/reset_heatmap', 'CameraController@reset_heatmap')->name('admin.camera.reset_heatmap');
        });

        Route::group(['prefix' => 'pit'], function () {
            Route::get('/', 'PitController@index')->name('admin.pit');
            Route::get('/edit/{pit}', 'PitController@edit')->name('admin.pit.edit');
            Route::get('/cameras_for_rule', 'PitController@cameras_for_rule')->name('admin.pit.cameras_for_rule');
            Route::get('/create_rule', 'PitController@create_rule')->name('admin.pit.create_rule');
            Route::get('/list', 'PitController@list')->name('admin.pit.list');
            Route::get('/detail', 'PitController@detail')->name('admin.pit.detail');
            Route::get('/past_analysis', 'PitController@past_analysis')->name('admin.pit.past_analysis');
            Route::post('/store', 'PitController@store')->name('admin.pit.store');
            Route::put('/update/{pit}', 'PitController@update')->name('admin.pit.update');
            Route::delete('/delete/{pit}', 'PitController@delete')->name('admin.pit.delete');
            Route::get('/ajaxGetData', 'PitController@ajaxGetData')->name('admin.pit.ajaxGetData');
        });

        Route::group(['prefix' => 'danger'], function () {
            Route::get('/', 'DangerController@index')->name('admin.danger');
            Route::get('/edit/{danger}', 'DangerController@edit')->name('admin.danger.edit');
            Route::get('/cameras_for_rule', 'DangerController@cameras_for_rule')->name('admin.danger.cameras_for_rule');
            Route::get('/create_rule', 'DangerController@create_rule')->name('admin.danger.create_rule');
            Route::get('/list', 'DangerController@list')->name('admin.danger.list');
            Route::get('/detail', 'DangerController@detail')->name('admin.danger.detail');
            Route::get('/past_analysis', 'DangerController@past_analysis')->name('admin.danger.past_analysis');
            Route::post('/store', 'DangerController@store')->name('admin.danger.store');
            Route::put('/update/{danger}', 'DangerController@update')->name('admin.danger.update');
            Route::delete('/delete/{danger}', 'DangerController@delete')->name('admin.danger.delete');
        });

        Route::group(['prefix' => 'shelf'], function () {
            Route::get('/', 'ShelfController@index')->name('admin.shelf');
            Route::get('/edit/{shelf}', 'ShelfController@edit')->name('admin.shelf.edit');
            Route::get('/cameras_for_rule', 'ShelfController@cameras_for_rule')->name('admin.shelf.cameras_for_rule');
            Route::post('/create_rule', 'ShelfController@create_rule')->name('admin.shelf.create_rule');
            Route::post('/store', 'ShelfController@store')->name('admin.shelf.store');
            Route::get('/list', 'ShelfController@list')->name('admin.shelf.list');
            Route::get('/detail', 'ShelfController@detail')->name('admin.shelf.detail');
            Route::get('/past_analysis', 'ShelfController@detail')->name('admin.shelf.past_analysis');
            Route::get('/save_sorted_imgage/{detect}', 'ShelfController@save_sorted_imgage')->name('admin.shelf.save_sorted_imgage');
            Route::delete('/delete/{shelf}', 'ShelfController@delete')->name('admin.shelf.delete');
        });

        Route::group(['prefix' => 'thief'], function () {
            Route::get('/', 'ThiefController@index')->name('admin.thief');
            Route::get('/cameras_for_rule', 'ThiefController@cameras_for_rule')->name('admin.thief.cameras_for_rule');
            Route::post('/create_rule', 'ThiefController@create_rule')->name('admin.thief.create_rule');
            Route::get('/edit/{thief}', 'ThiefController@edit')->name('admin.thief.edit');
            Route::post('/store', 'ThiefController@store')->name('admin.thief.store');
            Route::get('/list', 'ThiefController@list')->name('admin.thief.list');
            Route::get('/detail', 'ThiefController@detail')->name('admin.thief.detail');
            Route::get('/past_analysis', 'ThiefController@detail')->name('admin.thief.past_analysis');
            Route::delete('/delete/{thief}', 'ThiefController@delete')->name('admin.thief.delete');
        });

        Route::group(['prefix' => 'meter'], function () {
            Route::get('/', 'MeterController@index')->name('admin.meter');
            Route::get('/create', 'MeterController@create')->name('admin.meter.create');
            Route::post('/create_rule', 'MeterController@create_rule')->name('admin.meter.create_rule');
            Route::get('/edit', 'MeterController@edit')->name('admin.meter.edit');
            Route::post('/edit2', 'MeterController@edit2')->name('admin.meter.edit2');
            Route::get('/list', 'MeterController@list')->name('admin.meter.list');
            Route::get('/detail', 'MeterController@detail')->name('admin.meter.detail');
        });

        Route::group(['prefix' => 'analyze'], function () {
            Route::get('/', 'AnalyzeController@index')->name('admin.analyze');
            Route::get('/now_list', 'AnalyzeController@now_list')->name('admin.analyze.now_list');
            Route::get('/finish_list', 'AnalyzeController@finish_list')->name('admin.analyze.finish_list');
        });

        Route::post('logout', 'Auth\LoginController@logout')->name('admin.logout');
    });
});
