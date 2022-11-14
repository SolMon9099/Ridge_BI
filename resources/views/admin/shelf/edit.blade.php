@extends('admin.layouts.app')

@section('content')

<div id="wrapper">
    <div class="breadcrumb">
        <ul>
            <li><a href="{{route('admin.shelf')}}">棚乱れ検知</a></li>
            <li><a href="{{route('admin.shelf')}}">ルール一覧・編集</a></li>
            <li>ルール編集</li>
        </ul>
    </div>
    <div id="r-content">
        <div class="title-wrap">
            <h2 class="title">ルール編集</h2>
            <p><a data-target="howto" class="modal-open">使い方</a></p>
        </div>
        <div class="flow">
            <ul>
                <li class="active">エリア選択・検知設定</li>
            </ul>
        </div>
        <form action="{{route('admin.shelf.store')}}" method="post" name="form1" id="form_shelf_rule">
            @csrf
            <input type="hidden" name="camera_id" value="{{$camera_id}}" id = "camera_id" />
            <input type="hidden" name="operation_type" value="edit" />
            @include('admin.shelf._form')
        </form>
    </div>
</div>
@endsection
