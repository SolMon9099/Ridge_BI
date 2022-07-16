<?php

namespace App\Http\Controllers\Admin;

class ThiefController extends AdminController
{
    public function index()
    {
        return view('admin.thief.index');
    }

    public function create()
    {
        return view('admin.thief.create');
    }

    public function create_rule()
    {
        return view('admin.thief.create_rule');
    }

    public function edit()
    {
        return view('admin.thief.edit');
    }

    public function edit2()
    {
        return view('admin.thief.edit2');
    }

    public function list()
    {
        return view('admin.thief.list');
    }

    public function detail()
    {
        return view('admin.thief.detail');
    }
}
