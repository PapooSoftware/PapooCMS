<?php

/*



// +--------------------------------------------------------------------------+

// | Obige Zeilen dürfen nicht entfernt werden!    Do not remove above lines! |

// +--------------------------------------------------------------------------+

*/

?>


<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

<html>

<head>

<title><?php echo $DOC_TITLE; ?></title>
<meta http-equiv="CONTENT-TYPE" content="text/html; charset=iso-8859-1">
<meta name="AUTHOR" CONTENT="Norbert Josef Ronawati">
<meta name="COPYRIGHT" CONTENT="Copyright (c)2004 Norbert Josef Ronawati">



<style type="text/css">

body {
  margin: 1;
  color: #000000;
  background-color : ;
  background-repeat:no-repeat;
  background-image: url(./images/icons/);
  font-family: Verdana, Geneva, Arial, sans-serif;
  font-size: 9pt;
  text-decoration: none;
}

.desc {
  background-color:;
  border-right: ;
  border-bottom: #00C070 0px dotted;
  color: #FF0000;
  font-family: Verdana, Arial, sans-serif;
  font-size: 14pt;
  font-weight: bold;
  text-decoration: none;

}

.info {
  background-color:;
  border-right: ;
  border-bottom: #00C070 0px dotted;
  color: #000080;
  font-family: Verdana, Arial, sans-serif;
  font-size: 11pt;
  font-weight: bold;
  text-decoration: none;

}

.descsmall {
  background-color:;
  border-right: ;
  border-top: #000080 0px dotted;
  border-bottom: #000080 0px dotted;
  color: #000000;
  font-family: Verdana, Arial, sans-serif;
  font-size: 10pt;
  font-weight: bold;
  text-decoration: none;

}

.rem {
  background-color:;
  border-right: ;
  border-top: #000080 0px dotted;
  border-bottom: #000080 0px dotted;
  color: #000000;
  font-family: Verdana, Arial, sans-serif;
  font-size: 9pt;
  font-weight: regular;
  text-decoration: none;

}

.ber {
  background-color: ;
  border-right: ;
  border-top: #000080 0px dotted;
  border-bottom: #000080 0px dotted;
  color: #000000;
  font-family: Verdana, Arial, sans-serif;
  font-size: 9pt;
  font-weight: bold;
  text-decoration: none;

}




</style>

</head>
<body>
<?php



	include_once('lib.xml.inc.php');

	$rss = new XML('http://xml.schneeforum.de/schneebericht.xml');
	$items = $rss->getElementsByTagName('zeile');
        	foreach ($items as $k => $item) {

		$a = asArray($item->firstChild);

		if ($a['id_skigeb'] == "1") {


echo "<table border='0' bordercolor=#0000DF style='border-collapse: collapse' width=620 cellpadding='0' cellspacing='0' class=''>";
echo "<tr><td class='desc'  align='left'> Skigebiet ".$a['skigebiet']."</td></tr>";
echo "<tr><td class='info'>". $a['plz']." - ".$a['ort']."</td></tr>";
echo "</table><br>";

echo "<table border='1' bordercolor=#0000DF style='border-collapse: collapse' width=620 cellpadding='4' cellspacing='2' class=''>";
echo "<tr><td class='descsmall' > update: ".$a['datum']."<br> --> letzter Schneefall: ".$a['schneefall']." --> Neuschneemenge: ".$a['neuschnee']." cm </td></tr>";
echo "</table>";

echo "<table border='0' bordercolor=#0000DF style='border-collapse: collapse' width=620 cellpadding='4' cellspacing='2' class=''>";
echo "<tr><td class='info'> Betriebszeiten</td></tr>";
echo "<tr><td class='rem'> ".$a['betrieb']."</td></tr>";
echo "</table>";

echo "<table border='0' bordercolor=#0000DF style='border-collapse: collapse' width=620 cellpadding='4' cellspacing='2' class=''>";
echo "<tr><td class='info' colspan='4'> Schneehöhen - Pisten - Anlagen Info</td></tr>";

echo "</td></tr>";
echo "<tr class='rem' bgcolor='#F4E3AE' align='center'>";
echo "<td width='100'>Schnee Ort</td>";
echo "<td width='100'>Talstation</td>";
echo "<td width='100'>Mittelstation</td>";
echo "<td width='100'>Bergstation</td>";
echo "</tr>";
echo "<tr class='ber' bgcolor='#DFDBDB' align='center'>";
echo "<td width='100'> ".$a['schnee_ort']." cm</td>";
echo "<td width='100' bgcolor='$bg'> ".$a['talstation']." cm</td>";
echo "<td width='100'> ".$a['mittelstation']." cm</td>";
echo "<td width='100' bgcolor='$bga'> ".$a['bergstation']." cm</td>";
echo "</tr>";
echo "<tr><td class='info' colspan='4' height='5'></td></tr>";
echo "</td></tr>";
echo "<tr class='rem' bgcolor='#F4E3AE' align='center'>";
echo "<td width='100'>Pisten insgesamt</td>";
echo "<td width='100'>Pisten geöffnet</td>";
echo "<td width='100'>Anlagen insgesamt</td>";
echo "<td width='100'>Anlagen geöffnet</td>";
echo "</tr>";
echo "<tr class='ber' bgcolor='#DFDBDB' align='center'>";
echo "<td width='100'> ".$a['pistentotal']." km</td>";
echo "<td width='100' >  ".$a['pisten_run']." km</td>";
echo "<td width='100'> ".$a['anlagen']."</td>";
echo "<td width='100' > ".$a['geoeffnet']."</td>";
echo "</tr>";
echo "<tr><td class='info' colspan='4' height='5'></td></tr>";
echo "<tr><td class='descsmall' colspan='2' width=200> Talabfahrt:</td><td class='ber' colspan='2' width=550>".$a['talabfahrt']."</td></tr>";
echo "<tr><td class='descsmall' colspan='2' width=200> Pistenzustand:</td><td class='ber' colspan='2' width=550>".$a['pistenzustand']."</td></tr>";
echo "<tr><td class='descsmall' colspan='2' width=200> Schneezustand:</td><td class='ber' colspan='2' width=550>".$a['schneezustand']."</td></tr>";
echo "</table>";

echo "<table border='1' bordercolor=#0000DF style='border-collapse: collapse' width=620 cellpadding='4' cellspacing='2' class=''>";
echo "<tr><td class='info'> Flutlichtbetrieb: ".$a['flutlicht']."</td></tr>";
echo "<tr><td class='rem'> ".$a['betrieb_flut']."</td></tr>";
echo "</table><br>";

echo "<table border='1' bordercolor=#0000DF style='border-collapse: collapse' width=620 cellpadding='4' cellspacing='2' class=''>";
echo "<tr><td class='info'> Sonstiges</td></tr>";
echo "<tr><td class='rem'> ".$a['sonstiges']."</td></tr>";
echo "<tr><td class='rem'> <a href='".$a['webcam']."' target='_blank'><img src='".$a['webcam']."' width='160' border='0' alt='Webcambild vergr&ouml;&szlig;ern Skigebiet ".$a['skigebiet']."'></a></td></tr>";

echo "</table>";



echo "<table border='0' bordercolor=#0000DF style='border-collapse: collapse' width=620 cellpadding='4' cellspacing='2' class=''>";
echo "<tr><td class='info' colspan='4'> Loipen insgesamt ".$a['loipen']." km</td></tr>";
echo "</td></tr>";
echo "<tr class='rem' bgcolor='#F4E3AE' align='center'>";
echo "<td width='100'>davon Klassisch</td>";
echo "<td width='100'>davon Skating</td>";
echo "<td width='100'>gespurt Klassisch</td>";
echo "<td width='100'>gespurt Skating</td>";
echo "</tr>";
echo "<tr class='ber' bgcolor='#DFDBDB' align='center'>";
echo "<td width='100'> ".$a['nordic']." km</td>";
echo "<td width='100' > ".$a['skating']." km</td>";
echo "<td width='100'> ".$a['gespurt_n']." km</td>";
echo "<td width='100' > ".$a['gespurt_s']." km</td>";
echo "</tr>";
echo "</table>";

echo "<table border='0' bordercolor=#0000DF style='border-collapse: collapse' width=620 cellpadding='4' cellspacing='2' class=''>";
echo "<tr><td class='descsmall' colspan='2' width=400> Loipen Zustand Klassisch:</td><td class='ber' colspan='2' width=350>".$a['loipenzustand_n']."</td></tr>";
echo "<tr><td class='descsmall' colspan='2' width=400> Loipen Zustand Skating:</td><td class='ber' colspan='2' width=350>".$a['loipenzustand_s']."</td></tr>";
echo "</table><br>";


echo "<table border='1' bordercolor=#0000DF style='border-collapse: collapse' width=620 cellpadding='4' cellspacing='2' class=''>";
echo "<tr><td class='descsmall' colspan='2' width=400> Winterrodelbahn geöffnet:</td><td class='ber' colspan='2' width=350>".$a['rodelbahn']."</td></tr>";
echo "<tr><td class='descsmall' colspan='2' width=400> Halfpipe geöffnet:</td><td class='ber' colspan='2' width=350>".$a['halfpipe']."</td></tr>";
echo "<tr><td class='descsmall' colspan='2' width=400> Funpark geöffnet:</td><td class='ber' colspan='2' width=350>".$a['funpark']."</td></tr>";
echo "</table><br>";


echo "<table border='1' bordercolor=#0000DF style='border-collapse: collapse' width=620 cellpadding='4' cellspacing='2' class=''>";
echo "<tr><td class='descsmall' colspan='2' width=400> Winterwanderwege insgesamt:</td><td class='ber' colspan='2' width=350>".$a['wanderwege']." km</td></tr>";
echo "<tr><td class='descsmall' colspan='2' width=400> Winterwanderwege geräumt:</td><td class='ber' colspan='2' width=350>".$a['wanderwege_g']." km</td></tr>";
echo "</table><br>";


echo "<table border='1' bordercolor=#0000DF style='border-collapse: collapse' width=620 cellpadding='4' cellspacing='2' class=''>";
echo "<tr><td class='descsmall' colspan='2' width=400> Zufahrt Talstation:</td><td class='ber' colspan='2' width=350>".$a['zufahrt']."</td></tr>";
echo "<tr><td class='descsmall' colspan='2' width=400> Parkplatz vorhanden:</td><td class='ber' colspan='2' width=350>".$a['parken']."</td></tr>";
echo "</table><br>";

echo "<table border='1' bordercolor=#0000DF style='border-collapse: collapse' width=620 cellpadding='4' cellspacing='2' class=''>";
echo "<tr><td class='ber' colspan='2'width=350><a href='".$a['webcam']."' target=_'blank'>Webcam</a></td></tr>";




echo "</table><br>";

		}
	}

	// Wandelt das Objekt in ein Array um
	function asArray($item) {
		$a = array();
		while ($item) {
			$a[$item->nodeName] = $item->firstChild->nodeValue;
			$item = $item->nextSibling;
		}
		return $a;
	}




?>


</body>
</html>