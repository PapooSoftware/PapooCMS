<?php

namespace Papoo\Plugins\DSGVO;

/**
 * ########################################
 * # DSGVO XHR Controller                 #
 * # (c) 2018 Papoo Software & Media GmbH #
 * #          Dr. Carsten Euwens          #
 * # Authors: Christoph Zimmer            #
 * # http://www.papoo.de                  #
 * ########################################
 * # PHP Version >= 5.3                   #
 * ########################################
 * @copyright 2018 Papoo Software & Media GmbH
 * @author Christoph Zimmer <cz@papoo.de>
 * @date 2018-02-01
 */

class XHRController {
	public function __construct() {}

	public function process($request) {
		if (method_exists($this, "{$request}Action")) {
			call_user_func(array($this, "{$request}Action"));
		}
	}

	/**
	 * @param $message
	 */
	private function sendSuccessResponse($message) {
		echo json_encode(array(
			"jsonrpc" => "2.0",
			"result" => "success",
			"code" => 0,
			"message" => $message,
		));
		exit;
	}

	/**
	 * @param $error_code
	 * @param $message
	 */
	private function sendErrorResponse($error_code, $message) {
		echo json_encode(array(
			"jsonrpc" => "2.0",
			"result" => "error",
			"code" => (int)$error_code,
			"message" => $message,
		));
		exit;
	}
}

return new XHRController();