<?php
    $action_options = config('const.action');
    $login_user = Auth::guard('admin')->user();
    $super_admin_flag = ($login_user->authority_id == config('const.super_admin_code'));
    foreach ($rules as $key => $rule) {
        if ($rule->red_points != null && $rule->red_points != '') $rule->red_points = json_decode($rule->red_points);
        if ($rule->blue_points != null && $rule->blue_points != '') $rule->blue_points = json_decode($rule->blue_points);
        $max_permission_members = $rule->max_permission_members;
    }
?>
<div class="no-scroll">
    {{-- @include('admin.layouts.flash-message') --}}
    <div class="scroll">
        <table class="table">
            <tr>
                <th style="width:10%;">許容最大人数</th>
                <td>
                    <input style="background:white; width:50%;"
                        name = 'max_permission_members' type="number"
                        value="{{ old('max_permission_members', isset($max_permission_members)?$max_permission_members:'')}}"
                    />
                </td>
            </tr>
        </table>
        @error('max_permission_members')
            <p class="error-message">{{ $message }}</p>
        @enderror
        <div id="image-container" class="camera-image" style="background: url('{{$camera_image_data}}') no-repeat;"></div>
        <div class="description">青枠及び赤枠は4点をドラッグすることでサイズを変更することが出来ます。<div id="debug"></div></div>
        <p class="error-message area" style="display: none">エリアを選択してください。</p>
        @if(!$super_admin_flag)
        <div class="btns" id="direction">
            <button type="button" class="clear-btn history">クリア</button>
            <button type="button" class="ok save-btn">決定</button>
        </div>
        @endif
    </div>
    <input type="hidden" value="" name="rule_data" id = 'rule_data'/>
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
    .description{
        width: 1280px;
        margin-left: auto;
        margin-right: auto;
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
    var radius = "<?php echo config('const.camera_mark_radius');?>";
    radius = parseInt(radius);
    var red_points = [];
    var blue_points = [];
    var point_numbers = 0;
    var rule_numbers = "<?php echo count($rules);?>";
    rule_numbers = parseInt(rule_numbers);
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

    function is_intersect(s0, s1){
        var dx0 = s0[1].x-s0[0].x;
        var dx1 = s1[1].x-s1[0].x;
        var dy0 = s0[1].y-s0[0].y;
        var dy1 = s1[1].y-s1[0].y;
        var p0 = dy1*(s1[1].x-s0[0].x) - dx1*(s1[1].y-s0[0].y);
        var p1 = dy1*(s1[1].x-s0[1].x) - dx1*(s1[1].y-s0[1].y);
        var p2 = dy0*(s0[1].x-s1[0].x) - dx0*(s0[1].y-s1[0].y);
        var p3 = dy0*(s0[1].x-s1[1].x) - dx0*(s0[1].y-s1[1].y);
        return (p0*p1<=0) & (p2*p3<=0);
    }

    function expandLine(point1, point2, expands){
        var res = [{id:'blue_' + point1.id}, {id:'blue_' + point2.id}];
        var x_diff = Math.abs(point2.x - point1.x);
        var y_diff = Math.abs(point2.y - point1.y);
        if (x_diff == 0 || y_diff == 0){
            if (x_diff == 0){
                res[0].x = point1.x;
                res[1].x = point2.x;
                if (y_diff == 0){
                    res[0].y = point1.y;
                    res[1].y = point2.y;
                } else {
                    if (point1.y < point2.y){
                        res[0].y = Math.max(point1.y - expands, 0);
                        res[1].y = Math.min(point2.y + expands, 720);
                    } else {
                        res[0].y = Math.min(point1.y + expands, 720);
                        res[1].y = Math.max(point2.y - expands, 0);
                    }
                }
            } else {
                res[0].y = point1.y;
                res[1].y = point2.y;
                if (point1.x < point2.x){
                    res[0].x = Math.max(0,point1.x - expands);
                    res[1].x = Math.min(1280, point2.x + expands);
                } else {
                    res[0].x = Math.min(1280, point1.x + expands);
                    res[1].x = Math.max(0, point2.x - expands);
                }
            }
        } else {
            var x_delta = expands * x_diff/Math.sqrt(Math.pow(x_diff, 2) + Math.pow(y_diff,2));
            var y_delta = expands * y_diff/Math.sqrt(Math.pow(x_diff, 2) + Math.pow(y_diff,2));
            if (point1.x < point2.x){
                res[0].x = Math.max(0, point1.x - x_delta);
                res[1].x = Math.min(1280, point2.x + x_delta);
            } else {
                res[0].x = Math.min(1280, point1.x + x_delta);
                res[1].x = Math.max(0, point2.x - x_delta);
            }

            if (point1.y < point2.y){
                res[0].y = Math.max(0, point1.y - y_delta);
                res[1].y = Math.min(720, point2.y + y_delta);
            } else {
                res[0].y = Math.min(720, point1.y + y_delta);
                res[1].y = Math.max(0, point2.y - y_delta);
            }
        }
        return res;
    }

    function getAreaTriangle(point1, point2, point3){
        var res =  Math.abs(point1.x*(point2.y-point3.y) + point2.x*(point3.y-point1.y) + point3.x*(point1.y- point2.y));
        return res/2;
    }

    function findInsidePointOfRectangle(point1 , point2, point3, point4){
        var rect_area_1 = getAreaTriangle(point1, point2, point3) + getAreaTriangle(point1, point3, point4);
        var rect_area_2 = getAreaTriangle(point1, point2, point4) + getAreaTriangle(point2, point3, point4);
        if (rect_area_1 == rect_area_2) return 0;
        if (rect_area_1 < rect_area_2){
            if (getAreaTriangle(point1, point2, point4) > getAreaTriangle(point2, point3, point4)){
                return 3;
            } else {
                return 1;
            }
        } else {
            if (getAreaTriangle(point1, point3, point4) > getAreaTriangle(point1, point2, point3)){
                return 2;
            } else{
                return 4;
            }
        }
    }

    function getExpandRectanglePoints(rect_points, expands){
        var cross_line1 = [rect_points[0], rect_points[2]]; //[{(x0, y0), (x2, y2)}]
        var cross_line2 = [rect_points[1], rect_points[3]]; //[{(x1, y1), (x3, y3)}]
        var res = [{}, {}, {}, {}];
        if (is_intersect(cross_line1, cross_line2)){
            var temp_res = expandLine(rect_points[0], rect_points[2], expands);
            res[0] = temp_res[0];
            res[2] = temp_res[1];
            temp_res = expandLine(rect_points[1], rect_points[3], expands);
            res[1] = temp_res[0];
            res[3] = temp_res[1];
        } else {
            var inside_point_index = findInsidePointOfRectangle(rect_points[0], rect_points[1], rect_points[2], rect_points[3]);
            if (inside_point_index > 0){
                inside_point_index--;
                for (var i = 0; i< 4; i++){
                    if (i == inside_point_index) continue;
                    temp_res = expandLine(rect_points[inside_point_index], rect_points[i], expands);
                    if (Math.abs(i - inside_point_index) == 2){
                        res[i] = temp_res[1];
                        res[inside_point_index] = temp_res[0];
                    } else {
                        res[i] = temp_res[1];
                    }
                }
            } else {
                temp_res = expandLine(rect_points[0], rect_points[2], expands);
                res[0] = temp_res[0];
                res[2] = temp_res[1];
                temp_res = expandLine(rect_points[1], rect_points[3], expands);
                res[1] = temp_res[0];
                res[3] = temp_res[1];
            }
        }
        return res;
    }
    function drawCircle(center_point, point_index, color = null){
        var circle = new Konva.Circle({
            x: center_point.x,
            y: center_point.y,
            radius: radius,
            fill: color != null ? color: 'red',
            stroke: color != null ? color: 'red',
            strokeWidth: 1,
            draggable:true,
            id:color != null ? color + '_' + point_index : point_index
        });
        circle.on('mouseenter', function () {
            stage.container().style.cursor = 'pointer';
        });
        circle.on('mouseleave', function () {
            stage.container().style.cursor = 'default';
        });
        circle.on('dragmove', function (e) {
            if (point_numbers == 4){
                if (color == null){
                    var index = red_points.findIndex(point => point.id == e.target.id());
                    if (index > -1){
                        red_points[index].x = e.evt.offsetX;
                        red_points[index].y = e.evt.offsetY;
                        drawRect(red_points);
                    }
                } else {
                    index = blue_points.findIndex(point => point.id == e.target.id());
                    if (index > -1){
                        blue_points[index].x = e.evt.offsetX;
                        blue_points[index].y = e.evt.offsetY;
                        drawRect(red_points, blue_points);
                    }
                }

            }
        })
        layer.add(circle);
    }

    function drawRect(rect_points, blue_data = null){
        layer.find('Line').map(line_item => {
            line_item.destroy();
        })
        if (blue_data == null || blue_data == ''){
            if (layer.findOne('#blue_0') != undefined){
                layer.findOne('#blue_0').destroy();
            }
            if (layer.findOne('#blue_1') != undefined){
                layer.findOne('#blue_1').destroy();
            }
            if (layer.findOne('#blue_2') != undefined){
                layer.findOne('#blue_2').destroy();
            }
            if (layer.findOne('#blue_3') != undefined){
                layer.findOne('#blue_3').destroy();
            }

        }


        layer.draw();
        rect_points = sortRectanglePoints(rect_points);

        var red_rect_area = new Konva.Line({
            points: [
                rect_points[0].x, rect_points[0].y,
                rect_points[1].x, rect_points[1].y,
                rect_points[2].x, rect_points[2].y,
                rect_points[3].x, rect_points[3].y,
                rect_points[0].x, rect_points[0].y
            ],
            stroke: 'red',
            strokeWidth: radius - 3 > 0? radius - 3 : 2,
            lineCap: 'round',
            lineJoin: 'round',
        });
        if (blue_data == null || blue_data == ''){
            blue_points = getExpandRectanglePoints(rect_points, 60);
            blue_points.map((item, index) => {
                drawCircle(item, index, 'blue');
            });
        } else {
            blue_points = blue_data;
        }

        var blue_rect_area = new Konva.Line({
            points: [
                blue_points[0].x, blue_points[0].y,
                blue_points[1].x, blue_points[1].y,
                blue_points[2].x, blue_points[2].y,
                blue_points[3].x, blue_points[3].y,
                blue_points[0].x, blue_points[0].y
            ],
            stroke: 'blue',
            strokeWidth: radius - 3 > 0? radius - 3 : 2,
            lineCap: 'round',
            lineJoin: 'round',
        });
        layer.add(red_rect_area);
        layer.add(blue_rect_area);

        if (all_rules.length == 0){
            all_rules.push({id:0, camera_id:$('#camera_id').val(), red_points:null, blue_points:null})
        }
        all_rules[0].red_points = rect_points;
        all_rules[0].blue_points = blue_points;
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
                fill: 'red',
                stroke: 'red',
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
                    var index = red_points.findIndex(point => point.id == e.target.id());
                    if (index > -1){
                        red_points[index].x = e.evt.offsetX;
                        red_points[index].y = e.evt.offsetY;
                        drawRect(red_points);
                    }
                }
            })
            layer.add(circle);
            red_points.push({x:e.evt.offsetX, y:e.evt.offsetY, id:point_numbers})
            point_numbers++;
            if (point_numbers == 4){
                drawRect(red_points);
            }

        })
        stage.on('mousemove', function(e){
            document.getElementById("debug").innerHTML = `X座標${e.evt.offsetX}:Y座標${e.evt.offsetY}`;
        })
    }

    function checkValidation(){
        if (all_rules.length == 0){
            $('.error-message').show();
            return false;
        }
        if (all_rules[0].red_points == null || all_rules[0].red_points == ''){
            $('.error-message').show();
            return false;
        }

        $('#rule_data').val(JSON.stringify(all_rules));
        return true;
    }

    function saveRule(){
        $('.error-message').hide();
        if (!checkValidation()) return;
        $('#form_pit_rule').submit();
    }
    $(document).ready(function() {
        $('.save-btn').click(function(){
            saveRule();
        });
        $('.clear-btn').click(function(){
            red_points = [];
            point_numbers = 0;
            layer.find('Circle').map(circle_item => {
                circle_item.destroy();
            })
            layer.find('Line').map(line_item => {
                line_item.destroy();
            })
            layer.draw();
            if (all_rules.length > 0){
                all_rules[0].red_points = null;
                all_rules[0].blue_points = null;
            }
        });
        drawing();
        if (all_rules.length > 0){
            selected_rule = all_rules[0];
            if (selected_rule.red_points != null && selected_rule.red_points != ''){
                red_points =  selected_rule.red_points;
                red_points.map((center_point, point_index) => {
                    drawCircle(center_point, point_index);
                })
                if (selected_rule.blue_points != null && selected_rule.blue_points != ''){
                    selected_rule.blue_points.map((center_point, point_index) => {
                        drawCircle(center_point, point_index, 'blue');
                    })
                }
                point_numbers = 4;
                drawRect(red_points, selected_rule.blue_points);
            }
        }
    });
</script>

