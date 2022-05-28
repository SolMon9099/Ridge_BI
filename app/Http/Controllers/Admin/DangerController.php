<?php

namespace App\Http\Controllers\Admin;

class DangerController extends AdminController
{
    public function index()
    {
        return view('admin.danger.index');
    }

    public function edit()
    {
        return view('admin.danger.edit');
    }

    public function edit2()
    {
        return view('admin.danger.edit2');
    }

    public function create()
    {
        return view('admin.danger.create');
    }

    public function create2()
    {
        return view('admin.danger.create2');
    }

    public function list()
    {
        return view('admin.danger.list');
    }

    public function list2()
    {
        return view('admin.danger.list2');
    }
}
