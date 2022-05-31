<?php

namespace App\Http\Controllers\Admin;

class ShelfController extends AdminController
{
    public function index()
    {
        return view('admin.shelf.index');
    }

    public function edit()
    {
        return view('admin.shelf.edit');
    }

    public function edit2()
    {
        return view('admin.shelf.edit2');
    }

    public function create()
    {
        return view('admin.shelf.create');
    }

    public function create_rule()
    {
        return view('admin.shelf.create_rule');
    }

    public function list()
    {
        return view('admin.shelf.list');
    }

    public function list2()
    {
        return view('admin.shelf.list2');
    }
}
