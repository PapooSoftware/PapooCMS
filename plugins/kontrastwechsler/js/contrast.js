$(document).ready(function () {
	function setStyle(id) {
		$.ajax({
			url: ContrastChanger.config.webpath + "plugins/kontrastwechsler/lib/kontrastwechsler_ajax.php",
			data: {kontrast_id: id},
			dataType: "text",
		}).success(function(cssText) {
			document.getElementById('currentStyleSheet').innerHTML = cssText;
			//document.cookie = "contrast=" + id + "; path=" + ContrastChanger.config.webpath + ";";
		});
	}

	$('[data-kontrast]').click(function (event) {
		event.preventDefault();
		event.stopImmediatePropagation();

		var $target = $(this);

		$.ajax({
			url: ContrastChanger.config.webpath + "plugins/kontrastwechsler/lib/kontrastwechsler_ajax.php",
			data: {kontrast_id: $target.attr("data-kontrast")},
			dataType: "text",
		}).success(function(cssText) {
			document.getElementById('currentStyleSheet').innerHTML = cssText;
			$('#currentStyleSheet').attr('data-kontrast', $target.attr("data-kontrast"));
			//document.cookie = "contrast=" + $target.attr("data-kontrast") + "; path=" + ContrastChanger.config.webpath + ";";
		});
	});

	// Benötigt um den letzten ausgewählten Kontrast zu setzen.
	setStyle(ContrastChanger.config.initialStyle);
});