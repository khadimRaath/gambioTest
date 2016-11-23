<?php
/* --------------------------------------------------------------
	PayPalHeaderExtender.inc.php 2016-02-26
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2016 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/

/**
 * This HeaderExtender inserts required Javascript and styles for Payone.
 */
class PayoneHeaderExtender extends PayoneHeaderExtender_parent
{
	public function proceed()
	{
		parent::proceed();
		$isCheckoutPage = strpos($_SERVER['SCRIPT_NAME'], 'shopping_cart.php') !== false;
		$isCheckoutPage = $isCheckoutPage || strpos($_SERVER['SCRIPT_NAME'], 'checkout_') !== false;
		if($isCheckoutPage && $this->_payone_is_enabled())
		{
			$output_array = array();

			if(gm_get_env_info('TEMPLATE_VERSION') < 3)
			{
				// EyeCandy includes Payone styles in its stylesheet.css
			}
			else
			{
				$output_array['payonejs'] = '<script src="' . xtc_href_link('ext/payone/js/client_api.js', '', 'SSL').'"></script>';
				$output_array['payonestyles'] = '<link href="'.xtc_href_link('templates/'.CURRENT_TEMPLATE.'/assets/styles/payone.css', '', 'SSL').'" rel="stylesheet" type="text/css">';
			}

			if(!is_array($this->v_output_buffer))
			{
				$this->v_output_buffer = array();
			}
			$this->v_output_buffer = array_merge($this->v_output_buffer, $output_array);
		}
	}

	protected function _payone_is_enabled()
	{
		$payoneInstalled = strpos(MODULE_PAYMENT_INSTALLED, 'payone') !== false;
		return $payoneInstalled;
	}

}
