<?php
/**
 * #####################################
 * # CMS Papoo                         #
 * # (c) Dr. Carsten Euwens 2008       #
 * # Authors: Carsten Euwens           #
 * # http://www.papoo.de               #
 * # Internet                          #
 * #####################################
 * # PHP Version >4.2                  #
 * #####################################
 */

/**
 * Class make_nested
 */
class make_nested
{
	/**
	 * make_nested constructor.
	 */
	function __construct()
	{
	}

	/**
	 *  make_nested::make_nested_now()
	 * Das Array result muß so aussehen
	 * Array
	 *	(
	 *	    [0] => Array
	 *	        (
	 *	            [cat_id] => 1 			//Schlüssel
	 *	            [sub_cat_von] => 0 		//Unterschlüssel von
	 *	            [sub_cat_level] => 0 	//Level
	 *	        )
	 *	)
	 * sub_cat_von 		= Unterkategorie von
	 * sub_cat_level	= Der jeweilige Level der Kategorie
	 * @param mixed $result
	 * @param string $print
	 * @param int $checked_catid
	 * @return array
	 */
	function create_nested_array($result = array(), $print = "NO",$checked_catid=0)
	{
		$this->checked_catid=$checked_catid;

		if ( is_array($result) ) {
			//Array durchgehen
			foreach ( $result as $key => $value ) {
				//Alle Unterlevel in ein eigenes Array einlesen
				if ( $value['sub_cat_level'] > 0 ) {
					//Level und Unterkategorie von sind der jeweilige Schlüssel
					$levels[$value['sub_cat_level']][$value['sub_cat_von']][] = $value;
				}
				else {
					//Ebene 0 in eigenes Array einlesen
					$null_level[] = $value;
				}
			}
		}
		//Wenn es Unterlevel gibt

		IfNotSetNull($null_level);
		if (isset($levels) and is_array($levels) ) {
			//Ebene 0 durchlaufen
			if (is_array($null_level)) {
				foreach ( $null_level as $key => $value ) {
					$this->is_aktiv="";
					//Wenn aktiv dann setzen für Selectfelder
					if ($value['cat_id']==$this->checked_catid) {
						$this->is_aktiv=1;
						$value['is_aktiv']=$this->is_aktiv;
					}
					//Wenn es zu dem jeweiligen Punkt Unterpunkte gibt
					if (isset($levels['1'][$value['cat_id']]) && is_array($levels['1'][$value['cat_id']]) ) {
						//Zuerst den aktuellen Wert in Ausgabe Übergeben ebene 0
						$this->all_levels[] = $value;

						//Dann Unterlevel erzeugen
						$this->get_sub_levels( $levels, $value['cat_id'] );
					}
					//Keine Unterpunkte
					else {
						//Level Ebene 0 Übergeben
						$this->all_levels[] = $value;
					}
				}
			}
		}
		//Keine Unterlevel
		else {
			//0 Level Übergeben
			$this->all_levels = $null_level;
		}
		if (is_array($this->all_levels)) {
			IfNotSetNull($inaktiv);
			IfNotSetNull($aktiv);

			$i=0;
			foreach ($this->all_levels as $key=>$value) {
				if (isset($subcat_level) && $value['sub_cat_level']<$subcat_level && $aktiv==1) {
					$inaktiv=1;
				}
				if (isset($value['is_aktiv']) && $value['is_aktiv']==1 && $inaktiv!=1) {
					$aktiv=1;
				}
				if ($inaktiv==1) {
					$this->all_levels[$i]['is_aktiv']="0";
				}
				$subcat_level=$value['sub_cat_level'];
				$i++;
			}
		}
		return $this->all_levels;
	}

	/**
	 * make_nested::get_sub_levels()
	 *
	 * Hier werden die Unterebenen erzeugt
	 * Diese Funktion ruft sich selber immer wieder
	 * rekursiv auf bis die tiefste Ebene
	 * erreicht ist und springt wieder zurück
	 *
	 * @param mixed $sub
	 * @param integer $aktu_id
	 * @param integer $lev
	 * @return void
	 */
	function get_sub_levels( $sub = array(), $aktu_id = 0, $lev = 1 )
	{
		if ( is_array($sub[$lev]) ) {
			//Unterebene durchgehen des aktuellen Levels
			foreach ( $sub[$lev] as $key => $value ) {
				//Alle Einträge auf diesem Level
				foreach ( $value as $k => $v ) {
					$this->level=$lev;

					//Wenn aktiv dann setzen für Selectfelder
					if ($v['cat_id']==$this->checked_catid) {
						$this->is_aktiv=1;

					}
					$v['is_aktiv']=$this->is_aktiv;

					//Eintrag passend zum aktuellen Subpunkt 
					//der hier gerade durchläuft => akut_id
					if ( $aktu_id == $v['sub_cat_von'] ) {
						//Nächsten Level Nummer erzeugen
						$lev_plus = $lev + 1;

						//Wenn es einen Eintrag im nächsten Level gibt
						if (isset($sub[$lev_plus][$v['cat_id']]) && is_array($sub[$lev_plus][$v['cat_id']]) ) {
							//Leveldaten Übertragen
							$this->all_levels[] = $v;

							//Rekursiv eine Ebene tiefer gehen und Daten rausholen
							$this->get_sub_levels( $sub, $v['cat_id'], $lev_plus );
						}
						//Kein Eintrag, höchste jeweiliger Level
						else {
							//Leveldaten Übertragen
							$this->all_levels[] = $v;
						}
					}
				}
			}
		}
	}


	/**
	 * make_nested::create_nested_html_from_array()
	 *
	 * @param mixed $result
	 * @param string $ul_ol
	 * @param string $class
	 * @return array
	 */
	function create_nested_list_from_array($result=array(),$ul_ol="ul",$class="nested_ul")
	{
		$i=0;
		if (is_array($result)) {
			//Festlegen ob ul oder ol
			if ($ul_ol=="ul") {
				$ul_ol="ul";
			}

			if ($ul_ol=="ol") {
				$ul_ol="ol";
			}
			//Wenn keines dann ul
			if ($ul_ol!="ul" && $ul_ol!="ol") {
				$ul_ol="ul";
			}
			//Einträge durchgehen
			foreach ($result as $key=>$value) {
				IfNotSetNull($alt_level);

				$level=$value['sub_cat_level'];

				//Liste starten, also erstmal ein ul li
				if ($i==0) {
					$result[$key]['vor_ul']="<div class=\"".$class."\" ><".$ul_ol."><li>";
					$result[$key]['nach_ul']="</li>";
				}
				//Wenn Level hoch
				if ($alt_level<$level && $i>0) {
					$result[$key]['vor_ul']="<".$ul_ol."><li>";
					$result[$key]['nach_ul']="</li>";
				}
				//Wenn Level runter
				if ($alt_level>$level && $i>0) {
					$dif=$alt_level-$level;
					$ul="";
					for ($x = 1; $x <= $dif; $x++) {
						$ul.="</".$ul_ol."></li>";
					}

					$result[$key]['vor_ul']=$ul."<li>";
					$result[$key]['nach_ul']="</li>";
				}

				//Wenn Level gleich
				if ($alt_level==$level && $i>0) {
					$result[$key]['vor_ul']="<li>";
					$result[$key]['nach_ul']="</li>";
				}

				$alt_level=$value['sub_cat_level'];
				$i++;

				$nbsp="";
				for ($x = 1; $x <= $level; $x++) {
					$nbsp.="+";
				}
				$nbsp="".$nbsp;
				$result[$key]['nbsp']=$nbsp;
			}
		}
		if ($i == 0) {
			$result[]['vor_ul']="<div class=\"".$class."\" ><".$ul_ol."><li>";
			$result[]['nach_ul']="</li></".$ul_ol."></div>";
		}
		else {
			$result[$key]['nach_ul']="</li></".$ul_ol."></div>";
		}
		return $result;
	}
}