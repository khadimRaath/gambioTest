<?php

/* --------------------------------------------------------------
  $Id: Helper.php 0.1 2011-04-01 $

  brickfox Multichannel eCommerce
  http://www.brickfox.de

  Copyright (c) 2011 brickfox by NETFORMIC GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  -------------------------------------------------------------- */

class helper {

	/**
	 * get new brickfox configuration
	 *
	 * @return Brickfox_Lib_BrickfoxConfiguration
	 */
    function getBrickfoxConfiguration() {

	$url = '';
	$port = '';
	$username = '';
	$password = '';
	$excludeCategories = '';
	$excludeProducts = '';
	$status = false;

	if (isset($_POST['configuration']['MODULE_BRICKFOX_URL'])) {
	    $url = $_POST['configuration']['MODULE_BRICKFOX_URL'];
	}

	if (isset($_POST['configuration']['MODULE_BRICKFOX_PORT'])) {
	    $port = (int) $_POST['configuration']['MODULE_BRICKFOX_PORT'];
	}

	if (isset($_POST['configuration']['MODULE_BRICKFOX_USERNAME'])) {
	    $username = $_POST['configuration']['MODULE_BRICKFOX_USERNAME'];
	}

	if (isset($_POST['configuration']['MODULE_BRICKFOX_PASSWORD'])) {
	    $password = $_POST['configuration']['MODULE_BRICKFOX_PASSWORD'];
	}

	if (isset($_POST['configuration']['MODULE_BRICKFOX_EXCLUDE_CATEGORIES'])) {
	    $excludeCategories = $_POST['configuration']['MODULE_BRICKFOX_EXCLUDE_CATEGORIES'];
	}

	if (isset($_POST['configuration']['MODULE_BRICKFOX_EXCLUDE_PRODUCTS'])) {
	    $excludeProducts = $_POST['configuration']['MODULE_BRICKFOX_EXCLUDE_PRODUCTS'];
	}

	$brickfoxConfiguration = new Brickfox_Lib_BrickfoxConfiguration($url, $port, $username, $password, $excludeCategories, $excludeProducts, $status);

	return $brickfoxConfiguration;
    }

}

?>