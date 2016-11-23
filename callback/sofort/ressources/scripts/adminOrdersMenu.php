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
 * $Id: adminOrdersMenu.php 5326 2012-09-06 11:49:09Z boehm $
 * 
 * Should be included in admin/orders.php, line 734 (ca.)
 */

require_once(dirname(__FILE__).'/../../helperFunctions.php');

if(is_object($oInfo)) {
	$sofortPaymentMethods = array('sofort_sofortueberweisung', 'sofort_sofortvorkasse', 'sofort_sofortrechnung', 'sofort_lastschrift', 'sofort_sofortlastschrift');
	
	if (in_array($oInfo->payment_method, $sofortPaymentMethods)) {
		$sofortContents = array();
		$sofortInfoText = '';
		if (HelperFunctions::isGambio()) {
			$sofortContents[] = $contents[0];
			
			switch ($_SESSION['language']) {
				case 'german':
					$sofortInfoText = '<p><strong>HINWEIS:</strong> Beachten Sie, dass beim L&ouml;schen und Stornieren kein automatischer Datenabgleich mit der Zahlung bei SOFORT stattfindet. Sie sollten dies im Anbietermen&uuml; von SOFORT durchf&uuml;hren.</p>';
					break;
				case 'english':
				default:
					$sofortInfoText = '<p><strong>NOTICE:</strong> Please note, that in case of cancelation or deletion there will be no syncronisation with the SOFORT servers. You should do this in the SOFORT merchant menu!</p>';
					break;
			}
		}
		
		$languageShort = HelperFunctions::getShortCode($_SESSION['language']);
		
		switch($oInfo->payment_method) {
			
			// $contents[1] contains standard edit-button
			
			case('sofort_sofortvorkasse'):
				$sofortContents[] = array ('align' => 'left', 'text' => '<div align="center"><span><img src="https://images.sofort.com/'.$languageShort.'/sv/prepayment_small.png" alt="vorkasse" /></span>' . $sofortInfoText . '</div>');
				break;
			case('sofort_sofortrechnung'):
				$sofortContents[] = array ('align' => 'left', 'text' => '<div align="center"><span><img src="https://images.sofort.com/'.$languageShort.'/sr/logo_155x50.png" alt="Rechnung by sofort" /></span>' . $sofortInfoText . '</div>');
				break;
			case('sofort_sofortueberweisung'):
				$sofortContents[] = array ('align' => 'left', 'text' => '<div align="center"><span><img src="https://images.sofort.com/'.$languageShort.'/su/logo_155x50.png" alt="sofort&uuml;berweisung" /></span>' . $sofortInfoText . '</div>');
				break;
			case('sofort_lastschrift'):
				$sofortContents[] = array ('align' => 'left', 'text' => '<div align="center"><span><img src="https://images.sofort.com/'.$languageShort.'/ls/logo_155x50.png" alt="Lastschrift by sofort" /></span>' . $sofortInfoText . '</div>');
				break;
			case('sofort_sofortlastschrift'):
				$sofortContents[] = array ('align' => 'left', 'text' => '<div align="center"><span><img src="https://images.sofort.com/'.$languageShort.'/sl/logo_155x50.png" alt="sofortlastschrift" /></span>' . $sofortInfoText . '</div>');
				break;
			default:
			break;
		}
		
		$shopsystem = HelperFunctions::getIniValue('shopsystemVersion');
		
		switch($shopsystem) {
			case 'xtc3_sp2':
				$sofortContents[] = array ('align' => 'center', 'text' => '<a class="button" href="'.xtc_href_link(FILENAME_ORDERS, xtc_get_all_get_params(array ('oID', 'action')).'oID='.$oInfo->orders_id.'&action=edit').'">'.BUTTON_EDIT.'</a>');
				break;
			case 'cseo_2.0':
			case 'cseo_2.1':
				$sofortContents[] = array ('align' => 'center', 'text' => '<br /><a class="button" href="'.xtc_href_link(FILENAME_ORDERS, xtc_get_all_get_params(array ('oID', 'action', 'print_oID')).'oID='.$oInfo->orders_id.'&action=edit').'">'.BUTTON_EDIT.'</a><br /><br />');
				break;
			case 'mod_1.05':
			case 'mod_1.06':
				$sofortContents[] = array ('align' => 'center', 'text' => '<a class="button" href="'.xtc_href_link(FILENAME_ORDERS, xtc_get_all_get_params(array ('oID', 'action')).'oID='.$oInfo->orders_id.'&action=edit').'">'.BUTTON_EDIT.'</a>');
				break;
			case 'gambio_gx1':
			case 'gambio_gx2':
				$sofortContents[] = array ('align' => 'center', 'text' => '<div align="center"><a class="button" href="'.xtc_href_link(FILENAME_ORDERS, xtc_get_all_get_params(array ('oID', 'action')).'oID='.$oInfo->orders_id.'&action=edit').'">'.BUTTON_EDIT.'</a></div>');
			break;
			default:
				//Fallback. Should never happen!
				//$contents[] = array ('align' => 'left', 'text' => '<div align="center"><a class="button" href="'.xtc_href_link(FILENAME_ORDERS, xtc_get_all_get_params(array ('oID', 'action')).'oID='.$oInfo->orders_id.'&action=edit').'">'.BUTTON_EDIT.'</a></div>');
			
		}
		
		if (HelperFunctions::isGambio()) {
			unset($contents[0]); // heading
			unset($contents[1]); // standard edit-button
			$contents = array_merge($sofortContents, $contents);
		} else {
			$contents = $sofortContents;
		}
	}
}
?>