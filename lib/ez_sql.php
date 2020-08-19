<?php

if (strstr( $_SERVER['PHP_SELF'],'ez_sql.php')) die('You are not allowed to see this page directly');

include_once "ez_sql_core.php";
include_once "ez_sql_mysqli.php";

$db = new ezSQL_mysqli($db_user, $db_pw, $db_name, $db_host, 'utf-8');