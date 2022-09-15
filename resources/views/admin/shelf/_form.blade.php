<?php
    $max_figure_numbers = config('const.shelf_max_rect_numbers');
    $login_user = Auth::guard('admin')->user();
    $super_admin_flag = ($login_user->authority_id == config('const.super_admin_code'));
    $color = null;
    $points = array();
    $hour = '';
    $mins = '';
    foreach ($rules as $index => $rule) {
        if ($rule->points != null && $rule->points != '') {
            $rule->points = json_decode($rule->points);
            if (isset($rule->hour) && $rule->hour !== '') {
                $hour = $rule->hour;
            }
            if (isset($rule->mins) && $rule->mins !== '') {
                $mins = $rule->mins;
            }
            $rule->drawn_flag = true;
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
        <h3>検知設定</h3>
        <div class="setting-head">
            <div id="rule_items">
                <p class="close-items"><a class="close-icon">×</a></p>
                <div class="radio-area">
                    <input id="radio-rect" type="radio" value="0" checked>
                    <label for="radio-rect" class="radio-label radio-label-rect">四角形</label>
                    <input id="radio-polygon" type="radio" value="1">
                    <label for="radio-polygon" class="radio-label radio-label-polygon">多角形</label>
                </div>
                <div class="title-div">カラー</div>
                <div class="content-div"><input type="color" class="color" value=""/><button type="button" class="draw-btn disabled-btn">矩形描く</button></div>
            </div>
            <div class="time-wrap">
                <h4 style="font-size: 14px;white-space:nowrap;margin-bottom:5px;">定時撮影時刻を入力</h4>
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
                <p style="font-size: 13px;"><small>※指定時刻の陳列を正常とし検知します</small></p>
            </div>
            <div id="rule_item_area">
                @foreach ($rules as $index=>$rule)
                <div class="rule_items" data-index = {{$index}}>
                    <p class="close-items"><a class="close-icon" onclick="removeFigure({{$index}})">×</a></p>
                    <div class="radio-area">
                        @if(count($rule->points) == 4)
                            <input disabled = {{$index == count($rules) - 1 && count($rules) < $max_figure_numbers ? true : false }}
                                id={{"radio-rect_".$index}} name={{'figure_type_'.$index}} type="radio" value="0" onchange="changeFigure(this, '{{$index}}')" checked>
                            <label for={{"radio-rect_".$index}} class="radio-label">四角形</label>
                            <input disabled = {{$index == count($rules) - 1 && count($rules) < $max_figure_numbers ? true : false }}
                                id={{"radio-polygon_".$index}} name={{'figure_type_'.$index}} type="radio" value="1" onchange="changeFigure(this, '{{$index}}')">
                            <label for={{"radio-polygon_".$index}} class="radio-label">多角形</label>
                        @else
                            <input disabled = {{$index == count($rules) - 1 && count($rules) < $max_figure_numbers ? true : false }}
                                id={{"radio-rect_".$index}} name={{'figure_type_'.$index}} type="radio" value="0" onchange="changeFigure(this, '{{$index}}')">
                            <label for={{"radio-rect_".$index}} class="radio-label">四角形</label>
                            <input disabled = {{$index == count($rules) - 1 && count($rules) < $max_figure_numbers ? true : false }}
                                id={{"radio-polygon_".$index}} name={{'figure_type_'.$index}} type="radio" value="1" onchange="changeFigure(this, '{{$index}}')" checked>
                            <label for={{"radio-polygon_".$index}} class="radio-label">多角形</label>
                        @endif

                    </div>
                    <div class="title-div">カラー</div>
                    <div class="content-div">
                        <input onchange="changeColor(this, '{{$index}}')" type="color" class="color" value="{{isset($rule->color) ? $rule->color:'#000000'}}"/>
                        <button type="button" class="draw-btn disabled-btn">矩形描く</button>
                    </div>
                </div>
                @endforeach
            </div>
            <div class="add-figure-area">
                <button type="button" onclick="addNewFigure()" class="{{count($rules) < $max_figure_numbers ? 'draw-btn add-btn' : 'disabled-btn draw-btn add-btn' }}">検知設定を追加</button>
                <div class="balloon_danger">
                    <p>画像内をクリックし矩形を選択してください。</p>
                </div>
            </div>
        </div>

        <div class="n-area2">
            <div class="video-area" style="width:100%;">
                <div id="image-container" class="camera-image" style="background: url('{{$camera_image_data}}') no-repeat;"></div>
                <p class="error-message area" style="display: none">エリアを選択してください。</p>
                <div class="description">点をドラッグすることでサイズを変更することが出来ます。<div id="debug"></div></div>
                @if(!$super_admin_flag)
                    <div class="btns" id="direction">
                        <button type="button" onclick="clearImage()" class="edit clear-btn history">選択をクリア</button>
                        <button type="button" onclick="saveRule()" class="ok save-btn">決定</button>
                    </div>
                @endif
            </div>
        </div>
    </div>
    <input type="hidden" value="" name="rule_data" id = 'rule_data'/>
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
    .setting-head{
        padding-left:15px;
        display:flex;
    }
    .time-wrap{
        margin-right:15px;
    }
    .add-figure-area{
        position: relative;
        margin-top:45px;
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
    .draw-btn{
        background: linear-gradient(-135deg, #3178dd, #3178dd);
        font-size: 13px;
        color: #FFF;
        border: none;
        border-radius: 50px;
        letter-spacing: 5px;
        padding-left: 12px;
        padding-right: 12px;
        padding-top: 2px;
        padding-bottom: 2px;
        margin-left: 10px;
    }
    .add-btn{
        background: #CC0000;
        white-space: nowrap;
    }
    #rule_items{
        display: none;
    }
    .rule_items{
        margin-bottom: 5px;
        position: relative;
        padding-top:7px;
        margin-right: 30px;
        border-right: 1px solid gray;
        padding-right: 15px;
    }
    #rule_item_area{
        font-size: 12px;
        display: flex;
    }
    .disabled-btn{
        background: gray;
        cursor: default;
    }
    .close-items{
        position: absolute;
        right:0px;
        top:-5px;
    }
    .close-icon{
        font-size:16px;
        font-weight: bold;
        cursor: pointer;
    }
    .radio-label{
        padding-top: 4px;
        padding-right: 10px;
        padding-left:13px;
        font-size: 15px;
        white-space: nowrap;
    }
    .radio-label:before{
        margin-top:10px!important;
    }
    .radio-label:after{
        margin-top:5px!important;
    }
    .radio-area{
        height: 30px;
        margin-left: 10px;
        white-space: nowrap;
    }
    .balloon_danger{
        display: none;
        position: absolute;
        left:50%;
        transform: translateX(-50%);
        margin: 0 0;
        padding: 7px 10px;
        min-width: 180px;
        /* max-width: 100%; */
        color: #555;
        font-size: 12px;
        background: #FFF;
        top: 0px;
        margin-top:120px;
        box-shadow:0 0 10px #999;
        border-radius:5px;
        animation: ba 1s ease-in-out infinite;
    }
    .balloon_danger:before{
        content: "";
        position: absolute;
        top: 100%;
        left: 50%;
        margin-left: -10px;
        border: 10px solid transparent;
    }
    @media only screen and (max-width:768px) {
        .btns{
            display: flex;
        }
        .clear-btn{
            padding:15px 60px;
        }
        .save-btn{
            padding:15px 60px;
        }
        #rule_item_area{
            display: block;
        }
        .rule_items{
            border-bottom: 1px solid gray;
            border-right:none;
            padding-bottom: 10px;
        }
        .setting-head{
            display:block;
        }
        .add-figure-area{
            margin-top:10px;
        }
    }
</style>
<script src="{{ asset('assets/admin/js/konva.js?2') }}"></script>

<script>
    var stage = null;
    var layer = null;
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
    var max_figure_numbers = "<?php echo $max_figure_numbers;?>";
    max_figure_numbers = parseInt(max_figure_numbers);
    var enable_add_figure_flag = true;
    var selected_figure = 'rect';
    var radius = "<?php echo config('const.camera_mark_radius');?>";
    radius = parseInt(radius);
    var unique_rule_id = null;
    var rules = <?php echo json_encode($rules);?>;
    var rules_object = {};
    rules.map((rule_item, rule_index) => {
        rules_object[rule_index] = rule_item;
        if (unique_rule_id == null) {
            unique_rule_id = 0;
        } else {
            unique_rule_id++;
        }
    })

    function getInput(e, param){
        if (param == 'hour'){
            hour = e.value;
        }
        if (param == 'mins'){
            mins = e.value;
        }
    }
    function isLeft(p0, a, b) {
        return (a.x-p0.x)*(b.y-p0.y) - (b.x-p0.x)*(a.y-p0.y);
    }

    function distCompare(p0, a, b) {
        var distA = (p0.x-a.x)*(p0.x-a.x) + (p0.y-a.y)*(p0.y-a.y);
        var distB = (p0.x-b.x)*(p0.x-b.x) + (p0.y-b.y)*(p0.y-b.y);
        return distA - distB;
    }

    function angleCompare(p0, a, b) {
        var left = isLeft(p0, a, b);
        if (left == 0) return distCompare(p0, a, b);
        return left;
    }
    function sortFigurePoints(figure_points) {

        figure_points = figure_points.splice(0);
        var p0 = {};
        p0.y = Math.min.apply(null, figure_points.map(p=>p.y));
        p0.x = Math.max.apply(null, figure_points.filter(p=>p.y == p0.y).map(p=>p.x));
        figure_points.sort((a,b)=>angleCompare(p0, a, b));
        return figure_points;
    };

    function drawCircle(center_point, point_index, figure_color, rule_index){
        if (!(Object.keys(rules_object).length > 0)) return;
        var circle = new Konva.Circle({
            x: center_point.x,
            y: center_point.y,
            radius: radius,
            fill: figure_color,
            stroke: figure_color,
            strokeWidth: 1,
            draggable:true,
            id: rule_index.toString() + '_' + point_index.toString()
        });
        circle.on('mouseenter', function () {
            stage.container().style.cursor = 'pointer';
        });
        circle.on('mouseleave', function () {
            stage.container().style.cursor = 'default';
        });
        circle.on('dragmove', function (e) {
            if (e.evt.offsetX <= 5 || e.evt.offsetX >= 1275) {
                circle.stopDrag();
                return;
            }
            if (e.evt.offsetY <= 5 || e.evt.offsetY >= 715) {
                circle.stopDrag();
                return;
            }
            var circle_id = e.target.id();
            var rule_index = parseInt(circle_id.split('_')[0]);
            if (!isNaN(rule_index)){
                var index = rules_object[rule_index].points.findIndex(x => x.id == circle_id);
                if (index > -1){
                    rules_object[rule_index].points[index].x = e.evt.offsetX;
                    rules_object[rule_index].points[index].y = e.evt.offsetY;
                    if (enable_add_figure_flag == true || rule_index < Math.max(...Object.keys(rules_object))){
                        drawFigure(rule_index, rules_object[rule_index].color);
                    }
                }
            }
        })
        layer.add(circle);
    }


    function drawFigure(rule_index, figure_color = null){
        if (!(Object.keys(rules_object).length > 0)) return;
        if (rules_object[rule_index].points == undefined || rules_object[rule_index].points.length < 3) return;
        layer.find('Line').map(line_item => {
            if (line_item.attrs.id == rule_index){
                line_item.destroy();
            }
        });
        var figure_points = sortFigurePoints(rules_object[rule_index].points);
        var drawing_point_data = [];
        figure_points.map(item => {
            drawing_point_data.push(item.x);
            drawing_point_data.push(item.y);
        });
        drawing_point_data.push(figure_points[0].x);
        drawing_point_data.push(figure_points[0].y);
        var figure_area = new Konva.Line({
            points: drawing_point_data,
            stroke: figure_color != null ? figure_color : 'black',
            strokeWidth: radius - 3 > 0? radius - 3 : 2,
            lineCap: 'round',
            lineJoin: 'round',
            id:rule_index.toString(),
        });
        layer.add(figure_area);
        enable_add_figure_flag = true;
        if (Object.keys(rules_object).length < max_figure_numbers) $('.add-btn').removeClass('disabled-btn');
        rules_object[rule_index].points =  figure_points;
        rules_object[rule_index].drawn_flag = true;
        $('.balloon_danger').hide();
    }

    function changeFigure(e){
        if (e.value == 0){
            selected_figure = 'rect';

        } else {
            selected_figure = 'polygon';
        }
    }

    function changeColor(e, rule_index){
        layer.find('Circle').map(circle_item => {
            if (circle_item.attrs.id.includes(rule_index + '_')){
                circle_item.fill(e.value);
                circle_item.stroke(e.value);
            }
        })
        layer.find('Line').map(line_item => {
            if (line_item.attrs.id == rule_index){
                line_item.stroke(e.value);
            }
        })
        rules_object[rule_index].color = e.value;
    }

    function removeFigure(rule_index){
        $('[data-index="'+ rule_index + '"]').remove();
        layer.find('Line').map(line_item => {
            if (line_item.attrs.id == rule_index){
                line_item.destroy();
            }
        });
        layer.find('Circle').map(circle_item => {
            if (circle_item.attrs.id.includes(rule_index + '_')){
                circle_item.destroy();
            }
        })
        $('.add-btn').removeClass('disabled-btn');
        if (rule_index == Math.max(...Object.keys(rules_object))){
            $('.balloon_danger').hide();
            enable_add_figure_flag = true;
        }

        delete rules_object[rule_index];
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
            if (!(Object.keys(rules_object).length > 0)) return;
            if (rules_object[unique_rule_id] == undefined) return;
            if (rules_object[unique_rule_id].points == undefined) return;
            if (enable_add_figure_flag == true) return;
            var point_index = rules_object[unique_rule_id].points.length;
            var figure_color = rules_object[unique_rule_id].color != undefined ? rules_object[unique_rule_id].color :"black";
            drawCircle({x:e.evt.offsetX, y:e.evt.offsetY}, point_index, figure_color, unique_rule_id);
            rules_object[unique_rule_id].points.push({x:e.evt.offsetX, y:e.evt.offsetY, id:(unique_rule_id).toString() + '_' + point_index.toString()});
            if (point_index + 1 == 4 && selected_figure == 'rect'){
                drawFigure(unique_rule_id, figure_color);
            }
        })
        stage.on('mousemove', function(e){
            document.getElementById("debug").innerHTML = `X座標${e.evt.offsetX}:Y座標${e.evt.offsetY}`;
        })
    }

    function clearImage(){
        layer.find('Circle').map(circle_item => {
            circle_item.destroy();
        })
        layer.find('Line').map(line_item => {
            line_item.destroy();
        })
        layer.draw();
        $('.rule_items').remove();
        $('.add-btn').removeClass('disabled-btn');
        $('.balloon_danger').hide();
        enable_add_figure_flag = true;
        // rules = [];
        rules_object = {};

    }

    function checkValidation(){
        var res = true;
        var check_time_flag = false;
        if (mins === '') check_time_flag = true;
        if (hour === '') check_time_flag = true;
        if (hour > 23) check_time_flag = true;
        if (mins > 59) check_time_flag = true;
        if (check_time_flag == true){
            $('.error-message.time').show();
            res = false;
        }
        if (!(Object.keys(rules_object).length > 0)){
            $('.error-message.area').show();
            res = false;
        }
        Object.keys(rules_object).map(rule_index => {
            var rule_item = rules_object[rule_index];
            if (rule_item.points == undefined || rule_item.points.length < 3 || rule_item.drawn_flag != true){
                $('.error-message.area').show();
                res = false;
            }
        })
        return res;
    }

    function saveRule(){
        $('.error-message').hide();
        if (!checkValidation()) return;
        $('#rule_data').val(JSON.stringify(rules_object));
        $('#form_shelf_rule').submit();
    }

    function addNewFigure(){
        if (Object.keys(rules_object).length >= max_figure_numbers) return;
        if (enable_add_figure_flag != true) return;
        if (unique_rule_id == null){
            unique_rule_id = 0;
        } else {
            unique_rule_id++;
        }
        $('.rule_items .draw-btn').each(function(){
            $(this).addClass('disabled-btn');
        });
        $('.rule_items input[type="radio"]').each(function(){
            $(this).prop('disabled', true);
        })
        var template_item = $('#rule_items').clone();
        template_item.attr('id', '');
        template_item.addClass('rule_items');
        template_item.attr('data-index', unique_rule_id);
        template_item.show();
        $('#rule_item_area').append(template_item);
        $('#radio-rect', template_item).attr('name', 'figure_type_' + unique_rule_id);
        $('#radio-rect', template_item).attr('id', 'radio-rect_' + unique_rule_id);
        $('#radio-polygon', template_item).attr('name', 'figure_type_' + unique_rule_id);
        $('#radio-polygon', template_item).attr('id', 'radio-polygon_' + unique_rule_id);
        $('.radio-label-rect', template_item).attr('for', 'radio-rect_' + unique_rule_id);
        $('.radio-label-polygon', template_item).attr('for', 'radio-polygon_' + unique_rule_id);
        rules_object[unique_rule_id] = {
            points:[],
            color:'#000000',
        };
        $('.add-btn').addClass('disabled-btn');
        $('.balloon_danger').show();
        enable_add_figure_flag = false;
        selected_figure = 'rect';
        $('.close-icon', template_item).click(function(){
            var rule_index = template_item.attr('data-index');
            $('[data-index="'+ rule_index + '"]').remove();
            layer.find('Line').map(line_item => {
                if (line_item.attrs.id == rule_index){
                    line_item.destroy();
                }
            });
            layer.find('Circle').map(circle_item => {
                if (circle_item.attrs.id.includes(rule_index + '_')){
                    circle_item.destroy();
                }
            });
            $('.add-btn').removeClass('disabled-btn');
            if (rule_index == Math.max(...Object.keys(rules_object))){
                $('.balloon_danger').hide();
                enable_add_figure_flag = true;
            }
            delete rules_object[rule_index];
        });
        $('input', $('.radio-area', template_item)).change(function(){
            if ($(this).val() == 0){
                selected_figure = 'rect';
                $('.draw-btn', template_item).addClass('disabled-btn');
            } else {
                selected_figure = 'polygon';
                $('.draw-btn', template_item).removeClass('disabled-btn');
            }
        });
        $('.draw-btn', template_item).click(function(){
            var rule_index = template_item.attr('data-index');
            if (unique_rule_id == rule_index && selected_figure != 'rect'){
                if (rules_object[rule_index].points != undefined && rules_object[rule_index].points.length > 2 && rules_object[rule_index].drawn_flag != true){
                    var figure_color = rules_object[rule_index].color != undefined ? rules_object[rule_index].color :"black";
                    drawFigure(rule_index, figure_color);
                    $(this).addClass('disabled-btn');
                    $('input[type="radio"]', template_item).prop('disabled', true);
                }

            }
        });
        $('.color', template_item).change(function(){
            var rule_index = template_item.attr('data-index');
            layer.find('Circle').map(circle_item => {
                if (circle_item.attrs.id.includes(rule_index + '_')){
                    circle_item.fill($(this).val());
                    circle_item.stroke($(this).val());
                }
            });
            layer.find('Line').map(line_item => {
                if(line_item.attrs.id == rule_index){
                    line_item.stroke($(this).val());
                }
            });
            rules_object[rule_index].color = $(this).val();
        });
    }

    $(document).ready(function() {
        drawingStage();
        Object.keys(rules_object).map(rule_index => {
            var rule_item = rules_object[rule_index];
            if (rule_item.points != undefined && rule_item.points.length > 0){
                drawFigure(rule_index, rule_item.color);
                rule_item.points.map((center_point, point_index) => {
                    drawCircle(center_point, point_index, rule_item.color, rule_index);
                    rule_item.points[point_index].id = (rule_index).toString() + '_' + point_index.toString();
                })
            }
        })
    });
</script>
