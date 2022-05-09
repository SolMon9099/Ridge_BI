<?php

namespace App\Http\Controllers\Admin;

class TopController extends AdminController
{
    public function index()
    {
        return view('admin.top.index');
    }
}
