<?php 

require_once "../lib/site_conf.php";
require_once "../lib/classes/image_core_class.php";
require_once "../lib/classes/session_class.php";

$bild = new image_core_class();
$spamcode = isset($_SESSION['spamcode']) ? $_SESSION['spamcode'] : ' ';
$bild->image_zugangscode($spamcode);
