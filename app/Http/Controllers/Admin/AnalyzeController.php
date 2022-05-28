<?php

namespace App\Http\Controllers\Admin;

class AnalyzeController extends AdminController
{
    public function index()
    {
        return view('admin.analyze.index');
    }

    public function now_list()
    {
        return view('admin.analyze.now_list');
    }

    public function finish_list()
    {
        return view('admin.analyze.finish_list');
    }
}
