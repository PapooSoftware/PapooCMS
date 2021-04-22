<?php
/**
 * Created by PhpStorm.
 * User: andreas
 * Date: 19.08.15
 * Time: 11:50
 */

#require_once('Exception.php');

/**
 * Class KrakenError
 * @abstract Eine Custom-Exception die geworfen wird falls beim Kraken-Upload keine Credentials gefunden werden, oder ein
 * Kraken-Eigener Fehler auftritt.*
 */
class KrakenError extends Exception
{
	/**
	 * KrakenError constructor.
	 *
	 * @param $message
	 */
	public function __construct($message)
	{
		parent::__construct($message, 0, NULL);
	}
}