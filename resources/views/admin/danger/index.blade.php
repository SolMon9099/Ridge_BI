@extends('admin.layouts.app')

@section('content')

    <div id="wrapper">
        <div class="breadcrumb">
            <ul>
                <li><a href="{{route('admin.danger')}}">危険エリア侵入検知</a></li>
                <li>ルール一覧</li>
            </ul>
        </div>
        <div id="r-content">
            <div class="title-wrap">
                <h2 class="title">ルール一覧</h2>
                <div class="new-btn"><a href="{{route('admin.danger.cameras_for_rule')}}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" style="fill: rgba(255,255,255, 1);transform: ;msFilter:;">
                            <path d="M3 16c0 1.103.897 2 2 2h3.586L12 21.414 15.414 18H19c1.103 0 2-.897 2-2V4c0-1.103-.897-2-2-2H5c-1.103 0-2 .897-2 2v12zM5 4h14v12h-4.414L12 18.586 9.414 16H5V4z"></path>
                            <path d="M11 14h2v-3h3V9h-3V6h-2v3H8v2h3z"></path>
                        </svg>
                        新規登録</a> </div>
            </div>

            @include('admin.layouts.flash-message')
            {{ $dangers->appends([])->links('vendor.pagination.admin-pagination') }}
            <div class="scroll active">
                <table class="table2 text-centre">
                    <thead>
                    <tr>
                        <th>編集</th>
                        <th>カメラNo</th>
                        <th>現場名</th>
                        <th>設置フロア</th>
                        <th>設置場所</th>
                        <th>アクション</th>
                        <th>カラー</th>
                        <th>検知履歴</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($dangers as $danger)
                        <tr>
                            <td><button type="button" class="edit" onclick="location.href='{{route('admin.danger.edit', ['danger' => $danger->id])}}'">編集</button></td>
                            <td>{{$danger->camera_no}}</td>
                            <td>{{isset($locations[$danger->location_id])?$locations[$danger->location_id]:''}}</td>
                            <td>{{$danger->floor_number}}</td>
                            <td>{{$danger->installation_position}}</td>
                            <td>{{config('const.action')[$danger->action_id]}}</td>
                            <td><input disabled type="color" value = "{{$danger->color}}"/></td>
                            <td>
                                <button type="button" class="history">履歴表示</button>
                                {{-- <button type="button" class="delete_danger_rules history" delete_index="{{ $danger->id }}">削除</button>
                                <form id="frm_delete_{{ $danger->id }}" action="{{ route('admin.danger.delete', ['danger'=> $danger->id]) }}" method="POST" style="display: none;">
                                    @csrf
                                    @method('delete')
                                </form> --}}
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            {{ $dangers->appends([])->links('vendor.pagination.admin-pagination') }}
        </div>
    </div>

<div id="dialog-confirm" title="test" style="display:none">
    <p><span class="ui-icon ui-icon-alert" style="float:left; margin:12px 12px 20px 0;"></span>
    <span id="confirm_text">These items will be permanently deleted and cannot be recovered. Are you sure?</span></p>
</div>

<link href="{{ asset('assets/vendor/jquery-ui/jquery-ui.min.css') }}" rel="stylesheet">

<script src="{{ asset('assets/vendor/jquery-ui/jquery-ui.min.js') }}"></script>
<script src="{{ asset('assets/admin/js/helper.js?2') }}"></script>

<script>
    var delete_id = "";
    $(document).ready(function () {
      $(".delete_danger_rules").click(function(e){
          e.preventDefault();
          delete_id = $(this).attr('delete_index');
          helper_confirm("dialog-confirm", "削除", "ルールを削除します。<br />よろしいですか？", 300, "確認", "閉じる", function(){
              var frm_id = "#frm_delete_" + delete_id;
              $(frm_id).submit();
          });
      });
    });
</script>

@endsection
