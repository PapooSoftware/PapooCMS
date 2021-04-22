"use strict";

function hide_element(obj) {
	obj.attr('aria-hidden', 'true');
	obj.css('display', 'none');
}
function show_element(obj) {
	obj.attr('aria-hidden', 'false');
	obj.css('display', 'block');
}

function show_progress() {
	var obj = $('#statistics-progress');
	show_element(obj);
	obj.removeClass('error').addClass('message message-progress');
	show_element($('#statistics-progress-pending'));
	hide_element($('#statistics-progress-failed'));
	hide_element($('#statistics-progress-done'));
	$('#statistics-progress li').removeClass("failed done");
}

function ajax_statistics(evt) {
	evt.preventDefault();
	show_progress();
	$('button').prop('disabled', true);
	window.location.hash = 'statistics-progress';
	var box = $('#statistics-progress');
	
	var steps = $('#statistics-progress li');
	var i = 0;
	
	function next_step() {
		var step = steps[i];
		// Bei Erfolg
		if (!step) {
  	    	box.removeClass('message-progress');
			hide_element($('#statistics-progress-pending'));
            show_element($('#statistics-progress-done'));
			$('button').prop('disabled', false);
			return;
		}
		
		// Throbber aktivieren
		step.className = 'current';
		
		// AJAX-Request
		$.ajax(location.href, {
			type: 'POST',
			async: true,
			data: {
				ajax: 'generate_statistics',
				step: i
			},
			error: function(){
				box.removeClass('message').addClass('error');
				hide_element($('#statistics-progress-pending'));
				show_element($('#statistics-progress-failed'));
				step.className = 'failed';
				$('button').prop('disabled', false);
			},
			success: function(){
				step.className = 'done';
				// Nächster Schritt
				next_step();
			}
		});
		
		i+=1;
	}
	next_step();
	
	return false;
}
 
document.getElementById('btn-generate-statistics').onclick = ajax_statistics;