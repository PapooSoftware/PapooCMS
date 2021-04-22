<?php
/**
 *
 * @author Christoph Zimmer
 * @date 30.10.2014
 *
 * @modified by Stephan Bergmann
 * @date 2015-07-17
 *
 */

require_once('tinypng_core.php');

/**
 * Class TinyPngAjax
 */
class TinyPngAjax extends TinyPngCore
{
	/**
	 * TinyPngAjax constructor.
	 */
	public function __construct()
	{
		if(!isset($_GET['path']) || !isset($_GET['image']) || !isset($_GET['apikey'])) {
			exit;
		}

		$tempDir = $_SERVER['DOCUMENT_ROOT'] . $_GET['path'];
		$tempDir = preg_replace("/[\/]{2,}/", "/", $tempDir);

		parent::__construct($tempDir);
		$this->setApiKey($_GET['apikey']);

		$image = $_GET['image'];
		if (!$this->isShrinkable($image)) {
			header('HTTP/1.0 420 TinyPNG-Error');
			echo 'Compression failed ('.$image.'): no image to compress';
			exit;
		}

		$this->shrink($image);
	}

	/**
	 * @param $image
	 */
	private function shrink($image)
	{
		$curlOptions = array(
			CURLOPT_URL => $this->tinyPngApiUrl,
			CURLOPT_USERPWD => "api:" . $this->getApiKey(),
			CURLOPT_POSTFIELDS => file_get_contents($this->imageDir.$image),
			CURLOPT_BINARYTRANSFER => true,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_HEADER => true,
			/* Uncomment below if you have trouble validating our SSL certificate.
			   Download cacert.pem from: http://curl.haxx.se/ca/cacert.pem */
			// CURLOPT_CAINFO => __DIR__ . "/cacert.pem",
			CURLOPT_SSL_VERIFYPEER => true
		);
		$request = curl_init();
		curl_setopt_array($request, $curlOptions);

		$response = curl_exec($request);
		$curlInfo = curl_getinfo($request);

		if ($curlInfo['http_code'] === 201 ) {
			/* Compression was successful, retrieve output from Location header. */
			$headers = substr($response, 0, curl_getinfo($request, CURLINFO_HEADER_SIZE));
			foreach (explode("\r\n", $headers) as $header) {
				if (substr($header, 0, 10) === "Location: ") {
					$request = curl_init();
					curl_setopt_array($request, array(
						CURLOPT_URL => substr($header, 10),
						CURLOPT_RETURNTRANSFER => true,
						/* Uncomment below if you have trouble validating our SSL certificate. */
						// CURLOPT_CAINFO => __DIR__ . "/cacert.pem",
						CURLOPT_SSL_VERIFYPEER => true
					));
					//Sichere das Bild
					copy($this->imageDir.$image, $this->originalDir.$image);
					chmod($this->originalDir.$image, 0777);
					file_put_contents($this->imageDir.$image, curl_exec($request));
					chmod($this->imageDir.$image, 0777);
					echo 'did compress '.$image;
					exit;
				}
			}
		}
		else {
			/* Something went wrong! */
			header('HTTP/1.1 420 TinyPNG-Error');
			echo 'Compression failed ('.$image.'): '.curl_error($request)."\n".$response;
			exit;
		}
	}
}

$TinyPngAjax = new TinyPngAjax();
