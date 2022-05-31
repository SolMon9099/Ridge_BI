<?php

namespace App\Http\Controllers\Admin;

class MeterController extends AdminController
{
    public function index()
    {
        return view('admin.meter.index');
    }

    public function create()
    {
        return view('admin.meter.create');
    }

    public function create_rule()
    {
        return view('admin.meter.create_rule');
    }

    public function edit()
    {
        return view('admin.meter.edit');
    }

    public function edit2()
    {
        return view('admin.meter.edit2');
    }

    public function list()
    {
        return view('admin.meter.list');
    }

    public function list2()
    {
        return view('admin.meter.list2');
    }
}
