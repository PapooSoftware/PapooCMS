<?php

/**
 * Class newsletter_pop3
 */
#[AllowDynamicProperties]
class newsletter_pop3
{
	var $_connection;
	var $_host;
	var $_username;
	var $_password;
	var $_port;
	var $_messageCount;

	/**
	 * newsletter_pop3 constructor.
	 *
	 * @param string $host
	 * @param string $username
	 * @param string $password
	 * @param int $port
	 */
	function __construct($host = '', $username = '', $password = '', $port = 110)
	{
		$this->setHost($host);
		$this->setUsername($username);
		$this->setPassword($password);
		$this->setPort($port);
	}

	function connect()
	{
		$this->_connection = @fsockopen($this->getHost(), $this->getPort());
		$this->getResponse();
	}

	/**
	 * @return bool|string
	 */
	function login()
	{
		if (!$this->isConnected()) {
			return false;
		}

		$result  = '';
		$result .= $this->request('USER '. $this->getUsername());
		$result .= $this->request('PASS '. $this->getPassword());
		return $result;
	}

	/**
	 * @return bool
	 */
	function close()
	{
		if (!$this->isConnected()) {
			return false;
		}

		$this->request('QUIT');

		fclose($this->_connection);
		$this->_connection = null;

		return true;
	}

	function noop()
	{
		$this->request('NOOP');
	}

	/**
	 * @param $messageId
	 */
	function deleteMessage($messageId)
	{
		$this->request('DELE '. $messageId);
	}

	/**
	 * @param $messageId
	 * @param $lines
	 * @return bool|string
	 */
	function top($messageId, $lines)
	{
		return $this->request('TOP ' . $messageId . ' ' . $lines, true);
	}

	/**
	 * @param $messageId
	 * @return bool|string
	 */
	function getMessage($messageId)
	{
		return $this->retrieve($messageId);
	}

	/**
	 * @return mixed
	 */
	function getMessagesCount()
	{
		if ($this->_messageCount === null) {
			$result = $this->request('STAT');
			$result = explode(' ', $result);
			$this->_messageCount = $result[0];
		}

		return $this->_messageCount;
	}

	/**
	 * @return array
	 */
	function getMessages()
	{
		$messages = array();

		for ($messageNumber = 1; $messageNumber <= $this->getMessagesCount(); $messageNumber++) {
			$messages[$messageNumber] = new newsletter_message($this->getMessage($messageNumber));
		}
		return $messages;
	}

	function getHost()
	{
		return $this->_host;
	}

	function getUsername()
	{
		return $this->_username;
	}

	function getPassword()
	{
		return $this->_password;
	}

	function getPort()
	{
		return $this->_port;
	}

	/**
	 * @param $host
	 */
	function setHost($host)
	{
		$this->_host = $host;
	}

	/**
	 * @param $username
	 */
	function setUsername($username)
	{
		$this->_username = $username;
	}

	/**
	 * @param $password
	 */
	function setPassword($password)
	{
		$this->_password = $password;
	}

	/**
	 * @param $port
	 */
	function setPort($port)
	{
		$this->_port = $port;
	}

	/**
	 * @return bool
	 */
	function isConnected()
	{
		if (is_resource($this->_connection)) {
			return true;
		}
		return false;
	}

	/**
	 * @param $request
	 * @return bool|int
	 */
	function sendRequest($request)
	{
		if (!$this->isConnected()) {
			return false;
		}
		return @fputs($this->_connection, $request . "\r\n");
	}

	/**
	 * @param bool $multiline
	 * @return bool|string
	 */
	function getResponse($multiline = false)
	{
		if (!$this->isConnected()) {
			return false;
		}

		$result = @fgets($this->_connection);

		if (!is_string($result)) {
			return false;
		}

		$result = trim($result);
		if (strpos($result, ' ')) {
			list($status, $message) = explode(' ', $result, 2);
		}
		else {
			$status  = $result;
			$message = '';
		}

		if ($status != '+OK') {
			return false;
		}

		if ($multiline) {
			$message = '';
			$line = @fgets($this->_connection);
			while ($line && trim($line) != '.') {
				$message .= $line;
				$line = @fgets($this->_connection);
			};
		}

		return $message;
	}

	/**
	 * @param $messageId
	 * @return bool|string
	 */
	function retrieve($messageId)
	{
		return $this->request('RETR '. $messageId, true);
	}

	/**
	 * @param $request
	 * @param bool $multiline
	 * @return bool|string
	 */
	function request($request, $multiline = false)
	{
		$this->sendRequest($request);
		return $this->getResponse($multiline);
	}
}