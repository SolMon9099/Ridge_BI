@extends('admin.layouts.app')

@section('content')

<div id="wrapper">
    <div class="breadcrumb">
        <ul>
            <li><a href="{{route('admin.camera')}}">カメラ設定</a></li>
            <li>カメラ一覧</li>
        </ul>
    </div>
    <div id="r-content">
        <div class="title-wrap">
            <h2 class="title">カメラ一覧</h2>
            <div class="new-btn">
                <a href="{{route('admin.camera.create')}}">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" style="fill: rgba(255,255,255, 1);transform: ;msFilter:;">
                    <path d="M3 16c0 1.103.897 2 2 2h3.586L12 21.414 15.414 18H19c1.103 0 2-.897 2-2V4c0-1.103-.897-2-2-2H5c-1.103 0-2 .897-2 2v12zM5 4h14v12h-4.414L12 18.586 9.414 16H5V4z"></path>
                    <path d="M11 14h2v-3h3V9h-3V6h-2v3H8v2h3z"></path>
                </svg>
                新規登録</a>
            </div>
        </div>
        <form action="{{route('admin.camera')}}" method="get" name="form1" id="form1">
        @csrf
        <div class="title-wrap ver2 stick">
            <div class="sp-ma">
                <div class="sort">
                    <ul class="date-list">
                        <li><h4>現場名</h4></li>
                        <li>
                            <div class="select-c">
                                <select name="location">
                                <option>選択する</option>
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
                    <ul class="date-list">
                        <li><h4>設置フロア</h4></li>
                        <li>
                            <div>
                                <input type="text" name="installation_floor" value="{{ old('installation_floor', (isset($input) && $input->has('installation_floor'))?$input->installation_floor:'')}}"/>
                            </div>
                        </li>
                    </ul>
                    <ul class="date-list">
                        <li><h4>稼働状況 </h4></li>
                        <li>
                            <ul class="radio-list">
                                @foreach (config('const.camera_status') as $key => $status )
                                    <li><input name="is_enabled" type="radio" id="is_enabled_{{ $key }}" value="{{ $key }}" {{ old('is_enabled', (isset($input) && $input->has('is_enabled')) ? $input->is_enabled : config('const.enable_status_code.enable')) == $key ? 'checked' : ''  }}>
                                    <label for="is_enabled_{{ $key }}">{{  $status }}</label></li>
                                @endforeach
                            </ul>
                        </li>
                    </ul>
                    <button type="submit" class="apply">絞り込む</button>
                </div>
            </div>
        </div>
        </form>
        @include('admin.layouts.flash-message')
        {{ $cameras->appends([])->links('vendor.pagination.admin-pagination') }}
        <ul class="camera-list">
        @foreach($cameras as $camera)
            <li>
                <div class="pic"><img src="{{ $camera->img }}"></div>
                <div class="text">
                <button style="background:transparent;border:none;" class="edit2" onclick="location.href='{{route('admin.camera.edit', ['camera' => $camera->id])}}'">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" style="fill: rgba(46, 191, 67, 1);transform: ;msFilter:;">
                    <path d="M16 2H8C4.691 2 2 4.691 2 8v13a1 1 0 0 0 1 1h13c3.309 0 6-2.691 6-6V8c0-3.309-2.691-6-6-6zM8.999 17H7v-1.999l5.53-5.522 1.999 1.999L8.999 17zm6.473-6.465-1.999-1.999 1.524-1.523 1.999 1.999-1.524 1.523z"></path>
                    </svg>
                </button>
                <table class="table">
                    <tr>
                        <th>カメラNo.</th>
                        <td>{{$camera->camera_id}}</td>
                    </tr>
                    <tr>
                        <th>現場名</th>
                        <td>{{isset($locations[$camera->location_id])?$locations[$camera->location_id]:''}}</td>
                    </tr>
                    <tr>
                        <th>設置フロア</th>
                        <td>{{$camera->installation_floor}}</td>
                    </tr>
                    <tr>
                        <th>設置場所</th>
                        <td>{{$camera->installation_position}}</td>
                    </tr>
                </table>
                </div>
            </li>
        @endforeach
        </ul>
        {{-- <div class="scroll active">
            <table class="table2 text-centre">
            <thead>
                <tr>
                <th>編集</th>
                <th>カメラNo</th>
                <th>現場名</th>
                <th>設置フロア</th>
                <th>設置場所</th>
                <th>備考</th>
                <th>稼働状況</th>
                <th>削除</th>
                </tr>
            </thead>
            <tbody>
                @foreach($cameras as $camera)
                <tr>
                    <td><button type="button" class="edit" onclick="location.href='{{route('admin.camera.edit', ['camera' => $camera->id])}}'">編集</button></td>
                    <td>{{$camera->camera_id}}</td>
                    <td>{{isset($locations[$camera->location_id])?$locations[$camera->location_id]:''}}</td>
                    <td>{{$camera->installation_floor}}</td>
                    <td>{{$camera->installation_position}}</td>
                    <td>{{$camera->remarks}}</td>
                    <td>{{config('const.camera_status')[$camera->is_enabled]}}</td>
                    <td><button type="button" class="delete_cameras history" delete_index="{{ $camera->id }}">削除</button>
                        <form id="frm_delete_{{ $camera->id }}" action="{{ route('admin.camera.delete', ['camera'=> $camera->id]) }}" method="POST" style="display: none;">
                        @csrf
                        @method('delete')
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
            </table>
        </div> --}}
        {{ $cameras->appends([])->links('vendor.pagination.admin-pagination') }}
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
      $(".delete_cameras").click(function(e){
          e.preventDefault();
          delete_id = $(this).attr('delete_index');
          helper_confirm("dialog-confirm", "削除", "カメラを削除します。<br />よろしいですか？", 300, "確認", "閉じる", function(){
              var frm_id = "#frm_delete_" + delete_id;
              $(frm_id).submit();
          });
      });

  });
</script>

@endsection
