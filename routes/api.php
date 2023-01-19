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

Route::group(['prefix' => 'safie'], function () {
    Route::get('/getReactInfo', 'SafieController@getReactInfo')->name('api.safie.getReactInfo');
});

Route::group(['prefix' => 'detection'], function () {
    Route::post('/danger', 'DetectionController@saveDangerDetection')->name('api.detection.danger');
    Route::post('/shelf', 'DetectionController@saveShelfDetection')->name('api.detection.shelf');
    Route::post('/pit', 'DetectionController@savePitDetection')->name('api.detection.pit');
    Route::post('/thief', 'DetectionController@saveThiefDetection')->name('api.detection.thief');
    Route::post('/vehicle', 'DetectionController@saveVehicleDetection')->name('api.detection.vehicle');
    Route::post('/heatmap', 'DetectionController@saveHeatmap')->name('api.detection.heatmap');
});

Route::middleware('auth:api')->group(function () {
});
