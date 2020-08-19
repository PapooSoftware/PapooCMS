<?php
/**
 * ########################################
 * # Papoo CMS API Entry Point            #
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

define("PAPOO_API_CALL", isset($_GET["route"]) ? $_GET["route"] : "");

require_once "all_inc.php";