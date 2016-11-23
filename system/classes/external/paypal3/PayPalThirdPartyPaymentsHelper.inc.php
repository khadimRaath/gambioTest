<?php
/* --------------------------------------------------------------
	PayPalThirdPartyPaymentsHelper.inc.php 2015-09-21
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2015 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/

/**
 * Utility class for the generation of the PayPal Plus third party payments configuration during checkout
 */
class PayPalThirdPartyPaymentsHelper
{
	/**
	 * returns JSON-encoded configuration of third party payments.
	 * @return string JSON block containing third party payments configuration
	 */
	public function getThirdPartyPaymentsBlock()
	{
		$paypalConfiguration = MainFactory::create('PayPalConfigurationStorage');
		if(method_exists('LanguageTextManager', 'init_from_lang_file'))
		{
			$coo_lang_file_master = MainFactory::create_object('LanguageTextManager', array($_SESSION['languages_id']), true);
		}
		else
		{
			$coo_lang_file_master = false;
		}
		$shopBaseURL = GM_HTTP_SERVER.DIR_WS_CATALOG;
		$installedPaymentModules = explode(';', MODULE_PAYMENT_INSTALLED);
		$GLOBALS['order'] = new order();
		$unallowedPaymentModules = $this->getUnallowedPayments();
		$thirdPartyPayments = array();
		foreach($installedPaymentModules as $moduleClassFile)
		{
			$className = basename($moduleClassFile, '.php');
			if(in_array($className, $unallowedPaymentModules))
			{
				continue;
			}

			$allowed_zones = array();
			if(constant(MODULE_PAYMENT_ . strtoupper($className) . _ALLOWED) != '')
			{
				$allowed_zones = explode(',', constant(MODULE_PAYMENT_ . strtoupper($className) . _ALLOWED));
			}
			if(!empty($allowed_zones) && in_array($_SESSION['delivery_zone'], $allowed_zones) === false)
			{
				continue;
			}

			$moduleClassFilePath = DIR_FS_CATALOG.'includes/modules/payment/'.$moduleClassFile;
			if(!file_exists($moduleClassFilePath))
			{
				continue;
			}

			if($paypalConfiguration->get('thirdparty_payments/'.$className.'/mode') == 'paywall')
			{
				$moduleLangFilePath = 'lang/' . $_SESSION['language'] . '/modules/payment/' . $moduleClassFile;
				if($coo_lang_file_master !== false)
				{
					$coo_lang_file_master->init_from_lang_file($moduleLangFilePath);
				}
				else
				{
					if(file_exists(DIR_FS_CATALOG.$moduleLangFilePath))
					{
						require_once DIR_FS_CATALOG.$moduleLangFilePath;
					}
				}
				require_once $moduleClassFilePath;
				$paymentModule = new $className();
				if($paymentModule->enabled !== true)
				{
					continue;
				}
				$paymentSelection = $paymentModule->selection();

				$redirectUrl = str_replace('&amp;', '&', xtc_href_link('shop.php', 'do=PayPal/SetPayment&payment='.$paymentSelection['id'], 'SSL'));
				$thirdPartyPayment = array(
						'redirectUrl' => $redirectUrl,
						'methodName' => $paymentSelection['module'],
						// 'imageUrl' => '',
						'description' => $paymentSelection['description'],
					);
				if(!empty($paymentSelection['module_cost']))
				{
					$thirdPartyPayment['description'] .= ' ('.$paymentSelection['module_cost'].')';
				}
				$thirdPartyPayments[] = $thirdPartyPayment;
			}
		}

		require_once 'gm/classes/JSON.php';
		$json = MainFactory::create('Services_JSON');
		$thirdPartyPaymentsBlock = $json->encodeUnsafe($thirdPartyPayments);

		$logger = MainFactory::create('PayPalLogger');
		$logger->debug_notice("ThirdPartyPayments generated:\n". $thirdPartyPaymentsBlock);

		return $thirdPartyPaymentsBlock;
	}

	protected function getUnallowedPayments()
	{
		// load unallowed modules into array
		$unallowed_modules = explode(',', $_SESSION['customers_status']['customers_status_payment_unallowed'] . ',' . $GLOBALS['order']->customer['payment_unallowed']);

		// add unallowed modules/Download
		if($GLOBALS['order']->content_type == 'virtual' || $GLOBALS['order']->content_type == 'virtual_weight' || $GLOBALS['order']->content_type == 'mixed')
		{
			$unallowed_modules = array_merge($unallowed_modules, explode(',', DOWNLOAD_UNALLOWED_PAYMENT));
		}

		// disable payment method 'cod' for gift vouchers
		if($_SESSION['cart']->count_contents_non_virtual() == 0 && array_search('cod', $unallowed_modules) === false)
		{
			$unallowed_modules[] = 'cod';
		}
		return $unallowed_modules;
	}
}
