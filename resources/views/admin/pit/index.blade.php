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
                <li>ルール一覧・編集</li>
            </ul>
        </div>
        <div id="r-content">
            <div class="title-wrap">
                <h2 class="title">ルール一覧・編集</h2>
                @if(!$super_admin_flag)
                <div class="new-btn">
                    <a href="{{route('admin.pit.cameras_for_rule')}}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" style="fill: rgba(255,255,255, 1);transform: ;msFilter:;">
                            <path d="M3 16c0 1.103.897 2 2 2h3.586L12 21.414 15.414 18H19c1.103 0 2-.897 2-2V4c0-1.103-.897-2-2-2H5c-1.103 0-2 .897-2 2v12zM5 4h14v12h-4.414L12 18.586 9.414 16H5V4z"></path>
                            <path d="M11 14h2v-3h3V9h-3V6h-2v3H8v2h3z"></path>
                        </svg>
                    追加登録</a>
                </div>
                @endif
            </div>
            <div class="notice-area">こちらの画面では、カメラ毎に通知ルールの新規登録・既存の通知ルールの編集・削除が行えます。</div>
            <form action="{{route('admin.pit')}}" method="get" name="form1" id="form1">
            @csrf
            <div class="title-wrap ver2 stick">
                <div class="sp-ma">
                    <div class="sort">
                        <ul class="date-list">
                            <li>
                                <h4>カメラ</h4>
                            </li>
                            <li><a data-target="camera" class="modal-open setting">選択する</a></li>
                            <input type= 'hidden' name='selected_cameras' id = 'cameras_input' value="{{ old('selected_cameras', (isset($input) && $input->has('selected_cameras'))?$input->selected_cameras:'')}}"/>
                            {{-- @if($selected_rule != null)
                                <li><p class="selected-camera">{{$selected_rule->serial_no. '：'. $selected_rule->location_name.'('.$selected_rule->installation_position.')'}}</p></li>
                            @endif --}}
                        </ul>
                        <ul class="date-list">
                            <li><h4>設置エリア</h4></li>
                            <li>
                                <div class="select-c">
                                    <select name="location">
                                        <option value="">選択する</option>
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
                                <div class="select-c">
                                    <select name="floor_number">
                                        <option value="">選択する</option>
                                    @foreach($floor_numbers as $floor)
                                        @if (isset($input) && $input->has('floor_number') && $input->floor_number == $floor)
                                        <option value="{{$floor}}" selected>{{$floor}}</option>
                                        @else
                                        <option value="{{$floor}}">{{$floor}}</option>
                                        @endif
                                    @endforeach
                                    </select>
                                </div>
                                {{-- <input type="text" name="floor_number" value="{{ old('floor_number', (isset($input) && $input->has('floor_number'))?$input->floor_number:'')}}"/> --}}
                            </li>
                        </ul>
                        <ul class="date-list">
                            <li><h4>設置場所</h4></li>
                            <li>
                                <div class="select-c">
                                    <select name="installation_position">
                                    <option value="">選択する</option>
                                    @foreach($installation_positions as $position)
                                        @if (isset($input) && $input->has('installation_position') && $input->installation_position == $position)
                                        <option value="{{$position}}" selected>{{$position}}</option>
                                        @else
                                        <option value="{{$position}}">{{$position}}</option>
                                        @endif
                                    @endforeach
                                    </select>
                                </div>
                                {{-- <div>
                                    <input type="text" name="installation_position" value="{{ old('installation_position', (isset($input) && $input->has('installation_position'))?$input->installation_position:'')}}"/>
                                </div> --}}
                            </li>
                        </ul>
                        <button type="submit" class="apply">絞り込む</button>
                    </div>
                </div>
            </div>
            </form>
            @include('admin.layouts.flash-message')
            {{ $pits->appends([
                'selected_cameras'=> (isset($input) && $input->has('selected_cameras'))?$input->selected_cameras:'',
                'location'=> (isset($input) && $input->has('location'))?$input->location:'',
                'floor_number'=> (isset($input) && $input->has('floor_number'))?$input->floor_number:'',
                'installation_position'=> (isset($input) && $input->has('installation_position'))?$input->installation_position:'',
            ])->links('vendor.pagination.admin-pagination') }}
            <div class="scroll active">
                <table class="table2 text-centre">
                    <thead>
                    <tr>
                        <th>編集</th>
                        <th>ルール名</th>
                        <th>カメラNo</th>
                        <th>設置エリア</th>
                        <th>設置フロア</th>
                        <th>設置場所</th>
                        <th>ピット内人数</th>
                        <th>ピット内最大時間</th>
                        <th>カメラの稼働状況</th>
                        <th>削除</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($pits as $pit)
                        <tr>
                            <td><button type="button" class="edit" onclick="location.href='{{route('admin.pit.edit', ['pit' => $pit->id])}}'">編集</button></td>
                            <td>{{$pit->name}}</td>
                            <td>{{$pit->serial_no}}</td>
                            <td>{{$pit->location_name}}</td>
                            <td>{{$pit->floor_number}}</td>
                            <td>{{$pit->installation_position}}</td>
                            <td>{{$pit->min_members > 0 ? (string)($pit->min_members) : ''}}</td>
                            <td>{{$pit->max_permission_time > 0 ? (string)($pit->max_permission_time).'分' : ''}}</td>
                            <td>
                                @if(Storage::disk('recent_camera_image')->exists($pit->device_id.'.jpeg'))
                                    稼働中
                                @else
                                    停止中
                                @endif
                            </td>
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
            {{ $pits->appends([
                'selected_cameras'=> (isset($input) && $input->has('selected_cameras'))?$input->selected_cameras:'',
                'location'=> (isset($input) && $input->has('location'))?$input->location:'',
                'floor_number'=> (isset($input) && $input->has('floor_number'))?$input->floor_number:'',
                'installation_position'=> (isset($input) && $input->has('installation_position'))?$input->installation_position:'',
            ])->links('vendor.pagination.admin-pagination') }}
        </div>
    </div>
    <!--MODAL -->
    <div id="camera" class="modal-content">
        <div class="textarea">
            <div class="listing">
                <div class="scroll active sp-pl0">
                    <table class="table2 text-centre">
                        <thead>
                        <tr>
                            <th class="w10"></th>
                            <th>カメラNo</th>
                            <th>設置エリア</th>
                            <th>設置フロア</th>
                            <th>設置場所</th>
                            <th>カメラ画像確認</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                            $selected_cameras = old('selected_cameras', (isset($input) && $input->has('selected_cameras'))?$input->selected_cameras:'');
                            if ($selected_cameras != ''){
                                $selected_cameras = json_decode($selected_cameras);
                            } else {
                                $selected_cameras = [];
                            }
                        ?>
                        @foreach ($cameras as $camera)
                        <tr>
                            <td class="stick-t">
                                <div class="checkbtn-wrap">
                                    @if (in_array((int)$camera->id, $selected_cameras))
                                        <input value = '{{$camera->id}}' class='rule_checkbox' type="checkbox" id="{{'camera'.$camera->id}}" checked>
                                    @else
                                    <input value = '{{$camera->id}}' class='rule_checkbox' type="checkbox" id="{{'camera'.$camera->id}}">
                                    @endif
                                    <label class="custom-style" for="{{'camera'.$camera->id}}"></label>
                                </div>
                            </td>
                            <td>{{$camera->serial_no}}</td>
                            <td>{{$camera->location_name}}</td>
                            <td>{{$camera->floor_number}}</td>
                            <td>{{$camera->installation_position}}</td>
                            <td>
                                @if(Storage::disk('recent_camera_image')->exists($camera->camera_id.'.jpeg'))
                                    <img width="100px" src="{{asset('storage/recent_camera_image/').'/'.$camera->camera_id.'.jpeg'}}"/>
                                @else
                                    カメラ停止中
                                @endif
                            </td>
                        </tr>
                        @endforeach
                        @if(count($cameras) == 0)
                        <tr>
                            <td colspan="6">ピット入退場検知のルールが登録されたカメラがありません。ルールを設定してください</td>
                        </tr>
                        @endif
                        </tbody>
                    </table>
                    <div class="modal-set">
                        @if(count($cameras) > 0)
                            <button onclick="selectCameras()" type="button" class="modal-close">設 定</button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <p class="closemodal"><a class="modal-close">×</a></p>
    </div>
    <!-- -->

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
        function selectCameras(){
            var checked_cameras = [];
            $('.rule_checkbox').each(function(){
                if ($(this).is(":checked")){
                    checked_cameras.push($(this).val());
                }
            })
            if (checked_cameras.length == 0){
                $('#cameras_input').val('');
            } else {
                $('#cameras_input').val(JSON.stringify(checked_cameras));
            }
        }
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
