


window.onload = () => {
  // canvas準備
  const board = document.querySelector("#c"); //getElementById()等でも可。オブジェクトが取れれば良い。
  const ctx = board.getContext("2d");

  // 画像読み込み
  const chara = new Image();
  chara.src = "https://polsjapan.net/bi/common/img/pic-big.jpg"; // 画像のURLを指定
  chara.onload = () => {
    ctx.drawImage(chara, 0, 0);
  };

  const canvas = document.createElement('canvas');

  chara.onload = () => {
    // Canvasを画像のサイズに合わせる
    canvas.height = chara.height;
    canvas.width = chara.width;

    // Canvasに描画する
    ctx.drawImage(chara, 0, 0);
  };


  const slider = document.getElementById('zoom-slider');
  slider.value = 1;
  // 倍率の最小・最大値
  slider.min = 1;
  slider.max = 2;
  // 粒度
  slider.step = 'any';

  // スライダーが動いたら拡大・縮小して再描画する
  slider.addEventListener('input', e => {
    // 一旦クリア 
    ctx.clearRect(0, 0, chara.width, chara.height);
    // 倍率変更
    const scale = e.target.value;
    ctx.scale(scale, scale);
    // 再描画
    ctx.drawImage(chara, 0, 0);
    // 変換マトリクスを元に戻す
    ctx.scale(1 / scale, 1 / scale);
  });


  /** ドラッグで移動 */
  // ドラッグ状態かどうか
  const isDragging = false;
  // ドラッグ開始位置
  const start = {
    x: 0,
    y: 0
  };
  // ドラッグ中の位置
  const diff = {
    x: 0,
    y: 0
  };
  // ドラッグ終了後の位置
  const end = {
    x: 0,
    y: 0
  }
  const redraw = () => {
    ctx.clearRect(0, 0, chara.width, chara.height);
    ctx.drawImage(img, diff.x, diff.y)
  };
  chara.addEventListener('mousedown', event => {
    isDragging = true;
    start.x = event.clientX;
    start.y = event.clientY;
  });
  chara.addEventListener('mousemove', event => {
    if (isDragging) {
      diff.x = (event.clientX - start.x) + end.x;
      diff.y = (event.clientY - start.y) + end.y;
      redraw();
    }
  });
  chara.addEventListener('mouseup', event => {
    isDragging = false;
    end.x = diff.x;
    end.y = diff.y;
  });
};
//クリックで■

$(function () {
  /**
   * canvas 操作用の context を取得する。
   */
  function getCtx() {
    var canvas = document.getElementById('c');
    return canvas.getContext('2d');
  };

  /**
   * canvas 内のクリックした座標に青薄色の四角形を描画する
   */
  function drawRectAtClickedPos(ev) {
    // クリックした座標を取得する。
    var posX = ev.pageX - 300,
      posY = ev.pageY - 100;
    var ctx = getCtx();
    // クリックした箇所に半透明の青色の四角を描画する。
    ctx.fillStyle = 'rgba(0, 0, 200, 0)';
    ctx.lineWidth = 2; // 線幅
    ctx.strokeStyle = 'red';
    ctx.strokeRect(posX, posY, 100, 100);
    ctx.fillRect(posX, posY, 100, 100);
  };

  // canvas のクリックに紐付ける。
  $('canvas').one("click", drawRectAtClickedPos);

});


//フォルダー追加
$(function () {
  var wid = $(window).width();
  if (wid < 736) {
    $("#dropmenu li a").click(function () {
      $(this).toggleClass("on");
      $(this).next("ul").slideToggle();
    });
  }
});


$(function () {

  //data-hrefの属性を持つtrを選択しclassにclickableを付加
  $('table.area tr[data-href]').addClass('clickable')

    //クリックイベント
    .click(function (e) {

      //e.targetはクリックした要素自体、それがa要素以外であれば
      if (!$(e.target).is('a')) {

        //その要素の先祖要素で一番近いtrの
        //data-href属性の値に書かれているURLに遷移する
        window.location = $(e.target).closest('table.area tr').data('href');
      }
    });
});

$(function () {
  $("a#btn").click(function () {
    $("#leftside").fadeToggle('fast'); /*ふわっと表示*/
    $("#btn span").toggleClass("change");
  });
});

$(function () {
  const formInputs = document.getElementsByClassName("file");
  const length = formInputs.length;
  for (let i = 0; i < length; i++) {
    formInputs[i].onchange = function () {
      const file = this.files[0].name;
      const label = this.nextElementSibling;
      if (!label.classList.contains("changed")) {
        const span = document.createElement("span");
        span.className = "filename";
        this.parentNode.appendChild(span);
        label.classList.add("changed");
      }
      label.nextElementSibling.innerHTML = file;
    };
  }
});

$(function () {
  //tabをクリックしたときの動作
  $('.tab li').click(function () {
    //クリックしたタブのindexを取得
    var index = $('.tab li').index(this);

    $('.list .inner').hide().removeClass('active');
    $('.list .inner').eq(index).fadeIn().addClass('active');

    $('.tab li').removeClass('active');
    $(this).addClass('active');
  });

  //ページャーをクリックしたときの動作
  $('.tab_sub li').click(function () {
    //クリックしたページャーのindexを取得
    var index = $('.inner.active .tab_sub li').index(this);

    $('.inner.active .tab_sub li').removeClass('active');
    $(this).addClass('active');

    //テーブル操作
    $('.inner.active .scroll').hide().removeClass('active');
    $('.inner.active .scroll').eq(index).fadeIn().addClass('active');
  });


  //////////////////////////////////////////////////////////////////////////////////////////
});
$(function () {
  $('.js-upload-file').on('change', function (e) { //ファイルが選択されたら
    var reader = new FileReader();
    reader.onload = function (e) {
      $("#preview").attr('src', e.target.result);
    }
    reader.readAsDataURL(e.target.files[0]);
    let file = $(this).prop('files')[0]; //ファイルの情報を代入(file.name=ファイル名/file.size=ファイルサイズ/file.type=ファイルタイプ)
    $('.js-upload-filename').text(file.name); //ファイル名を出力
    $('.js-upload-fileclear').show(); //クリアボタンを表示
  });
  $('.js-upload-fileclear').click(function () { //クリアボタンがクリックされたら
    $('.js-upload-file').val(''); //inputをリセット
    $('.js-upload-filename').text('ファイルが未選択です'); //ファイル名をリセット
    $(this).hide(); //クリアボタンを非表示
  });
});
$(function () {
  var count = 1;
  var countup = function () {
    $('.count_up').text(count);
    count = count + 1;
  }
  setInterval(countup, 1000);
});


$(function () {
 var video = $("video").get(0);
  $(".play-video").on("click",function(){
			$(".draw-area").hide();
					$(".video-area").show();
   video.play();
 });
});