<?php
/**
 * @autor: Andreas Gritzan <ag@papoo.de>
 */

include('../../../lib/site_conf.php');
include ('../../../lib/ez_sql.php');

if(isset($_REQUEST['search'])) {
	global $db;
	global $db_praefix;

	$papoo_message = $db_praefix . "papoo_message";
	$papoo_user = $db_praefix . "papoo_user";

	$searchterm = $_REQUEST['search'];

	#$message_data = array ();

	$suche_sql = "AND CONCAT(t2.header, ' ', t2.lan_teaser, ' ', t2.lan_article_sans) LIKE '%" . $db->escape($searchterm) . "%' ";

	$order = " t1.stamptime DESC";

	$sqlsuch = "SELECT COUNT(reporeID) FROM " . $db_praefix . 'papoo_repore' . ", " . $db_praefix . 'papoo_language_article' . " ";
	//$sqlsuch .= " WHERE reporeID=lan_repore_id AND lang_id='".$this->cms->lang_id."'";
	$sqlsuch .= " WHERE reporeID=lan_repore_id";

	$get_suchergebniss = sprintf("SELECT DISTINCT * FROM (
                                                    SELECT DISTINCT (t1.reporeID), t2.lan_teaser, t2.header, t1.teaser_bild, t4.username, t1.timestamp, t1.dokuser_last, t1.dokuser
													FROM
													%s AS t1, %s AS t2,
													%s AS t4, %s AS t5

													WHERE t1.reporeID = t2.lan_repore_id AND t5.lart_id=t1.reporeID
													%s
													) AS articles
                                                    LEFT JOIN
                                                      (SELECT article_id AS noindex FROM %s) AS noindex_articles
                                                   ON noindex_articles.noindex=articles.reporeID
									",

		$db_praefix . 'papoo_repore', $db_praefix . 'papoo_language_article',
		$papoo_user, $db_praefix . 'papoo_lookup_art_cat',
		$suche_sql,
		$db_praefix . "noindex_article_ids"
	);

	$artikel_liste = array();
	$result2 = $db->get_results($get_suchergebniss);

	if (!empty ($result2)) {
		foreach ($result2 as $row) {
			$temp_array = array();
			$temp_array['ueberschrift'] = $row->header;
			$temp_array['reporeID'] = $row->reporeID;
			$temp_array['teaser'] = $row->lan_teaser;
			//$temp_array['timestamp'] = $row->timestamp;
			$temp_array['date_time'] = $row->timestamp;
			$sql = sprintf("SELECT username FROM %s WHERE userid='%d'",
				$db_praefix . 'papoo_user',
				$row->dokuser_last
			);
			$temp_array['username'] = $db->get_var($sql);
			if (empty($temp_array['username']))
			{
				$temp_array['username'] = "n.o.";
			}
			$sql = sprintf("SELECT username FROM %s WHERE userid='%d'",
				$db_praefix . 'papoo_user',
				$row->dokuser
			);
			$temp_array['autor'] = $db->get_var($sql);
			//$temp_array['lang_id'] = $this->cms->lang_back_content_id;
			$temp_array['noindex'] = $row->noindex;

			// Daten zuweisen
			$artikel_liste[] = $temp_array;
		}
	}

	// Doppelte funde filtern
	$artikel_liste = array_unique($artikel_liste, SORT_REGULAR);

	$name="Artikelsuche";
	$content = "";

	// Daten in html code umwandeln diese werden dann durch den jetzigen table.innerHTML ersetzt.

	foreach($artikel_liste as $artikel) {
		$content .=  '<div class="media-body">';

		$content .=  '<h2  class="media-heading">' . $artikel['ueberschrift'] . '</h2>';
		$content .=  '<div class="artikel_list_teaser_innen">';
		$content .=  '<p>';
		$content .=  '<div class="teaserdat">';
		$content .=  $artikel['teaser'];
		$content .=  '</div>';

		$content .=  '<a class="artikel_link_teaser  " href="artikel.php?menuid=11&reporeid=' . $artikel['reporeID'] . '&submitlang=' . $artikel['lang_id'] . '">' . $artikel['ueberschrift'] . ' </a><br />';
		$content .=  '<div class="authormessage ">';
		$content .=  'Datum letzte Änderung: ' . $artikel['date_time'] . ' - durchgeführt von: ' . $artikel['username'] . ' - Eigentümer: ' . $artikel['autor'];
		#{$letzte_aenderung}{$link.date_time|date_format:$lang_dateformat} {$letzte_aenderung_von} {$link.username}{$letzte_aenderung_autor} {$link.autor}
		$content .=  '</div>';
		$content .=  '</p>';
		$content .=  '</div>';

		$content .= '<div class="settings-saved message hidden" name="artikel_' . $artikel['reporeID'] . '">Änderungen gespeichert.</div>';
		$content .= '<div class="settings-saving message hidden" name="artikel_' . $artikel['reporeID'] . '">Speichere...</div>';
		$content .= '<div class="settings-error error hidden" name="artikel_' . $artikel['reporeID'] . '">Fehler beim Speichern!</div>';

		$content .=  '<form class="noindex_noreload" action="" method="POST" name="artikel_' . $artikel['reporeID'] . '">';
		$content .=  '<fieldset>';
		$content .=  '<input type="hidden" name="noindex_article[' . $artikel['reporeID'] . ']" value="0">';
		$content .=  '<label><input type="checkbox" name="noindex_article[' . $artikel['reporeID'] . ']" ' . ($artikel['noindex'] ? "checked" : "") . ' value="1">noindex</label>';
		$content .=  '<input type="submit" class="btn-info" value="Speichern">';
		$content .=  '</fieldset>';
		$content .=  '</form>';
		$content .=  '</div>';
	}

	$size = strlen($content);

	header("Content-length: $size");
	header("Content-type: text");
	header("Content-Disposition: attachment; filename=$name");

	echo $content;
}
exit;