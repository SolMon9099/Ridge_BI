<?php
    $login_user = Auth::guard('admin')->user();
    $super_admin_flag = ($login_user->authority_id == config('const.super_admin_code'));
?>
@extends('admin.layouts.app')

@section('content')

<div id="wrapper">
    <div class="breadcrumb">
        <ul>
            <li><a href="{{route('admin.location')}}">現場設定</a></li>
            <li>現場名一覧</li>
        </ul>
    </div>
    <div id="r-content">
        <div class="title-wrap">
            <h2 class="title">現場名一覧</h2>
            @if(!$super_admin_flag)
            <div class="new-btn">
                <a href="{{route('admin.location.create')}}">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" style="fill: rgba(255,255,255, 1);transform: ;msFilter:;">
                        <path d="M3 16c0 1.103.897 2 2 2h3.586L12 21.414 15.414 18H19c1.103 0 2-.897 2-2V4c0-1.103-.897-2-2-2H5c-1.103 0-2 .897-2 2v12zM5 4h14v12h-4.414L12 18.586 9.414 16H5V4z"></path>
                        <path d="M11 14h2v-3h3V9h-3V6h-2v3H8v2h3z"></path>
                    </svg>
                新規登録</a>
            </div>
            @endif
        </div>
        @include('admin.layouts.flash-message')
        {{ $locations->appends([])->links('vendor.pagination.admin-pagination') }}
        <div class="scroll">
            <table class="table2 text-centre">
                <thead>
                <tr>
                    <th>編集</th>
                    <th>現場コード</th>
                    <th>現場名</th>
                    {{-- <th>現場責任者</th> --}}
                    <th>現場担当者</th>
                    <th>有効設定</th>
                    <th>削除</th>
                </tr>
                </thead>
                <tbody>
                @foreach($locations as $location)
                <tr>
                    <td><button type="button" class="edit" onclick="location.href='{{route('admin.location.edit', ['location' => $location->id])}}'">編集</button></td>
                    <td>{{$location->code}}</td>
                    <td>{{$location->name}}</td>
                    {{-- <td>
                    @foreach(explode(",",$location->owner) as $owner)
                        @if(isset($admins[$owner]))
                        {{$admins[$owner]}}<br/>
                        @endif
                    @endforeach
                    </td> --}}
                    <td>
                    @foreach(explode(",",$location->manager) as $manager)
                        @if(isset($admins[$manager]))
                        {{$admins[$manager]}}<br/>
                        @endif
                    @endforeach
                    </td>
                    <td>{{$location->is_enabled ? "有効":"無効"}}</td>
                    <td><button type="button" class="history delete_locations" delete_index="{{ $location->id }}">削除</button>
                    <form id="frm_delete_{{ $location->id }}" action="{{ route('admin.location.delete', ['location'=> $location->id]) }}" method="POST" style="display: none;">
                        @csrf
                        @method('delete')
                    </form>
                    </td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        {{ $locations->appends([])->links('vendor.pagination.admin-pagination') }}
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
      $(".delete_locations").click(function(e){
          e.preventDefault();
          delete_id = $(this).attr('delete_index');
          helper_confirm("dialog-confirm", "削除", "現場を削除します。<br />よろしいですか？", 300, "確認", "閉じる", function(){
              var frm_id = "#frm_delete_" + delete_id;
              $(frm_id).submit();
          });
      });

  });
</script>

@endsection
