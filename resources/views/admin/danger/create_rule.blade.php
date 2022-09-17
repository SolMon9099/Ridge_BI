@extends('admin.layouts.app')

@section('content')

<div id="wrapper">
    <div class="breadcrumb">
      <ul>
        <li><a href="{{route('admin.danger')}}">危険エリア侵入検知</a></li>
        <li><a href="{{route('admin.danger')}}">ルール一覧</a></li>
        <li>ルール新規作成</li>
      </ul>
    </div>
    <div id="r-content">
        <div class="title-wrap">
            <div style="display: flex;">
                <a style="padding-top:17px;margin-right:10px;" href="{{route('admin.danger.cameras_for_rule')}}">
                    <img width="25px;" src="{{asset('assets/admin/img/icons8-back-arrow-48.png')}}"/>
                </a>
                <h2 class="title">ルール新規作成</h2>
            </div>
            <p><a data-target="howto" class="modal-open">使い方</a></p>
        </div>
        <div class="flow">
            <ul>
                <li><a href="{{route('admin.danger.cameras_for_rule')}}"><span>Step.1</span>カメラを選択</a></li>
            <li class="active"><span>Step.2</span>エリア選択・検知設定</li>
            </ul>
        </div>
        <form action="{{route('admin.danger.store')}}" method="post" name="form1" id="form_danger_rule">
            @csrf
            <input type="hidden" name="camera_id" value="{{$camera_id}}" id = "camera_id" />
            <input type="hidden" name="operation_type" value="register" />
            @include('admin.danger._form')
        </form>
    </div>
  </div>

@endsection
