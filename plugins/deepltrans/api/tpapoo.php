<?php
$apiKey = md5(__DIR__);
//3583a33c824d056b06fb826e71994b2a
if ($_GET['apiKey'] != $apiKey) {
	exit("nope");
}
//Damit es nicht versehentlich losläuft
//exit("Bitte im Skript aktivieren - hier steht ein exit() am Anfang zur Sicherheit \n");
//error_reporting(E_ALL);
//trans Klasse einbinden
require_once("transdeepl.php");

//papoo verarbeitungsklasse
require_once("transpapoo_vars.php");

//trans ini
$trans = new transdeepl();

//transpapoo ini
$transpapoo = new transpapoo_vars();

//zu übersetzende Sprachen "en", "fr","es","pl",
$langs = array("en", "fr", "es", "pt", "it", "nl", "ru", "zh");

/**
 * zuerst mal vergleichen
 */

//set errors on
#ini_set("display_errors", true);
#error_reporting(E_ALL);

//base is german
if (!empty($_GET['compare'])) {

	$count = array();
	$backfrount = array("backend", "frontend");
	foreach ($backfrount as $bfend) {

		$transfiles = [
			"0" => "/lib/messages/messages_" . $bfend . "_de.inc.php"
		];
		$pldirname = __DIR__ . "/../../../plugins/";
		$plugindirs = scandir($pldirname);

		foreach ($plugindirs as $k => $v) {
			if (strlen($v) < 3 || stristr($v,"Smarty")) {
				continue;
			}
			$transfiles[] = "/plugins/" . $v . "/messages/messages_" . $bfend . "_de.inc.php";
		}

		foreach ($transfiles as $k => $filename) {
			//print_r($filename);
			$base = array();
			//basedatafile -> de
			$transpapoo->get_vars($filename);
			//$base['de'] = $transpapoo->vars;
			foreach(new RecursiveIteratorIterator(new RecursiveArrayIterator($transpapoo->vars)) as $k=>$v){
				$base['de'][$k] = $v;
			}




			//dann die Daten aus den anderen Sprachen holen
			foreach ($langs as $kl => $lang) {
				//print_r($lang);exit();
				$filenameLang = str_ireplace('_de.', "_".$lang.'.', $filename);
				//print_r($filenameLang);
				$transpapoo->get_vars($filenameLang);
				//print_r($transpapoo->vars);
				//$base[$lang] = $transpapoo->vars;
				foreach(new RecursiveIteratorIterator(new RecursiveArrayIterator($transpapoo->vars)) as $k=>$v){
					$base[$lang][$k] = $v;
				}
				//Arrays for diffcount set each loop
				$count[$filename][$lang]['diff'] = 0;
				$count[$filename][$lang]['count'] = 0;
				//print_r($base);exit();
			}
			#if(stristr($filename,"bulk"))
			#{
				#print_r($filename);
				#print_r($base);
			#}

			//exit();
			//

			//jetzt abgleichen.
			foreach ($base['de'] as $var => $value) {
				foreach ($langs as $kl => $lang) {
					if (!empty($base[$lang])) {
						if (empty($base[$lang][$var])) {
							$count[$filename][$lang]['diff']++;
						}
					} else {
						$count[$filename][$lang]['diff']++;
					}
					$count[$filename][$lang]['count']++;
				}
			}
			//exit();
		}
		//print_r($count);
		//exit();
		$sumDiff = array();
		$sumCount = array();
		$plCounter = array();
		foreach ($count as $k => $v) {
			foreach ($v as $k1 => $v1) {
				if ($v1['count'] > 0) {
					$count[$k][$k1]['prozent'] = 100 - round(($v1['diff'] / $v1['count'] * 100), 0);
				} else {
					$count[$k][$k1]['prozent'] = 100;
				}
				if (!stristr($k, "lib")) {
					$sumDiff[$k1] += $v1['diff'];
					$sumCount[$k1] += $v1['count'];
					$plCounter[$k1]++;
				} else {
					$prozentDat[$bfend]['main'] = $count[$k];
				}
			}
		}
		foreach ($langs as $kl => $lang) {
			$prozentDat[$bfend]['plugin'][$lang]['prozent'] = round(100 - round(($sumDiff[$lang] / $sumCount[$lang] * 100), 2),0);
			$prozentDat[$bfend]['plugin'][$lang]['diff'] = $sumDiff[$lang];
			$prozentDat[$bfend]['plugin'][$lang]['count'] = $sumCount[$lang];
		}
	}
	#print_r($prozentDat);
	//print_r(trim(urlencode(json_encode($prozentDat,JSON_PRETTY_PRINT))));
	file_put_contents(__DIR__."/../../../interna/templates_c/trans.txt",json_encode($prozentDat,JSON_PRETTY_PRINT));
	//print_r(implod);
	exit();
}
//print_r($_GET['translate']);
if ($_GET['translate']==true)
{
	$transData = json_decode(urldecode($_GET['transData']),true);
	//print_r($transData);
	$transLang = array($transData['lang']);
	$base = "de";
	//$backfrount = json_decode($_GET['backfront']);
	$backfrount =	array($transData['backfront']);
	$deeplKey = $transData['deeplKey'];

	$pluginOrMain = $transData['pluginOrMain'];//($_GET['pluginOrMain']);

	foreach ($backfrount as $bfend) {
		$transfiles = array();

		if ($pluginOrMain == "main") {
			$transfiles = [
				"0" => "/lib/messages/messages_" . $bfend . "_de.inc.php"
			];
		}
		if ($pluginOrMain == "plugins") {
			$pldirname = __DIR__ . "/../../../plugins/";
			$plugindirs = scandir($pldirname);

			foreach ($plugindirs as $k => $v) {
				if (strlen($v) < 3 || stristr($v,"Smarty")) {
					continue;
				}
				$transfiles[] = "/plugins/" . $v . "/messages/messages_" . $bfend . "_de.inc.php";
			}
		}

		$i = 0;
		$isTranslated = 0;

		$new = array();
		foreach ($transLang as $single_lang) {
			foreach ($transfiles as $kfile => $filename) {

				$new = array();
				//print_r($filename);
				//Die Variablen aus der Datei holen die gerade dran ist...
				$transpapoo->get_vars($filename);
				$transpapoo->get_vars_target($filename,$single_lang);
				//print_r($transpapoo->vars);
				//die variablen durchloopen
				foreach ($transpapoo->vars as $pk => $pv) {
					//ein string... dann direkt übersetzen
					if (is_string($pv)) {
						if($transpapoo->isTranslatebleVar($pk,$pv))
						{
							$transtext = $trans->translate($single_lang, $pv,$deeplKey,$filename,$pk);
						}
						else{
							$transtext['trans_text'] = $transpapoo->targetVars[$pk];
						}
						$new[$pk] = $transtext['trans_text'];
					}

					//Alternativ ist array mit plugin
					if (is_array($pv)) {
						foreach ($pv as $k2 => $v2) {
							if (is_string($v2)) {
								if($transpapoo->isTranslatebleVarK2($pk,$k2,$v2)) {
									$transtext = $trans->translate($single_lang, $v2,$deeplKey,$filename,$k2);
									$isTranslated++;
								}
								else{
									$transtext['trans_text'] = $transpapoo->targetVars[$pk][$k2];
								}
								$new[$pk][$k2] = $transtext['trans_text'];
							}

							if (is_array($v2)) {
								foreach ($v2 as $k4 => $v4) {
									if (is_string($v4)) {
										if($transpapoo->isTranslatebleVarK3($pk,$k2,$k4,$v4)) {
											$transtext = $trans->translate($single_lang, $v4,$deeplKey,$filename,$k4);
											$isTranslated++;
										}
										else{
											$transtext['trans_text'] = $transpapoo->targetVars[$pk][$k2][$k4];
										}
										$new[$pk][$k2][$k4] = $transtext['trans_text'];
									}
									if (is_array($v4)) {
										foreach ($v4 as $k6 => $v6) {
											if (is_string($v6)) {
												if($transpapoo->isTranslatebleVarK4($pk,$k2,$k4,$k6,$v6)) {
													$transtext = $trans->translate($single_lang, $v6,$deeplKey,$filename,$k6);
													$isTranslated++;
												}
												else{
													$transtext['trans_text'] = $transpapoo->targetVars[$pk][$k2][$k4][$k6];
												}
												$new[$pk][$k2][$k4][$k6] = $transtext['trans_text'];
											}
										}
									}
								}
							}
						}
					}
				}
				//print_r($new);exit();
				$transpapoo->save_vars($filename, $new, $single_lang);
			}
		}
	}
	//print_r(json_encode(array("translation"=>true),JSON_PRETTY_PRINT));
	file_put_contents(__DIR__."/../../../interna/templates_c/transOK.txt",json_encode(array("translation"=>true,JSON_PRETTY_PRINT)));
}


?>
