<?php
/*
#####################################
#                                   #
# CMS Papoo                         #
# (c) Carsten Euwens                #
# http://www.papoo.de               #
#                                   #
#####################################
#                                   #
# Plugin:  sprechomat               #
# Autor:   Stephan Bergmann         #
#          aka b.legt               #
# http://www.sprechomat.de          #
#                                   #
#####################################
*/

// Pfad zum Template
$template = $_GET['template'];
if (!$template) $template = "plugins.html";
// benötigte Dateien einbauen
require_once "./all_inc.php";
