<?php
//Aktiv setzen
if ($_GET['token']=="fdgw455etzhe5hwreznjw352z6wzrjezrhbw54zh") {
	$neu_data="";
	//Verzeichnisse auslesen
	require_once("../../lib/site_conf.php");
	require_once("../../lib/classes/diverse_class.php");
	require_once("../../lib/classes/class_debug.php");
	require_once("../../lib/ez_sql.php");
	define("DB_PRAEFIX",$db_praefix);
	//Step 0 - Daten dieser Domain rausholen plugin_zentrale_inhalte
	$sql=sprintf("SELECT * FROM %s
					",
		$db_praefix."plugin_cotent_client"
	);

	$result=($db->get_results($sql,ARRAY_A));

	//Url der Zentrale
	$url=$result['0']['plugin_cotent_client_url_der_zentrale']."?token=".$result['0']['plugin_cotent_client_token_key_kommt_aus_der_zentrale']."&domain_key=".$result['0']['plugin_cotent_client_domain_key'];

	//Baseurl
	$url_base=get_base_url($url);

	//Daten von der Zentrale via Curl holen
	$retr=do_curl($url);
	debug::print_d($retr);
	exit();
	//Aus den Daten Array erzeugen
	$data=unserialize($retr);
	//Daten aus Lookup rausholen
	$sql=sprintf("SELECT * FROM %s
					",
		$db_praefix."plugin_cotent_client_lookup"
	);

	$result=($this->db->get_results($sql,ARRAY_A));
	$search=array();

	//Seitendaten
	$sql=sprintf("SELECT seitenname FROM %s
					",
		$db_praefix."papoo_daten"
	);

	$result_site=($this->db->get_var($sql));
	define("SITE_NAME",$result_site);
	//Seite in den Wartungsmodus setzen
	//Erstmal alle alten Eintr�ge l�schen
	if (is_array($result)) {
		foreach ($result as $key=>$value) {
			//Daten l�schen
			do_delete($value['lookup_tab_name'],$value['lookup_feld_serial']);
		}
	}

	$sql=sprintf("DELETE FROM %s ",
		DB_PRAEFIX."plugin_cotent_client_lookup",
		$this->db->escape($tab),
		$this->db->escape(serialize($data))
	);
	$this->db->query($sql);
	//Daten durchgehen und checken
	if (is_array($data)) {
		foreach ($data as $key=>$value) {
			if ($key!="replace_data") {
				//Jetzt die Eintr�ge der Tabellen durchgehen
				if (is_array($value)) {
					foreach ($value as $keyd=>$valued) {
						//Speichern
						insert($valued,$key,$data["replace_data"],$url_base);
					}
				}
			}
		}
	}
	else {
		die("Keine Daten vom Server...");
	}

	//Seite aus  den Wartungsmodus holen
	debug::print_d("Daten werden eingetragen");
}
else {
	echo("Keine Rechte f�r diese Aktion");
}
/**
 * @param $url
 * @return string
 */
function get_base_url($url)
{
	$url_dat= parse_url($url);
	return $url_dat['scheme']."://".$url_dat['host'];
}

/**
 * @param $tab
 * @param $data
 * @return bool|void
 */
function do_delete($tab, $data)
{
	global $db;
	$data_ar=unserialize($data);
	$sql_insert="";
	$i=0;
	//Felderdaten zuweisen
	foreach ($data_ar as $key=>$value) {
		if (stristr($key,"lang") &&  !stristr($key,"cat_lang_id") ) {
			continue;
		}

		if ($i<1) {
			$sql_insert.=" ".$key."='".$db->escape($value)."' ";
		}
		$i++;
	}

	if ($sql_insert==" cat_id='1' " || $sql_insert==" cat_lang_id='1' " || $sql_insert==" cat_rlid='1' " || $sql_insert==" cat_wlid='1' ") {
		return true;
	}
	#$sql_insert=substr($sql_insert,0,-4);

	//L�schen
	$sql=sprintf("DELETE FROM %s WHERE %s LIMIT 1",
		DB_PRAEFIX.$db->escape($tab),
		$sql_insert);
	$db->query($sql);

}

/**
 * @param string $data
 * @param $replace
 * @return mixed|string|string[]|null
 */
function get_local_data($data="", $replace)
{
	//replace Daten bereitstellen
	$rel_ar=explode("#",$replace);
	$repl="";
	global $neu_data;

	if (is_array($rel_ar)) {
		foreach ($rel_ar as $key=>$value) {
			//ungerade - das ist die Variable
			if ($key % 2 ==1) {
				$repl="#".$value."#";
				$neu[$repl]="";
			}
			else {
				$neu[$repl]=$value;
			}
		}
	}

	if (is_array($neu)) {
		foreach ($neu as $key=>$value) {
			if (!empty($key)) {
				$data=str_ireplace($key,$value,$data);
			}
		}
	}
	$sitename=SITE_NAME;

	$data=str_ireplace("http://www.insektum.de",$sitename,$data);
	return $data;
}

/**
 * @param $data
 * @param $tab
 * @param $replace
 * @param $url_base
 * @return bool|void
 */
function insert($data, $tab, $replace, $url_base)
{
	global $db;
	$sql_insert="";
	//Felder durchgehen und �bergeben Feld = Value
	foreach ($data as $key=>$value) {
		//Daten lokalisieren
		$value=get_local_data($value,$replace);

		//In Array �bergeben damit die auch in der Lookup korrekte gespeichert und sp�ter gel�scht werden
		$data[$key]=$value;

		//Insert zusammenstellen
		$sql_insert.=" ".$key."='".$db->escape($value)."', ";
	}
	$sql_insert=substr($sql_insert,0,-2);

	if (stristr($sql_insert,"cat_id='1'") || stristr($sql_insert,"cat_lang_id='1'") || stristr($sql_insert,"cat_rlid='1'") || stristr($sql_insert,"cat_wlid='1'")  ) {
		return true;
	}
	//Daten einragen
	$sql=sprintf("INSERT INTO %s SET %s",
		DB_PRAEFIX.$db->escape($tab),
		$sql_insert);
	$db->query($sql);

	//Dann Daten in Lookup eintragen
	$sql=sprintf("INSERT INTO %s SET lookup_tab_name='%s', lookup_feld_serial='%s'",
		DB_PRAEFIX."plugin_cotent_client_lookup",
		$db->escape($tab),
		$db->escape(serialize($data))
	);
	$db->query($sql);

	if ($tab=="papoo_images") {
		copy_image($data,$url_base);
	}
}

/**
 * @param array $data
 * @param $url
 */
function copy_image($data=array(), $url)
{
	$img=$data['image_name'];

	//Debugging
	#$url=$url."/papoo_trunk";

	if (!empty($img)) {
		copy($url."/images/".$img,PAPOO_ABS_PFAD."/images/".$img);
		copy($url."/images/thumbs/".$img,PAPOO_ABS_PFAD."/images/thumbs/".$img);
	}
}

/**
 * @param string $url
 * @return bool|string|void
 */
function do_curl($url="")
{
	if (function_exists("curl_init")) {
		$url =trim($url );
		echo $url;
		$ch = curl_init( $url );
		curl_setopt( $ch, CURLOPT_TIMEOUT, 100 );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $ch, CURLOPT_HEADER, 1 );
		curl_setopt( $ch, CURLOPT_USERAGENT,
			"Check Agent" );

		$curl_ret = curl_exec( $ch );
		curl_close( $ch );
		return $curl_ret;
	}
	if (empty($curl_ret)) {
		die("Fehler - curl auf dem Server aktivieren");
	}
}
