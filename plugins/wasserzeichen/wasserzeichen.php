<?php
//Aktiv setzen
if ($_GET['token']=="fdgw455etzhe5hwreznjw352z6wzrjezrhbw54zh") {
	//Verzeichnisse auslesen
	require_once("../../lib/site_conf.php");
	require_once("../../lib/classes/diverse_class.php");
	require_once("../../lib/classes/class_debug.php");
	require_once("../../lib/ez_sql.php");

	//Step 0 - Daten dieser Domain rausholen plugin_zentrale_inhalte
	$sql=sprintf("SELECT plugin_zentrale_inhalte_menupunkte FROM %s
					WHERE plugin_zentrale_inhalte_domainkey='%s'",
		$db_praefix."plugin_zentrale_inhalte",
		$db->escape($_GET['domain_key']));
	$replace=$data=unserialize($db->get_var($sql));

	$sql_sel=$sql_sel1=$sql_sel2=$sql_sel3=$sql_sel4="";

	//Step 1 - alle Men�punkte mit Sprachen
	if (is_array($data)) {
		foreach ($data as $key=>$value) {
			$sql_sel.=" menuid='".$value."' OR";
			$sql_sel1.=" menuid_id='".$value."' OR";
			$sql_sel2.=" menuid='".$value."' OR";
			$sql_sel3.=" menuid_id='".$value."' OR";
			$sql_sel4.=" menuid_cid='".$value."' OR";
		}
	}
	$sql_sel=substr($sql_sel,0,-2);
	$sql_sel1=substr($sql_sel1,0,-2);
	$sql_sel2=substr($sql_sel2,0,-2);
	$sql_sel3=substr($sql_sel3,0,-2);
	$sql_sel4=substr($sql_sel4,0,-2);

	$data=array();
	//Basis Tabelle
	$sql=sprintf("SELECT * FROM %s
					WHERE %s",
		$db_praefix."papoo_me_nu",
		$sql_sel
	);
	$data['papoo_me_nu']=$db->get_results($sql,ARRAY_A);

	//Sprachtabelle
	$sql=sprintf("SELECT * FROM %s
					WHERE %s",
		$db_praefix."papoo_menu_language",
		$sql_sel1
	);
	$data['papoo_menu_language']=$db->get_results($sql,ARRAY_A);

	//Rechte lesen papoo_lookup_men_ext
	$sql=sprintf("SELECT * FROM %s
					WHERE %s",
		$db_praefix."papoo_lookup_men_ext",
		$sql_sel2
	);
	$data['papoo_lookup_men_ext']=$db->get_results($sql,ARRAY_A);

	//rechte schreiben papoo_lookup_me_all_ext
	$sql=sprintf("SELECT * FROM %s
					WHERE %s",
		$db_praefix."papoo_lookup_me_all_ext",
		$sql_sel3
	);
	$data['papoo_lookup_me_all_ext']=$db->get_results($sql,ARRAY_A);

	//categorien papoo_lookup_mencat
	$sql=sprintf("SELECT * FROM %s
					WHERE %s",
		$db_praefix."papoo_lookup_mencat",
		$sql_sel4
	);
	$data['papoo_lookup_mencat']=$db->get_results($sql,ARRAY_A);

	$sql_sel=$sql_sel1=$sql_sel2=$sql_sel3=$sql_sel4="";

	if (is_array($data['papoo_lookup_mencat'])) {
		foreach ($data['papoo_lookup_mencat'] as $key=>$value) {
			$sql_sel.=" cat_id='".$value['cat_cid']."' OR";
			$sql_sel1.=" cat_lang_id='".$value['cat_cid']."' OR";
			$sql_sel2.=" cat_rlid='".$value['cat_cid']."' OR";
			$sql_sel3.=" cat_wlid='".$value['cat_cid']."' OR";
		}
	}
	$sql_sel=substr($sql_sel,0,-2);
	$sql_sel1=substr($sql_sel1,0,-2);
	$sql_sel2=substr($sql_sel2,0,-2);
	$sql_sel3=substr($sql_sel3,0,-2);

	//KAtegorien selber papoo_category
	$sql=sprintf("SELECT * FROM %s
					WHERE %s",
		$db_praefix."papoo_category",
		$sql_sel
	);
	$data['papoo_category']=$db->get_results($sql,ARRAY_A);


	//Kategorien Rechte papoo_category_lang
	$sql=sprintf("SELECT * FROM %s
					WHERE %s",
		$db_praefix."papoo_category_lang",
		$sql_sel1
	);
	$data['papoo_category_lang']=$db->get_results($sql,ARRAY_A);

	//Kategorien Rechte papoo_category_lookup_read
	$sql=sprintf("SELECT * FROM %s
					WHERE %s",
		$db_praefix."papoo_category_lookup_read",
		$sql_sel2
	);
	$data['papoo_category_lookup_read']=$db->get_results($sql,ARRAY_A);


	//Kategorien Sprache... papoo_category_lookup_write
	$sql=sprintf("SELECT * FROM %s
					WHERE %s",
		$db_praefix."papoo_category_lookup_write",
		$sql_sel3
	);
	$data['papoo_category_lookup_write']=$db->get_results($sql,ARRAY_A);

	$sql_sel=$sql_sel1=$sql_sel2=$sql_sel3=$sql_sel4="";
	//Step 2 - alle Artikel dieser Men�punkte inkl. Sprachen
	if (is_array($data['papoo_me_nu'])) {
		foreach ($data['papoo_me_nu'] as $key=>$value) {
			$sql_sel.=" lcat_id='".$value['menuid']."' OR";
		}
	}
	$sql_sel=substr($sql_sel,0,-2);

	$sql=sprintf("SELECT * FROM %s
					WHERE %s",
		$db_praefix."papoo_lookup_art_cat",
		$sql_sel
	);
	$data['papoo_lookup_art_cat']=$db->get_results($sql,ARRAY_A);

	//Dann von diesen Artikel die zugeh�rigen Daten und Unter Tabellen

	$sql_sel=$sql_sel1=$sql_sel2=$sql_sel3=$sql_sel4="";
	//Step 2 - alle Artikel dieser Men�punkte inkl. Sprachen
	if (is_array($data['papoo_lookup_art_cat'])) {
		foreach ($data['papoo_lookup_art_cat'] as $key=>$value) {
			$sql_sel.=" reporeID='".$value['lart_id']."' OR"; //papoo_repore
			$sql_sel1.=" lan_repore_id='".$value['lart_id']."' OR"; //papoo_language_article
			$sql_sel2.=" article_wid_id='".$value['lart_id']."' OR"; //papoo_lookup_write_article
			$sql_sel3.=" article_id='".$value['lart_id']."' OR";//papoo_lookup_article
		}
	}
	$sql_sel=substr($sql_sel,0,-2);
	$sql_sel1=substr($sql_sel1,0,-2);
	$sql_sel2=substr($sql_sel2,0,-2);
	$sql_sel3=substr($sql_sel3,0,-2);

	$sql=sprintf("SELECT * FROM %s
					WHERE %s",
		$db_praefix."papoo_repore",
		$sql_sel
	);
	$data['papoo_repore']=$db->get_results($sql,ARRAY_A);

	$sql=sprintf("SELECT * FROM %s
					WHERE %s",
		$db_praefix."papoo_language_article",
		$sql_sel1
	);
	$data['papoo_language_article']=$db->get_results($sql,ARRAY_A);

	$sql=sprintf("SELECT * FROM %s
					WHERE %s",
		$db_praefix."papoo_lookup_write_article",
		$sql_sel2
	);
	$data['papoo_lookup_write_article']=$db->get_results($sql,ARRAY_A);

	$sql=sprintf("SELECT * FROM %s
					WHERE %s",
		$db_praefix."papoo_lookup_article",
		$sql_sel3
	);
	$data['papoo_lookup_article']=$db->get_results($sql,ARRAY_A);

	//Step 3 - alle Bilder und Dateien die in den Artikel sich befinden... einfacher alle Bilder und Dateien einfach kopieren...

	//Erstmal die Bilder
	$sql=sprintf("SELECT * FROM %s
					",
		$db_praefix."papoo_images"
	);
	$data['papoo_images']=$db->get_results($sql,ARRAY_A);

	$sql_sel=$sql_sel1=$sql_sel2=$sql_sel3=$sql_sel4="";

	//die zu den Bilder geh�renden Tabellen
	if (is_array($data['papoo_images'])) {
		foreach ($data['papoo_images'] as $key=>$value) {
			$sql_sel.=" lan_image_id='".$value['image_id']."' OR"; //papoo_language_image
			$sql_sel1.=" image_id_id ='".$value['image_id']."' OR"; //papoo_lookup_image
		}
	}
	$sql_sel=substr($sql_sel,0,-2);
	$sql_sel1=substr($sql_sel1,0,-2);

	$sql=sprintf("SELECT * FROM %s
					WHERE %s",
		$db_praefix."papoo_language_image",
		$sql_sel
	);
	$data['papoo_language_image']=$db->get_results($sql,ARRAY_A);

	$sql=sprintf("SELECT * FROM %s
					WHERE %s",
		$db_praefix."papoo_lookup_image",
		$sql_sel1
	);
	$data['papoo_lookup_image']=$db->get_results($sql,ARRAY_A);

	//Kategorien der Bilder papoo_kategorie_bilder
	$sql=sprintf("SELECT * FROM %s
					",
		$db_praefix."papoo_kategorie_bilder"
	);
	$data['papoo_kategorie_bilder']=$db->get_results($sql,ARRAY_A);

	$sql_sel=$sql_sel1=$sql_sel2=$sql_sel3=$sql_sel4="";

	//die zu den Bilderkategorien geh�renden Tabellen
	if (is_array($data['papoo_kategorie_bilder'])) {
		foreach ($data['papoo_kategorie_bilder'] as $key=>$value) {
			$sql_sel.=" bilder_cat_id_id ='".$value['bilder_cat_id']."' OR"; //papoo_lookup_cat_images
		}
	}
	$sql_sel=substr($sql_sel,0,-2);

	$sql=sprintf("SELECT * FROM %s
					WHERE %s",
		$db_praefix."papoo_lookup_cat_images",
		$sql_sel
	);
	$data['papoo_lookup_cat_images']=$db->get_results($sql,ARRAY_A);

	//Dann die Dateien
	$sql=sprintf("SELECT * FROM %s
					",
		$db_praefix."papoo_download"
	);
	$data['papoo_download']=$db->get_results($sql,ARRAY_A);

	$sql_sel=$sql_sel1=$sql_sel2=$sql_sel3=$sql_sel4="";

	//die zu den Bilder geh�renden Tabellen
	if (is_array($data['papoo_download'])) {
		foreach ($data['papoo_download'] as $key=>$value) {
			$sql_sel.=" download_id_id 	='".$value['downloadid']."' OR"; //papoo_lookup_download
		}
	}
	$sql_sel=substr($sql_sel,0,-2);
	$sql_sel1=substr($sql_sel1,0,-2);

	$sql=sprintf("SELECT * FROM %s
					WHERE %s",
		$db_praefix."papoo_lookup_download",
		$sql_sel
	);
	$data['papoo_lookup_download']=$db->get_results($sql,ARRAY_A);


	//die zu den Dateikategorien geh�renden Tabellen
	$sql=sprintf("SELECT * FROM %s
					",
		$db_praefix."papoo_kategorie_dateien"
	);
	$data['papoo_kategorie_dateien']=$db->get_results($sql,ARRAY_A);
	$sql_sel=$sql_sel1=$sql_sel2=$sql_sel3=$sql_sel4="";


	if (is_array($data['papoo_kategorie_dateien'])) {
		foreach ($data['papoo_kategorie_dateien'] as $key=>$value) {
			$sql_sel.=" dateien_cat_id_id ='".$value['dateien_cat_id']."' OR"; //papoo_lookup_cat_dateien
		}
	}
	$sql_sel=substr($sql_sel,0,-2);

	$sql=sprintf("SELECT * FROM %s
					WHERE %s",
		$db_praefix."papoo_lookup_cat_dateien",
		$sql_sel
	);
	$data['papoo_lookup_cat_dateien']=$db->get_results($sql,ARRAY_A);
	//jsoncodieren gesamt und 

	//dann ersetzen mit Platzhaltern... damit das in allen Daten passiert 
	$sql=sprintf("SELECT plugin_zentrale_inhalte_platzhalter FROM %s
					WHERE plugin_zentrale_inhalte_domainkey='%s'",
		$db_praefix."plugin_zentrale_inhalte",
		$db->escape($_GET['domain_key']));
	$data['replace_data']=($db->get_var($sql));
	$serial_data=serialize($data);
	/** Die Ersetzung erfolgt beim Client... funzt sonst nicht, und da m�ssen wir sowieso durchgehen
	//replace Daten bereitstellen
	$rel_ar=explode("#",$replace);
	$repl="";
	if (is_array($rel_ar))
	{
	foreach ($rel_ar as $key=>$value)
	{
	//ungerade - das ist die Variable
	if ($key % 2 ==1)
	{
	$repl="#".$value."#";
	$neu[$repl]="";
	}
	else
	{
	$neu[$repl]=$value;
	}
	}
	}

	if (is_array($neu))
	{
	foreach ($neu as $key=>$value)
	{
	if (!empty($key))
	{
	$serial_data=str_ireplace($key,serialize($value),$serial_data);
	}
	}
	}
	 */

	print_r($serial_data);
}
else {
	echo("Keine Recht f�r diese Aktion");
}
