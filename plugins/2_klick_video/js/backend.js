"use strict";

$('form button[data-ajax="true"]').on('click', function(evt)
{
	var me = this;
	evt.preventDefault();
	var action = me.form.getAttribute('action');
	var data = {};
	data[me.name] = me.value;
	me.disabled = true;
	me.style.cursor = 'wait';

	$.ajax({
		url: action,
		method: 'POST',
		data: data,
		complete: function () {
			me.style.cursor = 'cursor';
			me.disabled = false;
		}
	});

	return false;
})