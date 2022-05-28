@extends('admin.layouts.app')

@section('content')

<?php
    $floors = array();
    $drawing_files = array();
    foreach ($drawings as $key => $drawing) {
        $floors[$drawing->id] = $drawing->floor_number;
        $drawing_files[$drawing->id] = $drawing->drawing_file_path;
    }
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
            <li><button type="button" class="new">設置場所の新規登録</button></li>
            <li><button type="button" class="new">設置場所の編集</button></li>
            <li><button type="button" class="new">設置場所の削除</button></li>
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
        <div id = "canvas-container" style="background: url({{$file_url}})">
            <canvas onclick="pointMark(event)" id="canvas"></canvas>
        </div>
        <div class="btns">
            <button type="submit" class="ok">更新</button>
        </div>

        <input type = "hidden" name="camera_mapping_info" id = "camera_mapping_info" value="" />
        </form>
    </div>
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

<style>
    #canvas-container{
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
</style>
<script>
    var delete_id = "";
    var radius = <?php echo config('const.camera_mark_radius');?>;
    var points = [];
    function changeFloor(){
        var selected_drawing_id = $('#select_floor').find(":selected").val();
        var path_array = <?php echo json_encode($drawing_files);?>;
        var img_path =  '<?php echo asset('storage/drawings/');?>' + '/' + path_array[selected_drawing_id];
        $('#canvas-container').css('background-image', 'url('+img_path+')');
    }

    function pointMark(e){
        let cvs = document.getElementById("canvas");
        let cx = cvs.getContext('2d');
        var container = document.getElementById('canvas-container');
        cvs.width = container.clientWidth;
        cvs.height = container.clientHeight;
        points.push([e.offsetX, e.offsetY]);

        cx.clearRect(0, 0, cvs.width, cvs.height);
        cx.strokeStyle = '#FF0000';
        cx.fillStyle = '#FF0000';
        cx.lineWidth = radius;
        for (var i in points){
            cx.beginPath();
            cx.arc(points[i][0], points[i][1], radius, 0, 2 * Math.PI);
            cx.fill();
        }
    }

    $(document).ready(function () {
        let cvs = document.getElementById("canvas");
        let cx = cvs.getContext('2d');
        var container = document.getElementById('canvas-container');
        cvs.width = container.clientWidth;
        cvs.height = container.clientHeight;

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
