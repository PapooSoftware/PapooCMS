<?php

/**
 * Hier handelt es sich um eine Beispiel Klasse
 * Die mu� man nicht so benutzen.
 * Im Prinzip kann man hier reinschreiben was man m�chte
 *
 * Class rss
 */
class rss
{
	/**
	 * rss constructor.
	 */
	function __construct()
	{
		global $content, $checked, $cms, $db;
		$this->content = &$content;
		$this->checked = &$checked;
		$this->cms = &$cms;
		$this->db = &$db;

		//Admin Ausgab erstellen
		$this->set_backend_message();

		//Frontend - dann Skript durchlaufen
		if (!defined("admin")) {
			//Fertige Seite einbinden
			global $output;
			//Zuerst check ob es auch vorkommt
			if (strstr( $output,"#rss")) {
				//Ausgabe erstellen
				$output=$this->create_rssintegration($output);
			}
		}
	}

	/**
	 * Hiermit setzt man die Eintr�ge in der Administrations�bersicht
	 *
	 * @return void
	 */
	function set_backend_message()
	{
		//Zuerst die �berschrift - de = Deutsch; en = Englisch
		$this->content->template['plugin_cm_head']['de'][]="Skript RSS Feed an beliebiger Stelle";

		//Dann den Beschreibungstext
		$this->content->template['plugin_cm_body']['de'][]="Mit diesem kleinen Skript kann man an beliebiger Stelle in Inhalten einen bestimmten RSS Feed ausgeben lassen, die Syntax lautet.<br /><strong>#rss_https://rss_url.xy#</strong><br /> Es werden die neuesten 3 Einträge ausgegeben. ";

		//Dann ein Bild, wenn keines vorhanden ist, dann Eintrag trotzdem leer drin belassen
		$this->content->template['plugin_cm_img']['de'][] = '';
	}

	/**
	 * @param string $inhalt
	 *
	 * @return mixed|string|string[]|null
	 */
	function create_rssintegration($inhalt = "")
	{
		//Ids rausholen mit Hilfe eines Regul�ren Ausdrucks
		preg_match_all("|#rss(.*?)#|", $inhalt, $ausgabe, PREG_PATTERN_ORDER);
		$i = 0;
		foreach ($ausgabe['1'] as $dat) {
			//Die Unterstriche rausholen
			$ndat = explode("_", $dat);

			//Mit Hilfe der ID einen Eintrag rausholen
			$banner_daten = $this->get_rss($ndat['1']);

			//Ersetzung durchf�hren
			$inhalt = str_ireplace($ausgabe['0'][$i], $banner_daten, $inhalt);
			$i++;
		}

		//Ge�nderten Inhalt zur�ckgeben
		return $inhalt;
	}

	/**
	 * @param int $id
	 *
	 * @return string
	 */
	function get_rss($id = 0)
	{
		//RSS Daten holen
		$rss_data=$this->get_rss_data($id);

		//Daraus HTML machen
		$html_data=$this->create_html_data($rss_data);

		return $html_data;
	}

	/**
	 * @param array $data
	 *
	 * @return string
	 */
	private function create_html_data($data=array())
	{
		$html="";
		$i=0;
		if (is_array($data)) {
			foreach ($data as $key=>$value) {
				if ($i>2)continue;
				$i++;
				preg_match_all( '/<img[^>]*>/Ui',$value['description'], $img_data );

				$html.='<div id="content_'.$key.'" class="teaser large-12 medium-12 columns">
						<div class="large-3 medium-12 columns">'.$img_data['0']['0'].'</div>
						<div class="large-9 medium-12 columns"><h4>'.$value['title'].'</h4>
						<div class="teasertext"><p><p>'.substr(strip_tags($value['description']),0,250).'...<br />
						<a target="blank" href="'.$value['link'].'">Zum Newseintrag "'.$value['title'].'" auf der Seite des Fachbetriebes...</a></p></div>
						</div>
						</div>
						<div class="clearfix"></div>';
			}
		}
		return $html;
	}

	/**
	 * @param string $feed_url
	 *
	 * @return array|null
	 */
	private function get_rss_data($feed_url="")
	{
		//Daten holen
		$data=$this->get_data($feed_url);
		//$data=$this->atom_to_xml($data);
		//In Array umwandeln
		$xml = simplexml_load_string($data,'SimpleXMLElement', LIBXML_NOCDATA);
		$json = json_encode($xml);
		$array = json_decode($json,TRUE);
		//Nur die Eintr�ge hinzuf�gen
		$items[]=$array['channel']['item'];

		//Eintr�ge durchgehen
		if (is_array($items)) {
			foreach ($items as $key=>$value) {
				if (is_array($value)) {
					foreach ($value as $key_item=>$value_item) {
						if (is_array($value_item)) {
							if ($value_item['comments']==1) {
								$time=time();
							}
							else {
								$time=strtotime($value_item['pubDate']);
							}
							$neu[$time]=$value_item;
						}

					}
				}
			}
		}
		if (isset($neu) && is_array($neu)) {
			krsort($neu);
		}
		IfNotSetNull($neu);
		return $neu;
	}

	/**
	 * @param $url
	 *
	 * @return bool|string|void
	 */
	private function get_data($url)
	{
		if (function_exists("curl_init")) {
			$ch = curl_init( trim($url) );
			curl_setopt( $ch, CURLOPT_TIMEOUT, 5 );
			curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
			curl_setopt( $ch, CURLOPT_HEADER, 0 );
			curl_setopt( $ch, CURLOPT_USERAGENT, "Check Agent" );

			$curl_ret = curl_exec( $ch );
			curl_close( $ch );
			return $curl_ret;
		}
	}
}

$rss=new rss();
