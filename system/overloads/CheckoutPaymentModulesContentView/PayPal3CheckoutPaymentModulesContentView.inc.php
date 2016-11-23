<?php
/* --------------------------------------------------------------
	PayPal3CheckoutPaymentModulesContentView.inc.php 2015-03-19
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2015 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/

/**
 * This class modifies the list of payment options displayed on checkout_payment for PayPal Plus and ECS guest flows.
 * For ECS guests, only PayPal will be available. For Plus, payments handled by the PayPal Plus paywall will be
 * filtered out.
 */
class PayPal3CheckoutPaymentModulesContentView extends PayPal3CheckoutPaymentModulesContentView_parent
{
	/**
	 * Makes paypal3 the only choice if account was created by ECS
	 * @param array $methodsArray
	 */
	public function set_methods_array(array $methodsArray)
	{
		if(is_callable(array('parent', 'set_methods_array')))
		{
			parent::set_methods_array($methodsArray);
			$methodsArray = $this->methods_array;
		}
		$this->methods_array = $methodsArray;
		$paypalConfiguration = MainFactory::create('PayPalConfigurationStorage');

		if(
			$paypalConfiguration->get('allow_selfpickup') == false &&
			isset($_SESSION['shipping']) &&
			is_array($_SESSION['shipping']) &&
			$_SESSION['shipping']['id'] == 'selfpickup_selfpickup'
		  )
		{
			return;
		}

		if($_SESSION['payment'] == 'paypal3' && isset($_SESSION['paypal_payment']) && (isset($_SESSION['paypal_payment']['is_guest']) || $_SESSION['paypal_payment']['type'] == 'ecs'))
		{
			// ECS may only pay by PayPal
			$pp3MethodsArray = array();
			foreach($methodsArray as $method)
			{
				if($method['id'] == 'paypal3')
				{
					$pp3MethodsArray[] = $method;
				}
			}
			$this->methods_array = $pp3MethodsArray;
		}
		else
		{
			$mobilePaymentModules = gm_get_conf('mobile_payment_modules');
			$activeInMobile = is_string($mobilePaymentModules) ? in_array('paypal3', explode('|', $mobilePaymentModules)) : false;
			$mobileAllowed = MOBILE_ACTIVE === 'true' ? $activeInMobile : true;
			if($mobileAllowed && isset($_SESSION['paypal_payment']) && $_SESSION['paypal_payment']['type'] == 'plus')
			{
				// don't show payment types handled by PayPalPlus (via 3rd-party interface)
				$handledByPaymentWall = $this->_getPaymentModulesHandledByPaymentWall($paypalConfiguration);
				$pp3MethodsArray = array();
				foreach($methodsArray as $method)
				{
					if(!in_array($method['id'], $handledByPaymentWall))
					{
						$pp3MethodsArray[] = $method;
					}
				}
				$this->methods_array = $pp3MethodsArray;
			}
		}
	}

	protected function _getPaymentModulesHandledByPaymentWall(PayPalConfigurationStorage $paypalConfiguration)
	{
		$paymentCodes = array('cod', 'moneyorder', 'invoice', 'cash', 'eustandardtransfer');
		$paywallPaymentCodes = array();
		foreach($paymentCodes as $paymentId)
		{
			if($paypalConfiguration->get('thirdparty_payments/'.$paymentId.'/mode') == 'paywall')
			{
				$paywallPaymentCodes[] = $paymentId;
			}
		}

		return $paywallPaymentCodes;
	}
}