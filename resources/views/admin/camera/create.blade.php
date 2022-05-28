@extends('admin.layouts.app')

@section('content')

<div id="wrapper">
    <div class="breadcrumb">
      <ul>
        <li><a href="{{route('admin.camera')}}">カメラ設定</a></li>
        <li><a href="{{route('admin.camera')}}">カメラ一覧</a></li>
        <li>カメラ新規登録</li>
      </ul>
    </div>
    <div id="r-content">
      <div class="title-wrap">
        <h2 class="title">カメラ新規登録</h2>
      </div>
      <form action="{{route('admin.camera.store')}}" method="post" name="form1" id="form1">
        @csrf
        @include('admin.camera._form')
        <div class="btns">
          <button type="submit" class="ok">登録</button>
        </div>
      </form>
    </div>
  </div>

@endsection
