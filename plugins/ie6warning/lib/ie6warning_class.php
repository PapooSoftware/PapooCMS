<?php

/**
 * Class ie6warning_class
 */
class ie6warning_class
{
	function __construct()
	{
		global $db, $cms;
		$this->db = &$db;
		$this->cms = &$cms;

		$this->make_ie6warning();
	}

	function make_ie6warning()
	{
		if (defined("admin")) {
			$this->activate_jquery();
		}
	}

	function activate_jquery()
	{
		//SQL-Anweisung f�r das Auslesen des H�kchens
		$query = sprintf("SELECT config_jquery_aktivieren_label FROM %s", $this->cms->tbname['papoo_config']);

		$result = $this->db->get_results($query);

		//$value ist 0, falls das h�kchen nicht gestetzt ist und 1, falls doch
		$value = $result[0]->config_jquery_aktivieren_label;

		//Falls das H�kchen nicht gesetzt ist, wird es gesetzt
		if ($value == 0) {
			$query = sprintf("UPDATE %s 
                            SET config_jquery_aktivieren_label='1'", $this->cms->tbname['papoo_config']);
			$this->db->query($query);
		}
	}

	function output_filter()
	{
		$ie6_output ="\n".
			'<script>
    var $buoop = {c:2};
    function $buo_f(){
     var e = document.createElement("script");
     e.src = "/plugins/ie6warning/js/ie6bar.min.js";
     document.body.appendChild(e);
    };
    try {document.addEventListener("DOMContentLoaded", $buo_f,false)}
    catch(e){window.attachEvent("onload", $buo_f)}
</script> ';

		global $output;

		$output = str_replace("</title>","</title>".$ie6_output,$output);
	}
}

$ie6warning_class = new ie6warning_class();
