<?php
/**
 * Uebungen Klasse
 *
 * Dazu gehören noch einige weitere Klassen
 * die entsprechend eingebunden werden
 * @package Papoo
 * @author Papoo Software
 * @copyright 2009
 * @version $Id$
 * @access public
 */

if ( stristr($_SERVER['PHP_SELF'],'class_debug.php') ) die( 'You are not allowed to see this page directly' );

/**
 * Class debug
 */
class debug
{
	/**
	 * class_uebungen::class_uebungen()
	 *
	 * @return void
	 */
	function __construct()
	{

	}

	/**
	 * debug::d_print()
	 *
	 * @param mixed $data
	 * @return void
	 */
	static function d_print($data)
	{
		echo "<pre>";
		print_r($data);
		echo "</pre>";
	}

	/**
	 * @param $stamp
	 */
	function printddate($stamp)
	{
		self::printdate($stamp);
	}

	/**
	 * @param $stamp
	 */
	function printdate($stamp)
	{
		self::d_print(date("d.m.Y - H:i",$stamp));
	}

	/**
	 * @param $data
	 */
	static function print_no($data)
	{
		echo '<div style="display:none;">';
		self::print_d($data);
		echo  "</div>";
	}

	/**
	 * debug::mail()
	 *
	 * @param mixed $data = Inhalt der Meldung
	 * @param string $ort = Von wo kommt die Meldung
	 * @return void
	 */
	public function mail($data, $ort="")
	{
		@mail("info@papoo.de","Debug Meldung " . $ort, $data);
	}

	/**
	 * @param $data
	 * @param string $ort
	 * @param $file
	 */
	public function log($data ,$ort="", $file)
	{
		$filename   = PAPOO_ABS_PFAD. $file;
		$inhalt = date( "d.m.Y - H:i:s; " );
		$inhalt .= $_SERVER['REMOTE_ADDR'];
		$inhalt .= "; ";
		$inhalt .= $data." - ".$ort;
		$inhalt .= "; ";
		$inhalt .= $_SERVER['HTTP_USER_AGENT'];
		$inhalt .= "\r";
		$file = fopen( $filename, "a" );
		@fwrite( $file, $inhalt );
		@fclose( $file );
	}

	/**
	 * debug::print_d()
	 *
	 * @param mixed $data
	 * @return void
	 */
	static function print_d($data)
	{
		self::d_print($data);
	}

	/**
	 * @param string $ausgabe
	 */
	public function stop($ausgabe="")
	{
		if (!empty($ausgabe)) {
			self::print_d("Ausgabe:");
			self::print_d($ausgabe);
		}

		self::print_d("STOP");
		exit();
	}
}