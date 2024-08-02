<?php
/**
#####################################
# CMS Papoo                         #
# (c) Dr. Carsten Euwens 2008       #
# Authors: Carsten Euwens           #
# http://www.papoo.de               #
# Internet                          #
#####################################
# PHP Version >4.2                  #
#####################################
 */

if (file_exists("./templates_c/redirect.csv")) {
	$links=file_get_contents("./templates_c/redirect.csv");

	$l_ar=explode("\n",$links);

	if (is_array($l_ar)) {
		foreach ($l_ar as $key=>$value) {
			$neu[] = explode(";",$value);
		}
	}
	if (stristr($_SERVER['REQUEST_URI'],"index/")) {
		do_redirect_404();
	}
	if (isset($neu) && is_array($neu)) {
		foreach ($neu as $key=>$value) {
			//$_SERVER['REQUEST_URI']
			if ($value['0'] == $_SERVER['REQUEST_URI'] && !empty($value['0'])) {
				do_redirect($value['1']);
			}
		}
	}
}
/**
 * @param $uri
 */
function do_redirect($uri)
{
	header("HTTP/1.1 301 Moved Permanently");
	header("Location: ".$uri);
	header("Connection: close");
	exit();
}

/**
 * @param string $uri
 */
function do_redirect_404($uri="http://www.papoo.de/cms-forum/sonstiges/404.html")
{
	header("HTTP/1.1 404 Not Found");
	header("Location: ".$uri);
	header("Connection: close");
	exit();
}

/**
 * if parameter template is sent check if it contains "shop"
 * in that case redirect to shop.php if not already running
 * otherwise check if script plugin.php is running
 * if not redirect there
 */
$requestArr = explode("?", $_SERVER['REQUEST_URI']);
if(sizeof($requestArr) >= 2 ) {
	//split all parameters into an array
	$params = explode("&", $requestArr[1]);
	foreach ($params as $param) {
		//does request uri contain parameter template
		if (strcmp(substr($param, 0, 9), "template=") == 0) {
			//does template contain the string shop
			if (stristr(substr($param, 9), "shop") and substr($param, 9, 5) !== 'shop_') {
				//is the script shop.php running
				if (!stristr($_SERVER['REQUEST_URI'], "shop.php")) {
					//redirect to shop.php
					header("Location: ./shop.php?" . $requestArr[1]);
					exit();
				}
			}
			//is the script plugin.php running
			else if (!stristr($_SERVER['REQUEST_URI'], "plugin.php")) {
				//redirect to plugin.php
				header("Location: ./plugin.php?" . $requestArr[1]);
				exit();
			}
		}
	}
}

unset($requestArr, $params);
