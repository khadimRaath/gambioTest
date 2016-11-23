<?php
/* --------------------------------------------------------------
   trusted_shops_cron.php 2014-04-02 gambio
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
$token = LogControl::get_secure_token();

if(!(isset($_GET['key']) && $_GET['key'] == $token)) {
	die('unauthorized.');
}

$service = new GMTSService();
$service->log('cronjob started.');

// check certificates
$service->log('cronjob checking all certificates (TS IDs).');
$service->checkAllCertificates(true);

// check applications for buyer protection
$service->log('cronjob updating status of applications for buyer protection');
$app_query = xtc_db_query("SELECT application_number FROM ts_protection WHERE result IS NULL OR result <= 0");
while($app_row = xtc_db_fetch_array($app_query)) {
	echo "updating request state for ". $app_row['application_number'] ."\n";
	$service->getRequestState($app_row['application_number']);
}

$service->log('cronjob finished.');
echo "Trusted Shops cronjob finished.\n";
