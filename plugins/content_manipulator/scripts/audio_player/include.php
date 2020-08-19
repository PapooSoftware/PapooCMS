<?php

/**
 * Class AudioPlayer
 */
class AudioPlayer
{
	/** @var content_class */
	public $content;
	/** @var checked_class */
	public $checked;
	/** @var cms */
	public $cms;
	/** @var ezSQL_mysqli */
	public $db;

	/** @var string regex Used for testing the template and fetching all occurrences */
	private $shortcodeRegex = "|#audioPlayer_(?'id'\d*)#|";

	/** @var string HTML snippet for creating new audio nodes */
	private $audioHtmlSnippet = <<< HTML
		<audio controls="controls">
			<source src="#filename#" type="#mediaType#">
			Your browser does not support the audio element.
		</audio>
HTML;

	/**
	 * AudioPlayer constructor.
	 */
	public function __construct()
	{
		global $content, $checked, $cms, $db;
		$this->content = &$content;
		$this->checked = &$checked;
		$this->cms = &$cms;
		$this->db = &$db;
		$this->setBackendMessage();

		if (!defined("admin") || ($_GET['is_lp'] == 1)) {
			global $output;
			if (preg_match($this->shortcodeRegex, $output)) {
				$output = $this->createAudioPlayer($output);
			}
		}
	}

	/**
	 * Adds the description for this script in the content manipulator dashboard
	 */
	private function setBackendMessage()
	{
		$this->content->template['plugin_cm_head']['de'][] = "Skript Audio Player";
		$this->content->template['plugin_cm_body']['de'][] =
			"Mit diesem kleinen Skript kann man an beliebiger Stelle im Inhalt einen HTML5-Audio-Player "
			. "einbauen. Die abzuspielende Datei muss vorher über die Papoo-Dateiverwaltung hochgeladen werden. "
			. "Die Syntax lautet<br /><strong>#audioPlayer_X#</strong><br />, wobei X die ID der Audio-Datei "
			. "in der Dateiverwaltung bezeichnet. Unterstützte Formate sind mp3, ogg und wav.";
		$this->content->template['plugin_cm_img']['de'][] = '';
	}

	/**
	 * Ersetzt alle Platzhalter mit dem html der jeweiligen Bilderliste
	 *
	 * @param string $inhalt
	 *
	 * @return mixed|string
	 */
	private function createAudioPlayer($inhalt = "")
	{
		preg_match_all($this->shortcodeRegex, $inhalt, $ausgabe, PREG_PATTERN_ORDER);

		for ($i = 0; !empty($ausgabe[0][$i]); $i++) {
			$filename = $this->getAudioFileName($ausgabe['id'][$i]);
			$ext = pathinfo($filename, PATHINFO_EXTENSION);

			$mediaType = null;
			if (in_array($ext, ['ogg', 'wav',])) {
				$mediaType = 'audio/' . $ext;
			}
			else if ($ext === 'mp3') {
				$mediaType .= 'audio/mpeg';
			}

			$html = '';
			if ($mediaType && $filename) {
				$html = $this->audioHtmlSnippet;
				// Nur ersetzen, wenn gültiger Mediatyp gefunden und Dateiname
				foreach (['#filename#' => $filename, '#mediaType#' => $mediaType] as $search => $replace) {
					$html = str_replace($search, $replace, $html);
				}
			}

			$inhalt = str_ireplace($ausgabe['0'][$i], $html, $inhalt);
		}

		return $inhalt;
	}

	/**
	 * Fetches the filename for file's given ID
	 *
	 * @param int $fileID This will be used to search for the filename
	 *
	 * @return string|null
	 */
	private function getAudioFileName($fileID)
	{
		$sql = sprintf(
			"SELECT downloadlink FROM %s WHERE downloadid = %d LIMIT 1",
			$this->cms->papoo_download,
			(int)$fileID
		);
		$filename = $this->db->get_row($sql, ARRAY_A)['downloadlink'];
		return $filename && is_readable(PAPOO_ABS_PFAD . $filename) ? $filename : null;
	}
}

$audioplayer = new AudioPlayer();
