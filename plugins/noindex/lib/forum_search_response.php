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

	$message_data = array ();

	// Die Ergebnisse  aus der Datenbank holen und anzeigen...
	$selectmessage = "SELECT " . $papoo_message . ".msgid, thema, counten, forumid, level, rootid, ordnung, messagetext,";
	$selectmessage .= "  " . $papoo_message . ".zeitstempel AS zeitstempel, ";
	$selectmessage .= "  username FROM " . $papoo_message . ", " . $papoo_user . " WHERE  ";

	$selectmessage .= " thema LIKE '%" . $db->escape($searchterm) . "%' AND ";
	$selectmessage .= " " . $papoo_message . ".userid = " . $papoo_user . ".userid OR ";
	$selectmessage .= " messagetext LIKE '%" . $db->escape($searchterm) . "%' AND " . $papoo_message . ".userid = " . $papoo_user . ".userid  ";

	#$selectmessage .= " ORDER BY msgid DESC ".$sqllimit;
	// Datenbank Abfrage durchf�hren
	$resultmessage = $db->get_results($selectmessage);
	// Wenn nicht leer, dann in Array einlesen

	if (!empty ($resultmessage)) {
		// F�r jedes Ergebnis ein Eintrag ins Array
		foreach ($resultmessage as $row) {
			$sql = sprintf("SELECT * FROM %s WHERE msgid=%d", $db_praefix . "noindex_forum_msgs", $row->msgid);

			$noindex_res = count($db->get_results($sql, ARRAY_A)) > 0;

			// Daten zuweisen in das loop array f�rs template... ###
			array_push($message_data, array (
				'rootid' => $row->rootid,
				'msgid' => $row->msgid,
				'forumid' => $row->forumid,
				'menuid' => 0,
				'username' => $row->username,
				'zeitstempel' => $row->zeitstempel,
				'counten' => $row->counten,
				'thema' => $row->thema,
				'messagetext' => $row->messagetext,
				'noindex' => $noindex_res
			));
		}
	}

	$name="Forumsuche";
	$content = "";

	// Daten in html code umwandeln diese werden dann durch den jetzigen table.innerHTML ersetzt.
	$content .= "<tr>";
	$content .= "<th>ID</th>";
	$content .= "<th>Betreff des Eintrages</th>";
	$content .= "<th> Benutzername</th>";
	$content .= "<th> Datum</th>";
	$content .= "<th> Wie oft angesehen</th>";
	$content .= "<th> NoIndex</th>";
	$content .= "</tr>";

	foreach($message_data as $message) {
		$content .= "<tr>";
		$content .= "<td>" . $message['msgid'] . "</td>";
		$content .= '<td style="width:30%;"><div><a href="./forum.php?msgid=' . $message['msgid'] . '&amp;menuid=42">' . $message['thema'] . '</a></div><div>' . substr($message['messagetext'], 0, 120) . '...</div></td>';
		$content .= "<td><i>" . $message['username'] . "</i></td>";
		$content .= "<td>" . $message['zeitstempel'] . "</td>";
		$content .= "<td>" . $message['counten'] . " x </td>";
		$content .= '<td>
                    <input type="hidden" name="noindex_forum_message[' . $message['msgid'] . ']" value="0"/>
                    <input type="checkbox" name="noindex_forum_message[' . $message['msgid'] . ']" ' . ($message['noindex'] ? 'checked' : '') . ' value="1"/>
                    </td>';
		$content .= "</tr>";
	}

	$size = strlen($content);

	header("Content-length: $size");
	header("Content-type: text");
	header("Content-Disposition: attachment; filename=$name");
	echo $content;
}
exit;