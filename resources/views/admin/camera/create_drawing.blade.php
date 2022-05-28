@extends('admin.layouts.app')

@section('content')

<div id="wrapper">
  <div class="breadcrumb">
    <ul>
        <li><a href="{{route('admin.camera')}}">カメラ設定</a></li>
        <li><a href="{{route('admin.camera.mapping')}}">カメラマッピング一覧</a></li>
        <li>カメラマッピング新規登録</li>
    </ul>
  </div>
  <div id="r-content">
    <div class="title-wrap">
      <h2 class="title">カメラマッピング新規登録</h2>
    </div>
    <form action="{{route('admin.camera.store_drawing')}}" method="post" name="form1" id="form1" enctype="multipart/form-data">
      @csrf
      @include('admin.camera._form_drawing')
      <div class="btns">
        <button type="submit" class="ok">登録</button>
      </div>
    </form>
  </div>
</div>

@endsection
