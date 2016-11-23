<?php
/* --------------------------------------------------------------
   iloxx_track.php 2014-11-25 gambio
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2012 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------


   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(ot_cod_fee.php,v 1.02 2003/02/24); www.oscommerce.com
   (C) 2001 - 2003 TheMedia, Dipl.-Ing Thomas Plänkers ; http://www.themedia.at & http://www.oscommerce.at
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: ot_cod_fee.php 1003 2005-07-10 18:58:52Z mz $)

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

require_once 'includes/application_top.php';
header('Content-Type: text/plain');

$iloxx = new GMIloxx();
$iloxx->log('Track: '. $_SERVER['REQUEST_URI']);

$key_valid = isset($_GET['key']) && $iloxx->verifyTrackingKey($_GET['key']);
$order_id = isset($_GET['order_id']) && is_numeric($_GET['order_id']) ? $_GET['order_id'] : false;
$tracking_data = array(
	'iloxxtrackid' => isset($_GET['iloxxtrackid']) ? $_GET['iloxxtrackid'] : false,
	'iloxxstatusid' => isset($_GET['iloxxstatusid']) ? $_GET['iloxxstatusid'] : false,
	'iloxxstatusdate' => isset($_GET['iloxxstatusdate']) ? $_GET['iloxxstatusdate'] : false,
	'iloxxorderprice' => isset($_GET['iloxxorderprice']) ? $_GET['iloxxorderprice'] : false,
);

if($key_valid && $order_id !== false) {
	$iloxx->recordTrackingEvent($order_id, $tracking_data);
	echo "Tracking event received - OK.";
}
