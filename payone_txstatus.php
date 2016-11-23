<?php
/* --------------------------------------------------------------
	payone_txstatus.php 2014-07-10 mabr
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2014 Gambio GmbH
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

require 'includes/application_top.php';
header('Content-Type: text/plain');

$logger = new FileLog('payment-payone-txlog', true);
$logger->write(str_repeat('=', 100)."\n");
$logger->write("-- ".date('c')."\n");
$logger->write(str_repeat('-', 100)."\n");

if($_SERVER['REQUEST_METHOD'] != 'POST') {
	$logger->write("not a POST request!\n");
	echo "NACK\n";
	exit;
}

$logger->write("received status from ".$_SERVER['REMOTE_ADDR']."\n");
$logger->write(print_r($_POST, true));

$payone = new GMPayOne();
$payone->saveTransactionStatus($_POST);

echo "TSOK\n";

xtc_db_close();