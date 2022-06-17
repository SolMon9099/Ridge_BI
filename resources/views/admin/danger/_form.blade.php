<?php
$action_options = config('const.action');
?>
<div class="no-scroll">
    <div class="title-wrap sp-m">
        <button type="button" class="edit left create-rull">＋ ルール追加</button>
    </div>
    @include('admin.layouts.flash-message')
    <div class="scroll">
        <table class="table2 text-centre">
            <thead>
                <tr>
                    <th>アクション</th>
                    <th>カラー</th>
                    <th></th>
                </tr>
            </thead>
            <tbody id="rull-list">
                <tr id="rule-template" style="display: none">
                    <td>
                        <select class="select-box">
                            <option value='0'>ルールを選択</option>
                            @foreach($action_options as $id => $action)
                                <option value={{$id}}>{{$action}}</option>
                            @endforeach
                        </select>
                        <p class="error-message rule-select" style="display: none">ルールを選択してください。</p>
                    </td>
                    <td><input type="color" class="color"/></td>
                    <td>
                        <button type="button" class="edit show_camera_image">エリア選択</button>
                    </td>
                </tr>
                @foreach ($rules as $index => $rule)
                <tr data-index = {{$index}} id = {{$rule->id}}>
                    <td>
                        <select class="select-box">
                            <option value='0'>ルールを選択</option>
                            @foreach($action_options as $id => $action)
                                @if ($id == $rule->action_id)
                                    <option value={{$id}} selected>{{$action}}</option>
                                @else
                                    <option value={{$id}}>{{$action}}</option>
                                @endif
                            @endforeach
                        </select>
                        <p class="error-message rule-select" style="display: none">ルールを選択してください。</p>
                    </td>
                    <td><input type="color" class="color" value="{{$rule->color}}" onchange="changeColor(this, {{$index}})"/></td>
                    <td>
                        <button type="button" class="edit show_camera_image" onclick="showCameraImage({{$index}})">エリア選択</button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    {{-- <div class="streaming-video" style="height:500px;">
        <safie-streaming-player></safie-streaming-player>
        <input type="button" value='Play' onClick="play()">
        <input type="button" value='Pause' onClick="pause()">
    </div> --}}
    <div class="video-area">
        <div id="image-container" style="background: url('{{$camera_image_data}}') no-repeat;">
        </div>
        <p class="error-message area" style="display: none">エリアを選択してください。</p>
        <div class="btns" id="direction">
            <button type="button" class="cancel-btn history">キャンセル</button>
            <button type="button" class="clear-btn history">クリア</button>
            <button type="button" class="ok save-btn">決定</button>
        </div>
    </div>
    <input type="hidden" value="" name="rule_data" id = 'rule_data'/>
</div>
<style>
    #image-container{
        background-size:100%;
        /* min-height: 200px; */
    }
    .clear-btn{
        margin:0;
        margin-right:15px;
        padding: 15px 75px;
    }
    .cancel-btn{
        margin:0;
        margin-right:15px;
        padding: 15px 75px;
    }
    .video-area{
        /* display: block; */
    }
</style>
<script src="{{ asset('assets/admin/js/konva.js?2') }}"></script>
<script src="https://swc.safie.link/latest/" onLoad="load()" defer></script>

<script>
    let safieStreamingPlayerElement;
    let safieStreamingPlayer;
    function load() {
        safieStreamingPlayerElement = document.querySelector('safie-streaming-player');
        safieStreamingPlayer = safieStreamingPlayerElement.instance;
        safieStreamingPlayer.on('error', (error) => {
            console.error(error);
        });
        // 初期化
        safieStreamingPlayer.defaultProperties = {
            defaultAccessToken: '<?php echo $access_token;?>',
            defaultDeviceId: '<?php echo $device_id;?>',
        };
    }
    function play() {
        safieStreamingPlayer.play();
    }
    function pause() {
        safieStreamingPlayer.pause();
    }
</script>

<script>
    var stage = null;
    var layer = null;
    var radius = "<?php echo config('const.camera_mark_radius');?>";
    radius = parseInt(radius);
    var container = document.getElementById('image-container');
    var points = [];
    var point_numbers = 0;
    var rule_numbers = "<?php echo count($rules);?>";
    rule_numbers = parseInt(rule_numbers);
    var selected_rule_index = null;
    var selected_color = 'black';

    function is_cross(line1, line2){
        var a = line1[0]; // A point
        var b = line1[1]; // B point
        var c = line2[0]; // C point
        var d = line2[1]; // D point
        let s = (b.x - a.x) * (c.y - a.y) - (c.x - a.x) * (b.y - a.y);
        let t = (b.x - a.x) * (d.y - a.y) - (d.x - a.x) * (b.y - a.y);
        return s * t < 0;
    }

    function sortRectanglePoints(rect_points) {
        var res = rect_points;
        if (is_cross([rect_points[0], rect_points[1]], [rect_points[2], rect_points[3]])) {
            res = [rect_points[0], rect_points[2], rect_points[1], rect_points[3]];
        } else if (is_cross([rect_points[0], rect_points[2]], [rect_points[1], rect_points[3]])) {
            res = [rect_points[0], rect_points[1], rect_points[2], rect_points[3]];
        } else {
            res = [rect_points[0], rect_points[1], rect_points[3], rect_points[2]];
        }
        return res;
    }

    function drawRect(rect_points){
        layer.find('Line').map(line_item => {
            line_item.destroy();
        })
        layer.draw();
        rect_points = sortRectanglePoints(rect_points);
        var rect_area = new Konva.Line({
            points: [
                rect_points[0].x, rect_points[0].y,
                rect_points[1].x, rect_points[1].y,
                rect_points[2].x, rect_points[2].y,
                rect_points[3].x, rect_points[3].y,
                rect_points[0].x, rect_points[0].y
            ],
            stroke: selected_color,
            strokeWidth: radius - 3 > 0? radius - 3 : 2,
            lineCap: 'round',
            lineJoin: 'round',
        });
        layer.add(rect_area);
    }

    function drawing(){
        stage = new Konva.Stage({
            container: 'image-container',
            width: container.clientWidth,
            height: window.innerHeight,
        })
        layer = new Konva.Layer();
        stage.add(layer);
        stage.on('click', function(e){
            if (point_numbers == 4) return;
            var circle = new Konva.Circle({
                x: e.evt.offsetX,
                y: e.evt.offsetY,
                radius: radius,
                fill: selected_color,
                stroke: selected_color,
                strokeWidth: 1,
                draggable:true,
                id:point_numbers
            });
            circle.on('mouseenter', function () {
                stage.container().style.cursor = 'pointer';
            });
            circle.on('mouseleave', function () {
                stage.container().style.cursor = 'default';
            });
            circle.on('dragmove', function (e) {
                if (point_numbers == 4){
                    var index = points.findIndex(point => point.id == e.target.id());
                    if (index > -1){
                        points[index].x = e.evt.offsetX;
                        points[index].y = e.evt.offsetY;
                        drawRect(points);
                    }
                }
            })
            layer.add(circle);
            points.push({x:e.evt.offsetX, y:e.evt.offsetY, id:point_numbers})
            point_numbers++;
            if (point_numbers == 4){
                drawRect(points);
            }

        })
    }

    function drawCircle(center_point, point_index){
        var circle = new Konva.Circle({
            x: center_point.x,
            y: center_point.y,
            radius: radius,
            fill: selected_color,
            stroke: selected_color,
            strokeWidth: 1,
            draggable:true,
            id:point_index
        });
        circle.on('mouseenter', function () {
            stage.container().style.cursor = 'pointer';
        });
        circle.on('mouseleave', function () {
            stage.container().style.cursor = 'default';
        });
        circle.on('dragmove', function (e) {
            if (point_numbers == 4){
                var index = points.findIndex(point => point.id == e.target.id());
                if (index > -1){
                    points[index].x = e.evt.offsetX;
                    points[index].y = e.evt.offsetY;
                    drawRect(points);
                }
            }
        })
        layer.add(circle);
    }

    function showCameraImage(index){
        point_numbers = 0;
        points = [];
        $(".video-area").show();
        drawing();
        selected_rule_index = index;
        let rules = <?php echo $rules;?>;
        var selected_rule = rules[index];
        points = [
            {x:selected_rule.first_x, y:selected_rule.first_y, id:0},
            {x:selected_rule.second_x, y:selected_rule.second_y, id:1},
            {x:selected_rule.third_x, y:selected_rule.third_y, id:2},
            {x:selected_rule.fourth_x, y:selected_rule.fourth_y, id:3},
        ];
        var selected_rule_tr = $('tr[data-index="'+ selected_rule_index +'"]');
        selected_color = $('.color', selected_rule_tr).val();
        points.map((center_point, point_index) => {
            drawCircle(center_point, point_index);
        })
        point_numbers = 4;
        drawRect(points);
        buttonSetting();
    }

    function buttonSetting(){
        $('.clear-btn').click(function(){
            points = [];
            point_numbers = 0;
            layer.find('Circle').map(circle_item => {
                circle_item.destroy();
            })
            layer.find('Line').map(line_item => {
                line_item.destroy();
            })
            layer.draw();
        });
        $('.cancel-btn').click(function(){
            points = [];
            point_numbers = 0;
            layer.find('Circle').map(circle_item => {
                circle_item.destroy();
            })
            layer.find('Line').map(line_item => {
                line_item.destroy();
            })
            layer.draw();
            $(".video-area").hide();
        });
        $('.save-btn').click(function(){
            saveRule();
        })
    }

    function saveRule(){
        $('.error-message').hide();
        if (points.length < 4) {
            $('.error-message.area').show();
            return;
        }
        var selected_rule_tr = $('tr[data-index="'+ selected_rule_index +'"]');
        var selected_action = $('.select-box', selected_rule_tr).val();
        if (!(selected_action > 0)) {
            $('.error-message', selected_rule_tr).show();
            return;
        }
        var rule_data = {};
        rule_data.id = selected_rule_tr.attr('id') > 0 ? selected_rule_tr.attr('id') : 0;
        rule_data.action_id = selected_action;
        rule_data.color = selected_color;
        rule_data.points = points;
        rule_data.camera_id = $('#camera_id').val();
        $('#rule_data').val(JSON.stringify(rule_data));
        $('#form_danger_rule').submit();
    }

    function changeColor(e, index){
        if (index != selected_rule_index) return;
        selected_color = e.value;
        layer.find('Circle').map(circle_item => {
            circle_item.fill(selected_color);
            circle_item.stroke(selected_color);
        })
        layer.find('Line').map(line_item => {
            line_item.stroke(selected_color);
        })

    }

    $(document).ready(function() {
        function addRule() {
            rule_numbers++;
            var tr_record = $("#rule-template").clone().show();
            tr_record.attr('id', '');
            tr_record.attr('data-index', rule_numbers-1);
            $(".show_camera_image", tr_record).click(function() {
                selected_rule_index = tr_record.attr('data-index');
                points = [];
                point_numbers = 0;
                $(".video-area").show();
                selected_color = $('.color', tr_record).val();
                drawing();
                $('.color', tr_record).change(function(){
                    if (selected_rule_index != tr_record.attr('data-index')) return;
                    selected_color = $(this).val();
                    layer.find('Circle').map(circle_item => {
                        circle_item.fill(selected_color);
                        circle_item.stroke(selected_color);
                    })
                    layer.find('Line').map(line_item => {
                        line_item.stroke(selected_color);
                    })
                });
            });

            $("#rull-list").append(tr_record);
            buttonSetting();
        }
        $(".create-rull").click(function(e) {
            addRule();
        });
    });
</script>
