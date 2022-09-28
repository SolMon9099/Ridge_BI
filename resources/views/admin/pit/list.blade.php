@extends('admin.layouts.app')

@section('content')

<div id="wrapper">
    <div class="breadcrumb">
        <ul>
            <li><a href="{{route('admin.pit')}}">ピット入退場検知</a></li>
            <li>検知リスト</li>
        </ul>
    </div>
    <div id="r-content">
        <div class="title-wrap">
            <h2 class="title">検知リスト</h2>
        </div>
        <div class='notice-area'>
            ピット内最大時間(ピット内の人数が０人から１人になった時点を始点とし時間を測定)を超えた際に検知を行います。<br/>
            ※時間計測開始時の画像を表示しています。
        </div>
        <form action="{{route('admin.pit.list')}}" method="get" name="form1" id="form1">
        @csrf
            <div class="title-wrap ver2 stick">
                <div class="sp-ma">
                    <div class="sort">
                        <ul class="date-list">
                            <li><h4>検出期間</h4></li>
                            <li>
                                <input type="date" id="starttime" name='starttime' value="{{ old('starttime', (isset($request) && $request->has('starttime'))?$request->starttime:date('Y-m-d', strtotime('-1 week')))}}">
                            </li>
                            <li>～</li>
                            <li>
                                <input type="date" id="endtime" name='endtime' value="{{ old('endtime', (isset($request) && $request->has('endtime'))?$request->endtime:date('Y-m-d'))}}">
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

        @if(count($pit_detections) > 0)
            <button type="button" class="add-to-toppage <?php echo $from_top?'from_top':'' ?>" onclick="addDashboard({{config('const.top_block_type_codes')['detect_list_pit']}})">ダッシュボートへ追加</button>
        @endif

        {{ $pit_detections->appends([
            'starttime'=> (isset($request) && $request->has('starttime'))?$request->starttime:date('Y-m-d', strtotime('-1 week')),
            'endtime'=> (isset($request) && $request->has('endtime'))?$request->endtime:date('Y-m-d'),
            'rule_ids' => isset($request) && $request->has('rule_ids')?$request->rule_ids:''
        ])->links('vendor.pagination.admin-pagination') }}
        <ul class="kenchi-list">
            @foreach ($pit_detections as $item)
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
                            <h2 class="icon-content">検知内容</h2>
                            <p>{{$item->nb_entry > $item->nb_exit ? 'ピット入場 '.($item->nb_entry - $item->nb_exit) :  'ピット退場 '.($item->nb_exit - $item->nb_entry)}}</p>
                        </li>
                    </ul>
                </div>
            </li>
            @endforeach
        </ul>
        {{ $pit_detections->appends([
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
    .notice-area{
        color:#999;
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
    function addDashboard(block_type){
        var options = {
            starttime:formatDateLine($('#starttime').val()),
            endtime:formatDateLine($('#endtime').val())
        };
        addToToppage(block_type, options);
    }
</script>
@endsection
