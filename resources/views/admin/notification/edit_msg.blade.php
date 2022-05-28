@extends('admin.layouts.app')

@section('content')

<div id="wrapper">
    <div class="breadcrumb">
      <ul>
        <li><a href="{{route('admin.top.permission_group')}}">権限設定</a></li>
        <li><a href="{{route('admin.notification')}}">通知設定</a></li>
        <li>通知メッセージ編集</li>
      </ul>
    </div>
    <div id="r-content">
      <div class="title-wrap">
        <h2 class="title">通知メッセージ編集</h2>
      </div>
      <p class="set">通知メッセージを設定して下さい</p>
      <form action="{{route('admin.notification.update_msg', ['msg'=> $msg->id])}}" method="post" name="form1" id="form1">
        @csrf
        @method('put')
        @include('admin.notification._form_msg')
        <div class="btns">
          <button type="submit" class="ok">更新</button>
        </div>
      </form>
    </div>
  </div>

@endsection
