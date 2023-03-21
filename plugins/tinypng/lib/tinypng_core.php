<?php

/**
 * Class TinyPngCore
 */
#[AllowDynamicProperties]
class TinyPngCore
{
	// all file-extensions that TinyPNG can shrink
	private $shrinkableFileExtensions = array('PNG', 'JPG', 'JPEG');

	// TinyPNG api-key as received from https://tinypng.com/developers
	private $apiKey;

	// tinPNG api url as documented at https://api.tinypng.com/developers
	protected $tinyPngApiUrl = 'https://api.tinify.com/shrink';

	// image directories
	protected $imageDir;
	protected $originalDir;
	protected $compressDir;

	/**
	 * TinyPngCore constructor.
	 *
	 * @param $imageDir
	 */
	public function __construct($imageDir)
	{
		$this->imageDir = $imageDir;
		$this->originalDir = $this->imageDir."original/";
		$this->compressDir = $this->imageDir."compressed/";
	}

	/**
	 * @param string $apiKey
	 */
	public function setApiKey($apiKey = '')
	{
		$this->apiKey = $apiKey;
	}

	/**
	 * @return mixed
	 */
	public function getApiKey()
	{
		return $this->apiKey;
	}

	/**
	 * @return bool
	 */
	protected function checkDirectories()
	{
		if( (int) substr(sprintf("%o",fileperms($this->imageDir)), -1) != 7) {
			return false;
		}

		//Erstelle backup Verzeichnis, falls es nicht existiert.
		if(!is_dir($this->originalDir)) {
			mkdir($this->originalDir);
			chmod($this->originalDir, 0777);
		}
		//Erstelle compressed Verzeichnis, falls es nicht existiert.
		if(!is_dir($this->compressDir)) {
			mkdir($this->compressDir);
			chmod($this->compressDir, 0777);
		}
		return true;
	}

	/**
	 * @param $directory
	 * @return array
	 */
	protected function getSchrinkableFiles($directory)
	{
		$tempReturn = array();
		if(($files = scandir($directory)) !== false) {
			foreach($files as $file) {
				if($this->isShrinkable($file)) {
					$tempReturn[] = $file;
				}
			}
		}
		return $tempReturn;
	}

	/**
	 * @param $filename
	 * @return bool
	 */
	protected function isShrinkable($filename)
	{
		$tempExtension = strtoupper(pathinfo($filename, PATHINFO_EXTENSION));

		return in_array($tempExtension, $this->shrinkableFileExtensions);
	}
}
