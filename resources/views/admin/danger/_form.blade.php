<?php
    $max_figure_numbers = config('const.danger_max_figure_numbers');
    $action_options = config('const.action');
    $login_user = Auth::guard('admin')->user();
    $super_admin_flag = ($login_user->authority_id == config('const.super_admin_code'));
    $actions = array();
    $color = null;
    $points = array();
    foreach ($rules as $index => $rule) {
        if ($rule->points != null && $rule->points != '') {
            $rule->points = json_decode($rule->points);
        }
        if ($rule->action_id != null && $rule->action_id != '') {
            $rule->action_id = json_decode($rule->action_id);
        }
        $rule->drawn_flag = true;
    }
?>
<link href="{{ asset('assets/vendor/jquery-ui/jquery-ui.min.css') }}" rel="stylesheet">
<div class="no-scroll">
    @include('admin.layouts.flash-message')
    <div class="scroll" style="position: relative;">
        <h3>検知設定</h3>
        @if(isset($view_only))
            @foreach ($rules as $index=>$rule)
            <div>
                <label style="margin-right: 20px"><span class="title-span">ルール名</span>：{{$rule->name}}</label>
                <label class=""><span class="title-span">アクション</span>：{{count($rule->action_id) > 0 ? $action_options[$rule->action_id[0]] : ''}}</label>
            </div>

            @endforeach
        @else
            <div class="setting-head">
                <div id="rule_items">
                    <p class="close-items"><a class="close-icon">×</a></p>
                    <div>
                        <label class="title-label">ルール名：</label>
                        <input type="text" class="rule_name" />
                    </div>
                    <p class="error-message rule-name" style="display: none">ルール名を6文字以内で入力してください</p>
                    <div class="radio-area">
                        <label class="title-label">図形：</label>
                        <select name = '' class="select-box select-figure" style="">
                            <option value="0" selected>四角形</option>
                            <option value="1">多角形</option>
                        </select>
                    </div>
                    <div class="title-div">
                        <label class="title-label">カラー選択：</label>
                        <input type="color" class="color" value=""/>
                    </div>
                    <div class="content-div action-div">
                        <label class="title-label">アクション：</label>
                        <select style="" class="select-box">
                            <option value=""></option>
                            @foreach($action_options as $id => $action)
                                <option value={{$id}}>{{$action}}</option>
                            @endforeach
                        </select>
                    </div>
                    <p class="error-message rule-select" style="display: none">アクションを選択してください。</p>
                    <button type="button" class="draw-btn area-draw-btn" style="display: none;">エリア選択</button>
                </div>
                <div id="rule_item_area">
                    @foreach ($rules as $index=>$rule)
                    <div class="rule_items" data-index = {{$index}}>
                        <p class="close-items"><a class="close-icon" onclick="removeFigure({{$index}})">×</a></p>
                        <div>
                            <label class="title-label">ルール名：</label>
                            <input value="{{$rule->name}}" onchange="changeName(this, '{{$index}}')" type="text" class="rule_name" />
                        </div>
                        <p class="error-message rule-name" style="display: none">ルール名を6文字以内で入力してください</p>
                        <div class="radio-area">
                            <label class="title-label">図形：</label>
                            <select name={{'figure_type_'.$index}} class="select-box" style=""
                            disabled = {{$index == count($rules) - 1 && count($rules) < $max_figure_numbers ? true : false }}>
                                @if(count($rule->points) == 4)
                                    <option value="0" selected>四角形</option>
                                    <option value="1">多角形</option>
                                @else
                                    <option value="0">四角形</option>
                                    <option value="1" selected>多角形</option>
                                @endif
                            </select>
                        </div>
                        <div class="title-div">
                            <label class="title-label">カラー選択：</label>
                            <input onchange="changeColor(this, '{{$index}}')" type="color" class="color" value="{{isset($rule->color) ? $rule->color:'#000000'}}"/>
                        </div>
                        <div class="content-div action-div">
                            <label class="title-label">アクション：</label>
                            <select name={{'figure_type_'.$index}} class="select-box" onchange="changeActions(this, {{$index}})">
                                <option value=""></option>
                                @foreach($action_options as $id => $action)
                                    @if (in_array($id, $rule->action_id))
                                        <option value={{$id}} selected>{{$action}}</option>
                                    @else
                                        <option value={{$id}}>{{$action}}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <p class="error-message rule-select" style="display: none">アクションを選択してください。</p>
                        @if(count($rule->points) != 4)
                            <button type="button" class="draw-btn disabled-btn area-draw-btn">エリア選択</button>
                        @endif
                    </div>
                    @endforeach
                </div>

                <div class="add-figure-area">
                    <button type="button" onclick="addNewFigure()" style="display: <?php echo count($rules) < $max_figure_numbers ? 'block':'none' ?>"
                        id="add-figure-btn" class="draw-btn add-btn">検知設定を追加
                    </button>
                    <div class="balloon_danger">
                        <p>画像内をクリックしエリアを選択してください。</p>
                    </div>
                </div>
                <div>
                    <div class="ai-guide-area" style="">
                        <input onchange="changeHeatMap(this)" class="tgl tgl-flat" id="ai_guide" type="checkbox">
                        <label class="tgl-btn" for="ai_guide"></label>
                        <label style="margin-left:5px;padding-top:3px;">AI検知精度ガイド</label>
                        <button style="margin-left: 10px;" onclick="resetHeatMap({{$camera_id}})" type='button' class="reset-heatmap-button">再取得</button>
                    </div>
                    <div style="color: #999;">※カメラの位置や画角を変更した場合は、<br/>AI検知精度の再取得を行う必要がありますので、<br/>「再取得」ボタンを押してください。
                    </div>
                </div>
            </div>
            <button type="button" onclick="clearImage()" class="clear-btn history">選択を全てクリア</button>
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
                @if(!isset($view_only))
                    <div class="btns" id="direction">
                        <button type="button" onclick="saveRule()" class="ok save-btn">決定</button>
                    </div>
                @endif
            </div>
            <div class="loader" style="display: none"></div>
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
                <li>①危険エリアの図形を四角形、多角形から選択し<br/>
                    動画内をクリックし危険エリアとして指定するエリアを作成します。
                    <small>※最大3エリア選択することができます。</small>
                    <small>※エリアは検知設定内の×ボタンで削除できます。</small>
                </li>
                <li>②指定したエリア毎に検知するアクションを選択します。</li>
                <li>③設定が完了したら「決定」ボタンを押下してください。
                    <small>※「選択を全てクリア」をクリックすると設定した情報が削除されます。</small>
                </li>
            </ul>
            <h3>【AI検知精度】</h3>
            <ul>
                <li>AI検知精度をONにするとカメラ映像内の検知精度が表示されます。
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
        top: 0px!important;
    }
    .setting-head{
        padding-left:15px;
        display:flex;
    }
    .rule_name{
        width:110px!important;
        background: white!important;
        border:1px lightgray solid!important;
    }
    .select-box{
        width:110px;
        background: white;
        padding:5px;
        margin-right:0px;
    }
    .title-label{
        font-size: 14px;
        width:93px;
        display: inline-block;
    }
    input[type="color"]{
        height:21px;
    }
    .area-draw-btn{
        margin-top: 10px;
    }
    .add-figure-area{
        position: relative;
        margin-top:45px;
    }
    .action-div{
        /* display: flex; */
        margin-top: 8px;
    }
    .action-div > div{
        white-space: nowrap;
    }
    .image-record{
        display: none;
    }
    #image-container{
        width:1280px;
        height:720px;
        display: block;
        margin-left: auto;
        margin-right: auto;
    }
    .content-div{
        margin-bottom: 5px;
    }
    .radio-label{
        padding-top: 4px;
        padding-right: 10px;
        padding-left:13px;
        font-size: 15px;
    }
    .radio-label:before{
        margin-top:10px!important;
    }
    .radio-label:after{
        margin-top:5px!important;
    }
    .radio-area{
        height: 30px;
        margin-left: 0px;
        margin-top:8px;
        margin-bottom: 8px;
    }
    #debug{
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
    }
    #rule_item_area{
        font-size: 12px;
        display: flex;
    }
    .custom-style{
        padding-top: 3px;
        padding-bottom: 3px;
        padding-left: 10px;
        padding-right: 15px;
    }
    .custom-style::before{
        height: 12px!important;
        width: 12px!important;
    }
    .disabled-btn{
        background: gray;
        cursor: default;
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
        z-index: 1000;
    }
    .balloon_danger:before{
        content: "";
        position: absolute;
        top: 100%;
        left: 50%;
        margin-left: -10px;
        border: 10px solid transparent;
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
    .ai-guide-area{
        margin-left: 10px;
        padding-top:100px;
        display:inline-flex;
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
    @media only screen and (max-width:768px) {
        .btns{
            display: flex;
        }
        .clear-btn{
            position: relative;
            top:0;
            margin-top: 10px;
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
        }
        .setting-head{
            display:block;
        }
        .add-figure-area{
            margin-top:10px;
        }
        .ai-guide-area{
            margin-left: 0px;
            padding-top: 10px;
        }
    }
</style>
<script src="{{ asset('assets/admin/js/konva.js?2') }}"></script>
<script src="{{ asset('assets/vendor/jquery-ui/jquery-ui.min.js') }}"></script>
<script>
    var mouse_pos = null;
    var heatmap_records = [];
    var max_figure_numbers = "<?php echo $max_figure_numbers;?>";
    var view_only = "<?php echo isset($view_only) ? 1: 0;?>";
    view_only = parseInt(view_only);
    max_figure_numbers = parseInt(max_figure_numbers);
    var stage = null;
    var layer = null;
    var enable_add_figure_flag = true;
    var selected_figure = 'rect';
    var radius = "<?php echo config('const.camera_mark_radius');?>";
    radius = parseInt(radius);
    var unique_rule_id = null;
    var rules = <?php echo json_encode($rules);?>;
    var rule_default_color = <?php echo json_encode(config('const.rule_default_color'));?>;

    var rules_object = {};
    rules.map((rule_item, rule_index) => {
        rules_object[rule_index] = rule_item;
        if (unique_rule_id == null) {
            unique_rule_id = 0;
        } else {
            unique_rule_id++;
        }
    })

    function drawCircle(center_point, point_index, figure_color, rule_index){
        if (!(Object.keys(rules_object).length > 0)) return;
        var circle = new Konva.Circle({
            x: center_point.x,
            y: center_point.y,
            radius: radius,
            fill: figure_color,
            stroke: figure_color,
            strokeWidth: 1,
            draggable: view_only == 1 ? false : true,
            id: rule_index.toString() + '_' + point_index.toString()
        });
        circle.on('mouseenter', function () {
            stage.container().style.cursor = 'pointer';
        });
        circle.on('mouseleave', function () {
            stage.container().style.cursor = 'default';
        });
        circle.on('dragmove', function (e) {
            var circle_id = e.target.id();
            var rule_index = parseInt(circle_id.split('_')[0]);
            var new_x = e.evt.offsetX;
            var new_y = e.evt.offsetY;
            var point_index = rules_object[rule_index].points.findIndex(x => x.id == circle_id);
            var delta = Math.abs(rules_object[rule_index].points[point_index].x - new_x) + Math.abs(rules_object[rule_index].points[point_index].y - new_y);
            if (mouse_pos.x >= window.innerWidth - 30 || mouse_pos.x <= 230 || mouse_pos.y <= 20 || mouse_pos.y >= window.innerHeight - 30 || delta > 150) {
                circle.stopDrag();
                circle.absolutePosition({x:rules_object[rule_index].points[point_index].x, y:rules_object[rule_index].points[point_index].y});
                return;
            }
            if (e.evt.offsetX <= 10 || e.evt.offsetX >= 1270) {
                new_x = e.evt.offsetX <=10?13:1267;
                circle.stopDrag();
                circle.absolutePosition({x:new_x, y:e.evt.offsetY});
                // return;
            }
            if (e.evt.offsetY <= 10 || e.evt.offsetY >= 710) {
                circle.stopDrag();
                new_y = e.evt.offsetY<=10?13:707;
                circle.absolutePosition({x:e.evt.offsetX, y:new_y});
                // return;
            }
            if (!isNaN(rule_index)){
                var index = rules_object[rule_index].points.findIndex(x => x.id == circle_id);
                if (index > -1){
                    rules_object[rule_index].points[index].x = new_x;
                    rules_object[rule_index].points[index].y = new_y;
                    if (enable_add_figure_flag == true || rule_index < Math.max(...Object.keys(rules_object))){
                        drawFigure(rule_index, rules_object[rule_index].color);
                        rules_object[rule_index].is_changed = true;
                    }
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
                rules_object[unique_rule_id].is_changed = true;
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
            if (rule_item.action_id == undefined || rule_item.action_id.length == 0){
                res = false;
                $('.error-message.rule-select', $('[data-index="'+ rule_index + '"]')).show();
            }
            if (rule_item.name != undefined && rule_item.name != null && rule_item.name.length > 6){
                res = false;
                $('.error-message.rule-name', $('[data-index="'+ rule_index + '"]')).show();
            }
        })
        return res;
    }

    function saveRule(){
        $('.error-message').hide();
        if (!checkValidation()) return;
        $('#rule_data').val(JSON.stringify(rules_object));
        $('#form_danger_rule').submit();
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
            strokeWidth: 2,
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

    function changeName(e, rule_index){
        rules_object[rule_index].name = e.value;
        rules_object[rule_index].is_name_color_changed = true;
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
        rules_object[rule_index].is_name_color_changed = true;
    }

    function changeActions (e, rule_index){
        if (rules_object[rule_index].action_id == undefined) rules_object[rule_index].action_id = [];
        var id_action = e.value;
        if ( id_action > 0){
            rules_object[rule_index].action_id = [parseInt(id_action)];
        } else {
            rules_object[rule_index].action_id = [];
        }
        rules_object[rule_index].is_changed = true;

        // var check_input_id = rule_index + '_action_' + id_action;
        // var add_remove_flag = $('#' + check_input_id).is(':checked');
        // if (add_remove_flag){
        //     var action_index = rules_object[rule_index].action_id.findIndex(x => x == id_action);
        //     if (action_index > -1){
        //         rules_object[rule_index].action_id.splice(action_index, 1);
        //     }
        // } else {
        //     rules_object[rule_index].action_id.push(parseInt(id_action));
        // }
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
        $('#add-figure-btn').show();
    }

    function addNewFigure(){
        if (Object.keys(rules_object).length >= max_figure_numbers) return;
        if (enable_add_figure_flag != true) return;
        if (Object.keys(rules_object).length == max_figure_numbers - 1){
            $('#add-figure-btn').hide();
        }
        if (unique_rule_id == null){
            unique_rule_id = 0;
        } else {
            unique_rule_id++;
        }
        $('.rule_items .draw-btn').each(function(){
            $(this).addClass('disabled-btn');
        });
        $('.rule_items .select-figure').each(function(){
            $(this).prop('disabled', true);
        })
        var template_item = $('#rule_items').clone();
        template_item.attr('id', '');
        template_item.addClass('rule_items');
        template_item.attr('data-index', unique_rule_id);
        template_item.show();
        $('#rule_item_area').append(template_item);
        // $('#radio-rect', template_item).attr('name', 'figure_type_' + unique_rule_id);
        // $('#radio-rect', template_item).attr('id', 'radio-rect_' + unique_rule_id);
        // $('#radio-polygon', template_item).attr('name', 'figure_type_' + unique_rule_id);
        // $('#radio-polygon', template_item).attr('id', 'radio-polygon_' + unique_rule_id);
        // $('.radio-label-rect', template_item).attr('for', 'radio-rect_' + unique_rule_id);
        // $('.radio-label-polygon', template_item).attr('for', 'radio-polygon_' + unique_rule_id);
        // $('input', $('.action-div', template_item)).each(function(){
        //     $(this).attr('id', unique_rule_id+'_'+$(this).attr('id'));
        // });
        // $('label', $('.action-div', template_item)).each(function(){
        //     $(this).attr('for', unique_rule_id+'_'+$(this).attr('for'));
        // });

        $('.color', template_item).val(rule_default_color[Object.keys(rules_object).length]);
        rules_object[unique_rule_id] = {
            points:[],
            color:rule_default_color[Object.keys(rules_object).length],
            action_id:[]
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
            $('#add-figure-btn').show();
        });
        $('select', $('.radio-area', template_item)).change(function(){
            if ($(this).val() == 0){
                selected_figure = 'rect';
                // $('.draw-btn', template_item).addClass('disabled-btn');
                $('.draw-btn', template_item).hide();
            } else {
                selected_figure = 'polygon';
                // $('.draw-btn', template_item).removeClass('disabled-btn');
                $('.draw-btn', template_item).show();
            }
        });
        $('select', $('.action-div', template_item)).change(function(){
            var rule_index = template_item.attr('data-index');
            if (rules_object[rule_index].action_id == undefined) rules_object[rule_index].action_id = [];
            if ($(this).val() > 0){
                rules_object[rule_index].action_id = [parseInt($(this).val())];
            } else {
                rules_object[rule_index].action_id = [];
            }
            rules_object[rule_index].is_changed = true;
            // if (rules_object[rule_index].action_id.includes($(this).val())){
            //     var action_index = rules_object[rule_index].action_id.findIndex(x => x == $(this).val());
            //     if (action_index > -1){
            //         rules_object[rule_index].action_id.splice(action_index, 1);
            //     }
            // } else {
            //     rules_object[rule_index].action_id.push(parseInt($(this).val()));
            // }
        });
        $('.draw-btn', template_item).click(function(){
            var rule_index = template_item.attr('data-index');
            if (unique_rule_id == rule_index && selected_figure != 'rect'){
                if (rules_object[rule_index].points != undefined && rules_object[rule_index].points.length > 2 && rules_object[rule_index].drawn_flag != true){
                    var figure_color = rules_object[rule_index].color != undefined ? rules_object[rule_index].color :"black";
                    drawFigure(rule_index, figure_color);
                    rules_object[rule_index].is_changed = true;
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
            rules_object[rule_index].is_name_color_changed = true;
        });
        $('.rule_name', template_item).change(function(){
            var rule_index = template_item.attr('data-index');
            rules_object[rule_index].name = $(this).val();
            rules_object[rule_index].is_name_color_changed = true;
        });
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
            heatmap_records = [];
            $('.grid').css('opacity', 0);
            $('.video-area').show();
            $('#ai_guide').prop('checked', false);
        });
    }

    $(document).ready(function() {
        document.onmousemove = handleMouseMove;
        getHeadtMapData();
        drawing();
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

        $('.grid-area').css('margin-left', $('.video-area').width() - 1280 > 0 ? ($('.video-area').width() - 1280)/2 : 0);
        window.addEventListener('resize', function(event) {
            console.log('resize');
            $('.grid-area').css('margin-left', $('.video-area').width() - 1280 > 0 ? ($('.video-area').width() - 1280)/2 : 0);
        }, true);
    });
</script>
