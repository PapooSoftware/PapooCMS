<?php
/**
* Neues Feld der Mitglieder Content Tabelle hinzuf�gen
*/
// Markierung f�r neue Feldtypen
// Formtypen der HTML-Formulare in die entsprechenden SQL Syntax �bersetzen
// Sollte vom Typ text statt varchar sein, da varchar bei MySQL die Summe aller L�ngen vom Typ varchar in einer Tabelle auf 65535 begrenzt.
// Jedoch kann text nicht als unique Index verwendet werden, auch nicht varchar(255), muss dann < 255 sein. Das nur am Rande, falls hier mal erforderlich.
$formtypes = array(
	"textarea" => "text",
	"textarea_tiny" => "text",
	"pre_select" => "text",
	"text" => "text",
	"email" => "text",
	"select" => "text",
	"radio" => "text",
	"check" => "text",
	"hidden" => "text",
	"password" => "text",
	"timestamp" => "text",
	"multiselect" => "text",
	"picture" => "text",
	"file" => "text",
	"galerie" => "text",
	"link" => "text",
	"zeitintervall" => "text",
	"artikel" => "text",
	"flex_verbindung" => "text",
	"flex_tree" => "text",
	"preisintervall" => "text",
	"sprechende_url" => "text"
	);
$sql = sprintf("SELECT MAX(mvcform_id)
						FROM %s
						WHERE mvcform_meta_id = '1'",
						
						$this->cms->tbname['papoo_mvcform'],
						
						$this->db->escape($this->checked->mv_id)
				);
$max_feld_id = $this->db->get_var($sql);
if ($max_feld_id == "") $max_feld_id = 0;
// wenn es die 2+x Felder f�r eine neue Mitlgiederverwaltung sind dann ++
if ($this->new_mv == "1") $max_feld_id++;
// Neues Feld in die Mitglieder Content Tabelle einf�gen (anh�ngen)
$sql = sprintf("ALTER TABLE %s
						ADD %s %s NOT NULL %s;",
						
						$this->cms->tbname['papoo_mv']
						. "_content_"
						. $this->db->escape($this->checked->mv_id),
						
						$this->db->escape($this->checked->mvcform_name
						. "_"
						. $max_feld_id),
						
						$formtypes[$this->checked->mvcform_type],
						$this->mv_default_wert
				);
#$this->db->query($sql);
// Sprachtabellen f�r die schnellere Suche
$sql = sprintf("SELECT mv_lang_id
						FROM %s",
						$this->cms->tbname['papoo_mv_name_language']
				);
$sprachen = $this->db->get_results($sql);
// Anh�ngen neues Feld f�r jede Sprache
if (!empty($sprachen))
{
	foreach($sprachen as $sprache)
	{
		$sql = sprintf("ALTER TABLE %s ADD %s %s NOT NULL %s;",
		
								$this->cms->tbname['papoo_mv']
								. "_content_"
								. $this->db->escape($this->checked->mv_id)
								. "_search_"
								. $this->db->escape($sprache->mv_lang_id),
								
								$this->db->escape($this->checked->mvcform_name
								. "_"
								. $max_feld_id),
								$formtypes[$this->checked->mvcform_type],
								$this->mv_default_wert
						);
		$this->db->query($sql);
	}
}
// Defaultwert = 0, bei diesen Feldtypen
if ($this->checked->mvcform_type == "multiselect"
	OR $this->checked->mvcform_type == "select"
	OR $this->checked->mvcform_type == "check")
{
	$sql = sprintf("UPDATE %s SET %s = '0'",

							$this->cms->tbname['papoo_mv']
							. "_content_"
							. $this->db->escape($this->checked->mv_id),
							
							$this->db->escape($this->checked->mvcform_name
							. "_"
							. $max_feld_id)
					);
	#$this->db->query($sql);
}
// Look-Up Tabellen generieren
// falls alte Tabelle mit dem selben Namen schon/noch existiert, erstmal l�schen
$sql = sprintf("DROP TABLE IF EXISTS %s",
						$this->cms->tbname['papoo_mv']
						. "_content_"
						. $this->db->escape($this->checked->mv_id)
						. "_lang_"
						. $this->db->escape($max_feld_id));
$this->db->query($sql);
$sql = sprintf("DROP TABLE IF EXISTS %s",
						$this->cms->tbname['papoo_mv']
						. "_content_"
						. $this->db->escape($this->checked->mv_id)
						. "_lookup_"
						. $this->db->escape($max_feld_id)
				);
#$this->db->query($sql);
// Lookup Tabelle
$sql = sprintf("CREATE TABLE %s (
									`content_id` int(11) NOT NULL default '0', 
									`lookup_id` int(11) NOT NULL default '0',
									 KEY `lookup_id` (`lookup_id`)
								)
								ENGINE = MyISAM
								DEFAULT CHARSET = utf8;",
								
								$this->cms->tbname['papoo_mv']
								. "_content_"
								. $this->db->escape($this->checked->mv_id)
								. "_lookup_"
								. $this->db->escape($max_feld_id)
				);
#$this->db->query($sql);
// erstellt Lang-Tab. und tr�gt die m�glichen Werte in die Lang Tabelle ein, jedoch nur f�r diese Feldtypen
if ($this->checked->mvcform_type == "select"
	|| $this->checked->mvcform_type == "radio"
	|| $this->checked->mvcform_type == "multiselect"
	|| $this->checked->mvcform_type == "check"
	|| $this->checked->mvcform_type == "pre_select")
{
	$sql = sprintf("CREATE TABLE %s (
										`lookup_id` int(11) NOT NULL default '0', 
										`content` text NOT NULL, 
										`lang_id` int(11) NOT NULL default '0', 
										`order_id` int(11) NOT NULL default '0',
										KEY `lookup_id` (`lookup_id`)
									)
									ENGINE = MyISAM
									DEFAULT CHARSET = utf8;",
									
									$this->cms->tbname['papoo_mv']
									. "_content_"
									. $this->db->escape($this->checked->mv_id)
									. "_lang_"
									. $this->db->escape($max_feld_id));
	$this->db->query($sql);
	if ($this->checked->mvcform_type == "radio" // f�r jeden Eintrag ein Button
		or $this->checked->mvcform_type == "select"
		or $this->checked->mvcform_type == "multiselect"
		or $this->checked->mvcform_type == "pre_select") $cdaten = explode("\r\n", trim($this->checked->mvcform_content_list));
	else 
	{
		if ($this->checked->feldid != "x") $cdaten[] = $this->checked->mvcform_content_list;
		else $cdaten = explode("\r\n", trim($this->checked->mvcform_content_list));
	}
	$lookup_id = 1;
	$i = 10;
	// checkbox nur 1 Wert. Wird in check_data_fields.php sichergestellt.
	foreach ($cdaten as $content)
	{
		$content = preg_replace("/\r/", "", $content);
		// lang Tabelle
		$sql = sprintf("INSERT INTO %s
								SET content = '%s', 
								lang_id = '%s', 
								lookup_id = '%s', 
								order_id = '%s'",
								
								$this->cms->tbname['papoo_mv']
								. "_content_"
								. $this->db->escape($this->checked->mv_id)
								. "_lang_"
								. $this->db->escape($max_feld_id),
								
								$this->db->escape($content),
								$this->db->escape($this->cms->lang_back_content_id),
								$lookup_id,
								$i
						);
		$this->db->query($sql);
		$lookup_id++;
		$i = $i + 10;
	}
	// holt alle Sprachen ausser der in $this->cms->lang_standard_id gespeicherten Sprache aus der Tabelle
	$sql = sprintf("SELECT * FROM %s
								WHERE mv_lang_id <> '%d'",
								$this->cms->tbname['papoo_mv_name_language'],
								$this->db->escape($this->cms->lang_back_content_id)
					);
	$sprachen = $this->db->get_results($sql);
	if (!empty($sprachen))
	{
		// Sprachen durchloopen
		foreach($sprachen as $sprache)
		{
			$lookup_id = 1;
			$i = 10;
			// Auswahlm�glichkeiten durchloopen
			foreach($cdaten as $content)
			{
				$content = preg_replace("/\r/", "", $content);
				$sql = sprintf("INSERT INTO %s
										SET content = '%s', 
										lang_id = '%s', 
										lookup_id = '%s', 
										order_id = '%s'",
										
										$this->cms->tbname['papoo_mv']
										. "_content_"
										. $this->db->escape($this->checked->mv_id)
										. "_lang_"
										. $max_feld_id,
										
										$this->db->escape($content),
										
										$this->db->escape($sprache->mv_lang_id),
										$lookup_id,
										$i
								);
				$this->db->query($sql);
				$lookup_id++;
				$i = $i +10;
			}
		}
	}
	/*$sql = sprintf("SELECT mv_content_id
							FROM %s",
							$this->cms->tbname['papoo_mv']
							. "_content_"
							. $this->db->escape($this->checked->mv_id)
					);
	$content_ids = $this->db->get_results($sql);*/
	// f�r alte Eintr�ge Default=0 Eintr�ge machen
	$lang = defined("admin") ? $this->cms->lang_back_content_id : $this->cms->lang_id;
	$sql = sprintf("UPDATE %s SET %s = ''",
									
									$this->cms->tbname['papoo_mv']
									. "_content_"
									. $this->db->escape($this->checked->mv_id)
									. "_search_"
									. $lang,
									
									$this->db->escape($this->checked->mvcform_name
									. "_"
									. $max_feld_id)
					);
	$this->db->query($sql);
}
?>
