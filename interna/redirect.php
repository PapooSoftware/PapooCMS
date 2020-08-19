<?php

if (file_exists('redirect.txt')) {
	$redirects = file('redirect.txt');
	foreach($redirects as $r){
		$r = explode(';',$r);
		if($_SERVER['QUERY_STRING'] == $r[0] && strstr($_SERVER['SCRIPT_NAME'],'interna')){
			header('HTTP/1.1 308 Permanent Redirect');
			header('Location: http://'.$_SERVER['HTTP_HOST'].$_SERVER['SCRIPT_NAME'].'?'.$r[1]);
		}
		else {
			continue;
		}
	}
}
