@extends('admin.layouts.app')

@section('content')

<div id="wrapper">
    <div class="breadcrumb">
      <ul>
        <li><a href="{{route('admin.camera')}}">カメラ設定</a></li>
        <li>カメラマッピング一覧</li>
      </ul>
    </div>
    <div id="r-content">
      <div class="title-wrap">
        <h2 class="title">カメラマッピング一覧</h2>
        <div class="new-btn"><a href="{{route('admin.camera.create_drawing')}}">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" style="fill: rgba(255,255,255, 1);transform: ;msFilter:;">
            <path d="M3 16c0 1.103.897 2 2 2h3.586L12 21.414 15.414 18H19c1.103 0 2-.897 2-2V4c0-1.103-.897-2-2-2H5c-1.103 0-2 .897-2 2v12zM5 4h14v12h-4.414L12 18.586 9.414 16H5V4z"></path>
            <path d="M11 14h2v-3h3V9h-3V6h-2v3H8v2h3z"></path>
          </svg>
          新規登録</a> </div>
      </div>
      <form action="{{route('admin.camera.mapping')}}" method="get" name="form1" id="form1">
        @csrf
        <div class="title-wrap ver2 stick">
          <div class="sp-ma">
            <div class="sort">
              <ul class="date-list">
                <li>
                  <h4>現場名</h4>
                </li>
                <li>
                  <div class="select-c">
                    <select name="location">
                      <option value="0">選択する</option>
                      @foreach($locations as $key => $loc)
                      @if (isset($input) && $input->has('location') && $input->location == $key)
                        <option value="{{$key}}" selected>{{$loc}}</option>
                      @else
                        <option value="{{$key}}">{{$loc}}</option>
                      @endif
                      @endforeach
                    </select>
                  </div>
                </li>
              </ul>
              <button type="submit" class="apply">絞り込む</button>
            </div>
          </div>
        </div>
        </form>
        @include('admin.layouts.flash-message')
        {{ $drawings->appends([])->links('vendor.pagination.admin-pagination') }}
        <div class="scroll active">
          <table class="table2 text-centre">
            <thead>
              <tr>
                <th>編集</th>
                <th>現場名</th>
                <th>設置フロア</th>
                <th>詳細</th>
                <th>削除</th>
              </tr>
            </thead>
            <tbody>
                @foreach($drawings as $drawing)
                <tr>
                    <td><button type="button" class="edit" onclick="location.href='{{route('admin.camera.edit_drawing', ['drawing' => $drawing->id])}}'">編集</button></td>
                    <td>{{$drawing->location->name}}</td>
                    <td>{{$drawing->floor_number}}</td>
                    <td><button type="button" class="detail" onclick="location.href='{{route('admin.camera.mapping.detail', ['drawing' => $drawing->id])}}'">詳細
                    </button></td>
                    <td><button type="button" class="delete_drawings history" delete_index="{{ $drawing->id }}">削除</button>
                    <form id="frm_delete_{{ $drawing->id }}" action="{{ route('admin.camera.delete_drawing', ['drawing'=> $drawing->id]) }}" method="POST" style="display: none;">
                    @csrf
                    @method('delete')
                    </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
          </table>
        </div>
        {{ $drawings->appends([])->links('vendor.pagination.admin-pagination') }}
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
      $(".delete_drawings").click(function(e){
          e.preventDefault();
          delete_id = $(this).attr('delete_index');
          helper_confirm("dialog-confirm", "削除", "現場図面を削除します。<br />よろしいですか？", 300, "確認", "閉じる", function(){
              var frm_id = "#frm_delete_" + delete_id;
              $(frm_id).submit();
          });
      });

  });
</script>

@endsection
