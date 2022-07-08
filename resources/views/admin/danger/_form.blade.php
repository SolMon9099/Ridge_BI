<?php
    $action_options = config('const.action');
    $login_user = Auth::guard('admin')->user();
    $super_admin_flag = ($login_user->authority_id == config('const.super_admin_code'));
?>
<div class="no-scroll">
    @if (!isset($danger))
    <div class="title-wrap sp-m">
        <button type="button" class="edit left create-rule">＋ ルール追加</button>
    </div>
    @endif
    @include('admin.layouts.flash-message')
    <div class="scroll">
        <table class="table2 text-centre">
            <thead>
                <tr>
                    <th>アクション</th>
                    <th>カラー</th>
                    <th>エリア選択</th>
					<th>削除</th>
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
                        <p class="error-message area" style="display: none">エリアを選択してください。</p>
                    </td>
                    <td><button type="button" class="delete_danger_rules history">削除</button></td>
                </tr>
                <tr class="" id = "image-record-template" style="display: none">
                    <td colspan="4">
                        <div id="" class="camera-image" style="background: url('{{$camera_image_data}}') no-repeat;"></div>
                        <div class="btns" id="direction">
                            <button type="button" class="cancel-btn history">閉じる</button>
                            <button type="button" class="clear-btn history">クリア</button>
                        </div>
                    </td>
                </tr>
                @foreach ($rules as $index => $rule)
                <?php if ($rule->points != null && $rule->points != '') $rule->points = json_decode($rule->points);?>
                <tr data-index = {{$index}} id = {{$rule->id}} class="tr_line">
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
                        <p class="error-message area" style="display: none">エリアを選択してください。</p>
                    </td>
                    <td>
                        <button type="button" class="delete_danger_rules history" onclick="deleteRule({{$index}})" >削除</button>
                    </td>
                </tr>
                <tr image-index = {{$index}} class="image-record">
                    <td colspan="4">
                        <div id="" class="camera-image" style="background: url('{{$camera_image_data}}') no-repeat;">
                        </div>
                        <div class="btns" id="direction">
                            <button type="button" class="cancel-btn history">閉じる</button>
                            <button type="button" class="clear-btn history">クリア</button>
                        </div>
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
    <input type="hidden" value="" name="rule_data" id = 'rule_data'/>
    @if(!$super_admin_flag)
    <div class="footer-area">
        <button type="button" class="ok save-btn">決定</button>
    </div>
    @endif
</div>
<style>
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
    .image-record{
        display: none;
    }
    #image-container{
        /* background-size:100%; */
        width:1280px;
        height:720px;
        margin-left: auto;
        margin-right: auto;
    }
    .footer-area{
        widows: 100%;
        text-align: center;
        margin-top: 10px;
    }
</style>
<script src="{{ asset('assets/admin/js/konva.js?2') }}"></script>
<script src="https://swc.safie.link/latest/" onLoad="load()" defer></script>

<script>
    let safieStreamingPlayerElement;
    let safieStreamingPlayer;
    function load() {
        safieStreamingPlayerElement = document.querySelector('safie-streaming-player');
        if(safieStreamingPlayerElement != undefined && safieStreamingPlayerElement != null){
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
    var points = [];
    var point_numbers = 0;
    var rule_numbers = "<?php echo count($rules);?>";
    rule_numbers = parseInt(rule_numbers);
    var selected_rule_index = null;
    var selected_color = 'black';
    var all_rules = <?php echo $rules;?>;
    if (rule_numbers == 0) all_rules = [];

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
        all_rules[selected_rule_index].points = rect_points;
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

    function drawing(){
        var container = document.getElementById('image-container');
        stage = new Konva.Stage({
            container: 'image-container',
            width: container.clientWidth,
            height: container.clientHeight,
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

    function showCameraImage(index){
        point_numbers = 0;
        points = [];
        selected_rule_index = index;
        var selected_rule = all_rules[index];
        var selected_image_tr = $('tr[image-index="'+ selected_rule_index +'"]');
        resetImageContainers();
        selected_image_tr.show();
        $('.camera-image', selected_image_tr).attr('id', 'image-container');
        drawing();
        if (selected_rule.points != null && selected_rule.points != ''){
            points =  selected_rule.points;
        }
        var selected_rule_tr = $('tr[data-index="'+ selected_rule_index +'"]');
        selected_color = $('.color', selected_rule_tr).val();

        buttonSetting(selected_image_tr);
        if (points.length < 4) return;

        points.map((center_point, point_index) => {
            drawCircle(center_point, point_index);
        })
        point_numbers = 4;
        drawRect(points);
    }

    function buttonSetting(selected_image_tr){
        $('.clear-btn', selected_image_tr).click(function(){
            points = [];
            point_numbers = 0;
            layer.find('Circle').map(circle_item => {
                circle_item.destroy();
            })
            layer.find('Line').map(line_item => {
                line_item.destroy();
            })
            layer.draw();
            all_rules[selected_rule_index].points = null;
        });
        $('.cancel-btn', selected_image_tr).click(function(){
            points = [];
            point_numbers = 0;
            layer.find('Circle').map(circle_item => {
                circle_item.destroy();
            })
            layer.find('Line').map(line_item => {
                line_item.destroy();
            })
            layer.draw();
            resetImageContainers();
        });
    }

    function checkValidation(){
        var res = true;
        $('.tr_line').each(function(index){
            var action = $('.select-box', $(this)).val();
            var color = $('.color', $(this)).val();
            if ($(this).css('display') != 'none'){
                if (!(action > 0)) {
                    res = false;
                    $('.error-message.rule-select', $(this)).show();
                }
                if (all_rules[index].points == null || all_rules[index].points == ''){
                    $('.error-message.area', $(this)).show();
                    res = false;
                }
            } else {
                all_rules[index].is_deleted = true;
            }

            all_rules[index].color = color;
            all_rules[index].action_id = action;
        })
        $('#rule_data').val(JSON.stringify(all_rules));
        return res;
    }

    function saveRule(){
        $('.error-message').hide();
        if (!checkValidation()) return;
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
    function deleteRule(index){
        $('tr[data-index="'+ index +'"]').hide();
        $('tr[image-index="'+ index +'"]').hide();
    }
    function resetImageContainers(){
        $('.image-record').each(function(){
            $('.camera-image', $(this)).attr('id', '');
            $(this).hide();
        })
    }
    function addRule() {
        rule_numbers++;
        var tr_record = $("#rule-template").clone().show();
        tr_record.attr('id', '');
        tr_record.attr('data-index', rule_numbers-1);
        tr_record.addClass('tr_line');
        all_rules.push({id:0, camera_id:$('#camera_id').val(), color:'black', action_id:0, points:null});
        var image_record = $('#image-record-template').clone().show();
        image_record.attr('id', '');
        image_record.attr('image-index', rule_numbers-1);
        image_record.addClass('image-record');

        $(".show_camera_image", tr_record).click(function() {
            selected_rule_index = tr_record.attr('data-index');
            var selected_rule = all_rules[selected_rule_index];
            points = [];
            point_numbers = 0;
            selected_color = $('.color', tr_record).val();

            var selected_image_tr = $('tr[image-index="'+ selected_rule_index +'"]');
            resetImageContainers();
            selected_image_tr.show();
            $('.camera-image', selected_image_tr).attr('id', 'image-container');
            drawing();
            if (selected_rule.points != null && selected_rule.points != ''){
                points =  selected_rule.points;
                points.map((center_point, point_index) => {
                    drawCircle(center_point, point_index);
                })
                point_numbers = 4;
                drawRect(points);
            }

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
                all_rules[selected_rule_index].color = selected_color;
            });

            buttonSetting(selected_image_tr);
        });
        $(".delete_danger_rules", tr_record).click(function() {
            tr_record.hide();
            image_record.hide();
        })

        $("#rull-list").append(tr_record);
        $("#rull-list").append(image_record);
        image_record.hide();
    }

    $(document).ready(function() {
        $(".create-rule").click(function(e) {
            addRule();
        });
        $('.save-btn').click(function(){
            saveRule();
        });
    });
</script>
