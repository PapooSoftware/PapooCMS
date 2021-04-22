
var checkboxes = $('input.activecheckbox');
checkboxes.removeAttr('disabled');
checkboxes.change(function(){
	var checkbox = this;
	var newstate = checkbox.checked;
	checkbox.disabled = true;
	$.ajax({
		type: "POST",
		data: {
			'ajax': '1',
			'messe_id': checkbox.name.substr(7),
			'active': (newstate)?"1":"0"
		},
		success: function() {
			checkbox.disabled = false;
		},
		error: function() {
			checkbox.checked = !newstate;
			checkbox.disabled = false;
		}
		
	});
});