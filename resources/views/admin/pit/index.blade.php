<?php
    $login_user = Auth::guard('admin')->user();
    $super_admin_flag = ($login_user->authority_id == config('const.super_admin_code'));
?>
@extends('admin.layouts.app')

@section('content')

    <div id="wrapper">
        <div class="breadcrumb">
            <ul>
                <li><a href="{{route('admin.pit')}}">ピット入退場検知</a></li>
                <li>ルール一覧</li>
            </ul>
        </div>
        <div id="r-content">
            <div class="title-wrap">
                <h2 class="title">ルール一覧</h2>
                @if(!$super_admin_flag)
                <div class="new-btn">
                    <a href="{{route('admin.pit.cameras_for_rule')}}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" style="fill: rgba(255,255,255, 1);transform: ;msFilter:;">
                            <path d="M3 16c0 1.103.897 2 2 2h3.586L12 21.414 15.414 18H19c1.103 0 2-.897 2-2V4c0-1.103-.897-2-2-2H5c-1.103 0-2 .897-2 2v12zM5 4h14v12h-4.414L12 18.586 9.414 16H5V4z"></path>
                            <path d="M11 14h2v-3h3V9h-3V6h-2v3H8v2h3z"></path>
                        </svg>
                    新規登録</a>
                </div>
                @endif
            </div>
            <div class="notice-area">こちらの画面では、カメラ毎に通知ルールの新規登録・既存の通知ルールの編集・削除が行えます。</div>
            @include('admin.layouts.flash-message')
            {{ $pits->appends([])->links('vendor.pagination.admin-pagination') }}
            <div class="scroll active">
                <table class="table2 text-centre">
                    <thead>
                    <tr>
                        <th>編集</th>
                        <th>カメラNo</th>
                        <th>設置エリア</th>
                        <th>設置フロア</th>
                        <th>設置場所</th>
                        <th>ピット内人数</th>
                        <th>ピット内最大時間</th>
                        {{-- <th>検知履歴</th> --}}
                        <th>削除</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($pits as $pit)
                        <tr>
                            <td><button type="button" class="edit" onclick="location.href='{{route('admin.pit.edit', ['pit' => $pit->id])}}'">編集</button></td>
                            <td>{{$pit->camera_no}}</td>
                            <td>{{$pit->location_name}}</td>
                            <td>{{$pit->floor_number}}</td>
                            <td>{{$pit->installation_position}}</td>
                            <td>{{$pit->min_members > 0 ? (string)($pit->min_members) : ''}}</td>
                            <td>{{$pit->max_permission_time > 0 ? (string)($pit->max_permission_time).'分' : ''}}</td>
                            {{-- <td>
                                <button type="button" class="history">履歴表示</button>
                            </td> --}}
                            <td>
                                @if (!$super_admin_flag)
                                    <button type="button" class="delete_pits history" delete_index="{{ $pit->id }}">削除</button>
                                    <form id="frm_delete_{{ $pit->id }}" action="{{ route('admin.pit.delete', ['pit'=> $pit->id]) }}" method="POST" style="display: none;">
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
            {{ $pits->appends([])->links('vendor.pagination.admin-pagination') }}
        </div>
    </div>

    <div id="dialog-confirm" title="test" style="display:none">
        <p><span class="ui-icon ui-icon-alert" style="float:left; margin:12px 12px 20px 0;"></span>
            <span id="confirm_text">These items will be permanently deleted and cannot be recovered. Are you sure?</span></p>
    </div>

    <link href="{{ asset('assets/vendor/jquery-ui/jquery-ui.min.css') }}" rel="stylesheet">

    <style>
        .notice-area{
            color: #999;
        }
    </style>
    <script src="{{ asset('assets/vendor/jquery-ui/jquery-ui.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/helper.js?2') }}"></script>

    <script>
      var delete_id = "";
      $(document).ready(function () {
          $(".delete_pits").click(function(e){
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
