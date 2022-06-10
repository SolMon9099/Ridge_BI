<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Service\SafieApiService;

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
    // return view('welcome');
    if (isset(request()->code)) {
        $safie_code = request()->code;
        $safie_service = new SafieApiService();
        $res_data = $safie_service->getAccessToken($safie_code);
        if (isset($res_data['token_type']) && $res_data['token_type'] == 'Bearer') {
            if (isset($res_data['access_token'])) {
                Session::put('access_token', $res_data['access_token']);
            }
            if (isset($res_data['refresh_token'])) {
                Session::put('refresh_token', $res_data['refresh_token']);
            }
        }
        Session::put('safie_code', $safie_code);
    }

    return redirect()->route('admin.top');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
