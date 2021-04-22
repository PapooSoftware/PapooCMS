<?php
/**
 *
 * @package Papoo
 * @author Papoo Software
 * @copyright 2009
 * @access public
 */

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
	header('HTTP/1.1 405 Method Not Allowed');
	return;
}

if(!empty($_GET['query'])) {
	// Header setzen
	header('Content-Type: application/json; charset=utf-8');
	header('Cache-Control: public, max-age=60');
	header('Vary: Accept-Encoding');
	header('Content-Security-Policy: default-src \'self\'; frame-ancestors \'none\'');
	// Datenbankverbindung
	require_once "../../../lib/site_conf.php";
	require_once PAPOO_ABS_PFAD . "/lib/ez_sql.php";
	/** @var ezSQL_mysqli $db */
	$db->query("SET NAMES 'utf8'");
	//$db2->query("SET CHARACTER SET 'utf8'");

	$query = $_GET['query'];
	$i = strrpos($query, ' ');
	if ($i !== false) {
		$query_prefix = substr($query, 0, $i+1);
		$query_last_word = substr($query, $i+1);
	}
	else {
		$query_prefix = '';
		$query_last_word = $query;
	}

	if (strlen($query_last_word) > 1) {
		/** @noinspection PhpUndefinedVariableInspection */
		$sql = sprintf(
			'SELECT ext_search_wort_id, AVG(ext_search_score_id) AS `ext_search_score_id` FROM %1$s
			WHERE ext_search_wort_id LIKE \'%2$s\' 
			GROUP BY `ext_search_wort_id`
			ORDER BY `ext_search_score_id` DESC, `ext_search_wort_id` ASC
			LIMIT 50',
			$db_praefix."plugin_ext_search_vorkommen",
			$db->escape($query_last_word."%")
		);
		$result=$db->get_results($sql,ARRAY_A);
	}
	else {
		$result = null;
	}

	$suggestions = array();
	if(is_array($result)) {
		foreach($result as $value) {
			$suggestions[] = $query_prefix.ucfirst($value['ext_search_wort_id']);
		}
	}

	$arr = array(
		'query' => '\'' . addslashes($query) . '\'',
		'data' => array(
			'LR', 'LY', 'LI', 'LT'
		),
		'suggestions' => $suggestions,
	);
	echo json_encode($arr);
	exit();
}
else {
	header('HTTP/1.1 400 Bad Request');
	return;
}