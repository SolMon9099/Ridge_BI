<?php
    $login_user = Auth::guard('admin')->user();
    $super_admin_flag = ($login_user->authority_id == config('const.super_admin_code'));
?>
@extends('admin.layouts.app')

@section('content')

<div id="wrapper">
    <div class="breadcrumb">
        <ul>
            <li><a href="{{route('admin.camera')}}">カメラ設定</a></li>
            <li><a href="{{route('admin.camera.mapping')}}">カメラマッピング一覧</a></li>
            <li>カメラマッピング編集</li>
        </ul>
    </div>
    <div id="r-content">
        <div class="title-wrap">
            <h2 class="title">カメラマッピング編集</h2>
        </div>
        <form action="{{route('admin.camera.update_drawing', ['drawing'=> $drawing->id])}}" method="post" name="form1" id="form1" enctype="multipart/form-data">
        @csrf
        @method('put')
        <input type="hidden" name="id" value="{{$drawing->id}}"/>
        @include('admin.camera._form_drawing')
        @if(!$super_admin_flag)
        <div class="btns">
            <button type="submit" class="ok">更新</button>
        </div>
        @endif
        </form>
    </div>
</div>

@endsection
