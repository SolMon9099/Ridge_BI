<?php
    $action_options = config('const.action');
    $login_user = Auth::guard('admin')->user();
    $super_admin_flag = ($login_user->authority_id == config('const.super_admin_code'));
    $actions = array();
    $color = null;
    $points = array();
    foreach ($rules as $index => $rule) {
        if ($rule->points != null && $rule->points != '') {
            $points = json_decode($rule->points);
        }
        $actions[] = $rule->action_id;
        if (isset($rule->color)){
            $color = $rule->color;
        }
    }
?>
<div class="no-scroll">
    @include('admin.layouts.flash-message')
    <div class="scroll">
        <div style="display: flex;width:100%;">
            <div id="image-container" class="camera-image" style="background: url('{{$camera_image_data}}') no-repeat;"></div>
            <div style="padding-left:10px;">
                <div class="title-div">カラー</div>
                <div class="content-div"><input onchange="changeColor(this)" name='color' type="color" class="color" value="{{isset($color) ? $color:'#000000'}}"/></div>
                <div class="title-div">アクション</div>
                <div class="content-div">
                    @foreach($action_options as $id => $action)
                        <div>
                        @if (in_array($id, $actions))
                            <input name="actions[]" value={{$id}} type="checkbox" id="{{'action_'.$id}}" checked>
                        @else
                            <input name="actions[]" value={{$id}} type="checkbox" id="{{'action_'.$id}}">
                        @endif
                        <label class="custom-style" for="{{'action_'.$id}}"></label>{{$action}}
                        </div>
                    @endforeach
                </div>
                <p class="error-message rule-select" style="display: none">アクションを選択してください。</p>
            </div>
        </div>
        <p class="error-message area" style="display: none">エリアを選択してください。</p>

        <div class="btns" id="direction">
            <button type="button" onclick="clearImage()" class="clear-btn history">選択をクリア</button>
            <button type="button" onclick="saveRule()" class="ok save-btn">決定</button>
        </div>
    </div>
    <div class="description">赤枠は4点をドラッグすることでサイズを変更することが出来ます。<div id="debug"></div></div>
    {{-- <div class="streaming-video" style="height:500px;">
        <safie-streaming-player></safie-streaming-player>
        <input type="button" value='Play' onClick="play()">
        <input type="button" value='Pause' onClick="pause()">
    </div> --}}
    <input type="hidden" value="" name="points_data" id = 'points_data'/>
</div>
<style>
    .clear-btn{
        margin:0;
        margin-right:15px;
        padding: 15px 75px;
    }
    .image-record{
        display: none;
    }
    #image-container{
        width:1280px;
        height:720px;
        display: block;
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
    .content-div{
        margin-bottom: 20px;
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
    var radius = "<?php echo config('const.camera_mark_radius');?>";
    radius = parseInt(radius);
    var selected_color = '<?php echo $color;?>';
    var points = <?php echo json_encode($points);?>;
    var point_numbers = 0;
    if (points.length == 4) point_numbers = 4;

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
        points = rect_points;
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
            var index = points.findIndex(point => point.id == e.target.id());
            if (index > -1){
                points[index].x = e.evt.offsetX;
                points[index].y = e.evt.offsetY;
                if (point_numbers == 4){
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
            drawCircle({x:e.evt.offsetX, y:e.evt.offsetY}, point_numbers);
            points.push({x:e.evt.offsetX, y:e.evt.offsetY, id:point_numbers})
            point_numbers++;
            if (point_numbers == 4){
                drawRect(points);
            }
        })
        stage.on('mousemove', function(e){
            document.getElementById("debug").innerHTML = `X座標${e.evt.offsetX}:Y座標${e.evt.offsetY}`;
        })
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
    }

    function checkValidation(){
        var res = true;
        if (points.length != 4){
            $('.error-message.area').show();
            res = false;
        }
        var action_boxs = $('input[type=checkbox]:checked');
        if (!(action_boxs.length > 0)) {
            res = false;
            $('.error-message.rule-select').show();
        }
        return res;
    }

    function saveRule(){
        $('.error-message').hide();
        if (!checkValidation()) return;
        $('#points_data').val(JSON.stringify(points));
        $('#form_danger_rule').submit();
    }

    function changeColor(e){
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
        drawing();

        points.map((center_point, point_index) => {
            drawCircle(center_point, point_index);
        });
        if (points.length == 4){
            drawRect(points);
        }
    });
</script>
