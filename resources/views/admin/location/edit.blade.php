<?php
    $login_user = Auth::guard('admin')->user();
    $super_admin_flag = ($login_user->authority_id == config('const.super_admin_code'));
?>
@extends('admin.layouts.app')

@section('content')

<div id="wrapper">
    <div class="breadcrumb">
        <ul>
            <li><a href="{{route('admin.location')}}">現場設定</a></li>
            <li><a href="{{route('admin.location')}}">設置エリア一覧</a></li>
            <li>現場編集</li>
        </ul>
    </div>
    <div id="r-content">
        <div class="title-wrap">
            <h2 class="title">現場編集</h2>
        </div>
        <form action="{{route('admin.location.update', ['location'=> $location->id])}}" method="post" name="form1" id="form1">
            @csrf
            @method('put')
            <input type="hidden" name="id" value="{{$location->id}}"/>
            @include('admin.location._form')
            @if(!$super_admin_flag)
            <div class="btns">
                <button type="submit" class="ok">更新</button>
            </div>
            @endif
        </form>
    </div>
</div>

@endsection
