  function playVideo() {
    const video = document.getElementById("video1");
    if (video.paused) {
      video.play();
    } else {
      video.pause();
    }


    const canvas = document.getElementById("c");
    setInterval(function () {
    canvas.getContext("2d").drawImage(video, 0, 0, 768, 414);
    }, 1000 / 30);
    const img = new Image();
    img.src = document.getElementById("c");
    img.crossOrigin = 'anonymous';

    img.onload = () => {
      // Canvasを画像のサイズに合わせる
      canvas.height = img.height;
      canvas.width = img.width;

      // Canvasに描画する
      ctx.drawImage(img, 0, 0);
    };

    const ctx = canvas.getContext('2d');
    const slider = document.getElementById('zoom-slider');
    slider.value = 1;
    // 倍率の最小・最大値
    slider.min = 1;
    slider.max = 2;
    // 粒度
    slider.step = '0.5';

    // スライダーが動いたら拡大・縮小して再描画する
    slider.addEventListener('input', e => {
      // 一旦クリア 
      ctx.clearRect(0, 0, canvas.width, canvas.height);
      // 倍率変更
      const scale = e.target.value;
      ctx.scale(scale, scale);
      // 再描画  
					
      ctx.drawImage(img, 0, 0);
      // 変換マトリクスを元に戻す
  ctx.scale(1 / scale, 1 / scale);
    });
			
			     function getCtx() {
          var canvas = document.getElementById('c');
          return canvas.getContext('2d');
        };

    function drawRectAtClickedPos(ev) {
      // クリックした座標を取得する。
      var posX = ev.pageX - 400,
        posY = ev.pageY - 400;
         var ctx = getCtx();

      // クリックした箇所に半透明の青色の四角を描画する。
      ctx.fillStyle = 'rgba(0, 0, 0, 1)';
      ctx.fillRect(posX, posY, 200, 200);
					
    };

    // canvas のクリックに紐付ける。
    $('canvas').click(drawRectAtClickedPos);


  }


  function drawVideo() {
    var video = document.getElementById("video1");
    var canvas2 = document.getElementById("c2");
    canvas2.getContext("2d").drawImage(video, 0, 0, 480, 270);
    canvas2.classList.add("show");
  }

  function restart() {
    var video = document.getElementById("video1");
    video.currentTime = 0;
  }


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
