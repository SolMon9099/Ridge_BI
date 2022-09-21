@extends('admin.layouts.app')

@section('content')
<?php
    $starttime = (isset($request) && $request->has('starttime'))?$request->starttime:date('Y-m-d', strtotime('-1 week'));
    $endtime = (isset($request) && $request->has('endtime'))?$request->endtime:date('Y-m-d');
    $search_period = (strtotime($endtime) - strtotime($starttime))/86400;
?>
<form action="{{route('admin.danger.past_analysis')}}" method="get" name="form1" id="form1">
@csrf
    <div id="wrapper">
        <div class="breadcrumb">
        <ul>
            <li><a href="{{route('admin.danger')}}">危険エリア侵入検知</a></li>
            <li>過去グラフ</li>
        </ul>
        </div>
        <div id="r-content">
            <div class="title-wrap">
                <h2 class="title">過去グラフ</h2>
            </div>
            <div class="title-wrap ver2 stick">
            <div class="sp-ma">
                <div class="sort">
                <ul class="date-list">
                    <li>
                    <h4>検出期間</h4>
                    </li>
                    <li>
                        <input id='starttime' type="date" name='starttime' onchange="search()" value="{{ old('starttime', $starttime)}}">
                    </li>
                    <li>～</li>
                    <li>
                        <input id='endtime' type="date" name='endtime' onchange="search()" value="{{ old('endtime', $endtime)}}">
                    </li>
                </ul>
                </div>
            </div>
            </div>
            <div class="list">
                <div class="inner active">
                    <ul class="tab_sub">
                        <?php
                            $selected_search_option = old('selected_search_option', (isset($request) && $request->has('selected_search_option'))?$request->selected_search_option:1);
                        ?>
                        <input type='hidden' name='selected_search_option' id = 'selected_search_option' value=""/>
                        <li class="{{$selected_search_option == 1 ? 'active':'' }}">
                            <a data-target="rule" class="modal-open blue" onclick="setSelectedSearchOption(1)">ルールから選択</a>
                        </li>
                        <li class="{{$selected_search_option == 2 ? 'active':'' }}">
                            <a data-target="camera" class="modal-open blue" onclick="setSelectedSearchOption(2)">カメラから選択</a>
                        </li>
                        <li class="{{$selected_search_option == 3 ? 'active':'' }}">
                            <a data-target="action" class="modal-open blue" onclick="setSelectedSearchOption(3)">アクションから選択</a>
                        </li>
                    </ul>
                    <button type="button" class="add-to-toppage <?php echo $from_top?'from_top':'' ?>" onclick="addToToppage({{config('const.top_block_type_codes')['past_graph_danger']}})">ダッシュボートへ追加</button>
                    <div class="active sp-ma-right">
                        <div class="period-select-buttons">
                        @if ($search_period < 1)
                            <button type="button" class="period-button selected" onclick="displayGraphData(this, 3)">3時間</button>
                            <button type="button" class="period-button" onclick="displayGraphData(this,6)">6時間</button>
                            <button type="button" class="period-button" onclick="displayGraphData(this,12)">12時間</button>
                            <button type="button" class="period-button" onclick="displayGraphData(this,24)">24時間</button>
                        @elseif ($search_period < 7)
                            <button type="button" class="period-button selected" onclick="displayGraphData(this, 'time')">時間別</button>
                            <button type="button" class="period-button" onclick="displayGraphData(this, 'day')">日別</button>
                        @elseif ($search_period <= 30)
                            <button type="button" class="period-button selected" onclick="displayGraphData(this, 'time')">時間別</button>
                            <button type="button" class="period-button" onclick="displayGraphData(this, 'day')">日別</button>
                        @elseif ($search_period <= 180)
                            <button type="button" class="period-button selected" onclick="displayGraphData(this, 'day')">日別</button>
                            <button type="button" class="period-button" onclick="displayGraphData(this, 'week')">週別</button>
                            <button type="button" class="period-button" onclick="displayGraphData(this, 'month')">月別</button>
                        @else
                            <button type="button" class="period-button selected" onclick="displayGraphData(this, 'day')">日別</button>
                            <button type="button" class="period-button" onclick="displayGraphData(this, 'week')">週別</button>
                            <button type="button" class="period-button" onclick="displayGraphData(this, 'month')">月別</button>
                        @endif
                        </div>
                        <canvas id="myLineChart1"></canvas>
                        <a class="prev" onclick="moveXRange(-1)">❮</a>
                        <a class="next" onclick="moveXRange(1)">❯</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!--MODAL -->
    <div id="rule" class="modal-content">
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
                        <th>アクション</th>
                        <th>カラー</th>
                        <th>カメラ画像確認</th>
                    </tr>
                    </thead>
                    <tbody>
                        <?php
                            $selected_rule_ids = old('selected_rules', (isset($request) && $request->has('selected_rules'))?$request->selected_rules:[]);
                        ?>
                        @foreach ($rules as $rule)
                        <tr>
                            <td class="stick-t">
                                <div class="checkbtn-wrap">
                                    @if (in_array($rule->id, $selected_rule_ids))
                                        <input name='selected_rules[]' value = '{{$rule->id}}' class='rule_checkbox' type="checkbox" id="{{'rule-'.$rule->id}}" checked>
                                    @else
                                        <input name='selected_rules[]' value = '{{$rule->id}}' class='rule_checkbox' type="checkbox" id="{{'rule-'.$rule->id}}">
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
                            $selected_camera_ids = old('selected_cameras', (isset($request) && $request->has('selected_cameras'))?$request->selected_cameras:[]);
                        ?>
                        @foreach ($cameras as $camera)
                        <tr>
                            <td class="stick-t">
                                <div class="checkbtn-wrap">
                                    @if (in_array($camera->id, $selected_camera_ids))
                                        <input name="selected_cameras[]" value = '{{$camera->id}}' type="checkbox" id="{{'camera'.$camera->id}}}}" checked>
                                    @else
                                        <input name="selected_cameras[]" value = '{{$camera->id}}' type="checkbox" id="{{'camera'.$camera->id}}}}">
                                    @endif
                                    <label class="custom-style" for="{{'camera'.$camera->id}}}}"></label>
                                </div>
                            </td>
                            <td>{{$camera->camera_id}}</td>
                            <td>{{$camera->location_name}}</td>
                            <td>{{$camera->floor_number}}</td>
                            <td>{{$camera->installation_position}}</td>
                            <td><img width="100px" src="{{$camera->img}}"/></td>
                        </tr>
                        @endforeach
                        </tbody>
                    </table>
                    <div class="modal-set">
                        <button type="submit" class="modal-close">設 定</button>
                    </div>
                </div>
            </div>
        </div>
        <p class="closemodal"><a class="modal-close">×</a></p>
    </div>
    <!-- -->

    <!--MODAL -->
    <div id="action" class="modal-content">
        <div class="textarea narrow">
            <div class="listing">
                <div class="scroll active sp-pl0">
                    <table class="table2 text-centre">
                        <thead>
                        <tr>
                            <th></th>
                            <th>アクション</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                            $selected_action_ids = old('selected_actions', (isset($request) && $request->has('selected_actions'))?$request->selected_actions:[]);
                        ?>
                        @foreach (config('const.action') as $id => $action_name)
                        <tr>
                            <td class="stick-t">
                                <div class="checkbtn-wrap">
                                    @if (in_array($id, $selected_action_ids))
                                        <input value='{{$id}}' name="selected_actions[]" type="checkbox" id="{{'action'.$id}}}}" checked>
                                    @else
                                        <input value='{{$id}}' name="selected_actions[]" type="checkbox" id="{{'action'.$id}}}}">
                                    @endif
                                    <label class="custom-style" for="{{'action'.$id}}}}"></label>
                                </div>
                            </td>
                            <td>{{$action_name}}</td>
                        </tr>
                        @endforeach
                        </tbody>
                    </table>
                    <div class="modal-set">
                        <button type="submit" class="modal-close">設 定</button>
                    </div>
                </div>
            </div>
        </div>
        <p class="closemodal"><a class="modal-close">×</a></p>
    </div>
    <!-- -->
</form>
<div id="alert-modal" title="test" style="display:none">
    <p><span id="confirm_text">These items will be permanently deleted and cannot be recovered. Are you sure?</span></p>
</div>
<link href="{{ asset('assets/vendor/jquery-ui/jquery-ui.min.css') }}" rel="stylesheet">
<style>
    .textarea{
        max-width: 1200px;
        width:100%;
    }
    .inner{
        position: relative;
    }
    .add-to-toppage{
        position: absolute;
        right:0px;
        top:0px;
        padding-left: 5px;
        padding-right:5px;
        padding-top:2px;
        padding-bottom:2px;
    }
    .period-select-buttons{
        position: absolute;
        right: 10px;
        top: 45px;
    }
    .period-select-buttons > button{
        padding:3px;
    }
    .period-select-buttons > .selected{
        background: lightgreen;
    }
    .prev, .next {
        cursor: pointer;
        position: absolute;
        top: 50%;
        width: auto;
        padding-left: 15px;
        padding-right: 15px;
        padding-top:8px;
        padding-bottom: 8px;
        font-weight: bold;
        font-size: 18px;
        transition: 0.6s ease;
        user-select: none;
    }
    .prev{
        left:-20px;
    }
    .next {
        right: -20px;
    }

    .prev:hover, .next:hover {
        background-color:lightcoral;
        color:white;
        border-radius: 20px;
    }
    .from_top{
        background: lightblue;
    }
</style>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.bundle.js"></script>
<script>
    function search(){
        $('#form1').submit();
    }

    function setSelectedSearchOption(value){
        $('#selected_search_option').val(value);
    }
    var ctx = document.getElementById("myLineChart1");
    var color_set = {
        1:'red',
        2:'#42b688',
        3:'#42539a',
        4:'black',
    }
    var search_period = "<?php echo $search_period;?>";
    var starttime = $('#starttime').val();
    starttime = new Date(starttime);
    starttime.setHours(0);
    starttime.setMinutes(0);
    starttime.setSeconds(0);
    var endtime = $('#endtime').val();
    endtime = new Date(endtime);
    endtime.setHours(24);
    endtime.setMinutes(0);
    endtime.setSeconds(0);

    var min_time = new Date(starttime);
    min_time.setHours(0);
    min_time.setMinutes(0);
    min_time.setSeconds(0);
    var grpah_init_type = 3;
    var period_unit = 'hour';
    var displayFormat = {'hour': 'H:mm'};
    var tooltip = "H:mm";
    if (search_period < 1){
        period_unit = 'hour';
        displayFormat = {'hour': 'H:mm'};
        tooltip = "H:mm";
        grpah_init_type = 3;
    } else if (search_period < 7){
        grpah_init_type = 'time';
        displayFormat = {'minute': 'DD日H時'};
        tooltip = "MM/DD H:mm";
        period_unit = 'minute';
    } else if (search_period <= 30){
        displayFormat = {'minute': 'DD日H時'};
        tooltip = "MM/DD H:mm";
        grpah_init_type = 'time';
        period_unit = 'minute';
    } else if (search_period <= 180){
        grpah_init_type = 'day';
        displayFormat = {'day': 'M/DD'};
        tooltip = "YY/MM/DD";
        period_unit = 'day';
    } else {
        grpah_init_type = 'day';
        displayFormat = {'day': 'M/DD'};
        tooltip = "YY/MM/DD";
        period_unit = 'day';
    }

    var all_data = <?php echo $all_data;?>;
    var actions = <?php echo json_encode(config('const.action'));?>;

    function moveXRange(increament = 1){
        switch(grpah_init_type){
            case 3:
                if (increament == 1){
                    min_time.setHours(min_time.getHours() + 3 >= 24 ? 0 : min_time.getHours() + 3);
                } else {
                    min_time.setHours(min_time.getHours() - 3 < 0 ? 21 : min_time.getHours() - 3);
                }
                break;
            case 6:
                if (increament == 1){
                    min_time.setHours(min_time.getHours() + 6 >= 24 ? 0 : min_time.getHours() + 6);
                } else {
                    min_time.setHours(min_time.getHours() - 6 < 0 ? 18 : min_time.getHours() - 6);
                }
                break;
            case 12:
                if (increament == 1){
                    min_time.setHours(min_time.getHours() + 12 >= 24 ? 0 : min_time.getHours() + 12);
                } else {
                    min_time.setHours(min_time.getHours() - 12 < 0 ? 12 : min_time.getHours() - 12);
                }
                break;
            case 24:
                return;
            case 'time':
                if (increament == 1){
                    min_time.setDate(min_time.getDate() + 1);
                    if (min_time.getTime() >= endtime.getTime()) {
                        min_time = new Date(starttime);
                    }
                } else {
                    min_time.setDate(min_time.getDate() - 1);
                    if (min_time.getTime() < starttime.getTime()) {
                        min_time = new Date(endtime);
                        min_time.setDate(min_time.getDate() -1);
                    }
                }
                break;
            case 'day':
                if (search_period < 7) return;
                if (increament == 1){
                    min_time.setDate(min_time.getDate() + 7);
                    if (min_time.getTime() >= endtime.getTime()) {
                        min_time = new Date(starttime);
                    }
                } else {
                    min_time.setDate(min_time.getDate() - 7);
                    if (min_time.getTime() < starttime.getTime()) {
                        min_time = new Date(endtime);
                        min_time.setDate(min_time.getDate() - 7);
                    }
                }
                break;
            case 'week':
                if (increament == 1){
                    min_time.setDate(min_time.getDate() + 28);
                    if (min_time.getTime() >= endtime.getTime()) {
                        min_time = new Date(starttime);
                    }
                } else {
                    min_time.setDate(min_time.getDate() - 28);
                    if (min_time.getTime() < starttime.getTime()) {
                        min_time = new Date(endtime);
                        min_time.setDate(min_time.getDate() - 28);
                    }
                }
                break;
            case 'month':
                if (search_period <= 180) return;
                if (increament == 1){
                    min_time.setMonth(min_time.getMonth() + 6);
                    if (min_time.getTime() >= endtime.getTime()) {
                        min_time = new Date(starttime);
                    }
                } else {
                    min_time.setMonth(min_time.getMonth() - 6);
                    if (min_time.getTime() < starttime.getTime()) {
                        min_time = new Date(endtime);
                        min_time.setMonth(min_time.getMonth() - 6);
                    }
                }
                break;
        }
        displayGraphData(null, grpah_init_type, false);
    }

    function resortData(data, time_period){
        var temp = {};
        switch(time_period){
            case 'day':
                Object.keys(data).map(date_time => {
                    var date = formatDateLine(date_time);
                    if (temp[date] == undefined) temp[date] = {};
                    Object.keys(actions).map(id => {
                        if (temp[date][id] == undefined) temp[date][id] = 0;
                    })
                    Object.keys(data[date_time]).map(action_id => {
                        temp[date][action_id] += data[date_time][action_id].length;
                    })
                })
                break;
            case 'week':
                Object.keys(data).map(date_time => {
                    var date = formatYearWeekNum(date_time);
                    if (temp[date] == undefined) temp[date] = {};
                    Object.keys(actions).map(id => {
                        if (temp[date][id] == undefined) temp[date][id] = 0;
                    })
                    Object.keys(data[date_time]).map(action_id => {
                        temp[date][action_id] += data[date_time][action_id].length;
                    })
                })
                break;
            case 'month':
                Object.keys(data).map(date_time => {
                    var date = formatYearMonth(date_time);
                    if (temp[date] == undefined) temp[date] = {};
                    Object.keys(actions).map(id => {
                        if (temp[date][id] == undefined) temp[date][id] = 0;
                    })
                    Object.keys(data[date_time]).map(action_id => {
                        temp[date][action_id] += data[date_time][action_id].length;
                    })
                })
                break;
            default:
                Object.keys(data).map(date_time => {
                    if (temp[date_time] == undefined) temp[date_time] = {};
                    Object.keys(actions).map(id => {
                        if (temp[date_time][id] == undefined) temp[date_time][id] = 0;
                    })
                    Object.keys(data[date_time]).map(action_id => {
                        temp[date_time][action_id] += data[date_time][action_id].length;
                    })
                })
        }
        return temp;
    }
    function displayGraphData(e = null, time_period = 3, start_init_flag = true){
        grpah_init_type = time_period;
        if (start_init_flag){
            min_time = new Date(starttime);
            min_time.setHours(0);
            min_time.setMinutes(0);
            min_time.setSeconds(0);
        }
        var grid_unit = 15;
        var date_labels = [];
        var totals_by_action = {};
        Object.keys(actions).map(id => {
            totals_by_action[id] = [];
        });
        if (e != null){
            $('.period-button').each(function(){
                $(this).removeClass('selected');
            });

            $(e).addClass('selected');
        }
        var max_time = new Date(min_time);
        switch(time_period){
            case 3:
                grid_unit = 15;
                period_unit = 'minute';
                displayFormat = {'minute': 'H:mm'};
                tooltip = "H:mm";
                max_time.setHours(max_time.getHours() + time_period);
                break;
            case 6:
                grid_unit = 30;
                period_unit = 'minute';
                displayFormat = {'minute': 'H:mm'};
                tooltip = "H:mm";
                max_time.setHours(max_time.getHours() + time_period);
                break;
            case 12:
                grid_unit = 60;
                period_unit = 'minute';
                displayFormat = {'minute': 'H:mm'};
                tooltip = "H:mm";
                max_time.setHours(max_time.getHours() + time_period);
                break;
            case 24:
                grid_unit = 60;
                period_unit = 'minute';
                displayFormat = {'minute': 'H:mm'};
                tooltip = "H:mm";
                max_time.setHours(max_time.getHours() + time_period);
                break;
            case 'time':
                grid_unit = 60;
                period_unit = 'minute';
                displayFormat = {'minute': 'DD日H時'};
                tooltip = "MM/DD H:mm";
                max_time.setDate(max_time.getDate() + 1);
                break;
            case 'day':
                grid_unit = 1;
                period_unit = 'day';
                displayFormat = {'day': 'M/DD'};
                tooltip = "YY/MM/DD";
                max_time.setDate(max_time.getDate() + 7);
                break;
            case 'week':
                grid_unit = 1;
                period_unit = 'week';
                displayFormat = {'week': 'M/DD'};
                tooltip = "YY/MM/DD";
                max_time.setDate(max_time.getDate() + 28);
                break;
            case 'month':
                grid_unit = 1;
                period_unit = 'month';
                displayFormat = {'month': 'YYYY/MM'};
                tooltip = "YY/MM";
                max_time.setMonth(max_time.getMonth() + 6);
                break;
        }
        if (max_time.getTime() > endtime.getTime()) max_time = new Date(endtime);
        var graph_data = resortData(all_data, time_period);

        var max_y = 0;
        var cur_time = new Date(min_time);
        if (time_period == 'week'){
            var first = cur_time.getDate() - cur_time.getDay();
            cur_time = new Date(cur_time.setDate(first));
        } else if (time_period == 'month'){
            cur_time.setDate(1);
        } else if (time_period == 'day'){
            cur_time.setHours(0);
            cur_time.setMinutes(0);
            cur_time.setSeconds(0);
        }
        while(cur_time.getTime() <= max_time.getTime()){
            date_labels.push(new Date(cur_time));

            if (time_period == 'day' || time_period == 'week' || time_period == 'month'){
                var date_key = formatDateLine(cur_time);
                if (time_period == 'week') date_key = formatYearWeekNum(cur_time);
                if (time_period == 'month') date_key = formatYearMonth(cur_time);
                if (graph_data[date_key] == undefined){
                    Object.keys(actions).map(id => {
                        totals_by_action[id].push(0);
                    })
                } else {
                    Object.keys(actions).map(id => {
                        totals_by_action[id].push(graph_data[date_key][id]);
                        if (max_y < graph_data[date_key][id]) max_y = graph_data[date_key][id];
                    })
                }

            } else {
                var y_add_flag = false;
                Object.keys(graph_data).map((detect_time, index) => {
                    var detect_time_object = new Date(detect_time);
                    if (detect_time_object.getTime() >= cur_time.getTime() && detect_time_object.getTime() < cur_time.getTime() + grid_unit * 60 * 1000){
                        if (index == 0){
                            y_add_flag = true;
                            if  (detect_time_object.getTime() != cur_time.getTime()){
                                date_labels.push(detect_time_object);
                                Object.keys(actions).map(id => {
                                    totals_by_action[id].push(0);
                                })
                            }
                        } else {
                            date_labels.push(detect_time_object);
                        }
                        Object.keys(actions).map(id => {
                            if (graph_data[detect_time][id] != undefined){
                                totals_by_action[id].push(graph_data[detect_time][id]);
                                if (graph_data[detect_time][id] > max_y) max_y = graph_data[detect_time][id];
                            } else {
                                totals_by_action[id].push(0);
                            }
                        })
                    }
                })
                if (y_add_flag == false){
                    Object.keys(actions).map(id => {
                        totals_by_action[id].push(0);
                    })
                }
            }
            switch(time_period){
                case 'time':
                    cur_time.setHours(cur_time.getHours() + 1);
                    break;
                case 'day':
                    cur_time.setDate(cur_time.getDate() + 1);
                    break;
                case 'week':
                    cur_time.setDate(cur_time.getDate() + 7);
                    break;
                case 'month':
                    cur_time.setMonth(cur_time.getMonth() + 1);
                    break;
                default:
                    cur_time.setMinutes(cur_time.getMinutes() + grid_unit);
                    break;
            }
        }

        var datasets = [];
        Object.keys(totals_by_action).map(action_id => {
            datasets.push({
                label:actions[action_id],
                data:totals_by_action[action_id],
                borderColor:color_set[action_id],
                backgroundColor:'white',
                lineTension:0,
            })
        });

        var myLineChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: date_labels,
                datasets
            },
            options: {
                title: {
                    display: true,
                    text: 'NGアクション毎の回数'
                },
                scales: {
                    yAxes: [{
                        ticks: {
                            suggestedMax: max_y + 1,
                            suggestedMin: 0,
                            stepSize: parseInt((max_y + 2)/10) + 1,
                            callback: function(value, index, values){
                                return  value +  '回'
                            }
                        }
                    }],
                    xAxes:[{
                        type: 'time',
                        time: {
                            unit: period_unit,
                            tooltipFormat:tooltip,
                            displayFormats:displayFormat,
                            // displayFormats: {
                            //     minute: 'H:mm'
                            // },
                            distribution: 'series',
                            stepSize: grid_unit,
                            // format:'HH:mm'
                        },
                        ticks: {
                            fontSize: 18,
                            max: max_time,
                            min: min_time,
                        }
                    }]
                },
            }
        });
    }

    $(document).ready(function() {
        displayGraphData(null, grpah_init_type);
    });
</script>
@endsection
