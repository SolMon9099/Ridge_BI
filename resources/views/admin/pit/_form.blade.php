<?php
    $action_options = config('const.action');
    $login_user = Auth::guard('admin')->user();
    $super_admin_flag = ($login_user->authority_id == config('const.super_admin_code'));
    $red_points = array();
    $blue_points = array();
    $max_permission_time = null;
    $min_members = null;
    $name = null;
    foreach ($rules as $key => $rule) {
        if ($rule->red_points != null && $rule->red_points != '') $red_points = json_decode($rule->red_points);
        if ($rule->blue_points != null && $rule->blue_points != '') $blue_points = json_decode($rule->blue_points);
        $max_permission_time = $rule->max_permission_time;
        $min_members = $rule->min_members;
        $name = $rule->name;
    }
?>
<link href="{{ asset('assets/vendor/jquery-ui/jquery-ui.min.css') }}" rel="stylesheet">
<div class="no-scroll">
    {{-- @include('admin.layouts.flash-message') --}}
    <div class="scroll" style="position: relative;">
        <h2>検知設定</h2>
        @if(isset($view_only))
            <label style="margin-right: 20px"><span class="title-span">ルール名</span>：{{isset($name)?$name:''}}</label>
            <label style="margin-right: 20px"><span class="title-span">ピット内人数</span>：{{isset($min_members)?$min_members.'人以上':''}}</label>
            <label style="margin-right: 20px"><span class="title-span">アラート対象滞在時間</span>：{{isset($max_permission_time)?$max_permission_time.'分を超えた時':''}}</label>
        @else
            <div class="guide">
                <p>動画内をクリックしピットの入り口を囲ってください。</p>
                <p>　※4点をクリックすると4角形が自動生成されます。</p>
                <p>　※赤枠は入場を検知し青枠は退場を検知します。</p>
            </div>
            <div class="setting-head">
                <input type="hidden" name="change_flag" id="change_flag" value=""/>
                <div style="margin-right:20px;">
                    <label>ルール名：</label>
                    <input name="name" class='name_input' type="text" value="{{old('name', isset($name)?$name:'')}}">
                    @error('name')
                        <p class="error-message name">{{ $message }}</p>
                    @enderror
                </div>
                <div style="margin-right:30px;">
                    <label>ピット内人数：</label>
                    <input name="min_members" type="number" inputmode="numeric" pattern="\d*" max='10' min='0' class='members_input'
                        value="{{old('min_members', isset($min_members)?$min_members:'')}}">
                        <span style="font-size: 14px;">人以上</span>
                    @error('min_members')
                        <p class="error-message min_members">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label>アラート対象滞在時間：</label>
                    <select name = 'max_permission_time' class="select-box" style="width:85px;margin-right:0px;">
                        <option value=''></option>
                        @foreach(config('const.pit_time_options') as $time)
                            @if (old('max_permission_time', isset($max_permission_time)?$max_permission_time:'') == $time)
                                <option selected value={{$time}}>{{$time}}分</option>
                            @else
                                <option value={{$time}}>{{$time}}分</option>
                            @endif
                        @endforeach
                    </select>
                    <span style="font-size: 14px;">を超えた時</span>
                    @error('max_permission_time')
                        <p class="error-message max_permission_time">{{ $message }}</p>
                    @enderror
                </div>
                <div class="ai-guide-area" style="">
                    <input onchange="changeHeatMap(this)" class="tgl tgl-flat" id="ai_guide" type="checkbox">
                    <label class="tgl-btn" for="ai_guide"></label>
                    <label style="margin-left:5px;padding-top:3px;">AI検知精度ガイド</label>
                </div>
                <div class="reset-heatmap-area">
                    <button onclick="resetHeatMap({{$camera_id}})" type='button' class="reset-heatmap-button">再取得</button>
                </div>
                <button type="button" class="clear-btn history" onclick="clearImage()">選択を全てクリア</button>
            </div>
            <div class="notice-area">※カメラの位置や画角を変更した場合は、AI検知精度の再取得を行う必要がありますので、「再取得」ボタンを押してください。</div>
        @endif

        <div class="n-area2">
            <div class="video-area" style="width:100%;">
                <div class="grid-area">
                    @for ($i = 0; $i < 72; $i++)
                        @for($j = 0; $j < 128; $j++)
                            <div class='{{"grid grid_".$i."_".$j}}'></div>
                        @endfor
                    @endfor
                </div>
            <div id="image-container" class="camera-image" style="background: url('{{asset('storage/').'/'.$camera_image_path}}') no-repeat;"></div>
            <p class="error-message area" style="display: none">エリアを選択してください。</p>
            <div id="debug"></div>
            @if(!$super_admin_flag && !isset($view_only))
            <div class="btns" id="direction">
                <button type="button" class="ok save-btn" onclick="saveRule()">決定</button>
            </div>
            @endif
        </div>
        <div class="loader" style="display: none"></div>
    </div>
</div>
<input type="hidden" value="" name="red_points_data" id = 'red_points_data'/>
<input type="hidden" value="" name="blue_points_data" id = 'blue_points_data'/>
<!--MODAL -->
<div id="howto" class="modal-content">
    <div class="textarea">
        <div class="explain">
            <h3>【使い方】</h3>
            <ul>
                <li>①カメラ映像をクリックしピットの入り口を囲ってください。
                    <small>※4点をクリックすると4角形が自動生成されます。</small>
                    <small>※赤枠は入場を検知し青枠は退場を検知します。</small>
                    <small>※赤枠及び青枠については点の部分をクリックしながら移動させることで調整できます。</small>
                    <small>　(赤枠と青枠の間に一人分の体が入る距離を目安として適宜調整してください。)</small>
                    <small>※赤枠・青枠のエリア調整は「赤枠→青枠」の順に調整してください。</small>
                </li>
                <li>②検知設定として「ピット内人数」　「アラート対象滞在時間」を指定してください。
                    <small>※何人以上の滞在が何分続いたという検知を行います。</small>
                </li>
                <li>③設定が完了したら「決定」ボタンを押下してください。
                    <small>※「選択を全てクリア」をクリックすると4角形の選択がクリアされます。</small>
                </li>
                <li>④AI検知精度
                    <small>AI検知精度をONにするとカメラ映像内の検知精度が表示されます。</small>
                    <small>　※下記の色で精度が高い順に表示されます。</small>
                    <small>　　精度高　<span style="color:#FF0000">■</span>＞
                        <span style="color:#FFFF00">■</span>＞
                        <span style="color:#008000">■</span>＞
                        <span style="color:#00FFFF">■</span>＞
                        <span style="color:#0000FF">■</span>　精度低</small>
                </li>
            </ul>
        </div>
    </div>
    <p class="closemodal"><a class="modal-close">×</a></p>
</div>
<!-- -->
<div id="dialog-confirm" title="test" style="display:none">
    <p><span class="ui-icon ui-icon-alert" style="float:left; margin:12px 12px 20px 0;"></span>
        <span id="confirm_text">These items will be permanently deleted and cannot be recovered. Are you sure?</span></p>
</div>
<style>
    .clear-btn{
        margin:0;
        padding: 10px 30px;
        position: absolute;
        right:0;
        top:45px!important;
    }
    .guide{
        position: absolute;
        left: 450px;
        top: -70px;
        white-space: nowrap;
    }
    label{
        white-space: nowrap;
    }
    .cancel-btn{
        margin:0;
        margin-right:15px;
        padding: 15px 75px;
    }
    .image-record{
        display: none;
    }
    .grid-area{
        width:1280px;
        height:720px;
        background-color:transparent;
        position: absolute;
        display: grid;
        grid-template-columns: repeat(128, 10px);
    }
    .grid-area > div{
        opacity: 0;
        background: lightgray;
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
    .disc-list-style{
        list-style: disc;
    }
    #debug{
    }
    .notice-area{
        color:#999;
    }
    .name_input{
        background: white!important;
        width:120px!important;
    }
    .members_input{
        background: white!important;
        width:60px!important;
    }
    .setting-head{
        display: flex;
        position: relative;
        white-space: nowrap;
    }
    .ai-guide-area{
        margin-left: 10px;
        padding-top:8px!important;
        display:inline-flex;
    }
    .reset-heatmap-area{
        margin-left:8px;
        padding-top:6px;
    }
    @media only screen and (max-width:768px) {
        .btns{
            display: flex;
        }
        .clear-btn{
            position: relative;
        }
        .guide{
            position: relative;
            top:0;
            left:0;
            margin-top:10px;

        }
        .save-btn{
            padding:15px 60px;
        }
        .setting-head{
            display: block;
        }
        .ai-guide-area{
            margin-left: 0px;
        }
    }
</style>
<script src="{{ asset('assets/admin/js/konva.js?2') }}"></script>
<script src="{{ asset('assets/vendor/jquery-ui/jquery-ui.min.js') }}"></script>
<script>
    var mouse_pos = null;
    var heatmap_records = [];
    var stage = null;
    var layer = null;
    var radius = "<?php echo config('const.camera_mark_radius');?>";
    var view_only = "<?php echo isset($view_only) ? 1: 0;?>";
    view_only = parseInt(view_only);
    radius = parseInt(radius);
    var red_points = <?php echo json_encode($red_points);?>;
    var blue_points = <?php echo json_encode($blue_points);?>;
    var point_numbers = 0;
    if (red_points.length == 4){
        point_numbers = 4;
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
            var x_delta = parseInt(expands * x_diff/Math.sqrt(Math.pow(x_diff, 2) + Math.pow(y_diff,2)));
            var y_delta = parseInt(expands * y_diff/Math.sqrt(Math.pow(x_diff, 2) + Math.pow(y_diff,2)));
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
            draggable:view_only == 1 ? false : true,
            id:color != null ? color + '_' + point_index : point_index
        });
        circle.on('mouseenter', function () {
            stage.container().style.cursor = 'pointer';
        });
        circle.on('mouseleave', function () {
            stage.container().style.cursor = 'default';
        });
        circle.on('dragmove', function (e) {
            var new_x = e.evt.offsetX;
            var new_y = e.evt.offsetY;
            var point_index = red_points.findIndex(point => point.id == e.target.id());
            var delta = null;
            if (point_index > -1){
                delta = Math.abs(red_points[point_index].x - new_x) + Math.abs(red_points[point_index].y - new_y);
            }
            if (color != null){
                point_index = blue_points.findIndex(point => point.id == e.target.id());
                delta = Math.abs(blue_points[point_index].x - new_x) + Math.abs(blue_points[point_index].y - new_y)
            }
            if (mouse_pos.x >= window.innerWidth - 30 || mouse_pos.x <= 230 || mouse_pos.y <= 20 || mouse_pos.y >= window.innerHeight - 30 || delta > 150) {
                if (color == null){
                    circle.absolutePosition({x:red_points[point_index].x, y:red_points[point_index].y});
                } else {
                    circle.absolutePosition({x:blue_points[point_index].x, y:blue_points[point_index].y});
                }
                circle.stopDrag();
                return;
            }
            if (e.evt.offsetX <= 10 || e.evt.offsetX >= 1270) {
                circle.stopDrag();
                new_x = e.evt.offsetX <=10 ? 13 : 1267;
                circle.absolutePosition({x:new_x, y:e.evt.offsetY});
                // return;
            }
            if (e.evt.offsetY <= 10 || e.evt.offsetY >= 710) {
                circle.stopDrag();
                new_y = e.evt.offsetY<=10?13:707;
                circle.absolutePosition({x:e.evt.offsetX, y:new_y});
                // return;
            }
            if (color == null){
                var index = red_points.findIndex(point => point.id == e.target.id());
                if (index > -1){
                    red_points[index].x = new_x;
                    red_points[index].y = new_y;
                    if (point_numbers == 4){
                        drawRect(red_points);
                        $('#change_flag').val('updated');
                    }
                }
            } else {
                index = blue_points.findIndex(point => point.id == e.target.id());
                if (index > -1){
                    blue_points[index].x = new_x;
                    blue_points[index].y = new_y;
                    if (point_numbers == 4){
                        drawRect(red_points, blue_points);
                        $('#change_flag').val('updated');
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
            strokeWidth: 2,
            lineCap: 'round',
            lineJoin: 'round',
        });
        if (blue_data == null || blue_data == ''){
            blue_points = getExpandRectanglePoints(rect_points, 120);
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
            strokeWidth: 2,
            lineCap: 'round',
            lineJoin: 'round',
        });
        layer.add(red_rect_area);
        layer.add(blue_rect_area);
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
                draggable:view_only == 1 ? false : true,
                id:point_numbers
            });
            circle.on('mouseenter', function () {
                stage.container().style.cursor = 'pointer';
            });
            circle.on('mouseleave', function () {
                stage.container().style.cursor = 'default';
            });
            circle.on('dragmove', function (e) {
                var new_x = e.evt.offsetX;
                var new_y = e.evt.offsetY;
                var point_index = red_points.findIndex(point => point.id == e.target.id());
                var delta = null;
                if (point_index > -1){
                    delta = Math.abs(red_points[point_index].x - new_x) + Math.abs(red_points[point_index].y - new_y);
                }
                if (mouse_pos.x >= window.innerWidth - 30 || mouse_pos.x <= 230 || mouse_pos.y <= 20 || mouse_pos.y >= window.innerHeight - 30 || delta > 150) {
                    circle.absolutePosition({x:red_points[point_index].x, y:red_points[point_index].y});
                    circle.stopDrag();
                    return;
                }
                if (e.evt.offsetX <= 10 || e.evt.offsetX >= 1270) {
                    circle.stopDrag();
                    new_x = e.evt.offsetX <=10 ? 13 : 1267;
                    circle.absolutePosition({x:new_x, y:e.evt.offsetY});
                    // return;
                }
                if (e.evt.offsetY <= 10 || e.evt.offsetY >= 710) {
                    circle.stopDrag();
                    new_y = e.evt.offsetY<=10?13:707;
                    circle.absolutePosition({x:e.evt.offsetX, y:new_y});
                    // return;
                }
                var index = red_points.findIndex(point => point.id == e.target.id());
                if (index > -1){
                    red_points[index].x = new_x;
                    red_points[index].y = new_y;
                    if (point_numbers == 4){
                        drawRect(red_points);
                        $('#change_flag').val('updated');
                    }
                }

            })
            layer.add(circle);
            red_points.push({x:e.evt.offsetX, y:e.evt.offsetY, id:point_numbers})
            point_numbers++;
            if (point_numbers == 4){
                drawRect(red_points);
                $('#change_flag').val('updated');
            }

        })
        stage.on('mousemove', function(e){
            document.getElementById("debug").innerHTML = `X座標${e.evt.offsetX}:Y座標${e.evt.offsetY}`;
        })
    }

    function checkValidation(){
        if (red_points.length != 4){
            $('.error-message').show();
            return false;
        }

        $('#red_points_data').val(JSON.stringify(red_points));
        $('#blue_points_data').val(JSON.stringify(blue_points));
        return true;
    }

    function saveRule(){
        $('.error-message').hide();
        if (!checkValidation()) return;
        $('#form_pit_rule').submit();
    }
    function clearImage(){
        red_points = [];
        point_numbers = 0;
        layer.find('Circle').map(circle_item => {
            circle_item.destroy();
        })
        layer.find('Line').map(line_item => {
            line_item.destroy();
        })
        layer.draw();
        $('#red_points_data').val('');
        $('#blue_points_data').val('');
    }

    function changeHeatMap(e){
        $('#ai_guide').attr('disabled', true);
        if (e.checked){
            setTimeout(() => {
                $('.video-area').hide();
                $('.loader').show();
                $('.grid').css('opacity', 0.5);
            }, 100);
            setTimeout(() => {
                for (var i = 0; i < 72; i++){
                    for(var j=0; j<128; j++){
                        var score = null;
                        var count = 0;
                        if (heatmap_records != undefined && heatmap_records.length > 0){
                            heatmap_records.map(heat_item => {
                                if (heat_item.heatmap_data != null && heat_item.heatmap_data.length > 0){
                                    if (!isNaN(parseInt(heat_item.heatmap_data[i][j]))){
                                        if (score === null) score = 0;
                                        score += parseFloat(heat_item.heatmap_data[i][j]);
                                        count++;
                                    }
                                }
                            })
                        }
                        if (count > 0) score = score / count;
                        // $('.grid_' + i + '_' + j).css('opacity', calcOpacity(score));
                        $('.grid_' + i + '_' + j).css('background-color', calcOpacity(score));
                    }
                }
                $('.video-area').show();
                $('.loader').hide();
                $('#ai_guide').attr('disabled', false);
            }, 500);
        } else {
            setTimeout(() => {
                $('.video-area').hide();
                $('.loader').show();
            }, 100);
            setTimeout(() => {
                $('.grid').css('opacity', 0);
                $('.video-area').show();
                $('.loader').hide();
                $('#ai_guide').attr('disabled', false);
            }, 500);
        }
    }
    function getHeadtMapData(){
        jQuery.ajax({
            url : '/admin/camera/getHeatmapData',
            method: 'post',
            data: {
                camera_id:'<?php echo $device_id;?>',
                _token:$('meta[name="csrf-token"]').attr('content'),
            },
            error : function(){
                console.log('failed');
            },
            success: function(result){
                console.log(result);
                heatmap_records = result;
            }
        });
    }
    function handleMouseMove(event){
        event = event || window.event;
        mouse_pos = {x:event.clientX, y:event.clientY};
    }

    function resetHeatMap(camera_id){
        helper_confirm("dialog-confirm", "再取得確認", "AI検知精度ガイドを再取得しますがよろしいですか？<br/>＜注意＞<br/>　・現在のAI検知精度ガイドはリセットされます<br/>　・AI検知精度の再取得まではお時間がかかります（最大１日程度）", 600, "再取得", "キャンセル", function(){
            resetHeatMapAjax(camera_id);
        });
    }

    $(document).ready(function() {
        document.onmousemove = handleMouseMove;
        getHeadtMapData();
        drawing();
        if (red_points != null && red_points.length == 4){
            red_points.map((center_point, point_index) => {
                drawCircle(center_point, point_index);
            })
            if (blue_points != null && blue_points.length == 4){
                blue_points.map((center_point, point_index) => {
                    drawCircle(center_point, point_index, 'blue');
                })
            }
            drawRect(red_points, blue_points);
        }
        $('.grid-area').css('margin-left', $('.video-area').width() - 1280 > 0 ? ($('.video-area').width() - 1280)/2 : 0);
        window.addEventListener('resize', function(event) {
            $('.grid-area').css('margin-left', $('.video-area').width() - 1280 > 0 ? ($('.video-area').width() - 1280)/2 : 0);
        }, true);
    });
</script>

