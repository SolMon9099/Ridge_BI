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
        <li>現場新規登録</li>
      </ul>
    </div>
    <div id="r-content">
        <div class="title-wrap">
            <h2 class="title">現場新規登録</h2>
        </div>
        <form action="{{route('admin.location.store')}}" method="post" name="form1" id="form1">
            @csrf
            @include('admin.location._form')
            <div class="btns">
                @if(!$super_admin_flag)
                <button type="submit" class="ok">登録</button>
                @endif
            </div>
        </form>
    </div>
</div>

@endsection
