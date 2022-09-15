<?php
    $max_figure_numbers = config('const.thief_max_rect_numbers');
    $login_user = Auth::guard('admin')->user();
    $super_admin_flag = ($login_user->authority_id == config('const.super_admin_code'));
    foreach ($rules as $index => $rule) {
        if ($rule->points != null && $rule->points != '') {
            $rule->points = json_decode($rule->points);
            $rule->drawn_flag = true;
        }
    }
?>
<div class="no-scroll">
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
                <div class="title-div mt-7">エリア内のハンガーの色を選択</div>
                <div class="content-div hanger-area">
                    <div class="demo_color" style="background: black"></div>
                    {{-- <p class="error-message hanger" style="display: none">ハンガーの色を選択してください。</p> --}}
                </div>
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
                    <div class="title-div mt-7">エリア内のハンガーの色を選択</div>
                    <div class="content-div hanger-area" onclick="switchHanger(this, '{{$index}}')">
                        <div class="demo_color" style="background: {{isset($rule->hanger) && $rule->hanger !== '' ? $rule->hanger : black}}"></div>
                        {{-- <p class="error-message hanger" style="display: none">ハンガーの色を選択してください。</p> --}}
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
                <div id="image-container" class="camera-image"></div>
                <p class="error-message area" style="display: none">エリアを選択してください。</p>
                <div class="description">点をドラッグすることでサイズを変更することが出来ます。<div id="debug"></div></div>
            </div>
        </div>

        @if(!$super_admin_flag)
            <div class="btns" id="direction">
                <button type="button" onclick="clearImage()" class="edit clear-btn history">選択をクリア</button>
                <button type="button" onclick="saveRule()" class="ok save-btn">決定</button>
            </div>
        @endif

    </div>
    <input type="hidden" value="" name="rule_data" id = 'rule_data'/>

</div>
<!--MODAL -->
<div id="howto" class="modal-content">
    <div class="textarea">
        <div class="explain">
            <h3>【使い方】</h3>
            <ul>
                <li>①ハンガーの色をスポイトで選択</li>
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
    .mt-7{
        margin-top:7px;
    }
    .setting-head{
        padding-left:15px;
        display:flex;
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
    canvas{
        width:1280px;
        height:720px;
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
    .demo_color{
        width: 55px;
        height: 25px;
    }
    .hanger-area{
        background: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABgAAAAYCAYAAADgdz34AAAAAXNSR0IArs4c6QAAARZJREFUSEvVlFsNAjEURM8qAAk4AAmggOAACTgAFIAFHOAAHAAScIAEMkmbdC/dbbuPD5oQfjZnpnOnt2LkU43MZwiBKbB3Ro/AJzTdV0DwG7Bw0CewCkW6CByAswOGcG+8JlIqILjiEETHO7ejVFT6tngGiuQOzBPl6CUQiyXUewFLP4eSiMKBCqJjb1KDl0Rk4XKoE8b1A88ViMHV9WRFcwRy4GFcG+Ca+9By4T4u/dfgbTcogddWg61vrEWDwZtuoFeq+oWtaBJNLmN7Az39B/B2a8C2JVrFNhUrsANOwAXYmioWw2MRqQVr1wa5VzNmJq5kLG01FXRiCJ2ce4aNSAOWY60A//Oruch5k0AnSMmQ/0/gC62wVRmbnJBoAAAAAElFTkSuQmCC) no-repeat left 50%;
        background-size:24px 24px;
        padding-left: 34px;
        cursor: pointer;
        border: 1px solid transparent;
        max-width: 180px
    }
    .selected{
        /* box-shadow: inset -10px -10px 15px rgba(255, 255, 255, 0.5), inset 10px 10px 15px rgba(70, 70, 70, 0.12); */
        box-shadow: inset -10px -10px 15px transparent, inset 10px 10px 15px transparent;
        border: 1px solid gray;
    }
    .img-magnifier-glass {
        position: absolute;
        border: 3px solid #000;
        border-radius: 50%;
        /* cursor: none; */
        cursor:url({{ asset('assets/admin/img/bxs-eyedropper.cur') }}), default;

        z-index: 10;
        /*Set the size of the magnifier glass:*/
        width: 100px;
        height: 100px;
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
    var checked_hanger_flag = true;
    var selected_rule_index = null;
    var enable_add_figure_flag = true;
    var selected_figure = 'rect';
    var camera_image_data = "<?php echo $camera_image_data;?>";
    var max_figure_numbers = "<?php echo $max_figure_numbers;?>";
    max_figure_numbers = parseInt(max_figure_numbers);
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

    function magnify(imgID, zoom) {
        var img, glass, w, h, bw;
        img = document.getElementById(imgID);
        /*create magnifier glass:*/
        glass = document.createElement("DIV");
        glass.setAttribute("class", "img-magnifier-glass");
        /*insert magnifier glass:*/
        img.parentElement.insertBefore(glass, img);
        /*set background properties for the magnifier glass:*/
        // glass.style.backgroundImage = "url('" + img.src + "')";
        glass.style.backgroundImage = "url('" + camera_image_data + "')";
        glass.style.backgroundRepeat = "no-repeat";
        glass.style.backgroundSize = (img.width * zoom) + "px " + (img.height * zoom) + "px";
        bw = 3;
        w = glass.offsetWidth / 2;
        h = glass.offsetHeight / 2;
        /*execute a function when someone moves the magnifier glass over the image:*/
        glass.addEventListener("mousemove", moveMagnifier);
        img.addEventListener("mousemove", moveMagnifier);
        /*and also for touch screens:*/
        glass.addEventListener("touchmove", moveMagnifier);
        img.addEventListener("touchmove", moveMagnifier);
        function moveMagnifier(e) {
            var pos, x, y;
            /*prevent any other actions that may occur when moving over the image*/
            e.preventDefault();
            /*get the cursor's x and y positions:*/
            pos = getCursorPos(e);
            x = pos.x;
            y = pos.y;
            /*prevent the magnifier glass from being positioned outside the image:*/
            if (x > img.width - (w / zoom)) {x = img.width - (w / zoom);}
            if (x < w / zoom) {x = w / zoom;}
            if (y > img.height - (h / zoom)) {y = img.height - (h / zoom);}
            if (y < h / zoom) {y = h / zoom;}
            /*set the position of the magnifier glass:*/
            glass.style.left = (x - w) + "px";
            glass.style.top = (y - h) + "px";
            /*display what the magnifier glass "sees":*/
            glass.style.backgroundPosition = "-" + ((x * zoom) - w + bw) + "px -" + ((y * zoom) - h + bw) + "px";
        }
        function getCursorPos(e) {
            var a, x = 0, y = 0;
            e = e || window.event;
            /*get the x and y positions of the image:*/
            a = img.getBoundingClientRect();
            /*calculate the cursor's x and y coordinates, relative to the image:*/
            x = e.pageX - a.left;
            y = e.pageY - a.top;
            /*consider any page scrolling:*/
            x = x - window.pageXOffset;
            y = y - window.pageYOffset;
            return {x : x, y : y};
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

    function switchHanger(e, rule_index){
        $('.img-magnifier-glass').remove();
        if (checked_hanger_flag == true || selected_rule_index === rule_index){
            checked_hanger_flag = !checked_hanger_flag;
        }

        if (checked_hanger_flag != true){
            $('canvas').attr('id', 'canvas-container2');
            selected_rule_index = rule_index;
            $('.hanger-area').removeClass('selected');
            $('.hanger-area', $('[data-index="'+ rule_index + '"]')).addClass('selected');
            magnify('canvas-container2', 3);
        } else {
            $('canvas').attr('id', '');
            selected_rule_index = null;
            $('.hanger-area').removeClass('selected');
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

        if (parseInt(selected_rule_index) === parseInt(rule_index)){
            checked_hanger_flag = true;
            selected_rule_index = null;
            $('canvas').attr('id', '');
            $('.img-magnifier-glass').remove();
        }
        delete rules_object[rule_index];
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
            hanger:'#000000',
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
            if (parseInt(selected_rule_index) === parseInt(rule_index)){
                checked_hanger_flag = true;
                selected_rule_index = null;
                $('canvas').attr('id', '');
                $('.img-magnifier-glass').remove();
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
        $('.hanger-area', template_item).click(function(){
            var rule_index = template_item.attr('data-index');
            switchHanger(null, rule_index);
        })
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
        // $('canvas').attr('id', 'canvas-container2');
        var imageObj = new Image();
        imageObj.onload = function () {
            var backImage = new Konva.Image({
                x: 0,
                y: 0,
                image: imageObj,
                width: container.clientWidth,
                height: container.clientHeight,
            });

            // add the shape to the layer
            layer.add(backImage);
        };
        imageObj.src = camera_image_data;
        // imageObj.src = url;

        stage.on('click', function(e){
            if (checked_hanger_flag != true && selected_rule_index != null){
                var canvas = document.getElementsByTagName('canvas')[0];
                var ctx = canvas.getContext('2d');
                var glass = $('.img-magnifier-glass');
                var x_pos = glass.position().left + e.evt.offsetX;
                var y_pos = glass.position().top + e.evt.offsetY + 8;
                if (x_pos < 0) x_pos = 0;
                if (y_pos < 0) y_pos = 0;
                if (x_pos > 1280) x_pos = 1280;
                if (y_pos > 720) y_pos = 720;
                var imgData = ctx.getImageData(parseInt(x_pos), parseInt(y_pos), 1, 1);
                var rgba = imgData.data;
                hanger = "#" + parseInt(rgba[0]).toString(16) + parseInt(rgba[1]).toString(16) + parseInt(rgba[2]).toString(16) + parseInt(rgba[3]).toString(16);

                rules_object[selected_rule_index].hanger = hanger;
                $('.demo_color', $('[data-index="'+ selected_rule_index + '"]')).css('background-color', hanger);
                return;
            }
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
            if (checked_hanger_flag != true && selected_rule_index != null){
                var glass = $('.img-magnifier-glass');
                var x_pos = glass.position().left + e.evt.offsetX;
                var y_pos = glass.position().top + e.evt.offsetY + 8;
                if (x_pos < 0) x_pos = 0;
                if (y_pos < 0) y_pos = 0;
                if (x_pos > 1280) x_pos = 1280;
                if (y_pos > 720) y_pos = 720;
                document.getElementById("debug").innerHTML = `X座標${parseInt(x_pos)}:Y座標${parseInt(y_pos)}`;
            } else {
                document.getElementById("debug").innerHTML = `X座標${e.evt.offsetX}:Y座標${e.evt.offsetY}`;
            }

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
        checked_hanger_flag = true;
        $('canvas').attr('id', '');
        $('.img-magnifier-glass').remove();
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
        })
        return res;
    }

    function saveRule(){
        $('.error-message').hide();
        if (!checkValidation()) return;
        $('#rule_data').val(JSON.stringify(rules_object));
        $('#form_thief_rule').submit();
    }

    $(document).ready(function() {
        drawingStage();
        setTimeout(() => {
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
        }, 500);
    });
</script>
