<?php

/**
 * Diese Klasse übersetzt den übergebenden Inhalt in die gewünschte Sprache
 * Class translate
 */

class translate
{
	/**
	 * @var transDeeplNow
	 * die TranslateDeepl Klasse
	 */
	private $transDeeplNow;

	/**
	 * translate constructor.
	 */
	public function __construct()
	{
		global $user, $db_abs, $db, $db_praefix, $checked, $content,$cms;
		$this->user = &$user;
		$this->db_abs = &$db_abs;
		$this->db = &$db;
		$this->db_praefix = &$db_praefix;
		$this->checked = &$checked;
		$this->content = &$content;
		$this->cms = &$cms;


		require_once __DIR__."/transdeepl.php";
		$this->transDeeplNow = new transdeepl();
	}

	/**
	 * @return transDeeplNow
	 */
	public function getTransDeeplNow()
	{
		return $this->transDeeplNow;
	}

	public function get_active_lngs()
	{
		$sql = sprintf("SELECT lang_short FROM %s WHERE more_lang=2 AND lang_short<>'de'",
			DB_PRAEFIX. "papoo_name_language"
		);
		return $this->db->get_results($sql, ARRAY_A);
	}

	public function translate_freie_module()
	{
		print_r("hier");
		$sql = sprintf("SELECT * FROM %s
								WHERE freiemodule_lang='de' ",
						DB_PRAEFIX."papoo_freiemodule_daten"
		);
		$result = $this->db->get_results($sql,ARRAY_A);
		//print_r($result);
		$active_lng_data = $this->get_active_lngs();
		
		#print_r($result);
		$trans_frei_content = "";

		foreach ($active_lng_data as $lk=>$lv)
		{
			if (is_array($result))
			{
				foreach ($result as $k=>$v)
				{
					print_r(($v['freiemodule_code']));
					$trans_frei_content = $this->transDeeplNow->translate($lv['lang_short'],$v['freiemodule_code']);
					print_r(($trans_frei_content));
					//exit();
				}
			}
		}

		print_r("<p>Freie Module fertig </p>");
		return true;
	}

	public function translate_menu()
	{
		print_r("<p>Menu fertig </p>");
		return true;
	}


}
?>