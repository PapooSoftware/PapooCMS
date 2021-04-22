<?php
// #####################################
// # CMS Papoo                #
// # (c) Carsten Euwens 2003           #
// # Authors: Carsten Euwens           #
// # http://www.papoo.de               #
// # Internet                          #
// #####################################
// # PHP Version 4.2                   #
// #####################################

/*require_once "./artikel_class.php";
require_once "./cms_class.php";*/

/**
 * Diese Klasse zählt die User und gibt die Anzahl der User
 * aus, die die Seite besucht haben.
 * Wir zur Statistik Klasse noch ausgebaut.
 *
 * Class counter
 */
class counter {

	/** @var mixed Anzahl der Besucher */
	var  $counter;

	/**
	 * counter constructor.
	 */
	function __construct()
	{
		/**
		Klassen globalisieren
		 */

		// cms Klasse einbinden
		global $cms;
		$this->cms = & $cms;
		// einbinden des Objekts der Datenbak Abstraktionsklasse ez_sql
		global $db;
		$this->db = & $db;
		//Content Klasse
		global $content;
		$this->content = & $content;

		// Anzeige der bisherigen Besucher-Zahl setzen
		$this->content->template['gesamtcount'] = $this->get_count();
	}

	/**
	 *
	 */
	function count()
	{
		$this->make_count();
		$this->content->template['gesamtcount'] = $this->get_count();
	}
	/**
	 * Besucher zählen, mit IP-Sperre
	 */
	function make_count()
	{
		//zeitpunkt
		$time=time();
		//1 Tag zurück
		$timegestern=$time-86400;
		//aktuelle IP
		$ip=$_SERVER["REMOTE_ADDR"];
		$this->db->csrfok = true;

		//Alle Einträge löschen die älter l 24 h sind
		$sql=sprintf("DELETE FROM %s WHERE counter_stamp<'%s'",
			$this->cms->tbname['papoo_counter'],
			$this->db->escape($timegestern)
		);
		$this->db->query($sql);

		//Raussuchen ob ip existiert
		$sql=sprintf("SELECT * FROM %s WHERE counter_ip='%s'",
			$this->cms->tbname['papoo_counter'],
			$this->db->escape($ip)
		);
		$result = $this->db->get_results($sql);

		//Eintrag existiert nicht, also war der Besucher noch nicht hier
		if (empty($result)) {
			$sql=sprintf("INSERT INTO %s SET counter_ip='%s', counter_stamp='%s'",
				$this->cms->tbname['papoo_counter'],
				$this->db->escape($ip),
				$this->db->escape($time)
			);
			$this->db->query($sql);
			$sql = "UPDATE ".$this->cms->papoo_daten." SET gesamtcount=gesamtcount+1 ";
			$this->db->query($sql);
		}

		//KOntrolle ob Tabelle ok
		$sql=sprintf("SELECT * FROM %s LIMIT 1",
			$this->cms->tbname['papoo_counter']
		);
		$result=$this->db->get_results($sql);

		//Wenn leer
		if (empty($result)) {
			//Dann stimmt was nicht und Tabelle muß repariert werden
			$sql=sprintf("REPAIR TABLE %s ",
				$this->cms->tbname['papoo_counter']
			);
			$this->db->query($sql);
		}
		$this->db->csrfok = false;
	}

	/**
	 * @return mixed|null
	 */
	function get_count()
	{
		// Zahl der Besucher ermitteln
		/*
		$resultcount = $this->db->get_results("SELECT gesamtcount FROM  ".$this->cms->papoo_daten." WHERE datenid='2'");
		foreach ($resultcount as $rowcount)
		{
			$gesamtcount = $rowcount->gesamtcount;
		}
		*/
		if (empty($_SESSION['dbp']['gesamtcount'])) {
			$gesamtcount = $this->db->get_var("SELECT gesamtcount FROM  ".$this->cms->papoo_daten." WHERE datenid='2'");
			$_SESSION['dbp']['gesamtcount']=$gesamtcount;
		}
		else {
			$gesamtcount=$_SESSION['dbp']['gesamtcount'];
		}
		$this->counter=$gesamtcount;
		return $gesamtcount;
	}
}

$counter = new counter();
