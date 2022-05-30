<?php
$action_options = config('const.action');
?>
<div class="no-scroll">
    <div class="title-wrap sp-m">
        <button type="button" class="edit left create-rull">＋ ルール追加</button>
    </div>
    <form action="danger_area.php" method="post" name="form1" id="form1">
        <div class="scroll">
            <table class="table2 text-centre">
                <thead>
                <tr>
                    <th>アクション</th>
                    <th>カラー</th>
                    <th></th>
                </tr>
                </thead>
                <tbody id="rull-list">
                <tr id="route-template" style="display: none">
                    <td><select class="select-box">
                            <option>ルールを選択</option>
                            @foreach($action_options as $action)
                                <option>{{$action}}</option>
                            @endforeach
                        </select>
                    </td>
                    <td><input type="color" id="color2" name="color2"></td>
                    <td><button type="button" id="btn_area" class="edit play-video" data-id="2">エリア選択</button></td>
                </tr>
                </tbody>
            </table>
        </div>
        <div class="video-area">
            <div id="canvas-container" style="background: url({{asset('assets/admin/img/canvas.jpg')}}) no-repeat;	background-size:100%;">
                <canvas id="c" onclick="drawSquare(this)"></canvas>
            </div>
            <div id="debug"></div>
            <div class="btns" id="direction">
                <button type="submit" class="ok">決定</button>
            </div>
        </div>

        <input type="hidden" id="is_rule_number" name="is_rule_number">
    </form>
</div>
</div>
<script>

    $(document).ready(function() {
        function addRoute() {
            var tr = $("#route-template").clone().show();
            $("#btn_area", tr).click(function() {
                $(".draw-area").hide();
                $(".video-area").show();
                $("input#is_rule_number").val( $(this).data('id') );
            });
            $("#rull-list").append(tr);
        }
        $(".create-rull").click(function(e) {
            addRoute();
        });
    });
    function drawSquare(event) {

    }
</script>
