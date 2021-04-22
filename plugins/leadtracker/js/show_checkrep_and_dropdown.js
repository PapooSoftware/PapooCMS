"use strict";

function hide_element(obj) {
    obj.attr('aria-hidden', 'true');
    obj.css('display', 'none');
}
function show_element(obj) {
    obj.attr('aria-hidden', 'false');
    obj.css('display', 'block');
}

function open(evt) {
	evt.preventDefault();
    var obj1 = $('#form_placeholders');
    obj1.removeClass('placeholders_hidden').addClass('placeholders_shown');
    var obj2 = $('#checkrep_placeholders');
    obj2.removeClass('placeholders_hidden').addClass('placeholders_shown');
    var obj3 = $('#placeholder_label_open');
    obj3.removeClass('placeholders_label_shown').addClass('placeholders_label_hidden');
    var obj4 = $('#placeholder_label_close');
    obj4.removeClass('placeholders_label_hidden').addClass('placeholders_label_shown');
}

function close(evt) {
    evt.preventDefault();
    var obj1 = $('#form_placeholders');
    obj1.removeClass('placeholders_shown').addClass('placeholders_hidden');
    var obj2 = $('#checkrep_placeholders');
    obj2.removeClass('placeholders_shown').addClass('placeholders_hidden');
    var obj3 = $('#placeholder_label_open');
    obj3.removeClass('placeholders_label_hidden').addClass('placeholders_label_shown');
    var obj4 = $('#placeholder_label_close');
    obj4.removeClass('placeholders_label_shown').addClass('placeholders_label_hidden');
}

function showvars() {
    var obj1 = $('#leadtracker_checkreplace_select');
    var obj2 = $('#checkrep_isset');
    if (leadtracker_checkreplace_activate.checked == 1)
    {
        show_element(obj1);
        show_element(obj2);
    }
    else
    {
        hide_element(obj1);
        hide_element(obj2);
        obj1.value('default');
    }
}
 
document.getElementById('placeholder_label_open').onclick = open;
document.getElementById('placeholder_label_close').onclick = close;