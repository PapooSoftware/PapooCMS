<?php

/**
 * ########################################
 * # Papoo CMS API                        #
 * # (c) 2017 Papoo Software & Media GmbH #
 * #          Dr. Carsten Euwens          #
 * # Authors: Christoph Zimmer            #
 * # http://www.papoo.de                  #
 * ########################################
 * # PHP Version >= 5.4                   #
 * ########################################
 * @copyright 2017 Papoo Software & Media GmbH
 * @author Christoph Zimmer <cz@papoo.de>
 * @date 2017-08-18
 */

/**
 * Manage menus, articles etc. via API calls
 */
#[AllowDynamicProperties]
class PapooAPI
{
	/** @var array  */
	private static $ACTIONS = [
		["route" => "~^menu/(?!0)(\d+)/publish$~", "method" => "POST", "callback" => "publishMenu"],
		["route" => "~^article/(?!0)(\d+)/publish$~", "method" => "POST", "callback" => "publishArticle"],
		["route" => "~^menu/(?!0)(\d+)/read-permission-all$~", "method" => "POST", "callback" => "readPermissionAllMenu"],
		["route" => "~^article/(?!0)(\d+)/read-permission-all$~", "method" => "POST", "callback" => "readPermissionAllArticle"],
		["route" => "~^order/(?!0)(\d+)/paid$~", "method" => "POST", "callback" => "shopInvoicePaid"],
	];

	/** @var null  */
	private $_response;
	/** @var bool  */
	private $_success;

	/**
	 * PapooAPI constructor.
	 */
	public function __construct() {
		$this->_response = null;
		$this->_success = false;
	}

	/**
	 * @param $response
	 */
	private function setResponse($response) {
		$this->_response = json_encode($response);
	}

	/**
	 * @param $user_id
	 * @param $menu_id
	 * @return bool
	 */
	private function userHasWritePermissionForMenu($user_id, $menu_id) {
		global $cms, $db;

		$user_id = (int)$user_id;
		$menu_id = (int)$menu_id;

		return (int)$db->get_var("SELECT COUNT(*) ".
				"FROM {$cms->tbname["papoo_lookup_ug"]} ug ".
				"JOIN {$cms->tbname["papoo_lookup_men_ext"]} mg ON ug.gruppenid = mg.gruppenid ".
				"WHERE ug.userid = $user_id AND mg.menuid = $menu_id") > 0;
	}

	/**
	 * @param $user_id
	 * @param $article_id
	 * @return bool
	 */
	private function userHasWritePermissionForArticle($user_id, $article_id) {
		global $cms, $db;

		$user_id = (int)$user_id;
		$article_id = (int)$article_id;

		return (int)$db->get_var("SELECT COUNT(*) ".
				"FROM {$cms->tbname["papoo_lookup_ug"]} ug ".
				"JOIN {$cms->tbname["papoo_lookup_write_article"]} ag ON ug.gruppenid = ag.gruppeid_wid_id ".
				"WHERE ug.userid = $user_id AND ag.article_wid_id = $article_id") > 0;
	}

	/**
	 * @param $user_id
	 * @return bool
	 */
	private function userHasAccessToBilling($user_id) {
		$user_id = (int)$user_id;

		return (int)$GLOBALS["db"]->get_var("SELECT COUNT(_menu.menuid) ".
				"FROM {$GLOBALS["cms"]->tbname["papoo_user"]} _user ".
				"JOIN {$GLOBALS["cms"]->tbname["papoo_lookup_ug"]} _user_group ON _user_group.userid = _user.userid ".
				"JOIN {$GLOBALS["cms"]->tbname["papoo_lookup_men_int"]} _menu_group ON _menu_group.gruppenid = _user_group.gruppenid ".
				"JOIN {$GLOBALS["cms"]->tbname["papoo_menuint"]} _menu ON _menu.menuid = _menu_group.menuid ".
				"WHERE _user.userid = $user_id AND _menu.menulink LIKE 'plugin:papoo_shop/templates/papoo_shop_order.html' ".
				"GROUP BY _menu.menuid") > 0;
	}

	/**
	 * @param $menu_id
	 */
	public function publishMenu($menu_id) {
		global $cms, $db, $user;

		$menu_id = (int)$menu_id;
		$userId = (int)$user->userid;
		$langId = isset($_SESSION["langdata_front"]["lang_id"]) ? (int)$_SESSION["langdata_front"]["lang_id"] : 1;

		$userHasWritingPermission = $this->userHasWritePermissionForMenu($userId, $menu_id);

		$db->csrfok = true;
		if ($userHasWritingPermission) {
			$db->query("UPDATE {$cms->tbname["papoo_menu_language"]} ".
				"SET publish_yn_lang_men = IF(publish_yn_lang_men = 1, 0, 1) ".
				"WHERE menuid_id = $menu_id AND lang_id = $langId");
		}

		$published = (int)$db->get_var("SELECT publish_yn_lang_men ".
				"FROM {$cms->tbname["papoo_menu_language"]} ".
				"WHERE menuid_id = $menu_id AND lang_id = $langId") == 1;
		$db->csrfok = false;

		$this->setResponse(["userId" => $userId, "permission" => $userHasWritingPermission, "menuId" => $menu_id, "newState" => $published]);
	}

	/**
	 * @param $article_id
	 */
	public function publishArticle($article_id) {
		global $cms, $db, $user;

		$article_id = (int)$article_id;
		$userId = (int)$user->userid;
		$langId = isset($_SESSION["langdata_front"]["lang_id"]) ? (int)$_SESSION["langdata_front"]["lang_id"] : 1;

		$userHasWritingPermission = $this->userHasWritePermissionForArticle($userId, $article_id);

		$db->csrfok = true;
		if ($userHasWritingPermission) {
			$db->query("UPDATE {$cms->tbname["papoo_language_article"]} ".
				"SET publish_yn_lang = IF(publish_yn_lang = 1, 0, 1) ".
				"WHERE lan_repore_id = $article_id AND lang_id = $langId");
		}

		$published = (int)$db->get_var("SELECT publish_yn_lang ".
				"FROM {$cms->tbname["papoo_language_article"]} ".
				"WHERE lan_repore_id = $article_id AND lang_id = $langId") == 1;

		$db->csrfok = false;
		$this->setResponse(["userId" => $userId, "permission" => $userHasWritingPermission, "articleId" => $article_id, "newState" => $published]);
	}

	/**
	 * @param $menu_id
	 */
	public function readPermissionAllMenu($menu_id) {
		global $cms, $db, $user;

		$menu_id = (int)$menu_id;
		$userId = (int)$user->userid;

		$db->csrfok = true;
		$userHasWritingPermission = $this->userHasWritePermissionForMenu($userId, $menu_id);

		if ($userHasWritingPermission) {
			$permissionGranted = (int)$db->get_var("SELECT COUNT(*) ".
					"FROM {$cms->tbname["papoo_lookup_me_all_ext"]} ".
					"WHERE menuid_id = $menu_id AND gruppeid_id = 10") > 0;

			if ($permissionGranted) {
				$db->query("DELETE FROM {$cms->tbname["papoo_lookup_me_all_ext"]} ".
					"WHERE menuid_id = $menu_id AND gruppeid_id = 10");
			}
			else {
				$db->query("INSERT INTO {$cms->tbname["papoo_lookup_me_all_ext"]} ".
					"SET menuid_id = $menu_id, gruppeid_id = 10");
			}
		}

		$permissionGranted = (int)$db->get_var("SELECT COUNT(*) ".
				"FROM {$cms->tbname["papoo_lookup_me_all_ext"]} ".
				"WHERE menuid_id = $menu_id AND gruppeid_id = 10") > 0;
		$db->csrfok = false;

		$this->setResponse(["userId" => $userId, "permission" => $userHasWritingPermission, "menuId" => $menu_id, "newState" => $permissionGranted]);
	}

	/**
	 * @param $article_id
	 */
	public function readPermissionAllArticle($article_id) {
		global $cms, $db, $user;

		$article_id = (int)$article_id;
		$userId = (int)$user->userid;

		$userHasWritingPermission = $this->userHasWritePermissionForArticle($userId, $article_id);

		if ($userHasWritingPermission) {
			$permissionGranted = (int)$db->get_var("SELECT COUNT(*) ".
					"FROM {$cms->tbname["papoo_lookup_article"]} ".
					"WHERE article_id = $article_id AND gruppeid_id = 10") > 0;

			// Temporarily disable CSRF protection
			$oldCsrfState = $db->csrfok;
			$db->csrfok = true;

			if ($permissionGranted) {
				$db->query("DELETE FROM {$cms->tbname["papoo_lookup_article"]} ".
					"WHERE article_id = $article_id AND gruppeid_id = 10");
			}
			else {
				$db->query("INSERT INTO {$cms->tbname["papoo_lookup_article"]} ".
					"SET article_id = $article_id, gruppeid_id = 10");
			}

			// Revert CSRF protection to old state
			$db->csrfok = $oldCsrfState;
		}

		$permissionGranted = (int)$db->get_var("SELECT COUNT(*) ".
				"FROM {$cms->tbname["papoo_lookup_article"]} ".
				"WHERE article_id = $article_id AND gruppeid_id = 10") > 0;

		$this->setResponse(["userId" => $userId, "permission" => $userHasWritingPermission, "articleId" => $article_id, "newState" => $permissionGranted]);
	}

	/**
	 * @param $order_number
	 */
	public function shopInvoicePaid($order_number) {
		/** @var ezSQL_mysqli $db */
		global $db;
		/** @var checked_class $checked */
		global $checked;

		if (class_exists('shop_class_fakturierung') == false) {
			header('HTTP/1.1 500 Internal Server Error');
			$this->setResponse(['error' => 'Shop plugin not installed']);
			return;
		}

		$order_number = (int)$order_number;
		$userId = (int)$GLOBALS["user"]->userid;
		$orderId = (int)$db->get_var(
			"SELECT order_id ".
			"FROM {$GLOBALS['cms']->tbname['plugin_shop_order']} ".
			"WHERE order_order_number = $order_number"
		) ?: null;
		$extUserId = (int)$db->get_var(
			"SELECT kunden_order_user_id ".
			"FROM {$GLOBALS['cms']->tbname['plugin_shop_order_lookup_kunde']} ".
			"WHERE kunden_order_id = $orderId"
		) ?: null;

		if ($orderId == null || $extUserId == null) {
			if (class_exists('shop_class_fakturierung') == false) {
				header('HTTP/1.1 500 Internal Server Error');
				$this->setResponse(['error' => 'Order or customer could not be determined']);
				return;
			}
		}

		$userHasAccessToBilling = $this->userHasAccessToBilling($userId);

		$db->csrfok = true;
		if ($userHasAccessToBilling) {
			(int)$GLOBALS["db"]->query(
				"UPDATE {$GLOBALS["cms"]->tbname["plugin_shop_order"]} ".
				"SET order_is_payd = IF (order_is_payd = 1, 0, 1) ".
				"WHERE order_order_number = $order_number"
			);
		}

		$newState = (bool)$GLOBALS["db"]->get_var(
			"SELECT order_is_payd ".
			"FROM {$GLOBALS["cms"]->tbname["plugin_shop_order"]} ".
			"WHERE order_order_number = $order_number ".
			"LIMIT 1"
		);

		if ($newState && $userHasAccessToBilling) {
			$products = $db->get_col(
				"SELECT produkte_order_produkt_id ".
				"FROM {$GLOBALS['cms']->tbname['plugin_shop_order_lookup_produkte']} ".
				"WHERE produkte_order_id = $orderId"
			);

			$checked->extuser_id = $extUserId;
			$fakturierung = new shop_class_fakturierung();

			foreach ($products as $productId) {
				$fakturierung->insert_lookup_download($orderId, $productId);
				$fakturierung->insert_lookup_gruppenrechte($productId, $orderId);
			}
		}

		$this->setResponse([
			"userId" => $userId,
			"permission" => $userHasAccessToBilling,
			"orderNumber" => $order_number,
			"newState" => $newState,
		]);
	}

	/**
	 * @return boolean|void
	 */
	public function processRequest() {
		if (isset($_SERVER["HTTP_X_REQUESTED_WITH"]) == false || $_SERVER["HTTP_X_REQUESTED_WITH"] !== "XMLHttpRequest") {
			return false;
		}

		foreach (self::$ACTIONS as $action) {
			if (preg_match($action["route"], PAPOO_API_CALL, $match) == 1 &&
				$_SERVER["REQUEST_METHOD"] === $action["method"] &&
				method_exists($this, $action["callback"])
			) {
				array_shift($match);
				call_user_func_array([$this, $action["callback"]], $match);
				break;
			}
		}
	}

	/**
	 *
	 */
	public function sendResponse() {
		header("Content-Type: application/json");
		header("Content-Length: ".strlen($this->_response));
		echo $this->_response;
		exit;
	}
}
