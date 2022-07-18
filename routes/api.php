<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['prefix' => 'camera'], function () {
    Route::get('/getImage', 'SafieController@getImage')->name('api.camera.getImage');
    Route::get('/getLiveStreaming', 'SafieController@getLiveStreaming')->name('api.camera.getLiveStreaming');
});

Route::group(['prefix' => 'detection'], function () {
    Route::get('/danger', 'DetectionController@saveDangerDetection')->name('api.detection.danger');
    Route::get('/shelf', 'DetectionController@saveShelfDetection')->name('api.detection.shelf');
    Route::get('/pit', 'DetectionController@savePitDetection')->name('api.detection.pit');
    Route::get('/thief', 'DetectionController@saveThiefDetection')->name('api.detection.thief');
});

Route::middleware('auth:api')->group(function () {
});
