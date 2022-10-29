@extends('admin.layouts.app')

@section('content')

<?php
    $floors = array();
    $drawing_files = array();
    foreach ($drawings as $key => $drawing) {
        $floors[$drawing->id] = $drawing->floor_number;
        $drawing_files[$drawing->id] = $drawing->drawing_file_path;
    }
    $login_user = Auth::guard('admin')->user();
    $super_admin_flag = ($login_user->authority_id == config('const.super_admin_code'));
?>
<div id="wrapper">
    <div class="breadcrumb">
        <ul>
            <li><a href="{{route('admin.camera')}}">カメラ設定</a></li>
            <li><a href="{{route('admin.camera.mapping')}}">カメラマッピング一覧</a></li>
            <li>カメラマッピング詳細</li>
        </ul>
    </div>
    <div id="r-content">
        <div class="title-wrap">
            <h2 class="title">カメラマッピング詳細</h2>
        </div>

        <form action="{{route('admin.camera.mapping.store')}}" method="post" name="form1" id="form1">
        @csrf
        <ul class="three-btns">
            <li><button type="button" class="new modal-open" data-target="add_camera" onclick="buttonClick('add')">設置場所の新規登録</button></li>
            <li><button type="button" class="new" onclick="buttonClick('edit')">設置場所の編集</button></li>
            <li><button type="button" class="new" onclick="buttonClick('delete')">設置場所の削除</button></li>
        </ul>
        @include('admin.layouts.flash-message')
        <div class="select-c mapping">
            <select id="select_floor" onchange = "changeFloor()">
                @foreach ($floors as $drawing_id => $floor)
                    @if (isset($selected_drawing) && $selected_drawing->id == $drawing_id)
                        <option value = "{{$drawing_id}}" selected>{{$floor}}</option>
                    @else
                        <option value = "{{$drawing_id}}">{{$floor}}</option>
                    @endif
                @endforeach
            </select>
        </div>
        <p class="image-title">現在の図面イメージ</p>
        <?php
            $file_url = '';
            if (isset($selected_drawing)) {
                $file_url = $selected_drawing->drawing_file_path;
                $file_url = asset('storage/drawings/' . $file_url);
            }
        ?>
        <div id = "container-canvas" style="background: url({{$file_url}})">
        </div>
        @if(!$super_admin_flag)
        <div class="btns">
            <button type="submit" class="ok">更新</button>
        </div>
        @endif
        <input type = "hidden" name="camera_mapping_info" id = "camera_mapping_info" value="" />
        </form>
    </div>
</div>

<div id="add_camera" class="modal-content">
    <div class="textarea">
        <div class="listing">
            <div class="scroll active sp-pl0">
                <table class="table2 text-centre">
                    <thead>
                    <tr>
                        <th class="w10"></th>
                        <th>ID</th>
                        <th>カメラNo</th>
                        <th>設置フロア</th>
                        <th>設置場所</th>
                        <th>備考</th>
                    </tr>
                    </thead>
                    <tbody class="camera_candidates">
                        @foreach ($cameras as $camera)
                        <tr id="tr_{{$camera->id}}">
                            <td class="stick-t">
                                <div class="checkbtn-wrap radio-wrap-div">
                                    <input name="selected_camera" type="radio" id="td_{{$camera->id}}" value="{{$camera->id}}">
                                    <label for="td_{{$camera->id}}"></label>
                                </div>
                            </td>
                            <td>{{$camera->id}}</td>
                            <td>{{$camera->serial_no}}</td>
                            <td>{{$camera->floor_number}}</td>
                            <td>{{$camera->installation_position}}</td>
                            <td>{{$camera->remarks}}</td>
                        </tr>
                        @endforeach
                        <tr class="no-camera-record" style="display: none;">
                            <td colspan="6">登録されたカメラがありません。カメラを設定してください</td>
                        </tr>
                    </tbody>
                </table>
                <p class="error-message">カメラを選択してください。</p>
                <div class="modal-set">
                    @if(count($cameras) > 0)
                    <button onclick="selectCamera()" type="submit" class="set-button">設 定</button>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <p class="closemodal"><a class="modal-close">×</a></p>
</div>


<div id="dialog-confirm" title="test" style="display:none">
    <p>
        <span class="ui-icon ui-icon-alert" style="float:left; margin:12px 12px 20px 0;"></span>
        <span id="confirm_text">These items will be permanently deleted and cannot be recovered. Are you sure?</span>
    </p>
</div>

<link href="{{ asset('assets/vendor/jquery-ui/jquery-ui.min.css') }}" rel="stylesheet">

<script src="{{ asset('assets/vendor/jquery-ui/jquery-ui.min.js') }}"></script>
<script src="{{ asset('assets/admin/js/helper.js?2') }}"></script>
<script src="{{ asset('assets/admin/js/konva.js?2') }}"></script>

<style>
    #container-canvas{
        margin-left: auto;
        margin-right: auto;
        /* width:1280px; */
        /* height:720px; */
        background-repeat: no-repeat!important;
        background-size:contain!important;
    }
    canvas{
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
    }
    .modal-open{
        color:black;
    }
    .modal-open:hover{
        color:black!important;
    }
    .modal-open:before{
        content:none;
    }
    .error-messag{
        display: none;
    }
</style>
<script>
    var stage = null;
    var layer = null;
    var delete_id = "";
    var type = "";
    var selected_camera_id = '';
    var selected_drawing_id = '';
    var selected_edit_camera_id = '';
    var selected_edit_camera_object = null;
    var selected_edit_drawing_id = '';
    var drawing_width = "<?php echo config('const.drawing_width_criteria');?>";
    drawing_width = parseInt(drawing_width);
    var drawing_height = "<?php echo config('const.drawing_height_criteria');?>";
    drawing_height = parseInt(drawing_height);
    var radius = "<?php echo config('const.camera_mark_radius');?>";
    var camera_mapping_info = <?php echo json_encode($camera_mapping_info);?>;

    function showCameraCandidates(){
        var selected_camera_ids = [];
        Object.keys(camera_mapping_info).map(drawing_id => {
            camera_mapping_info[drawing_id].map(camera_item => {
                if (camera_item.is_deleted != true){
                    selected_camera_ids.push(parseInt(camera_item.camera_id));
                }
            })
        });
        var cameras = <?php echo $cameras;?>;
        $('.camera_candidates tr').each(function(){
            $(this).show();
        });
        selected_camera_ids.map(selected_id => {
            $('#tr_' + selected_id).hide();
        });
        if (selected_camera_ids.length == cameras.length){
            $('.no-camera-record').show();
            $('.set-button').hide();
        } else {
            $('.no-camera-record').hide();
            $('.set-button').show();
        }
    }

    function changeFloor(){
        selected_drawing_id = $('#select_floor').find(":selected").val();
        var path_array = <?php echo json_encode($drawing_files);?>;
        var img_path =  '<?php echo asset('storage/drawings/');?>' + '/' + path_array[selected_drawing_id];
        $('#container-canvas').css('background-image', 'url('+img_path+')');

        layer.find('Circle').map(circle_item => {
            circle_item.destroy();
        })
        layer.draw();
        drawCameraMapping();
    }

    function buttonClick(button_type){
        type = button_type;
        selected_camera_id = '';
        $('.error-message').hide();
        showCameraCandidates();
    }

    function selectCamera(){
        selected_camera_id = $('input[name="selected_camera"]:checked').val();
        if (!(selected_camera_id > 0)) {
            $('.error-message').show();
            return;
        }
        $('.modal-overlay').fadeOut('fast', function () {
            var modal = $('#add_camera');
            modal.hide();
            // html、bodyの固定解除
            $('html, body').removeClass('lock');
            // オーバーレイを削除
            $('.modal-overlay').remove();
            // モーダルコンテンツを囲む要素を削除
            $(modal).unwrap("<div class='modal-wrap'></div>");
        });
    }

    //init draw camera mapping--------------------
    function drawCameraMapping(){
        selected_drawing_id = $('#select_floor').find(":selected").val();
        if (camera_mapping_info[selected_drawing_id] != undefined){
            camera_mapping_info[selected_drawing_id].map(camera_item => {
                if (camera_item.is_deleted != true){
                    var circle = new Konva.Circle({
                        x: camera_item.x_coordinate,
                        y: camera_item.y_coordinate,
                        radius: parseInt(radius),
                        fill: 'red',
                        stroke: 'black',
                        strokeWidth: 1,
                        name:'camera_mark_' + camera_item.camera_id,
                        id:camera_item.id > 0 ? camera_item.id : 0
                    });
                    circle.on('mouseenter', function () {
                        stage.container().style.cursor = 'pointer';
                    });

                    circle.on('mouseleave', function () {
                        stage.container().style.cursor = 'default';
                    });
                    layer.add(circle);
                }
            })
        }
    }
    //------------------------------------------

    $(document).ready(function () {
        var container = document.getElementById('container-canvas');
        $('#camera_mapping_info').val(JSON.stringify(camera_mapping_info));
        $('#container-canvas').css('width', drawing_width);
        $('#container-canvas').css('height', drawing_height);
        stage = new Konva.Stage({
            container: 'container-canvas',
            width: drawing_width,
            height: drawing_height,
        })

        layer = new Konva.Layer();
        stage.add(layer);
        drawCameraMapping();

        stage.on('click', function(e){
            if (type == '') return;
            switch(type){
                case 'add':
                    if (!(selected_camera_id > 0)) return;
                    if (!e.target.name().includes('camera_mark')){
                        var x = e.evt.offsetX;
                        var y = e.evt.offsetY;
                        var circle = new Konva.Circle({
                            x: x,
                            y: y,
                            radius: parseInt(radius),
                            fill: 'red',
                            stroke: 'black',
                            strokeWidth: 1,
                            name:'camera_mark_' + selected_camera_id,
                            id:0,
                        });
                        circle.on('mouseenter', function () {
                            stage.container().style.cursor = 'pointer';
                        });

                        circle.on('mouseleave', function () {
                            stage.container().style.cursor = 'default';
                        });
                        layer.add(circle);
                        camera_mapping_info[selected_drawing_id].push({
                            drawing_id:selected_drawing_id,
                            camera_id:selected_camera_id,
                            x_coordinate:x,
                            y_coordinate:y
                        });
                        $('#camera_mapping_info').val(JSON.stringify(camera_mapping_info));

                        selected_camera_id = '';
                        $('input[name="selected_camera"]').prop('checked', false);
                    }
                    break;
                case 'edit':
                    if (e.target.name().includes('camera_mark')){
                        var fill = e.target.fill() == 'red' ? '#00d00f' : 'red';
                        e.target.fill(fill);
                        if (fill == 'red'){
                            selected_edit_camera_id = '';
                            selected_edit_camera_object = null;
                            selected_edit_drawing_id = '';
                        } else {
                            selected_edit_camera_id = parseInt(e.target.name().replace('camera_mark_', ''));
                            selected_edit_camera_object = e.target;
                            selected_edit_drawing_id = selected_drawing_id;
                        }
                    } else {
                        if (selected_edit_camera_id > 0){
                            if (selected_edit_drawing_id == selected_drawing_id){
                                var index = camera_mapping_info[selected_drawing_id].findIndex(x => x.camera_id == selected_edit_camera_id);
                                camera_mapping_info[selected_drawing_id][index].x_coordinate = e.evt.offsetX;
                                camera_mapping_info[selected_drawing_id][index].y_coordinate = e.evt.offsetY;

                                selected_edit_camera_object.x(e.evt.offsetX);
                                selected_edit_camera_object.y(e.evt.offsetY);
                                selected_edit_camera_object.fill('red');
                            } else {
                                camera_mapping_info[selected_drawing_id].push({
                                    id:selected_edit_camera_object.id(),
                                    drawing_id:selected_drawing_id,
                                    camera_id:selected_edit_camera_id,
                                    x_coordinate:e.evt.offsetX,
                                    y_coordinate:e.evt.offsetY,
                                });
                                camera_mapping_info[selected_edit_drawing_id] = camera_mapping_info[selected_edit_drawing_id].filter(x => x.camera_id != selected_edit_camera_id);

                                var circle = new Konva.Circle({
                                    x: e.evt.offsetX,
                                    y: e.evt.offsetY,
                                    radius: parseInt(radius),
                                    fill: 'red',
                                    stroke: 'black',
                                    strokeWidth: 1,
                                    name:'camera_mark_' + selected_edit_camera_id,
                                    id:selected_edit_camera_object.id(),
                                });
                                circle.on('mouseenter', function () {
                                    stage.container().style.cursor = 'pointer';
                                });

                                circle.on('mouseleave', function () {
                                    stage.container().style.cursor = 'default';
                                });
                                layer.add(circle);
                            }

                            $('#camera_mapping_info').val(JSON.stringify(camera_mapping_info));


                            selected_edit_camera_id = '';
                            selected_edit_camera_object = null;
                        }
                    }
                    break;
                case 'delete':
                    if (e.target.name().includes('camera_mark')){
                        if (e.target.id() > 0){
                            camera_mapping_info[selected_drawing_id][camera_mapping_info[selected_drawing_id].findIndex(x => x.id == e.target.id())].is_deleted = true;
                        } else {
                            camera_mapping_info[selected_drawing_id] = camera_mapping_info[selected_drawing_id].filter(x => x.camera_id != parseInt(e.target.name().replace('camera_mark_', '')));
                        }
                        $('#camera_mapping_info').val(JSON.stringify(camera_mapping_info));
                        e.target.remove();
                    }
                    break;
            }
        });

        showCameraCandidates();

        $(".delete_drawings").click(function(e){
            e.preventDefault();
            delete_id = $(this).attr('delete_index');
            helper_confirm("dialog-confirm", "削除", "現場図面を削除します。<br />よろしいですか？", 300, "確認", "閉じる", function(){
                var frm_id = "#frm_delete_" + delete_id;
                $(frm_id).submit();
            });
        });

  });
</script>

@endsection
