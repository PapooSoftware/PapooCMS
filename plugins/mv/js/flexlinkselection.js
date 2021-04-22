function flexselection_to(select_dom)
{
	var mv_id = select_dom.value;
	var form_id = $(select_dom).attr('data-of-form-id');
	$('select.mv-select-flex-entry[data-of-form-id="'+form_id+'"]').hide().prop('disabled', true);
	$('select.mv-select-flex-entry[data-of-mv="'+mv_id+'"][data-of-form-id="'+form_id+'"]').show().prop('disabled', false);
}

// $("form[id=\"formk\"]").submit(function(form_event) {
// 	// console.log(JSON.stringify(this));
// 	// console.log(JSON.stringify(form_event));
//
// 	$("form[id=\"formk\"] .baumname-auswahl-feld").each(function(idx, baumnameauswahlfeld) {
// 		var value_to_append_to_radio_button = $(baumnameauswahlfeld).children("select")[0].value;
// 		var active_radio_button = $(baumnameauswahlfeld).siblings(".eintrag-auswahl-radio").find("input[type=\"radio\"]:checked")[0];
//
// 		console.log(active_radio_button);
// 		if(active_radio_button) {
// 			active_radio_button = $(active_radio_button);
// 			active_radio_button.attr("value", active_radio_button.attr("value") + ";" + value_to_append_to_radio_button); 
// 		}
// 	});
// });
