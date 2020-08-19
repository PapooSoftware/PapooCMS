<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     function.GooglePR.php
 * Type:     function
 * Name:     GooglePR
 * Purpose:  displays the GooglePR of this or a given site
 * -------------------------------------------------------------
 */

function smarty_function_GooglePR($params, &$smarty)
{
	define('GOOGLE_MAGIC', 0xE6359A60);
	if (!empty($params['uri'])) $target = trim($params['uri']);
	else $target = "localhost" ? "" : $_SERVER['SERVER_NAME'];
	if($target != "")
	{
		$server = "www.google.com";
		
		/* Alternate Server:
		$server = "toolbarqueries.google.com";
		$server = "64.233.161.99";
		$server = "64.233.161.104";
		$server = "66.102.7.99";
		$server = "66.102.7.104";
		$server = "216.239.59.99";
		$server = "216.239.59.104";
		$server = "216.239.37.104";
		$server = "216.239.39.99";
		$server = "216.239.39.104";
		$server = "66.102.11.99";
		$server = "66.102.11.104";
		$server = "216.239.57.99";
		$server = "216.239.57.104";
		$server = "66.102.9.99";
		$server = "66.102.9.104";
		$server = "216.239.53.99";
		$server = "216.239.53.104";
		*/
		
		$url = "info:" . "$target";
		$ch = trim(str_replace("-", "", sprintf("6%u\n", GoogleCH(StringOrder($url)))));
		$res = "http://$server/search?client=navclient-auto&ch=$ch&features=Rank&q=$url";
		$data = fopen("$res", r);
		if($data)
		{
			while($line = fgets($data, 1024)) { if(substr($line, 0, 7) == "Rank_1:") $rankline = $line; }
			fclose($data);
			$pagerank = trim(substr($rankline, 9, 2));
			$pagerank == "" ? "0" : $pagerank;
			echo $pagerank;
		}
		else echo "No data received from Google";
	}
}

function ZeroFill($a, $b)
{
	$z = hexdec(80000000);
	if($z&$a)
	{
		$a = ($a >> 1);
		$a &= (~$z);
		$a |= 0x40000000;
		$a = ($a >> ($b - 1));
	}
	else $a = ($a >> $b);
	return $a;
}

function Mix($a, $b, $c)
{
	$a -= $b;
	$a -= $c;
	$a ^= (ZeroFill($c, 13));
	$b -= $c;
	$b -= $a;
	$b ^= ($a << 8);
	$c -= $a;
	$c -= $b;
	$c ^= (ZeroFill($b, 13));
	$a -= $b;
	$a -= $c;
	$a ^= (ZeroFill($c, 12));
	$b -= $c;
	$b -= $a;
	$b ^= ($a << 16);
	$c -= $a;
	$c -= $b;
	$c ^= (ZeroFill($b, 5));
	$a -= $b;
	$a -= $c;
	$a ^= (ZeroFill($c, 3));
	$b -= $c;
	$b -= $a;
	$b ^= ($a << 10);
	$c -= $a;
	$c -= $b;
	$c ^= (ZeroFill($b, 15));
	return array($a, $b, $c);
}

function GoogleCH($url, $length = null, $init = GOOGLE_MAGIC)
{
	if(is_null($length)) $length  = sizeof($url);
	$a = $b = 0x9E3779B9;
	$c = $init;
	$k = 0;
	$len = $length;
	while($len >= 12)
	{
		$a += ($url[$k + 0] + ($url[$k +1 ] << 8) + ($url[$k + 2] << 16) + ($url[$k + 3] << 24));
		$b += ($url[$k + 4] + ($url[$k + 5] << 8) + ($url[$k + 6] << 16) + ($url[$k + 7] << 24));
		$c += ($url[$k + 8] + ($url[$k + 9] << 8) + ($url[$k + 10] << 16) + ($url[$k + 11] << 24));
		$mix = Mix($a, $b, $c);
		$a = $mix[0];
		$b = $mix[1];
		$c = $mix[2];
		$k += 12;
		$len -= 12;
	}
	$c += $length;
	switch($len)
	{
		case 11: $c += ($url[$k + 10] << 24);
		case 10: $c += ($url[$k + 9] << 16);
		case 9: $c += ($url[$k + 8] << 8);
		case 8: $b += ($url[$k + 7] << 24);
		case 7: $b += ($url[$k + 6] << 16);
		case 6: $b += ($url[$k + 5] << 8);
		case 5: $b += ($url[$k + 4]);
		case 4: $a += ($url[$k +3 ] << 24);
		case 3: $a += ($url[$k + 2] << 16);
		case 2: $a += ($url[$k + 1] << 8);
		case 1: $a += ($url[$k + 0]);
	}
	$mix = Mix($a, $b, $c);
	return $mix[2];
}

function StringOrder($string)
{
	for ($i = 0; $i < strlen($string); $i++) { $result[$i] = ord($string[$i]); }
	return $result;
}
?>
