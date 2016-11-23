<?php
/* --------------------------------------------------------------
	JSAmazonAdvPayCheckout.inc.php 2014-07-09_1604 mabr
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2014 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/

class JSAmazonAdvPayCheckout extends JSAmazonAdvPayCheckout_parent
{
	public function proceed()
	{
		parent::proceed();
		if($this->_amzadvpay_is_enabled() && empty($_SESSION['amazonadvpay_order_ref_id']) !== true)
		{
			$coo_aap = MainFactory::create_object('AmazonAdvancedPayment');
			$t_seller_id = $coo_aap->seller_id;
			$t_amazon_order_reference_id = $_SESSION['amazonadvpay_order_ref_id'];
			$t_text_country_not_allowed = $coo_aap->get_text('country_not_allowed');
			include_once(get_usermod(DIR_FS_CATALOG . 'gm/javascript/AmazonAdvPayCheckout.js'));
		}
	}

	protected function _amzadvpay_is_enabled()
	{
		$t_is_enabled = (defined('MODULE_PAYMENT_AMAZONADVPAY_STATUS') && MODULE_PAYMENT_AMAZONADVPAY_STATUS == 'True');
		$t_is_enabled = $t_is_enabled && strpos(MODULE_PAYMENT_INSTALLED, 'amazonadvpay.php') !== false;
		return $t_is_enabled;
	}
}