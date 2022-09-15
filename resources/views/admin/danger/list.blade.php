@extends('admin.layouts.app')

@section('content')

<div id="wrapper">
    <div class="breadcrumb">
        <ul>
            <li><a href="{{route('admin.danger')}}">危険エリア侵入判定</a></li>
            <li>危険エリア検知リスト(アーカイブ)</li>
        </ul>
    </div>
    <div id="r-content">
        <div class="title-wrap">
            <h2 class="title">危険エリア検知リスト(アーカイブ)</h2>
        </div>
        <form action="{{route('admin.danger.list')}}" method="get" name="form1" id="form1">
        @csrf
            <div class="title-wrap ver2 stick">
                <div class="sp-ma">
                    <div class="sort">
                        <ul class="date-list">
                            <li><h4>検出期間</h4></li>
                            <li>
                                <input type="date" name='starttime' value="{{ old('starttime', (isset($request) && $request->has('starttime'))?$request->starttime:date('Y-m-d', strtotime('-1 week')))}}">
                            </li>
                            <li>～</li>
                            <li>
                                <input type="date" name='endtime' value="{{ old('endtime', (isset($request) && $request->has('endtime'))?$request->endtime:date('Y-m-d'))}}">
                            </li>
                        </ul>
                        <ul class="date-list">
                            <li><h4>ルール</h4></li>
                            <li><a data-target="rule" class="modal-open setting">選択する</a></li>
                        </ul>
                        <input type= 'hidden' name='rule_ids' id = 'rule_id_input' value="{{ old('rule_ids', (isset($request) && $request->has('rule_ids'))?$request->rule_ids:'')}}"/>
                        <button type="submit" class="apply">絞り込む</button>
                    </div>
                </div>
            </div>
        </form>

        @if(count($danger_detections) > 0)
            <button type="button" class="add-to-toppage" onclick="addToToppage({{config('const.top_block_type_codes')['detect_list_danger']}})">ダッシュボートへ追加</button>
        @endif

        {{ $danger_detections->appends([
            'starttime'=> (isset($request) && $request->has('starttime'))?$request->starttime:date('Y-m-d', strtotime('-1 week')),
            'endtime'=> (isset($request) && $request->has('endtime'))?$request->endtime:date('Y-m-d'),
            'rule_ids' => isset($request) && $request->has('rule_ids')?$request->rule_ids:''
        ])->links('vendor.pagination.admin-pagination') }}
        <ul class="kenchi-list">
            @foreach ($danger_detections as $item)
            <?php
                $video_path = '';
                $video_path .= asset('storage/video/').'/';
                $video_path .= $item->video_file_path;

                if (isset($item->thumb_img_path) && $item->thumb_img_path != ''){
                    $thumb_path = asset('storage/thumb/').'/'.$item->thumb_img_path;
                } else {
                    $thumb_path = asset('assets/admin/img/samplepic.png');
                }
            ?>
            <li>
                <div class="movie" video-path = '{{$video_path}}'>
                    <a data-target="movie0000" onclick="videoPlay('{{$video_path}}')" class="modal-open setting2 play">
                        <img src="{{$thumb_path}}"/>
                    </a>
                    <div class="cap">
                        <time>{{date('Y/m/d H:i', strtotime($item->starttime))}}</time>
                    </div>
                </div>
                <div class="text">
                    <p class="camera-id">カメラID:{{$item->camera_no}}</p>
                    <ul class="pit-list">
                        <li>
                            <h2 class="icon-map">設置場所</h2>
                            <dl>
                                <dt>設置エリア</dt>
                                <dd>{{$item->location_name}}</dd>
                            </dl>
                            <dl>
                                <dt>設置フロア</dt>
                                <dd>{{$item->floor_number}}</dd>
                            </dl>
                            <dl>
                                <dt>設置場所</dt>
                                <dd>{{$item->installation_position}}</dd>
                            </dl>
                        </li>
                        <li>
                            <h2 class="icon-condition">検知条件</h2>
                            <dl>
                                <dt><p>{{isset($item->detection_action_id) && $item->detection_action_id > 0 ? config('const.action_cond_statement')[$item->detection_action_id] : ''}}</p></dt>
                                <dd>1人</dd>
                            </dl>
                        </li>
                        <li>
                            <h2 class="icon-content">検知内容</h2>
                            <p>{{isset($item->detection_action_id) && $item->detection_action_id > 0 ? config('const.action_statement')[$item->detection_action_id] : ''}}</p>
                        </li>
                        <li>
                            <h2 class="icon-rule">ルール</h2>
                            <dl>
                                <dt>{{$item->detection_action_id}}</dt>
                                <dd>{{isset($item->detection_action_id) && $item->detection_action_id > 0 ? config('const.action')[$item->detection_action_id] : ''}}</dd>
                            </dl>
                        </li>
                    </ul>
                </div>
            </li>
            @endforeach
        </ul>
        {{ $danger_detections->appends([
            'starttime'=> (isset($request) && $request->has('starttime'))?$request->starttime:date('Y-m-d', strtotime('-1 week')),
            'endtime'=> (isset($request) && $request->has('endtime'))?$request->endtime:date('Y-m-d'),
            'rule_ids' => isset($request) && $request->has('rule_ids')?$request->rule_ids:''
        ])->links('vendor.pagination.admin-pagination') }}
    </div>
</div>
<!--MODAL -->
<div id="rule" class="modal-content">
    <div class="textarea">
        <div class="listing">
            <h3>検索対象となる、ルールを選択してください</h3>
            <div class="scroll active sp-pl0">
            <table class="table2 text-centre">
                <thead>
                <tr>
                    <th class="w10"></th>
                    <th>カメラNo</th>
                    <th>設置エリア</th>
                    <th>設置フロア</th>
                    <th>設置場所</th>
                    <th>アクション</th>
                    <th>カラー</th>
                    <th>カメラ画像確認</th>
                </tr>
                </thead>
                <tbody>
                    <?php
                        $selected_rule_ids = old('rule_ids', (isset($request) && $request->has('rule_ids'))?$request->rule_ids:'');
                        if ($selected_rule_ids != ''){
                            $selected_rule_ids = json_decode($selected_rule_ids);
                        } else {
                            $selected_rule_ids = [];
                        }
                    ?>
                    @foreach ($rules as $rule)
                    <tr>
                        <td class="stick-t">
                            <div class="checkbtn-wrap">
                                @if (in_array($rule->id, $selected_rule_ids))
                                    <input value = '{{$rule->id}}' class='rule_checkbox' type="checkbox" id="{{'rule-'.$rule->id}}" checked>
                                @else
                                    <input value = '{{$rule->id}}' class='rule_checkbox' type="checkbox" id="{{'rule-'.$rule->id}}">
                                @endif
                                <label for="{{'rule-'.$rule->id}}" class="custom-style"></label>
                            </div>
                        </td>
                        <td> {{$rule->camera_no}}</td>
                        <td>{{$rule->location_name}}</td>
                        <td>{{$rule->floor_number}}</td>
                        <td>{{$rule->installation_position}}</td>
                        <td>
                            @foreach (json_decode($rule->action_id) as $action_code)
                                <div>{{config('const.action')[$action_code]}}</div>
                            @endforeach
                        </td>
                        <td><input disabled type="color" value = "{{$rule->color}}"></td>
                        <td><img width="100px" src="{{$rule->img}}"/></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="modal-set">
                <button onclick="selectRule()" class="modal-close">設 定</button>
            </div>
            </div>
        </div>
    </div>
    <p class="closemodal"><a class="modal-close">×</a></p>
</div>
<!-- -->
<!--MODAL -->
<div id="movie0000" class="modal-content">
<div class="textarea">
    <div class="v">
        <video id = 'video-container' src = '' type= 'video/mp4' controls>
        </video>
    </div>
</div>
<p class="closemodal"><a class="modal-close">×</a></p>
</div>
<!-- -->
<div id="alert-modal" title="test" style="display:none">
    <p><span id="confirm_text">These items will be permanently deleted and cannot be recovered. Are you sure?</span></p>
</div>
<link href="{{ asset('assets/vendor/jquery-ui/jquery-ui.min.css') }}" rel="stylesheet">
<style>
    .textarea{
        max-width: 1200px;
        width:100%;
    }
</style>
<script>
    function selectRule(){
        var checked_rules = [];
        $('.rule_checkbox').each(function(){
            if ($(this).is(":checked")){
                checked_rules.push($(this).val());
            }
        })
        $('#rule_id_input').val(JSON.stringify(checked_rules));
    }

    function videoPlay(path){
        var video = document.getElementById('video-container');
        video.pause();
        $('#video-container').attr('src', path);
        video.play();
    }
</script>
@endsection
