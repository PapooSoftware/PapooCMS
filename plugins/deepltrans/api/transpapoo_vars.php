<?php
/**
 * Papoo Variablen holen und speichern
 */

class transpapoo_vars
{
	public $vars;

	public function __construct()
	{
	}

	public function get_vars($file = "")
	{
		//Arrays zurücksezten - addiert sich sonst auf...
		$this->vars = array();
		@$this->content->template = array();

		//error_reporting(E_ALL);
		if (file_exists("../../../" . $file)) {
			require("../../../" . $file);
			$this->vars = ($this->content->template);
		} else {
			$this->vars = array();
		}
	}

	public function get_vars_target($file = "", $lang)
	{
		//Arrays zurücksezten - addiert sich sonst auf...
		$this->targetVars = array();
		@$this->content->template = array();
		$file = str_ireplace("de", $lang, $file);

		if (file_exists("../../../" . $file)) {
			require("../../../" . $file);
			$this->targetVars = ($this->content->template);
		} else {
			$this->targetVars = array();
		}
	}

	public function isTranslatebleVar($baseKey = "", $baseValue = "")
	{
		if (empty($this->targetVars[$baseKey]) || $this->targetVars[$baseKey] == $baseValue) {
			return true;
		}
		return false;
	}

	public function isTranslatebleVarK2($baseKey = "", $baseKey2 = "", $baseValue = "")
	{
		if (empty($this->targetVars[$baseKey][$baseKey2]) || $this->targetVars[$baseKey][$baseKey2] == $baseValue) {
			return true;
		}
		return false;
	}

	public function isTranslatebleVarK3($baseKey = "", $baseKey2 = "", $baseKey3 = "", $baseValue = "")
	{
		if (empty($this->targetVars[$baseKey][$baseKey2][$baseKey3]) || $this->targetVars[$baseKey][$baseKey2][$baseKey3] == $baseValue) {
			return true;
		}
		return false;
	}

	public function isTranslatebleVarK4($baseKey = "", $baseKey2 = "", $baseKey3 = "", $baseKey4 = "", $baseValue = "")
	{
		if (empty($this->targetVars[$baseKey][$baseKey2][$baseKey3][$baseKey4]) || $this->targetVars[$baseKey][$baseKey2][$baseKey3][$baseKey4] == $baseValue) {
			return true;
		}
		return false;
	}

	/**
	 * @param mixed $vars
	 */
	public function save_vars($file = "", $vars = array(), $lang = "en")
	{
		//content ini
		$content = "<?php \n";

		//Neuer Dateiname mit dem passenden sprachkürzel
		$file = str_ireplace("_de.", "_" . $lang . ".", $file);

		//variablen durchgehen und text generieren
		foreach ($vars as $k => $v) {
			if (is_string($v)) {
				$content .= '$this->content->template[\'' . $k . '\'] = "' . $this->correctVars($v) . '"; ' . "\n";
			}

			//Alternativ ist array mit plugin
			if (is_array($v)) {
				foreach ($v as $k2 => $v2) {
					if (is_string($v2)) {
						$content .= '$this->content->template[\'' . $k . '\'][\'' . $k2 . '\'] = "' . $this->correctVars($v2) . '"; ' . "\n";
					}

					if (is_array($v2)) {
						foreach ($v2 as $k3 => $v3) {
							if (is_string($v3)) {
								$content .= '$this->content->template[\'' . $k . '\'][\'' . $k2 . '\'][\'' . $k3 . '\'] = "' . $this->correctVars($v3) . '"; ' . "\n";
							}
							if (is_array($v3)) {
								foreach ($v3 as $k4 => $v4) {
									if (is_string($v4)) {
										$content .= '$this->content->template[\'' . $k . '\'][\'' . $k2 . '\'][\'' . $k3 . '\'][\'' . $k4 . '\'] = "' . $this->correctVars($v4) . '"; ' . "\n";
									}
								}
							}
						}
					}
				}
			}
		}
		$content .= "\n ?>";
		file_put_contents("../../../".$file,$content);
		return true;
	}

	public function correctVars($v = "")
	{
		$v = str_ireplace('nobr<', 'nobr:<', $v);
		$v = str_ireplace('"', '\"', $v);
		return $v;
	}

}
