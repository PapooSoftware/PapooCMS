var FORM_ID = 2;
var FIELDSETS = [5, 6];

// ----------------------------------------------------

if (location.search.indexOf('form_manager_id='+String(FORM_ID))) {
$(document).ready(function(){

	/* Pro betroffenes Fieldset (Hinfahrt und Rückfahrt) */
	$.each(FIELDSETS, function (index, fsetid) {
		fsetid = 'field_'+String(fsetid);
		var fset = $('fieldset#'+fsetid);
		$('<input type="hidden" />').attr('name', fsetid+'_multiple').attr('value', '1').appendTo(fset);
		
		var variables = {};
		var precount = 1;
		
		/* Vorbelegte Werte extrahieren */
		fset.find('.labdiv input').each(function() {
			variables[$(this).attr('id')] = $(this).attr('value').split(';');
			if (variables[$(this).attr('id')].length > precount)
				precount = variables[$(this).attr('id')].length;
			$(this).attr('value', '');
		});
		
		/* Tabelle einfügen */
		var table = $('<table></table>').attr('id', fsetid+'_table').addClass('fahrt-table').appendTo(fset);
		var labels_tr = $('<tr></tr>').appendTo(table);
		labels_tr.append('<td title="Teilstrecke">TS</td>');
		
		/* Template-Tabellenzeile generieren */
		var template = $('<tr></tr>');
		template.append('<td>?</td>');
		
		fset.find('.labdiv label').each(function() {
			var th = $('<th></th>').appendTo(labels_tr);
			$(this).appendTo(th);
		});
		fset.find('.labdiv input').each(function() {
			var td = $('<td></td>').appendTo(template);
			//$(this).attr('id', $(this).attr('id')+'_1');
			//$(this).attr('name', $(this).attr('name')+'[]');
			$(this).appendTo(td);
		});
		fset.find('.labdiv').remove();
		
		$('<p class="addremovebuttons"><button style="visibility: hidden;" class="remove" type="button">Teilstrecke entfernen</button> <button class="add" type="button">Teilstrecke hinzufügen</button></p>').appendTo(fset);

		
		// -----------------
		
		var count = 0;
		
		function add_row() {
			if (count >= 30) return;
			count++;
			var row = template.clone().attr('id', fsetid+'_'+String(count)).appendTo(table);
			row.children().first().text(String(count));
			row.find('input').each(function() {
				var id = $(this).attr('id');
				if (variables[id].length >= count)
					$(this).attr('value', variables[id][count-1]);
				$(this).attr('id', $(this).attr('id')+'_'+String(count));
			});
			
			if (count > 1)
				fset.find('button.remove').css('visibility', 'visible');
		}
		function remove_row() {
			if (count >= 2) {
				table.find('tr').last().remove();
				count--;
			}
			
			if (count < 2)
				fset.find('button.remove').css('visibility', 'hidden');
			
		}
		
		fset.find('button.remove').click(remove_row);
		fset.find('button.add').click(add_row);
		
		for (var i = 0; i < precount; ++i)
			add_row();
		
		fset.parents('form').first().submit(function () {
		
			template.find('input').each(function() {
				var id = $(this).attr('id');
				var name = $(this).attr('name');
				var inputs = fset.find('input[name="'+name+'"]');
				var destination = $('<input type="hidden"/>').attr('name', name).attr('id', id).appendTo(fset);
				var result = [];
				inputs.each(function () {
					result.push($(this).attr('value').replace(';', ','));
					$(this).removeAttr('name');
					$(this).attr('disabled', 'disabled');
				});
				destination.attr('value', result.join(';'));
			});
		});
	});
});
}