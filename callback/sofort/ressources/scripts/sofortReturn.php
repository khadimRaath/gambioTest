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
 * $Id: sofortReturn.php 5326 2012-09-06 11:49:09Z boehm $
 */

chdir('../../../../');
require_once('includes/application_top.php');
require_once(DIR_FS_CATALOG.'callback/sofort/helperFunctions.php');

switch ($_REQUEST['sofortaction']) {
	case 'success':
		$params = '';
		
		//add sv-bankdata
		if ($_REQUEST['sofortcode'] == 'sofort_sofortvorkasse') {
			$params .= 'holder='.strip_tags($_GET['holder']);
			$params .= '&account_number='.strip_tags($_GET['account_number']);
			$params .= '&iban='.strip_tags($_GET['iban']);
			$params .= '&bank_code='.strip_tags($_GET['bank_code']);
			$params .= '&bic='.strip_tags($_GET['bic']);
			$params .= '&amount='.strip_tags($_GET['amount']);
			$params .= '&reason_1='.strip_tags($_GET['reason_1']);
			$params .= '&reason_2='.strip_tags($_GET['reason_2']);
		}
		
		$url = xtc_href_link(FILENAME_CHECKOUT_PROCESS, $params, 'SSL', true, false);
		break;
	case 'cancel':
		$url = HelperFunctions::getCancelUrl(strip_tags($_REQUEST['sofortcode']));
		break;
	default:
		$url = xtc_href_link(FILENAME_DEFAULT);
		break;
}

xtc_redirect($url);