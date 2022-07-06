@extends('admin.layouts.app')

@section('content')

<div id="wrapper">
    <div class="breadcrumb">
        <ul>
            <li><a href="{{route('admin.camera')}}">カメラ設定</a></li>
            <li><a href="{{route('admin.camera')}}">カメラ一覧</a></li>
            <li>カメラ編集</li>
        </ul>
    </div>
    <div id="r-content">
		<div class="title-wrap">
            <h2 class="title">カメラ編集</h2>
		</div>
        <form action="{{route('admin.camera.update', ['camera'=> $camera->id])}}" method="post" name="form1" id="form1">
            @csrf
            @method('put')
            <input type="hidden" name="id" value="{{$camera->id}}"/>
            @include('admin.camera._form')
            <div class="btns">
                <button type="submit" class="ok">更新</button>
                <button type="button" class="delete2" onclick="deleteCamera()">削除</button>
            </div>
        </form>
        <form id="camera_delete" action="{{ route('admin.camera.delete', ['camera'=> $camera->id]) }}" method="POST" style="display: none;">
            @csrf
            @method('delete')
        </form>
    </div>
</div>


<div id="dialog-confirm" title="test" style="display:none">
    <p>
        <span class="ui-icon ui-icon-alert" style="float:left; margin:12px 12px 20px 0;"></span>
        <span id="confirm_text">These items will be permanently deleted and cannot be recovered. Are you sure?</span>
    </p>
</div>

<link href="{{ asset('assets/vendor/jquery-ui/jquery-ui.min.css') }}" rel="stylesheet">
<script src="{{ asset('assets/vendor/jquery-ui/jquery-ui.min.js') }}"></script>
<script src="{{ asset('assets/admin/js/helper.js?2') }}"></script>

<script>
    function deleteCamera(){
        helper_confirm("dialog-confirm", "削除", "カメラを削除します。<br />よろしいですか？", 300, "確認", "閉じる", function(){
            $('#camera_delete').submit();
        });
    }
</script>


@endsection
