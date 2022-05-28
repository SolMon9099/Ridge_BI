@extends('admin.layouts.app')

@section('content')

<div id="wrapper">
    <div class="breadcrumb">
      <ul>
        <li><a href="{{route('admin.top.permission_group')}}">権限設定</a></li>
        <li>通知設定</li>
      </ul>
    </div>
    <div id="r-content">
      <div class="title-wrap">
        <h2 class="title">通知設定</h2>
      </div>
        <ul class="tab">
          <li class="{{(!isset($active_tab)) ? 'active' : ''}}"><a href="#tab1">送信先</a></li>
          <li class="{{(isset($active_tab) && $active_tab == 'tab2') ? 'active' : ''}}"><a href="#tab2">通知メッセージ</a></li>
        </ul>
    
        <div class="list">
        @include('admin.layouts.flash-message')
          <div class="inner {{(!isset($active_tab)) ? 'active' : ''}}">
											    <div class="title-wrap">
          <h2></h2>
          <div class="new-btn"><a href="{{route('admin.notification.create')}}">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" style="fill: rgba(255,255,255, 1);transform: ;msFilter:;">
              <path d="M3 16c0 1.103.897 2 2 2h3.586L12 21.414 15.414 18H19c1.103 0 2-.897 2-2V4c0-1.103-.897-2-2-2H5c-1.103 0-2 .897-2 2v12zM5 4h14v12h-4.414L12 18.586 9.414 16H5V4z"></path>
              <path d="M11 14h2v-3h3V9h-3V6h-2v3H8v2h3z"></path>
            </svg>
            新規登録</a> </div>
        </div>
        {{ $groups->appends([])->links('vendor.pagination.admin-pagination') }}
            <div class="scroll active">
              <table class="table2 text-centre">
                <thead>
                  <tr>
                    <th class="w10">編集</th>
                    <th>送信先グループ名</th>
                    <th>送信先アドレス</th>
                    <th>削除</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach ($groups as $group)
                  <tr>
                    <td><button type="button" class="edit" onclick="location.href='{{route('admin.notification.edit', ['group' => $group->id])}}'">編集</button></td>
                    <td>{{$group->name}}</td>
                    <td>{!! implode("<br/>", explode(",", $group->emails)) !!}</td>
                    <td><button type="button" class="delete_groups history" delete_index="{{ $group->id }}">削除</button></td>
                    <form id="frm_delete_{{ $group->id }}" action="{{ route('admin.notification.delete', ['group'=> $group->id]) }}" method="POST" style="display: none;">
                      @csrf
                      @method('delete')
                    </form>
                  </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
        {{ $groups->appends([])->links('vendor.pagination.admin-pagination') }}
          </div>
          <!-- .inner end -->
          
          <div class="inner {{(isset($active_tab) && $active_tab == 'tab2') ? 'active' : ''}}">
          <div class="title-wrap">
          <h2></h2>
          <div class="new-btn"><a href="{{route('admin.notification.create_msg')}}">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" style="fill: rgba(255,255,255, 1);transform: ;msFilter:;">
              <path d="M3 16c0 1.103.897 2 2 2h3.586L12 21.414 15.414 18H19c1.103 0 2-.897 2-2V4c0-1.103-.897-2-2-2H5c-1.103 0-2 .897-2 2v12zM5 4h14v12h-4.414L12 18.586 9.414 16H5V4z"></path>
              <path d="M11 14h2v-3h3V9h-3V6h-2v3H8v2h3z"></path>
            </svg>
            新規登録</a> </div>
        </div>
        {{ $msgs->appends([])->links('vendor.pagination.admin-pagination') }}
          <div class="scroll active">
              <table class="table2 text-centre">
                <thead>
                  <tr>
                    <th class="w10">編集</th>
                    <th>メッセージ名</th>
                    <th class="w42">送信メッセージ</th>
                    <th>削除</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($msgs as $msg)
                  <tr>
                    <td><button type="button" class="edit" onclick="location.href='{{route('admin.notification.edit_msg', ['msg' => $msg->id])}}'">編集</button></td>
                    <td>{{$msg->title}}</td>
                    <td><p class=" text-left">{!! nl2br($msg->content) !!}</p></td>
                    <td><button type="button" class="delete_msgs history" delete_msg_index="{{ $msg->id }}">削除</button></td>
                    <form id="frm_msg_delete_{{ $msg->id }}" action="{{ route('admin.notification.delete_msg', ['msg'=> $msg->id]) }}" method="POST" style="display: none;">
                      @csrf
                      @method('delete')
                    </form>
                  </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
            {{ $msgs->appends([])->links('vendor.pagination.admin-pagination') }}
          </div>
          <!-- .inner end --> 
        </div>
        <!-- .list end --> 
        
        <!--
        <div class="tour-content mt25">
          <div class="float-l pager"><a href="">＜</a><span class="current">1</span><a href="">2</a><a href="">3</a><a href="">4</a><a href="">5</a><a href="">＞</a></div>
        </div>
-->
        
    </div>
  </div>


<div id="dialog-confirm" title="test" style="display:none">
  <p><span class="ui-icon ui-icon-alert" style="float:left; margin:12px 12px 20px 0;"></span>
      <span id="confirm_text">These items will be permanently deleted and cannot be recovered. Are you sure?</span></p>
</div>

<div id="dialog-msg-confirm" title="test" style="display:none">
  <p><span class="ui-icon ui-icon-alert" style="float:left; margin:12px 12px 20px 0;"></span>
      <span id="confirm_text">These items will be permanently deleted and cannot be recovered. Are you sure?</span></p>
</div>

<link href="{{ asset('assets/vendor/jquery-ui/jquery-ui.min.css') }}" rel="stylesheet">

<script src="{{ asset('assets/vendor/jquery-ui/jquery-ui.min.js') }}"></script>
<script src="{{ asset('assets/admin/js/helper.js?2') }}"></script>

<script>
  var delete_id = "";
  $(document).ready(function () {
      $(".delete_groups").click(function(e){
          e.preventDefault();
          delete_id = $(this).attr('delete_index');
          helper_confirm("dialog-confirm", "削除", "グループを削除します。 <br />よろしいですか？", 300, "確認", "閉じる", function(){
              var frm_id = "#frm_delete_" + delete_id;
              console.log('frm', frm_id);
              $(frm_id).submit();
          });
      });
      $(".delete_msgs").click(function(e){
          e.preventDefault();
          delete_id = $(this).attr('delete_msg_index');
          helper_confirm("dialog-msg-confirm", "削除", "メッセージを削除します。 <br />よろしいですか？", 300, "確認", "閉じる", function(){
              var frm_id = "#frm_msg_delete_" + delete_id;
              console.log('frm', frm_id);
              $(frm_id).submit();
          });
      });
  });
</script>


@endsection
