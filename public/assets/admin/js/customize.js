


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
    $("header").fadeToggle('fast'); /*ふわっと表示*/
    $("#btn span").toggleClass("change");
  });
});
$(function () {
  $("ul.three-btns li button").click(function () {
    $("ul.three-btns li button").removeClass("active"); /*ふわっと表示*/
    $(this).addClass("active");
  });
});
$(function () {
  $(".cog").click(function () {
			$(this).toggleClass("on");
    $(".hidden-box").fadeToggle('fast'); /*ふわっと表示*/
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


$(function(){

var hash = location.hash;
if(hash.length){
if(hash.match(/#tab/)){
$('.list .inner').hide();
    $('.tab li').removeClass('active');
var tabname = hash.slice(4.1);
tabname = tabname - 1;
$('.list .inner').eq(tabname).fadeIn();
    $('.tab li').eq(tabname).addClass('active');
}else{
    $('.tab li').eq(0).addClass('active');
$('.list .inner').hide();
$('.list .inner').eq(0).fadeIn();
}
}
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
	$("input#is_rule_number").val( $(this).data('id') );
   //video.play();
 });
});
$(function () {
$('.video-pit').hover( function() {

      $('.video-pit p').fadeOut();

    },
    function() {

        //マウスカーソルが離れた時の処理

    }
);
	 });

    function addToToppage(block_type){
        let token = $('meta[name="csrf-token"]').attr('content');

        jQuery.ajax({
            url : '/admin/save_block',
            method: 'post',
            data: {
                block_type,
                _token:token,
            },

            error : function(){
                console.log('failed');
                helper_alert('alert-modal', '登録失敗', result, 300, '閉じる');
            },
            success: function(result){
                console.log(result);
                helper_alert('alert-modal', '登録完了', result, 300, '閉じる');
            }});
    }


