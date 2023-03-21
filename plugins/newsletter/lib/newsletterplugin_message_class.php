<?php

/**
 * Class newsletter_message
 */
#[AllowDynamicProperties]
class newsletter_message
{

	var $_mailRaw;
	var $_headersRaw;
	var $_bodyRaw;
	var $_headers;
	var $_isMultipart;
	var $_contentType;
	var $_contentTypePrimary;
	var $_contentTypeSecondary;
	var $_parts;
	var $_boundary;
	var $_encoding;

	/**
	 * newsletter_message constructor.
	 *
	 * @param $mail
	 */
	function __construct($mail)
	{
		$this->_mailRaw   = $mail;
		$this->getBodyRaw();
		$this->getHeadersRaw();
		$this->decodeBody();
	}

	/**
	 * @return mixed
	 */
	function getBodyRaw()
	{
		if ($this->_bodyRaw === null) {
			$match = array();
			preg_match("/^(.*?)\r?\n\r?\n(.*)/s", $this->_mailRaw, $match);
			$this->_bodyRaw = $match[2];
		}
		return $this->_bodyRaw;
	}

	/**
	 * @return mixed
	 */
	function getHeadersRaw()
	{
		if ($this->_headersRaw === null) {
			$match = array();
			preg_match("/^(.*?)\r?\n\r?\n(.*)/s", $this->_mailRaw, $match);
			$this->_headersRaw = $match[1];
		}
		return $this->_headersRaw;
	}

	/**
	 * @return mixed
	 */
	function getBody()
	{
		return $this->_body;
	}

	/**
	 * @return bool
	 */
	function hasParts()
	{
		if (0 == count((array) $this->_parts)) {
			return false;
		}
		return true;
	}

	/**
	 * @param $partId
	 * @return array
	 */
	function getPart($partId)
	{
		return (array) $this->_parts[$partId];
	}

	/**
	 * @return array
	 */
	function getParts()
	{
		return (array) $this->_parts;
	}

	/**
	 * @return array
	 */
	function getHeaders()
	{
		$headersRaw = $this->getHeadersRaw();
		$headers = preg_replace("/\r?\n/", "\r\n", $headersRaw);
		$headers = preg_replace("/\r\n(\t| )+/", ' ', $headers);
		$headers = explode("\r\n", trim($headers));

		$this->_headers = array();
		foreach ($headers as $headerLine) {
			$match = array();
			preg_match('/(.*?):\s*(.*)\s*/is', $headerLine, $match);

			if (3 == count($match)) {
				$this->_headers[strtolower($match[1])] = $match[2];
			}
		}

		return $this->_headers;
	}

	/**
	 * @param $header
	 * @return mixed
	 */
	function getHeader($header) {
		if ($this->_headers === null) {
			$this->getHeaders();
		}

		return $this->_headers[$header];
	}

	/**
	 * @return bool
	 */
	function isMultipart()
	{
		if ($this->_isMultipart === null) {
			$this->_isMultipart = $this->getContentType('primary') == 'multipart' ? true : false;
		}
		return $this->_isMultipart;

	}

	function decodeBody()
	{
		$bodyRaw    = $this->getBodyRaw();
		$headersRaw = $this->getHeadersRaw();

		switch ($this->getContentType('primary')) {
		case 'multipart':
			$boundary = $this->getBoundary();

			if ('' != $boundary) {
				$bodyRaw = str_replace('--' . $boundary . '--', '', $bodyRaw);
				$parts = explode('--'.$boundary, $bodyRaw);

				for ($partNumber = 1; $partNumber <= count($parts); $partNumber++) {
					$part = trim($parts[$partNumber]);
					if ('' != $part) {
						$this->_parts[$partNumber-1] = new newsletter_message($part);
					}
				}
			}
			break;

		case 'image' :
		case 'audio' :
		case 'video' :
		case 'application' :
		case 'other' :
			//$this->_body = $this->decodeText($bodyRaw, 'base64');
			break;

		case 'text':
		default:
			$this->_body = $this->decodeText($bodyRaw, $this->getEncoding());
			break;
		}
	}

	/**
	 * @param $text
	 * @param string $encoding
	 * @return bool|string|string[]|null
	 */
	function decodeText($text, $encoding = '7bit')
	{
		switch ($encoding) {
		case 'quoted-printable':
			return $this->quotedPrintableDecode($text);
			break;

		case 'base64':
			return base64_decode($text);
			break;

		default:
		case '7bit':
			return $text;
			break;
		}
	}

	/**
	 * @param $text
	 * @return string|string[]|null
	 */
	function quotedPrintableDecode($text)
	{
		$text = preg_replace("/=\r?\n/", '', $text);
		$text = preg_replace('/=([a-f0-9]{2})/ie', "chr(hexdec('\\1'))", $text);

		return $text;
	}

	/**
	 * @param string $mode
	 * @return mixed
	 */
	function getContentType($mode = 'full')
	{
		if ($this->_contentType === null) {
			$contentType = $this->getHeader('content-type');
			$match = array();
			preg_match('/((.*?)\/(.*?));.*/is', $contentType, $match);
			$this->_contentType          = $match[1];
			$this->_contentTypePrimary   = $match[2];
			$this->_contentTypeSecondary = $match[3];
		}

		switch ($mode) {
		case 'primary' :
			return $this->_contentTypePrimary;
			break;

			// FIXME: Hier war vorher primary
		case 'secondary' :
			return $this->_contentTypeSecondary;
			break;

		case 'full' :
		default :
			return $this->_contentType;
			break;
		}
	}

	/**
	 * @return mixed
	 */
	function getBoundary()
	{
		if ($this->_boundary === null) {
			$contentType = $this->getHeader('content-type');
			$match = array();
			preg_match('/.*?;\s*boundary="(.*)"/is', $contentType, $match);
			$this->_boundary = $match[1];
		}

		return $this->_boundary;
	}

	/**
	 * @return mixed|string
	 */
	function getEncoding()
	{
		if ($this->_encoding === null) {
			$encoding = $this->getHeader('content-transfer-encoding');
			if ('' == $encoding) {
				$encoding = '7bit';
			}
			$this->_encoding = $encoding;
		}

		return $this->_encoding;
	}
}