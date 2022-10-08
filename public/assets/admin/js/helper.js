/* str to split*/
function helper_confirm(id, title, content, width, btnConfirm, btnClose, callback) {
    var dialog = $( "#"+id );

    dialog.attr('title', title);
    $("#confirm_text", dialog).html(content);

    $('html, body').addClass('lock');
    // オーバーレイ用の要素を追加
    $('body').append('<div class="modal-overlay"></div>');
    // オーバーレイをフェードイン
    $('.modal-overlay').fadeIn('fast');
    var confirm_buttons = {};
    if(btnConfirm){
        confirm_buttons[btnConfirm] = function() {
            if(callback)
                callback();
                $( this ).dialog( "close" );
                $( this ).parent().unwrap("<div class='modal-wrap'></div>");
                $('.modal-overlay').fadeOut('fast', function () {
                    // html、bodyの固定解除
                    $('html, body').removeClass('lock');
                    // オーバーレイを削除
                    $('.modal-overlay').remove();
                });
        };
    }
    if(btnClose){
        confirm_buttons[btnClose] = function() {
            $( this ).dialog( "close" );
            $( this ).parent().unwrap("<div class='modal-wrap'></div>");
            $('.modal-overlay').fadeOut('fast', function () {
                // html、bodyの固定解除
                $('html, body').removeClass('lock');
                // オーバーレイを削除
                $('.modal-overlay').remove();
            });
        };
    }

    dialog.dialog({
        resizable: false,
        height: "auto",
        width: width,
        modal: true,
        buttons: confirm_buttons,
    });
    dialog.parent().wrap("<div class='modal-wrap'></div>");
    $('.modal-wrap').fadeIn();
    dialog.parent().css('position', 'initial');
    dialog.parent().css('margin', 'auto');
    dialog.parent().css('margin-top', '20%');
    $('.ui-dialog').click(function (e) {
        e.stopPropagation();
    });
    $('.modal-wrap, .ui-dialog-titlebar-close').off().click(function () {
        dialog.dialog( "close" );
        $('.modal-overlay').fadeOut('fast', function () {
            // html、bodyの固定解除
            $('html, body').removeClass('lock');
            // オーバーレイを削除
            $('.modal-overlay').remove();
            dialog.parent().unwrap("<div class='modal-wrap'></div>");
        });
    });
}

function helper_alert(id, title, content, width, btnClose) {
    var dialog = $("#" + id);

    dialog.attr('title', title);
    $('span.ui-dialog-title', dialog.parent()).text(title);
    $("#confirm_text", dialog).html(content);

    $('html, body').addClass('lock');
    // オーバーレイ用の要素を追加
    $('body').append('<div class="modal-overlay"></div>');
    // オーバーレイをフェードイン
    $('.modal-overlay').fadeIn('fast');

    var confirm_buttons = {};
    confirm_buttons[btnClose] = function() {
        $(this).dialog("close");
        $( this ).parent().unwrap("<div class='modal-wrap'></div>");
        $('.modal-overlay').fadeOut('fast', function () {
            // html、bodyの固定解除
            $('html, body').removeClass('lock');
            // オーバーレイを削除
            $('.modal-overlay').remove();
        });
    };

    dialog.dialog({
        resizable: false,
        height: "auto",
        width: width,
        modal: true,
        buttons: confirm_buttons
    });
    dialog.parent().wrap("<div class='modal-wrap'></div>");
    $('.modal-wrap').fadeIn();
    dialog.parent().css('position', 'initial');
    dialog.parent().css('margin', 'auto');
    dialog.parent().css('margin-top', '20%');
    $('.ui-dialog').click(function (e) {
        e.stopPropagation();
    });
    $('.modal-wrap, .ui-dialog-titlebar-close').off().click(function () {
        dialog.dialog( "close" );
        $('.modal-overlay').fadeOut('fast', function () {
            // html、bodyの固定解除
            $('html, body').removeClass('lock');
            // オーバーレイを削除
            $('.modal-overlay').remove();
            dialog.parent().unwrap("<div class='modal-wrap'></div>");
        });
    });
}
