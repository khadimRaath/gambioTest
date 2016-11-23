<?php
/* --------------------------------------------------------------
   ipayment_htrigger.php 2013-02-28 mabr
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2013 Gambio GmbH
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

/*
** this is the "hidden trigger" script for ipayment
*/

require_once 'includes/application_top.php';

if(!$_SERVER['REQUEST_METHOD'] == 'POST') {
   die('invalid access');
}

if(preg_match('/\.ipayment\.de$/', gethostbyaddr($_SERVER["REMOTE_ADDR"])) != 1) {
   die('invalid access (RA)');
}


if(!(isset($_POST['trx_paymenttyp']))) {
   die('incomplete data');
}

$module_type = 'ipayment_'.$_POST['trx_paymenttyp'];

try {
	$ipayment = new GMIPayment($module_type);
	$ipayment->processHiddenTrigger($_POST);
}
catch(Exception $e) {
	die('ERROR');
}
