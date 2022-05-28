@extends('admin.layouts.app')

@section('content')

<div id="wrapper">
    <div class="breadcrumb">
      <ul>
        <li><a href="{{route('admin.top.permission_group')}}">権限設定</a></li>
        <li><a href="{{route('admin.notification')}}">通知設定</a></li>
        <li>送信先グループ新規登録</li>
      </ul>
    </div>
    <div id="r-content">
      <div class="title-wrap">
        <h2 class="title">送信先グループ新規登録</h2>
      </div>
      <p class="set">通知先グループを設定して下さい</p>
      <form action="{{route('admin.notification.store')}}" method="post" name="form1" id="form1">
        @csrf
        @include('admin.notification._form')
        <div class="btns">
          <button type="submit" class="ok">登録</button>
        </div>        
      </form>
    </div>
  </div>

@endsection
