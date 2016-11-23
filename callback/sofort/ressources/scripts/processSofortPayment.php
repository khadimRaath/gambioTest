<?php
/**
 * @version SOFORT Gateway 5.2.0 - $Date: 2012-09-06 13:49:09 +0200 (Thu, 06 Sep 2012) $
 * @author SOFORT AG (integration@sofort.com)
 * @link http://www.sofort.com/
 *
 * Copyright (c) 2012 SOFORT AG
 *
 * Released under the GNU General Public License (Version 2)
 * [http://www.gnu.org/licenses/gpl-2.0.html]
 *
 * $Id: processSofortPayment.php 5326 2012-09-06 11:49:09Z boehm $
 */

chdir('../../../../');

require_once('includes/application_top.php');
require_once(DIR_FS_CATALOG.'callback/sofort/helperFunctions.php');

$language = HelperFunctions::getSofortLanguage($_SESSION['language']);
$coo_lang_file_master->init_from_lang_file('lang/' . $language . '/modules/payment/sofort_general.php');

$errorUrl = xtc_href_link(FILENAME_CHECKOUT_PAYMENT, 'payment_error='.$_SESSION['sofort']['sofort_payment_method'], 'SSL', true, false);

if (!isset($_SESSION['sofort']['sofort_payment_url']) || !$_SESSION['sofort']['sofort_payment_url'] ){
	$sofortPaymentUrl = $errorUrl;
} else {
	$sofortHost = (getenv('sofortApiUrl') != '') ? getenv('sofortApiUrl') : 'https://www.sofort.com';
	$hostToCheck = parse_url($sofortHost, PHP_URL_HOST);
	$paymentHost = parse_url($_SESSION['sofort']['sofort_payment_url'], PHP_URL_HOST);
	
	$sofortPaymentUrl = (strpos($paymentHost, $hostToCheck) === false) ? $errorUrl : $_SESSION['sofort']['sofort_payment_url'];
}

if (isset($_SESSION['sofort']['sofort_payment_url']))	 unset($_SESSION['sofort']['sofort_payment_url']);
if (isset($_SESSION['sofort']['sofort_payment_method'])) unset($_SESSION['sofort']['sofort_payment_method']);

echo '
	<head>
		<meta http-equiv="refresh" content="0; URL='.$sofortPaymentUrl.'/">
		<meta content="text/html; charset='.HelperFunctions::getIniValue('shopEncoding').'" http-equiv="Content-Type">
	</head>
	<body>
		<div style="text-align:center;">
			<div style="height:50px;">&nbsp;</div>
			<div style="height:50px;">
				<img src="'.DIR_WS_CATALOG.'callback/sofort/ressources/images/loader.gif" alt="" />
			</div>
			<div style="height:50px;">
				'.MODULE_PAYMENT_SOFORT_MULTIPAY_FORWARDING.'
			</div>
		</div>
	</body>';