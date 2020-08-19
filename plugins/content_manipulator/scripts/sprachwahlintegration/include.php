<?php

/**
 * Class sprachwahlintegration
 */
class sprachwahlintegration
{
	/**
	 * sprachwahlintegration constructor.
	 */
	function __construct()
	{
		global $content, $checked, $cms, $db;
		$this->content = &$content;
		$this->checked = &$checked;
		$this->cms = &$cms;
		$this->db = &$db;

		$this->set_backend_message();

		IfNotSetNull($_GET['is_lp']);

		if (!defined("admin") || ($_GET['is_lp'] == 1)) {
			global $output;
			if (strpos($output, "#sprachwahl#") !== false) {
				#$output = $this->create_sprachwahlintegration_styles_default($output);
				$output = $this->create_sprachwahlintegration_foundation_papoo($output);
			}
		}
	}

	/**
	 * sprachwahlintegration::set_backend_message()
	 * @return void
	 */
	function set_backend_message()
	{
		$this->content->template['plugin_cm_head']['de'][] = "Skript Sprachwahl an beliebiger Stelle";
		$this->content->template['plugin_cm_body']['de'][] = "Mit diesem kleinen Skript kann man an beliebiger Stelle in Inhalten die Sprachwahl ausgeben lassen, die Syntax lautet.<br /><strong>#sprachwahl#</strong>";
		$this->content->template['plugin_cm_img']['de'][] = "";
	}

	/**
	 * @param $output
	 * @return mixed
	 */
	function create_sprachwahlintegration_styles_default($output)
	{
		$sprachwahl = array();

		$sprachwahl[] = "<!-- MODUL: sprachwahl -->\n";
		$sprachwahl[] = "<div class=\"modul\" id=\"mod_sprachwahl\">\n";
		$sprachwahl[] = "<ul>\n";

		foreach($this->content->template['languageget'] as $lang) {
			$sprachwahl[] = sprintf("<li class=\"languageli\"><a class=\"toplink\" href=\"%s\" rel=\"alternate\" hreflang=\"%s\"%s>",
				$lang["lang_link"],
				$lang["lang_short"],
				($this->content->template['aktulanglong'] == $lang["language"]) ? " id=\"aktulang\"" : ""
			);
			$sprachwahl[] = sprintf("<img src=\"%sbilder/%s\" width=\"20\" height=\"14\" style=\"margin-top:0px;\" alt=\"%s\" /> %s",
				$this->content->template['slash'],
				$lang["lang_bild"],
				$lang["lang_title"],
				$lang["language"]
			);
			$sprachwahl[] = "</a><span class=\"ignore\">.</span></li>\n";
		}

		$sprachwahl[] = "</ul>\n";
		$sprachwahl[] = "</div>\n";
		$sprachwahl[] = "<!-- ENDE MODUL: sprachwahl -->\n";

		return str_replace("#sprachwahl#", implode("", $sprachwahl), $output);
	}

	/**
	 * @param $output
	 * @return mixed
	 */
	function create_sprachwahlintegration_foundation_papoo($output)
	{
		$sprachwahl = array();

		$sprachwahl[] = "<!-- MODUL: sprachwahl -->\n";
		$sprachwahl[] = "<div class=\"modul\" id=\"mod_sprachwahl\">\n";

		// primary language visible to the user
		foreach($this->content->template['languageget'] as $lang) {
			if($this->content->template['aktulanglong'] == $lang["language"]) {
				$sprachwahl[] = sprintf("<a data-dropdown=\"drop1\" class=\"dropdown\" href=\"%s\" rel=\"alternate\" hreflang=\"%s\" id=\"aktulang\">" .
					"<img src=\"%sbilder/%s\" width=\"20\" height=\"14\" style=\"margin-top:0px;\" alt=\"%s\" /> </a>\n",
					$lang["lang_link"],
					$lang["lang_short"],
					$this->content->template['slash'],
					$lang["lang_bild"],
					$lang["lang_title"]
				);
			}
		}

		$sprachwahl[] = "<ul id=\"drop1\" data-dropdown-content class=\"f-dropdown\">\n";

		// other languages hidden in dropdown
		foreach($this->content->template['languageget'] as $lang) {
			if($this->content->template['aktulanglong'] != $lang["language"]) {
				$sprachwahl[] = sprintf("<li class=\"languageli\"><a class=\"toplink\" href=\"%s\" rel=\"alternate\" hreflang=\"%s\">" .
					"<img src=\"%sbilder/%s\" width=\"20\" height=\"14\" style=\"margin-top:0px;\" alt=\"%s\" /> </a></li>\n",
					$lang["lang_link"],
					$lang["lang_short"],
					$this->content->template['slash'],
					$lang["lang_bild"],
					$lang["lang_title"]
				);
			}
		}
		$sprachwahl[] = "</ul>\n";
		$sprachwahl[] = "</div>\n";
		$sprachwahl[] = "<!-- ENDE MODUL: sprachwahl -->\n";

		return str_replace("#sprachwahl#", implode("", $sprachwahl), $output);
	}
}

$sprachwahlintegration = new sprachwahlintegration();
