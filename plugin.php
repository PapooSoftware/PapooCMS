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
$template = isset($_GET['template']) ? $_GET['template'] : "";

if (substr(basename($template), -5) !== ".html") $template = "index.html";

// benötigte Dateien einbauen
require_once "./all_inc_front.php";
