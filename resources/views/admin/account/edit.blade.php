@extends('admin.layouts.app')

@section('content')

<div id="wrapper">
    <div class="breadcrumb">
        <ul>
            <li><a href="{{route('admin.top.permission_group')}}">権限設定</a></li>
            <li><a href="{{route('admin.account')}}">アカウント管理</a></li>
            <li>編集</li>
        </ul>
    </div>
    <div id="r-content">
		<div class="title-wrap">
            <h2 class="title">アカウント編集</h2>
		</div>
        <form action="{{route('admin.account.update', ['admin'=> $admin->id])}}" method="post" name="form1" id="form1">
            @csrf
            @method('PUT')
            <input type="hidden" name="id" value="{{$admin->id}}"/>

            @include('admin.account._form')
		    <!--
                <div class="tour-content mt25">
                <div class="float-l pager"><a href="">＜</a><span class="current">1</span><a href="">2</a><a href="">3</a><a href="">4</a><a href="">5</a><a href="">＞</a></div>
                </div>
            -->
            <div class="btns">
                <button type="submit" class="ok">更新</button>
            </div>
        </form>
    </div>
</div>

@endsection
