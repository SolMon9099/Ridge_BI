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
    var drawVerticalLineAnim = function (p1, p2) {
        let count = 50;
        let counter = 0;
        let cvs = document.getElementById('c');
        let ctx = cvs.getContext('2d');
        var render = function () {
            counter++;
            let next = [
                Math.round((p2[0] - p1[0]) / count * counter) + p1[0],
                Math.round((p2[1] - p1[1]) / count * counter) + p1[1]
            ];
            ctx.beginPath();
            ctx.moveTo(p1[0], p1[1]);
            ctx.lineTo(next[0], next[1]);
            ctx.closePath();
            ctx.stroke();

            // 描画を繰り返す条件
            if (counter <= count) {
                requestAnimationFrame(render);
            }
        };
        render();
    };
    function drawSquare(event) {

    }
    var points = [];
    drawSquare = function (e) {
        let cvs = document.getElementById('c');
        let ctx = cvs.getContext('2d');
        // 2022.3.17 Add
        let container = document.getElementById('canvas-container');
        //親要素のサイズをCanvasに指定
        cvs.width = container.clientWidth;
        cvs.height = container.clientHeight;
        let color = "#color" + $("input#is_rule_number").val();
        color = $(color).val();
        // 2022.3.17 Add
        if (points.length === 4) {
            points = [];
        }
        points.push([e.offsetX, e.offsetY]);
        ctx.clearRect(0, 0, cvs.width, cvs.height);
        ctx.strokeStyle = color;
        ctx.fillStyle = color;
        ctx.lineWidth = 5;
        for (let i in points) {
            ctx.beginPath();
            ctx.arc(points[i][0], points[i][1], 5, 0, Math.PI * 2, true);
            ctx.fill();
        }
        if (points.length === 4) {
            // draw square
            let p = points.slice(0, points.length);
            let s = [];
            // 外積を用いる方法
            let is_cross = function (line1, line2) {
                var a = line1[0]; // A
                var b = line1[1]; // B
                var c = line2[0]; // C
                var d = line2[1]; // D
                let s = (b[0] - a[0]) * (c[1] - a[1]) - (c[0] - a[0]) * (b[1] - a[1]);
                let t = (b[0] - a[0]) * (d[1] - a[1]) - (d[0] - a[0]) * (b[1] - a[1]);
                return s * t < 0;
            }
            if (is_cross([p[0], p[1]], [p[2], p[3]])) {
                s = [p[0], p[2], p[1], p[3]];
            } else if (is_cross([p[0], p[2]], [p[1], p[3]])) {
                s = [p[0], p[1], p[2], p[3]];
            } else {
                s = [p[0], p[1], p[3], p[2]];
            }
            // draw lines
            for (let i = 0, j = s.length; i < j; i++) {
                let k = (i === s.length - 1 ? 0 : i + 1);
                drawVerticalLineAnim(s[i], s[k]);
            }
            checkDirection(s);
        }
    };
    mouseMove = function (e) {
        var borderWidth = 1;
        var rect = e.target.getBoundingClientRect();
        var x = e.clientX - rect.left - borderWidth;
        var y = e.clientY - rect.top - borderWidth;
        document.getElementById("debug").innerHTML = `X座標${x}:Y座標${y}`;
    };
</script>
