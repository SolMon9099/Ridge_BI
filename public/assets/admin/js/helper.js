/* str to split*/
function helper_confirm(id, title, content, width, btnConfirm, btnClose, callback) {
    var dialog = $( "#"+id );

    dialog.attr('title', title);
    $("#confirm_text", dialog).html(content);
    var confirm_buttons = {};
    if(btnConfirm){
        confirm_buttons[btnConfirm] = function() {
            if(callback)
                callback();
                $( this ).dialog( "close" );
        };
    }
    if(btnClose){
        confirm_buttons[btnClose] = function() {
            $( this ).dialog( "close" );
        };
    }

    dialog.dialog({
        resizable: false,
        height: "auto",
        width: width,
        modal: true,
        buttons: confirm_buttons
    });
}

function helper_alert(id, title, content, width, btnClose) {
    var dialog = $("#" + id);

    dialog.attr('title', title);
    $('span.ui-dialog-title', dialog.parent()).text(title);
    $("#confirm_text", dialog).html(content);
    var confirm_buttons = {};
    confirm_buttons[btnClose] = function() {
        $(this).dialog("close");
    };

    dialog.dialog({
        resizable: false,
        height: "auto",
        width: width,
        modal: true,
        buttons: confirm_buttons
    });
}
