<?php
    $login_user = Auth::guard('admin')->user();
    $super_admin_flag = ($login_user->authority_id == config('const.super_admin_code'));
?>
@extends('admin.layouts.app')

@section('content')

<div id="wrapper">
    <div class="breadcrumb">
        <ul>
            <li><a href="{{route('admin.thief')}}">大量盗難検知</a></li>
            <li>ルール一覧</li>
        </ul>
    </div>
    <div id="r-content">
        <div class="title-wrap">
            <h2 class="title">ルール一覧</h2>
            @if(!$super_admin_flag)
            <div class="new-btn">
                <a href="{{route('admin.thief.cameras_for_rule')}}">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" style="fill: rgba(255,255,255, 1);transform: ;msFilter:;">
                    <path d="M3 16c0 1.103.897 2 2 2h3.586L12 21.414 15.414 18H19c1.103 0 2-.897 2-2V4c0-1.103-.897-2-2-2H5c-1.103 0-2 .897-2 2v12zM5 4h14v12h-4.414L12 18.586 9.414 16H5V4z"></path>
                    <path d="M11 14h2v-3h3V9h-3V6h-2v3H8v2h3z"></path>
                </svg>
                新規登録
                </a>
            </div>
            @endif
        </div>
        @include('admin.layouts.flash-message')
        {{ $thiefs->appends([])->links('vendor.pagination.admin-pagination') }}
        <div class="scroll">
            <table class="table2 text-centre">
                <thead>
                    <tr>
                        <th>編集</th>
                        <th>カメラNo</th>
                        <th>現場名</th>
                        <th>設置フロア</th>
                        <th>設置場所</th>
                        <th>ハンガーの色</th>
                        <th>カラー</th>
                        <th>検知履歴</th>
                        <th>削除</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($thiefs as $thief)
                        <tr>
                            <td><button type="button" class="edit" onclick="location.href='{{route('admin.thief.edit', ['thief' => $thief->id])}}'">編集</button></td>
                            <td>{{$thief->camera_no}}</td>
                            <td>{{isset($locations[$thief->location_id])?$locations[$thief->location_id]:''}}</td>
                            <td>{{$thief->floor_number}}</td>
                            <td>{{$thief->installation_position}}</td>
                            <td><input disabled type="color" value = "{{substr($thief->hanger, 0, 7)}}"/></td>
                            <td><input disabled type="color" value = "{{$thief->color}}"/></td>
                            <td>
                                <button type="button" class="history">履歴表示</button>
                            </td>
                            <td>
                                @if (!$super_admin_flag)
                                    <button type="button" class="delete_thief_rules history" delete_index="{{ $thief->id }}">削除</button>
                                    <form id="frm_delete_{{ $thief->id }}" action="{{ route('admin.thief.delete', ['thief'=> $thief->id]) }}" method="POST" style="display: none;">
                                        @csrf
                                        @method('delete')
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        {{ $thiefs->appends([])->links('vendor.pagination.admin-pagination') }}
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
      $(".delete_thief_rules").click(function(e){
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
