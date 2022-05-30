@extends('admin.layouts.app')

@section('content')

<div id="wrapper">
    <div class="breadcrumb">
      <ul>
           <li>危険エリア侵入検知</li>
        <li>ルール一覧</li>
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
        <form action="{{route('admin.danger.store')}}" method="post" name="form1" id="form1">
            @csrf
            @include('admin.danger._form')
      </form>
    </div>
  </div>

@endsection
