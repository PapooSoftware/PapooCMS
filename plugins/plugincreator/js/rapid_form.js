/**
 * Created by andreas on 23.02.16.
 */

//$.fn.bindFirst = function(name, fn) {
//    this.bind(name, fn);
//    var handlers = this.data('events')[name.split('.')[0]];
//    var handler = handlers.pop;
//    handlers.splice(0, 0, handler);
//};

$(function() {

    var diag = $("div.artikel .popup-info-content");

    diag.dialog(
        {
            minWidth: 500,
            autoOpen: false,

            show: {
                effect: "blind",
                duration: 250
            },
            hide: {
                effect: "blind",
                duration: 250
            }
        }
    );

    $("div.artikel .rapid_form > .rapid_dev_help_button").click(function () {

        diag.dialog("open");

        //$(".ui-dialog > div.ui-dialog-titlebar > button.ui-button.ui-dialog-titlebar-close").bindFirst("click", function() {
        //    var content = $(".popup-info-content");
        //    $("div.artikel .popup-info-slot").html(content);
        //
        //    $("div.artikel .popup-info-content").removeClass("ui-dialog-content").removeClass("ui-widget-content").removeAttr("id", "style");
        //
        //    $(".ui-dialog > div.ui-dialog-titlebar > button.ui-button.ui-dialog-titlebar-close").unbind();
        //
        //    $("div.ui-dialog.ui-widget.ui-widget-content.ui-front[role=\"dialog\"]").remove();
        //
        //
        //});
    });

});
//$("div.artikel .rapid_form > .rapid_dev_ablosen").click(function () {
//
//    $("div.artikel .rapid_form").dialog();
//
//    $(".ui-dialog > div.ui-dialog-titlebar > button.ui-button.ui-dialog-titlebar-close").bindFirst("click", function() {
//        var content = $("div.rapid_form");
//        $("div.artikel > #rapid_form_slot").html(content);
//
//        $("div.artikel div.rapid_form").removeClass("ui-dialog-content", "ui-widget-content").removeAttr("id", "style");
//
//        //$("div.ui-dialog.ui-widget.ui-widget-content.ui-front[role=\"dialog\"]").remove();
//    });
//});

//if($("div.artikel #rapid_form_slot").attr("data-isopen")) {
//    $("div.artikel .rapid_form > .rapid_dev_ablosen").trigger("click");
//}
