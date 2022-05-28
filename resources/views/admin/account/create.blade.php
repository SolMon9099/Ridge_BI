@extends('admin.layouts.app')

@section('content')

<div id="wrapper">
    <div class="breadcrumb">
        <ul>
            <li><a href="{{route('admin.top.permission_group')}}">権限設定</a></li>
            <li><a href="{{route('admin.account')}}">アカウント管理</a></li>
            <li>新規登録</li>
        </ul>
    </div>
    <div id="r-content">
	    <div class="title-wrap">
        <h2 class="title">アカウント新規登録</h2>
		  </div>
      <form action="{{route('admin.account.store')}}" method="post" name="form1" id="form1">
        @csrf
        @include('admin.account._form')
        <div class="btns">
          <button type="submit" class="ok">登録</button>
        </div>
      </form>
    </div>
  </div>

@endsection
