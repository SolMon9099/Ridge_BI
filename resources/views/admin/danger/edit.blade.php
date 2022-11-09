@extends('admin.layouts.app')

@section('content')

<div id="wrapper">
    <div class="breadcrumb">
        <ul>
            <li><a href="{{route('admin.danger')}}">危険エリア侵入検知</a></li>
            <li><a href="{{route('admin.danger')}}">ルール一覧・編集</a></li>
            @if(isset($view_only))
			    <li>ルール詳細</li>
            @else
                <li>ルール編集</li>
            @endif
        </ul>
    </div>
    <div id="r-content">
        <div class="title-wrap">
            @if(isset($view_only))
                <h2 class="title">ルール詳細</h2>
            @else
                <h2 class="title">ルール編集</h2>
                <p><a data-target="howto" class="modal-open">使い方</a></p>
            @endif
        </div>
        @if(!isset($view_only))
        <div class="flow">
            <ul>
            <li class="active"><span>Step.2</span>エリア選択・検知設定</li>
            </ul>
        </div>
        @endif
        <form action="{{route('admin.danger.store')}}" method="post" name="form1" id="form_danger_rule">
            @csrf
            <input type="hidden" name="camera_id" value="{{$camera_id}}" id = "camera_id" />
            <input type="hidden" name="operation_type" value="edit" />
            @include('admin.danger._form')
        </form>
    </div>
</div>

@endsection
