<?php
    $login_user = Auth::guard('admin')->user();
    $super_admin_flag = ($login_user->authority_id == config('const.super_admin_code'));
    $color = null;
    $points = array();
    $hour = '';
    $mins = '';
    foreach ($rules as $index => $rule) {
        if ($rule->points != null && $rule->points != '') {
            $points[$index]['positions'] =  json_decode($rule->points);
            if (isset($rule->color) && $rule->color != '') {
                $color = $rule->color;
                $points[$index]['color'] = $color;
            }
            if (isset($rule->hour) && $rule->hour !== '') {
                $hour = $rule->hour;
            }
            if (isset($rule->mins) && $rule->mins !== '') {
                $mins = $rule->mins;
            }
        }
    }
?>
<div class="no-scroll">
    {{-- @if (!isset($shelf))
    <div class="title-wrap sp-m">
        <button type="button" class="edit left create-rule">＋ ルール追加</button>
    </div>
    @endif --}}
    @include('admin.layouts.flash-message')
    <div class="scroll">
        <div class="n-area2">
            <div class="video-area" style="width:85%;">
                <div id="image-container" class="camera-image" style="background: url('{{$camera_image_data}}') no-repeat;"></div>
                <p class="error-message area" style="display: none">エリアを選択してください。</p>
                <div class="description">赤枠は4点をドラッグすることでサイズを変更することが出来ます。<div id="debug"></div></div>
            </div>
            <div style="width:10%;">
                <div class="time-wrap">
                    <h3>定時撮影時刻を入力</h3>
                    <ul class="time">
                        <li>
                            <input name="hour" type="number" inputmode="numeric" pattern="\d*" max='23' min='0'
                                onchange= "getInput(this, 'hour')" value="{{old('hour', isset($hour)?$hour:'')}}">
                        </li>
                        <li>:</li>
                        <li>
                            <input name="mins" type="number" inputmode="numeric" pattern="\d*" max='59' min='0'
                                onchange= "getInput(this, 'mins')" value="{{old('mins', isset($mins)?$mins:'')}}">
                        </li>
                    </ul>
                    <p class="error-message time" style="display: none">定時撮影時刻を正しく入力してください。</p>
                    <p><small>※指定時刻の陳列を正常とし検知します</small></p>
                </div>
                <div class="color-wrap">
                    <h3>カラーを選択</h3>
                    <input onchange="changeColor(this)" name='color' type="color" class="color"
                        value="{{old('color', isset($color) ? $color:'#000000')}}"/>
                </div>
            </div>
        </div>

        <div class="btns" id="direction">
            <button type="button" onclick="clearImage()" class="edit reset clear-btn history">選択をクリア</button>
            <div class="balloon1">
                <p>こちらをクリックして描写してください。</p>
            </div>
            <button type="button" id="area_select" class="edit add" onclick="enableDraw()">
                選択エリアを追加
            </button>
            <button type="button" onclick="saveRule()" class="ok save-btn">決定</button>
        </div>
    </div>
    {{-- <div class="streaming-video" style="height:500px;">
        <safie-streaming-player></safie-streaming-player>
        <input type="button" value='Play' onClick="play()">
        <input type="button" value='Pause' onClick="pause()">
    </div> --}}
    <input type="hidden" value="" name="points_data" id = 'points_data'/>

</div>
<!--MODAL -->
<div id="howto" class="modal-content">
    <div class="textarea">
        <div class="explain">
            <h3>【使い方】</h3>
            <ul>
                <li>①日々棚乱れが発生していない時間(開店時間等)を「定時撮影時刻」として指定</li>
                <li>②「選択エリアを追加」ボタンを押下し検知するエリアを4点を選択し矩形で囲ってください。
                    <small>※最大3エリア作成可能</small>
                </li>
                <li>③選択が完了したら「決定」ボタンを押下してください。</li>
            </ul>
        </div>
    </div>
    <p class="closemodal"><a class="modal-close">×</a></p>
</div>
<!-- -->
<style>
    .clear-btn{
        /* margin:0;
        margin-right:15px;
        padding: 15px 75px; */
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
    .description{
        margin-top:10px;
        color: #999;
        font-size: 13px;
    }
    #debug{
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
    var enable_darw_flag = false;
    var point_numbers = 0;
    var hour = "<?php echo old('hour', isset($hour)?$hour:'');?>";
    if (isNaN(parseInt(hour))){
        hour = '';
    } else {
        hour = parseInt(hour);
    }
    var mins = "<?php echo old('mins', isset($mins)?$mins:'');?>";
    if (isNaN(parseInt(mins))){
        mins = '';
    } else {
        mins = parseInt(mins);
    }
    var shelf_max_rect_numbers = "<?php echo config('const.shelf_max_rect_numbers');?>";
    shelf_max_rect_numbers = parseInt(shelf_max_rect_numbers);
    var radius = "<?php echo config('const.camera_mark_radius');?>";
    radius = parseInt(radius);
    var points = <?php echo json_encode($points);?>;

    var selected_color = "<?php echo isset($color) && $color != '' ? $color : 'black';?>";

    function getInput(e, param){
        if (param == 'hour'){
            hour = e.value;
        }
        if (param == 'mins'){
            mins = e.value;
        }
    }
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

    function drawRect(rect_points_data){
        if (rect_points_data == undefined || rect_points_data == null || rect_points_data.length == 0) return;
        layer.find('Line').map(line_item => {
            line_item.destroy();
        });
        layer.draw();
        rect_points_data.map(item => {
            if (item.positions != undefined && item.positions.length == 4){
                var rect_color = item.color != null && item.color != undefined && item.color != '' ? item.color : selected_color;
                var rect_points = sortRectanglePoints(item.positions);
                var rect_area = new Konva.Line({
                    points: [
                        rect_points[0].x, rect_points[0].y,
                        rect_points[1].x, rect_points[1].y,
                        rect_points[2].x, rect_points[2].y,
                        rect_points[3].x, rect_points[3].y,
                        rect_points[0].x, rect_points[0].y
                    ],
                    stroke: rect_color,
                    strokeWidth: radius - 3 > 0? radius - 3 : 2,
                    lineCap: 'round',
                    lineJoin: 'round',
                });
                layer.add(rect_area);
            }
        })
        enable_darw_flag = false;
        if (point_numbers < shelf_max_rect_numbers * 4){
            $('.balloon1').css('opacity', 1);
            $('button#area_select').attr('disabled', false);
		    $('button#area_select').css('opacity', 1);
        }
    }


    function drawCircle(center_point, point_index, color = null){
        var circle = new Konva.Circle({
            x: center_point.x,
            y: center_point.y,
            radius: radius,
            fill: color != null ? color: selected_color,
            stroke: color != null ? color: selected_color,
            strokeWidth: 1,
            draggable:true,
            id: point_index
        });
        circle.on('mouseenter', function () {
            stage.container().style.cursor = 'pointer';
        });
        circle.on('mouseleave', function () {
            stage.container().style.cursor = 'default';
        });
        circle.on('dragmove', function (e) {
            var rect_index  = null;
            var position_index = -1;
            points.map((point_item, point_item_index) => {
                if (point_item.positions != undefined && point_item.positions != null){
                    var index = point_item.positions.findIndex(z => z.id == e.target.id());
                    if (index > -1){
                        points[point_item_index].positions[index].x = e.evt.offsetX;
                        points[point_item_index].positions[index].y = e.evt.offsetY;
                        if (point_item.positions.length == 4){
                            rect_index = point_item_index;
                            position_index = index;
                        }
                    }
                }
            });
            if (position_index > -1 && rect_index != null){
                drawRect(points);
            }

        })
        layer.add(circle);
    }

    function drawingStage(){
        var container = document.getElementById('image-container');
        stage = new Konva.Stage({
            container: 'image-container',
            width: container.clientWidth,
            height: container.clientHeight,
        })
        layer = new Konva.Layer();
        stage.add(layer);
        stage.on('click', function(e){
            if (enable_darw_flag != true) return;
            if (point_numbers == shelf_max_rect_numbers * 4) return;
            var rect_index = parseInt(point_numbers/4);
            point_numbers++;
            drawCircle({x:e.evt.offsetX, y:e.evt.offsetY}, point_numbers, selected_color);
            if (points[rect_index] != undefined){
                if (points[rect_index].positions == undefined) points[rect_index].positions = [];
                if (points[rect_index].positions.length == 4) return;
                points[rect_index].positions.push({x:e.evt.offsetX, y:e.evt.offsetY, id:point_numbers});
                points[rect_index].color = selected_color;
            } else {
                points.push({'positions':[{x:e.evt.offsetX, y:e.evt.offsetY, id:point_numbers}], 'color':selected_color});
            }
            if (point_numbers % 4 == 0){
                drawRect(points);
            }
        })
        stage.on('mousemove', function(e){
            document.getElementById("debug").innerHTML = `X座標${e.evt.offsetX}:Y座標${e.evt.offsetY}`;
        })
    }

    function enableDraw(){
        if (point_numbers >= shelf_max_rect_numbers * 4) return;
        enable_darw_flag = true;
        $('.balloon1').css('opacity', 0);
        $('button#area_select').attr('disabled', true);
		$('button#area_select').css('opacity', 0.3);
    }

    function clearImage(){
        points = [];
        point_numbers = 0;
        layer.find('Circle').map(circle_item => {
            circle_item.destroy();
        })
        layer.find('Line').map(line_item => {
            line_item.destroy();
        })
        layer.draw();
        enable_darw_flag = false;
        if (point_numbers < shelf_max_rect_numbers * 4){
            $('.balloon1').css('opacity', 1);
            $('button#area_select').attr('disabled', false);
		    $('button#area_select').css('opacity', 1);
        }
    }

    function checkValidation(){
        var check_time_flag = false;
        if (mins === '') check_time_flag = true;
        if (hour === '') check_time_flag = true;
        if (hour > 23) check_time_flag = true;
        if (mins > 59) check_time_flag = true;
        if (check_time_flag == true){
            $('.error-message.time').show();
            return false;
        }
        var check_points_flag = false;
        if (points == undefined || points.length == 0) check_points_flag = true;
        if (check_points_flag == false){
            if(points[0].positions == undefined || points[0].positions.length != 4) check_points_flag = true;
        }
        if (check_points_flag == true){
            $('.error-message.area').show();
            return false;
        }
        $('#points_data').val(JSON.stringify(points));
        return true;
    }

    function saveRule(){
        $('.error-message').hide();
        if (!checkValidation()) return;
        $('#form_shelf_rule').submit();
    }

    function changeColor(e, index){
        selected_color = e.value;
    }

    $(document).ready(function() {
        drawingStage();
        point_numbers = 0;
        if (points.length > 0){
            points.map(record => {
                if (record.positions != undefined && record.positions.length == 4){
                    record.positions.map((point) => {
                        point_numbers++;
                        drawCircle(point, point_numbers, record.color);
                        point.id = point_numbers;
                    });
                }
            })
            if (point_numbers % 4 == 0 && point_numbers > 0){
                drawRect(points);
            }
        }
        if (point_numbers < shelf_max_rect_numbers * 4){
            $('.balloon1').css('opacity', 1);
        }
    });
</script>
