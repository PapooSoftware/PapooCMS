<?php
/**
 * @autor: Andreas Gritzan <ag@papoo.de>
 */


include('../../../lib/site_conf.php');
include ('../../../lib/ez_sql.php');

if(isset($_REQUEST['search'])) {
	global $db, $db_praefix;

	$article_version_language = $db_praefix . "papoo_version_language_article";
	$article_language = $db_praefix . "papoo_language_article";
	$article_version_repore = $db_praefix . "papoo_version_repore";

	$suchterm = $_REQUEST['search'];

	$suchterm = $db->escape($suchterm);

	$sql = sprintf("SELECT tabelle_ohne_lastedit.*, repore_tabelle.timestamp as lastedit FROM (
                                        SELECT langtable.lan_repore_id, langtable.versionid as version, langtable.lan_metatitel, langtable.lan_teaser, COUNT(langtable.versionid) as versions
                                        FROM (SELECT * FROM %s a LEFT JOIN (SELECT lan_repore_id AS existing_article_id FROM %s) b ON a.lan_repore_id=b.existing_article_id ORDER BY a.versionid DESC) langtable
                                        WHERE langtable.existing_article_id IS NULL
                                        GROUP BY langtable.lan_repore_id
                                      ) tabelle_ohne_lastedit
                                      LEFT JOIN %s repore_tabelle ON tabelle_ohne_lastedit.version=repore_tabelle.versionid
                                      WHERE tabelle_ohne_lastedit.lan_teaser LIKE '%%%s%%' OR tabelle_ohne_lastedit.lan_metatitel LIKE '%%%s%%' OR tabelle_ohne_lastedit.lan_repore_id LIKE '%%%s%%';
                            ",
		$article_version_language,
		$article_language,
		$article_version_repore,
		$suchterm,
		$suchterm,
		$suchterm);

	#$sql = sprintf("SELECT versionid FROM %s WHERE lan_repore_id=4", $this->version_language);

	$result = $db->get_results($sql, ARRAY_A);

	foreach($result as &$entry) {
		$teaser = &$entry['lan_teaser'];

		$teaser = strip_tags($teaser);
	}

	$name="Artikelsuche";
	$content = "";

	// Daten in html code umwandeln diese werden dann durch den jetzigen table.innerHTML ersetzt.
	$content .= "<tr>";
	$content .= "<th> Artikel ID</th>";
	$content .= "<th> Überschrift</th>";
	$content .= '<th style="width:30%;"> Teaser</th>';
	$content .= "<th> Zuletzt bearbeitet</th>";
	$content .= "<th> Versionen</th>";
	$content .= '<th style="width: 10%;"> Wiederherstellen</th>';
	$content .= "</tr>";

	foreach($result as $article) {
		$content .= '<tr>';
		$content .= '<form action="#" method="post" class="feedback_button" id="article_' . $article['lan_repore_id'] . '" name="article_' . $article['lan_repore_id'] . '">';
		$content .= '<td>' . $article['lan_repore_id'] . '</td>';
		$content .= '<td>' . $article['lan_metatitel'] . '</td>';
		$content .= '<td style="width:30%;">' . $article['lan_teaser'] . '...</td>';
		$content .= '<td>' . $article['lastedit'] . '</td>';
		$content .= '<td>' . $article['versions'] . '</td>';
		$content .= '<td style="width:10%;">';
		$content .= '<input type="hidden" value="' . $article['version'] . '" id="article_hidden_' . $article['lan_repore_id'] . '"  name="restorearticle[' . $article['lan_repore_id'] . ']"/>';
		$content .= '<button type="submit" name="article_' . $article['lan_repore_id'] . '" id="article_button_' . $article['lan_repore_id'] . '"  class="btn-info button-restoring"/>Version ' . $article['version'] . ' wiederherstellen</button>';
		$content .= '</td>';
		$content .= '</form>';
		$content .= '</tr>';
	}

	$size = strlen($content);

	#header("Content-length: $size");
	#header("Content-type: text");
	#header("Content-Disposition: attachment; filename=$name");
	echo $content;
}
exit;