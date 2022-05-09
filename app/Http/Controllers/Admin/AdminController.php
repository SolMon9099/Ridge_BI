<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
// use Auth;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    protected $platform = 'admin';
    protected $per_page = 20;

    public function __construct()
    {
        $this->middleware('auth:admin');

        app('view')->composer('admin.*', function ($view) {
            $v_platform = $this->platform;
            $v_admin = Auth::guard('admin')->user();

            $view->with(compact('v_platform', 'v_admin'));
        });
    }
}
