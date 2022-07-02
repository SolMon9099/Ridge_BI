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
            <h2 class="title">ルール新規作成</h2>
        </div>
        <div class="flow">
            <ul>
            <li><span>Step.1</span>カメラを選択</li>
            <li class="active"><span>Step.2</span>アクションとエリアを選択</li>
            </ul>
        </div>
        <form action="{{route('admin.danger.store')}}" method="post" name="form1" id="form_danger_rule">
            @csrf
            <input type="hidden" name="camera_id" value="{{$camera_id}}" id = "camera_id" />
            @include('admin.danger._form')
        </form>
    </div>
  </div>

@endsection
