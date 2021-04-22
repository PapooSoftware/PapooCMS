

function flex_select(field1, field2, mappings)
{
	var f1_id = field1;
	var f2_id = field2;
	$(document).ready(function() {
		var form = $('input[name="onemv"]').parents('form');
		var f1 = form.find('select[name$="_'+parseInt(f1_id)+'"]');
		var f2 = form.find('select[name$="_'+parseInt(f2_id)+'"]');
		if (form.length != 1) return;
		if (f1.length != 1) return;
		if (f2.length != 1) return;
		// Speichere Kopie der Optionen außerhalb des DOM
		var options = f2.children('option').clone();
		
		// Bei Auswahl in Feld 1
		f1.change(function() {
			// Lösche alle Optionen in Feld 2
			f2.children('option').remove();
			var ok = false;
			// Gehe Zuordnungsliste durch
			if (f1.val() != '')
			{
				var item = parseInt(f1.val());
				for(var i = 0; i < mappings.length; i++)
				{
					if (mappings[i][0] == item) {
						options.each(function(index, Element){
							if (this.getAttribute('value') == '') $(this).clone().appendTo(f2);
							else
								for(var j = 0; j < mappings[i][1].length; j++)
								{
									// Wenn Option in Liste, wieder zu Feld 2 hinzufügen
									if (parseInt(this.getAttribute("value")) == mappings[i][1][j]) {
										ok = true;
										$(this).clone().appendTo(f2);
										break;
									}
								}
						});
					}
				}
			}
			// Wenn Liste leer, zeige alle Elemente an
			if (!ok) options.clone().appendTo(f2);
			// Erzeuge Changed-Event für gebundenes Feld
			f2.trigger('change');
		})
	});
}
