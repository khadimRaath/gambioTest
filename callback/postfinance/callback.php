<?php
/* --------------------------------------------------------------
   callback.php 2014-08-22 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------

/* -----------------------------------------------------------------------------------------
   $Id: callback.php, v.2.1 swisswebXperts GmbH
   2014-01-24 swisswebXperts GmbH

	 Copyright (c) 2009 swisswebXperts GmbH www.swisswebxperts.ch
	 Released under the GNU General Public License (Version 2)
	 [http://www.gnu.org/licenses/gpl-2.0.html]
   ---------------------------------------------------------------------------------------*/
chdir('../../');

define('_VALID_XTC', true);

include ('includes/application_top.php');
include_once('admin/includes/classes/order.php');
include_once('inc/get_usermod.inc.php');
include_once('inc/xtc_date_long.inc.php');
include_once('inc/xtc_address_format.inc.php');
include_once('includes/modules/payment/postfinanceag/postfinance.php');

try {
    $postfinanceModule = new postfinance();
    $postfinanceModule->processCallback();
} catch (Exception $e) {
    file_put_contents(DIR_FS_CATALOG . 'logfiles/postfinance_debug.txt', print_r($_POST, true));
}
