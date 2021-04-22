eintraege_ausblenden();

function register_eintrag_zeigen($eintrag_id)
{
	eintraege_ausblenden();
	$('div#register_eintrag_'+$eintrag_id).css({"display": "block"});
}

function eintraege_ausblenden()
{
	$('div.register_eintraege').css({"display": "none"});
}
