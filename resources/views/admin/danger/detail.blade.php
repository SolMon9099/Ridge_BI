@extends('admin.layouts.app')

@section('content')

<form action="{{route('admin.danger.detail')}}" method="get" name="form1" id="form1">
@csrf
    <div id="wrapper">
        <div class="breadcrumb">
        <ul>
            <li><a href="{{route('admin.danger')}}">危険エリア侵入検知</a></li>
            <li>過去データ</li>
        </ul>
        </div>
        <div id="r-content">
            <div class="title-wrap">
                <h2 class="title">過去データ</h2>
            </div>
            <div class="title-wrap ver2 stick">
            <div class="sp-ma">
                <div class="sort">
                <ul class="date-list">
                    <li>
                    <h4>検出期間</h4>
                    </li>
                    <li>
                        <input id='starttime' type="date" name='starttime' onchange="search()"
                            value="{{ old('starttime', (isset($request) && $request->has('starttime'))?$request->starttime:date('Y-m-d', strtotime('-1 week')))}}">
                    </li>
                    <li>～</li>
                    <li>
                        <input id='endtime' type="date" name='endtime' onchange="search()"
                            value="{{ old('endtime', (isset($request) && $request->has('endtime'))?$request->endtime:date('Y-m-d'))}}">
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
                    <div class="active sp-ma-right">  <canvas id="myLineChart1"></canvas> </div>
                    {{-- <div class="scroll sp-ma-right">  <canvas id="myLineChart2"></canvas></div>
                    <div class="scroll sp-ma-right"> <canvas id="myLineChart3"></canvas> </div> --}}
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
                        <th>現場名</th>
                        <th>設置フロア</th>
                        <th>設置場所</th>
                        <th>アクション</th>
                        <th>カラー</th>
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
                            <th>現場名</th>
                            <th>設置フロア</th>
                            <th>設置場所</th>
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.bundle.js"></script>
<script>

    function search(){
        $('#form1').submit();
    }

    function setSelectedSearchOption(value){
        $('#selected_search_option').val(value);
    }

    var starttime = $('#starttime').val();
    starttime = new Date(starttime);
    var endtime = $('#endtime').val();
    endtime = new Date(endtime);
    var all_data = <?php echo $all_data;?>;
    var actions = <?php echo json_encode(config('const.action'));?>;
    var date_labels = [];
    var totals_by_action = {};
    var color_set = {
        1:'red',
        2:'#42b688',
        3:'#42539a',
        4:'black',
    }
    Object.keys(actions).map(id => {
        totals_by_action[id] = [];
    })
    var max_y = 0;
    for (var d = starttime; d <= endtime; d.setDate(d.getDate() + 1)) {
        var date_key = d.getFullYear();
        var month = d.getMonth() + 1 > 9 ? (d.getMonth() + 1).toString(): '0' + (d.getMonth() + 1).toString();
        var date = d.getDate() > 9 ? (d.getDate()).toString(): '0' + (d.getDate()).toString();
        date_key += '-' + month + '-' + date;
        var month_date_label = (d.getMonth() + 1).toString() + '/' + d.getDate();
        date_labels.push(month_date_label);
        if (all_data[date_key] == undefined){
            Object.keys(actions).map(id => {
                totals_by_action[id].push(0);
            })
        } else {
            Object.keys(actions).map(id => {
                if (all_data[date_key][id] == undefined){
                    totals_by_action[id].push(0);
                } else {
                    totals_by_action[id].push(all_data[date_key][id].length);
                    if (all_data[date_key][id].length > max_y) max_y = all_data[date_key][id].length;
                }
            })
        }
    }
    var datasets = [];
    Object.keys(totals_by_action).map(action_id => {
        datasets.push({
            label:actions[action_id],
            data:totals_by_action[action_id],
            borderColor:color_set[action_id],
            backgroundColor:'white'
        })
    });

    var ctx = document.getElementById("myLineChart1");
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
                    suggestedMax: max_y + 2,
                    suggestedMin: 0,
                    stepSize: 1,
                    callback: function(value, index, values){
                    return  value +  '回'
                    }
                }
                }]
            },
        }
    });

    // var ctx2 = document.getElementById("myLineChart2");
    // var myLineChart2 = new Chart(ctx2, {
    //     type: 'line',
    //     data: {
    //         labels: ['5/1', '5/2', '5/3', '5/4', '5/5', '5/6', '5/7'],
    //         datasets: [
    //             {
    //             label: 'A',
    //             data: [35, 34, 37, 35, 34, 35, 34, 25],
    //             borderColor: "#42b688",
    //             backgroundColor: "rgba(0,0,0,0)"
    //             },
    //             {
    //             label: 'B',
    //             data: [25, 27, 27, 25, 26, 27, 25, 21],
    //             borderColor: "#42539a",
    //             backgroundColor: "rgba(0,0,0,0)"
    //             }
    //         ],
    //     },
    //     options: {
    //     title: {
    //         display: true,
    //         text: 'NGアクション毎の回数'
    //     },
    //     scales: {
    //         yAxes: [{
    //         ticks: {
    //             suggestedMax: 40,
    //             suggestedMin: 0,
    //             stepSize: 10,
    //             callback: function(value, index, values){
    //             return  value +  '回'
    //             }
    //         }
    //         }]
    //     },
    //     }
    // });

	// var ctx3 = document.getElementById("myLineChart3");
    // var myLineChart3 = new Chart(ctx3, {
    //     type: 'line',
    //     data: {
    //         labels: ['5/1', '5/2', '5/3', '5/4', '5/5', '5/6', '5/7'],
    //         datasets: [
    //             {
    //                 label: 'A',
    //                 data: [35, 34, 37, 35, 34, 35, 34, 25],
    //                 borderColor: "#42b688",
    //                 backgroundColor: "rgba(0,0,0,0)"
    //             },
    //             {
    //                 label: 'B',
    //                 data: [25, 27, 27, 25, 26, 27, 25, 21],
    //                 borderColor: "#42539a",
    //                 backgroundColor: "rgba(0,0,0,0)"
    //             }
    //         ],
    //     },
    //     options: {
    //         title: {
    //             display: true,
    //             text: 'NGアクション毎の回数'
    //         },
    //         scales: {
    //             yAxes: [{
    //                 ticks: {
    //                     suggestedMax: 40,
    //                     suggestedMin: 0,
    //                     stepSize: 10,
    //                     callback: function(value, index, values){
    //                     return  value +  '回'
    //                     }
    //                 }
    //             }]
    //         },
    //     }
    // });
  </script>
  @endsection
