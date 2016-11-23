<?php
/*
 * Shopgate GmbH
 * http://www.shopgate.com
 * Copyright © 2012-2015 Shopgate GmbH
 * 
 * Released under the GNU General Public License (Version 2)
 * [http://www.gnu.org/licenses/gpl-2.0.html]
 */



date_default_timezone_set("Europe/Berlin");

include_once dirname(__FILE__) . '/shopgate_library/shopgate.php';

// Change to a base directory to include all files from
$dir = realpath(dirname(__FILE__) . "/../");

// @chdir hack for warning: "open_basedir restriction in effect"
if (@chdir($dir) === false) {
    chdir($dir . '/');
}

// fix for bot-trap. Sometimes they block requests by mistake.
define("PRES_CLIENT_IP", @$_SERVER["SERVER_ADDR"]);

ini_set('session.use_trans_sid', false);
define("GZIP_COMPRESSION", "false");
error_reporting(E_ALL ^ E_NOTICE);
$_POST = array();

/** application_top.php must be included in this file because of errors on other gambio extensions */
include_once('includes/application_top.php');
include_once dirname(__FILE__) . '/plugin.php';
$ShopgateFramework = new ShopgatePluginGambioGX();
$ShopgateFramework->handleRequest($_REQUEST);
