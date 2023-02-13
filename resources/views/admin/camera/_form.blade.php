<?php $file_url = '';?>
<div class="no-scroll">
    <table class="table">
        <thead>
            <tr>
                <th></th>
                <td><img src="{{ isset($camera) && isset($camera->img)? $camera->img :asset('assets/admin/img/samplepic.svg') }}" class="w50"></td>
            </tr>
            <tr>
                <th>カメラNo</th>
                <td>
                    @if (!isset($camera))
                        <select name="camera_id" id="camera_id" onchange="selectCamera(this)">
                            <option value="">カメラを選択する</option>
                            @if(isset($all_devices) && count($all_devices) > 0)
                            @foreach ($all_devices as $camera_item)
                                {{-- @if ($camera_item['device_id'] == old('camera_id', isset($camera->camera_id)?$camera->camera_id:'')) --}}
                                    {{-- <option value="{{$camera_item['device_id']}}" selected>{{$camera_item['serial'].'('.$camera_item['setting']['name'].')'}}</option> --}}
                                    {{-- <option value="{{$camera_item['device_id']}}" selected>{{$camera_item['serial']}}</option> --}}
                                {{-- @else --}}
                                    {{-- <option value="{{$camera_item['device_id']}}">{{$camera_item['serial'].'('.$camera_item['setting']['name'].')'}}</option> --}}
                                    <option value="{{$camera_item['device_id']}}">{{$camera_item['serial']}}</option>
                                {{-- @endif --}}
                            @endforeach
                            @endif
                        </select>
                        <input type="hidden" id="camera_serial_no" name="serial_no" value="{{old('serial_no', isset($camera->serial_no)?$camera->serial_no:'')}}">
                    @else
                        <input type="hidden" id="camera_id" readonly value="{{isset($camera->camera_id)?$camera->camera_id:''}}">
                        <input type="text" id="camera_serial_no" readonly value="{{isset($camera->serial_no)?$camera->serial_no:''}}">
                    @endif
                    @error('camera_id')
                    <p class="error-message">{{ $message }}</p>
                    @enderror
                </td>
            </tr>
            <tr>
                <th>設置エリア</th>
                <td>
                    <div class="select-c">
                    <select name="location_id" onchange="selectLocation(this)" id = 'location_id'>
                        <option value="">設置エリアを選択してください</option>
                        @foreach($locations as $key => $location)
                            @if (old('location_id', isset($camera->location_id)?$camera->location_id:'') == $key)
                            <option value="{{$key}}" selected>{{$location}}</option>
                            @else
                            <option value="{{$key}}">{{$location}}</option>
                            @endif
                        @endforeach
                    </select>
                    </div>
                @error('location_id')
                <p class="error-message">{{ $message }}</p>
                @enderror
                </td>
            </tr>
            <tr>
                <th>設置場所</th>
                <td><input type="text" placeholder="設置場所を入力してください" name="installation_position" value="{{ old('installation_position', isset($camera->installation_position)?$camera->installation_position:'')}}">
                @error('installation_position')
                <p class="error-message">{{ $message }}</p>
                @enderror
                </td>
            </tr>
            <tr>
                <th>設置フロア</th>
                <td>
                    <input type="hidden" name="drawing_id" value="{{old('drawing_id', isset($camera->drawing_id) ? $camera->drawing_id:'')}}" id="drawing_id"/>
                    <select class="floor_number" onchange="selectFloor(this)">
                        <option value="">設置フロアを選択してください</option>
                        @if(old('location_id', isset($camera->location_id)?$camera->location_id:'') > 0)
                            @if (isset($drawing_data[old('location_id', isset($camera->location_id)?$camera->location_id:'')]))
                            @foreach ($drawing_data[old('location_id', isset($camera->location_id)?$camera->location_id:'')] as $drawing_item)
                                @if(old('drawing_id', isset($camera->drawing_id) ? $camera->drawing_id:'') == $drawing_item->id)
                                <option value="{{$drawing_item->id}}" selected>{{$drawing_item->floor_number}}</option>
                                @else
                                <option value="{{$drawing_item->id}}">{{$drawing_item->floor_number}}</option>
                                @endif
                            @endforeach
                            @endif
                        @endif
                    </select>
                </td>
            </tr>
            <tr>
                <th style="vertical-align: top;">
                    <br/><br/>
                    カメラ位置の指定
                    @if(old('drawing_file_path', isset($camera->drawing_file_path)?$camera->drawing_file_path:'') != '')
                        <p class="img-notice">図面上にカメラ位置を設定できます。クリックでポイントカメラの位置がポイントされます。<br/>下のクリアボタンで選択をクリアできます。</p>
                        <button type="button" class="clear-img">カメラ位置のクリア</button>
                    @else
                        <p class="img-notice" style="display: none;">図面上にカメラ位置を設定できます。クリックでポイントカメラの位置がポイントされます。<br/>下のクリアボタンで選択をクリアできます。</p>
                        <button type="button" style="display: none;" class="clear-img">カメラ位置のクリア</button>
                    @endif
                </th>
                <td>
                    <input type="hidden" name="drawing_file_path" value="{{old('drawing_file_path', isset($camera->drawing_file_path)?$camera->drawing_file_path:'')}}" id="drawing_file_path"/>
                    <input type="hidden" name="x_coordinate" value="{{old('x_coordinate', isset($camera->x_coordinate)?$camera->x_coordinate:'')}}" id="x_coordinate"/>
                    <input type="hidden" name="y_coordinate"  value="{{old('y_coordinate', isset($camera->y_coordinate)?$camera->y_coordinate:'')}}" id="y_coordinate"/>
                    @if(old('drawing_file_path', isset($camera->drawing_file_path)?$camera->drawing_file_path:'') != '')
                    <?php
                        $file_url = old('drawing_file_path', isset($camera->drawing_file_path)?$camera->drawing_file_path:'');
                        $file_url = asset('storage/drawings/' . $file_url);
                    ?>
                        <div id = "container-canvas" style="background: url({{$file_url}})"></div>
                    @else
                        <div id = "container-canvas" style=""></div>
                    @endif

                </td>
            </tr>
            <tr>
                <th>備考</th>
                <td><textarea name="remarks">{{ old('remarks', isset($camera->remarks)?$camera->remarks:'')}}</textarea></td>
            </tr>
            {{-- <tr>
                <th>稼働状況</th>
                <td>
                    <ul class="radio-list">
                    @foreach (config('const.camera_status') as $key => $status )
                        <li>
                            <input name="is_enabled" type="radio" id="is_enabled_{{ $key }}" value="{{ $key }}" {{ old('is_enabled', isset($camera->is_enabled) ? $camera->is_enabled : config('const.enable_status_code.enable')) == $key ? 'checked' : ''  }}>
                            <label for="is_enabled_{{ $key }}">{{  $status }}</label>
                        </li>
                    @endforeach
                    </ul>
                </td>
            </tr> --}}
        </thead>
    </table>
</div>

<style>
    #container-canvas{
        margin-left: auto;
        margin-right: auto;
        /* width:1024px; */
        /* height:auto; */
        background-repeat: no-repeat!important;
        background-size:contain!important;
    }
    .img-notice{
        margin-top:15px;
        margin-bottom: 15px;
        color:darkgray;
    }
    .selectize-control.multi .selectize-input.has-items{
        display: none;
    }
</style>
<link href="{{ asset('assets/vendor/jquery-ui/jquery-ui.min.css') }}" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.12.6/css/selectize.bootstrap3.min.css" integrity="sha256-ze/OEYGcFbPRmvCnrSeKbRTtjG4vGLHXgOqsyLFTRjg=" crossorigin="anonymous" />
<script src="{{ asset('assets/vendor/jquery-ui/jquery-ui.min.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.12.6/js/standalone/selectize.min.js" integrity="sha256-+C0A5Ilqmu4QcSPxrlGpaZxJ04VjsRjKu+G82kl5UJk=" crossorigin="anonymous"></script>
<script src="{{ asset('assets/admin/js/helper.js?2') }}"></script>
<script src="{{ asset('assets/admin/js/konva.js?2') }}"></script>

<script>
    var stage = null;
    var layer = null;
    var selected_camera_id = $('#camera_id').val();
    var selected_location_id = $('#location_id').val();
    var drawing_width = "<?php echo config('const.drawing_width_criteria');?>";
    drawing_width = parseInt(drawing_width);
    var drawing_height = "<?php echo config('const.drawing_height_criteria');?>";
    drawing_height = parseInt(drawing_height);
    var file_url = "<?php echo $file_url;?>";
    var radius = "<?php echo config('const.camera_mark_radius');?>";
    var selected_camera_device_id = "<?php echo old('camera_id', isset($camera->camera_id)?$camera->camera_id:'');?>";
    var drawing_data = <?php echo json_encode($drawing_data);?>;


    function clearCameraPoint(){
        if (selected_camera_id == null || selected_camera_id == '') return;
        if (stage == null || layer == null) return;
        var circles = layer.find('Circle');
        if (circles != null){
            circles.map(circle_item => {
                if (circle_item.attrs.id == selected_camera_id){
                    circle_item.destroy();
                    $('#x_coordinate').val('');
                    $('#y_coordinate').val('');
                }
            })
        }

    }

    function drawCircle(x, y, camera_id){
        selected_camera_id = camera_id;
        clearCameraPoint();
        var circle = new Konva.Circle({
            x: x,
            y: y,
            radius: parseInt(radius),
            fill: 'red',
            stroke: 'black',
            strokeWidth: 1,
            name:camera_id,
            id:camera_id
        });
        circle.on('mouseenter', function () {
            stage.container().style.cursor = 'pointer';
        });

        circle.on('mouseleave', function () {
            stage.container().style.cursor = 'default';
        });
        layer.add(circle);

        $('#x_coordinate').val(x);
        $('#y_coordinate').val(y);
    }

    function selectCamera(e) {
        clearCameraPoint();
        selected_camera_id = e.value;
        $('#camera_serial_no').val(e.options[e.selectedIndex].text);
    }

    function initImageData(){
        clearCameraPoint();
        $('#drawing_id').val('');
        $('#drawing_file_path').val('');
        file_url = '';
        if (stage != null){
            if (layer != null){
                layer.clear();
            }
            stage.clear();
            stage = null;
            layer = null;
            $('.konvajs-content').remove();
            $('#container-canvas').css('background', '');
            $('#container-canvas').css('width', 0);
            $('#container-canvas').css('height', 0);
            $('.img-notice').hide();
            $('.clear-img').hide();
        }
    }

    function selectLocation(e) {
        initImageData();

        selected_location_id = e.value;
        var floor_selected_element = $('.floor_number');
        floor_selected_element.empty();
        floor_selected_element.append($("<option></option>").attr("value", '').text('設置フロアを選択してください'))
        if (selected_location_id > 0 && drawing_data[selected_location_id] != undefined){
            Object.keys(drawing_data[selected_location_id]).map(drawing_id => {
                var drawing_item = drawing_data[selected_location_id][drawing_id];
                floor_selected_element.append($("<option></option>").attr("value", drawing_id).text(drawing_item.floor_number));
            })
        }

    }

    function selectFloor(e) {
        initImageData();
        if (e.value > 0){
            var draw_data = drawing_data[selected_location_id][e.value];
            $('#drawing_id').val(e.value);
            if (draw_data.drawing_file_path != null && draw_data.drawing_file_path != ''){
                $('#drawing_file_path').val(draw_data.drawing_file_path);
                file_url = "<?php echo asset('storage/drawings/');?>" + "/" + draw_data.drawing_file_path;
                $('#container-canvas').css('background-image', 'url('+file_url+')');
                $('.img-notice').show();
                $('.clear-img').show();

                var container = document.getElementById('container-canvas');
                container.style.width = drawing_width + 'px';
                container.style.height = drawing_height + 'px';
                stage = new Konva.Stage({
                    container: 'container-canvas',
                    width: drawing_width,
                    height: drawing_height,
                });
                layer = new Konva.Layer();
                stage.add(layer);
                stage.on('click', function(e){
                    if (selected_camera_id != null && selected_camera_id != ''){
                        drawCircle(e.evt.offsetX, e.evt.offsetY, selected_camera_id);
                    }
                })
            }
        }
    }

    $(document).ready(function () {
        selected_location_id = $('#location_id').val();
        if (file_url != ''){
            var container = document.getElementById('container-canvas');
            container.style.width = drawing_width + 'px';
            container.style.height = drawing_height + 'px';
            stage = new Konva.Stage({
                container: 'container-canvas',
                width: drawing_width,
                height: drawing_height,
            });
            layer = new Konva.Layer();
            stage.add(layer);

            var camera_id = $('#camera_id').val();
            var x_coordinate = $('#x_coordinate').val();
            x_coordinate = parseInt(x_coordinate);
            var y_coordinate = $('#y_coordinate').val();
            y_coordinate = parseInt(y_coordinate);
            if (x_coordinate >= 0 && y_coordinate >= 0 && camera_id != ''){
                drawCircle(x_coordinate, y_coordinate, camera_id);
            }
            stage.on('click', function(e){
                drawCircle(e.evt.offsetX, e.evt.offsetY, camera_id);
            })
        }

        $('.clear-img').click(function(){
            clearCameraPoint();
        });
        var selectize_item = $('#camera_id').selectize({
            sortField: 'text',
        });
        var selectize_item = selectize_item[0].selectize;
        if (selected_camera_device_id != ''){
            selectize_item.setValue(selected_camera_device_id);
        }
        // $(".delete_drawings").click(function(e){
        //     e.preventDefault();
        //     delete_id = $(this).attr('delete_index');
        //     helper_confirm("dialog-confirm", "削除", "現場図面を削除します。<br />よろしいですか？", 300, "確認", "閉じる", function(){
        //         var frm_id = "#frm_delete_" + delete_id;
        //         $(frm_id).submit();
        //     });
        // });

  });
</script>
