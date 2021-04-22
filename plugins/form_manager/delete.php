<?php
//Aktiv setzen
$del="1";

if ($del==1 && $_GET['token']=="fdgw455etzhe5hwreznjw352z6wzrjezrhbw54zh") {
	//Verzeichnisse auslesen
	require_once("../../lib/classes/diverse_class.php");
	require_once("../../lib/classes/class_debug.php");
	require_once("../../lib/site_conf.php");
	require_once("../../lib/ez_sql.php");

	$data=diverse_class::lese_dir("/dokumente/files/actenium/");

	if (is_array($data)) {
		foreach ($data as $key=>$value) {
			//IDs auslesen
			if (is_dir(PAPOO_ABS_PFAD."/dokumente/files/".$value['name'])) {
				$id=@file_get_contents(PAPOO_ABS_PFAD."/dokumente/files/actenium/".$value['name']."/id.csv");

				if (is_numeric(trim($id))) {
					//Eintr�ge in DB l�schen
					$sql=sprintf("DELETE  FROM %s
												WHERE form_manager_lead_id='%d'",
						$db_praefix."papoo_form_manager_leads",
						$id
					);
					//$db->query($sql);

					$sql=sprintf("DELETE  FROM %s
												WHERE form_manager_content_lead_id_id='%d'",
						$db_praefix."papoo_form_manager_lead_content",
						$id
					);
					//$db->query($sql);

				}

				//Verzeichnisse l�schen inkl. Inhalt
				diverse_class::rec_rmdir(PAPOO_ABS_PFAD."/dokumente/files/actenium/".$value['name']);
			}
		}
	}
	debug::print_d("Dateien und Verzeichnisse wurden gel�scht");
}
else {
	echo("Keine Recht f�r diese Aktion");
}
