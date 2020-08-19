<?php

require_once __DIR__.'/vendor/autoload.php';
require_once __DIR__.'/../../../lib/site_conf.php';
define("SHARIFF_CONFIG_FILE",  PAPOO_ABS_PFAD . "/templates_c/shariff.json");

use Heise\Shariff\Backend;
use Zend\Config\Reader\Json;

class Application
{
    public static function run()
    {
        header('Content-type: application/json');
        header('Access-Control-Allow-Origin: *');

        if (!isset($_GET["url"])) {
            echo json_encode(null);
            return;
        }

        $reader = new Json();

        $shariff = new Backend($reader->fromFile(SHARIFF_CONFIG_FILE));
        echo json_encode($shariff->get($_GET["url"]));
    }
}

Application::run();
