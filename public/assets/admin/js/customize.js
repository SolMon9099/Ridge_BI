


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
        if ($(this).hasClass('on')){
            $('svg', $(this)).hide();
            $('.close-gear-icon', $(this)).show();
            $(this).css('background', 'transparent');
        } else {
            $('svg', $(this)).show();
            $('.close-gear-icon', $(this)).hide();
            $(this).css('background', 'white');
        }
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

function addToToppage(block_type, options = null){
    let token = $('meta[name="csrf-token"]').attr('content');

    $.ajax({
        headers: {
            'X-CSRF-TOKEN': token
        },
        url : '/admin/save_block',
        method: 'post',
        data: {
            block_type,
            options,
            _token:token,
        },

        error : function(){
            console.log('failed');
            helper_alert('alert-modal', '登録失敗', result, 320, '閉じる');
        },
        success: function(result){
            console.log(result);
            helper_alert('alert-modal', '登録完了', result, 320, '閉じる');
        }});
}

function saveSearchOptions(page_name, search_params){
    let token = $('meta[name="csrf-token"]').attr('content');
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': token
        },
        url : '/admin/save_search_option',
        method: 'post',
        data: {
            page_name:page_name,
            search_params,
            _token:token,
        },

        error : function(){
            console.log('failed');
        },
        success: function(result){
            console.log(result);
        }});
}

function updateTopBlockData(changed_data){
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url : '/admin/AjaxUpdate',
        method: 'post',
        data: {
            changed_data,
            _token:$('meta[name="csrf-token"]').attr('content'),
        },
        error : function(){
            console.log('failed');
        },
        success: function(result){
            console.log(result);
        }
    });
}

function deleteTopBlock(block_id){
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url : '/admin/AjaxDelete',
        method: 'post',
        data: {
            id:block_id,
            _token:$('meta[name="csrf-token"]').attr('content'),
        },
        error : function(){
            console.log('failed');
        },
        success: function(result){
            console.log(result);
        }
    });
}

function refresshCameraImg(){
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url : '/admin/camera/AjaxRefreshImg',
        method: 'post',
        data: {
            _token:$('meta[name="csrf-token"]').attr('content'),
        },
        error : function(){
            console.log('failed');
        },
        success: function(result){
            console.log(result);
        }
    });
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

function formatDateLine(val) {  // format : 2018-02-21
    if (val == undefined || val == null || val =='') return '';
    let dt = new Date(val);
    var y = dt.getFullYear();
    var m = ("00" + (dt.getMonth() + 1)).slice(-2);
    var d = ("00" + dt.getDate()).slice(-2);
    var result = y + '-' + m + '-' + d;
    return result;
};

function formatYearMonth(val){  // format : 2018-02
    if (val == undefined || val == null || val =='') return '';
    let dt = new Date(val);
    var y = dt.getFullYear();
    var m = ("00" + (dt.getMonth() + 1)).slice(-2);
    var result = y + '-' + m;
    return result;
}

function getWeekNumber(d) {
    // Copy date so don't modify original
    d = new Date(Date.UTC(d.getFullYear(), d.getMonth(), d.getDate()));
    // Set to nearest Thursday: current date + 4 - current day number
    // Make Sunday's day number 7
    d.setUTCDate(d.getUTCDate() + 4 - (d.getUTCDay()||7));
    // Get first day of year
    var yearStart = new Date(Date.UTC(d.getUTCFullYear(),0,1));
    // Calculate full weeks to nearest Thursday
    var weekNo = Math.ceil(( ( (d - yearStart) / 86400000) + 1)/7);
    // Return array of year and week number
    return weekNo;
}

function formatYearWeekNum(val){    //// format : 2018/42
    if (val == undefined || val == null || val =='') return '';
    let dt = new Date(val);
    var y = dt.getFullYear();
    var w = getWeekNumber(dt);
    var result = y + '/' + w;
    return result;
}

function formatDateTime(val) {
    if(val == undefined || val == null || val == "") return "";

    if(val instanceof Date) {
        var y = val.getFullYear();
        var m = ("00" + (val.getMonth() + 1)).slice(-2);
        var d = ("00" + val.getDate()).slice(-2);
        var h = ("00" + val.getHours()).slice(-2);
        var min = ("00" + val.getMinutes()).slice(-2);
        var s = ("00" + val.getSeconds()).slice(-2);
        var result = y + "/" + m + "/" + d + " " + h + ":" + min + ":" + s;
        return new Date(result);
    }

    var datas = val.split(" ");
    var ymd = datas[0].split("-");
    var temp = "";
    if(parseInt(ymd[1]) < 10) {
        temp = parseInt(ymd[1]);
        ymd[1] = "0" + temp.toString();
    }
    if(parseInt(ymd[2]) < 10) {
        temp = parseInt(ymd[2]);
        ymd[2] = "0" + temp.toString();
    }
    var dt_str = ymd.join("/");
    if(datas.length>1){
        dt_str = dt_str +" "+ datas[1];
    }
    return new Date(dt_str);
};
function formatDateTimeStr (val) {
    if(val == undefined || val == null || val == "")
        return "";

    if(val instanceof Date) {
        var y = val.getFullYear();
        var m = ("00" + (val.getMonth() + 1)).slice(-2);
        var d = ("00" + val.getDate()).slice(-2);
        var h = ("00" + val.getHours()).slice(-2);
        var min = ("00" + val.getMinutes()).slice(-2);
        var s = ("00" + val.getSeconds()).slice(-2);
        var result = y + "-" + m + "-" + d + " " + h + ":" + min + ":" + s;
        return result;
    } else {
        return "";
    }
};

function calcOpacity(score){
    if (score === null) return 0.8;

    if (score <= 1 && score > 0.8) return 0;
    if (score <= 0.8 && score > 0.6) return 0.2;
    if (score <= 0.6 && score > 0.4) return 0.4;
    if (score <= 0.4 && score > 0) return 0.6;
    return 0.6;
}
