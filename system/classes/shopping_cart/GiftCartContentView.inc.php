<?php
/* --------------------------------------------------------------
   GiftCartContentView.inc.php 2015-10-15
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------


   based on: 
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(best_sellers.php,v 1.20 2003/02/10); www.oscommerce.com 
   (c) 2003	 nextcommerce (best_sellers.php,v 1.10 2003/08/17); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: best_sellers.php 1292 2005-10-07 16:10:55Z mz $)

   Released under the GNU General Public License 
   -----------------------------------------------------------------------------------------
   Third Party contributions:
   Enable_Disable_Categories 1.3        	Autor: Mikel Williams | mikel@ladykatcostumes.com

   Released under the GNU General Public License 
   ---------------------------------------------------------------------------------------*/


class GiftCartContentView extends ContentView
{
	protected $xtcPrice;


	public function __construct()
	{
		parent::__construct();
		$this->set_content_template('module/gift_cart.html');
		$this->set_flat_assigns(true);

		$this->xtcPrice = new xtcPrice($_SESSION['currency'], $_SESSION['customers_status']['customers_status_id']);
	}


	public function prepare_data()
	{
		$this->build_html = false;
		if(ACTIVATE_GIFT_SYSTEM == 'true')
		{
			$this->set_content_data('ACTIVATE_GIFT', 'true');

			$this->_setAmount();
			$this->_setCouponAmount();
			$this->_setCoupon();
			$this->_setFormData();

			$this->build_html = true;
		}
	}


	protected function _setAmount()
	{
		if(isset($_SESSION['customer_id']))
		{
			$this->set_content_data('C_FLAG', 'true');

			$gv_query  = xtc_db_query("select amount from " . TABLE_COUPON_GV_CUSTOMER . " where customer_id = '" .
									  (int)$_SESSION['customer_id'] . "'");
			$gv_result = xtc_db_fetch_array($gv_query);
			if($gv_result['amount'] > 0)
			{
				$this->set_content_data('GV_AMOUNT', $this->xtcPrice->xtcFormat($gv_result['amount'], true, 0, true));
				$this->set_content_data('GV_SEND_TO_FRIEND_LINK', xtc_href_link(FILENAME_GV_SEND, '', 'SSL'));
			}
			else
			{
				$this->set_content_data('GV_AMOUNT', 0);
			}
		}
	}


	protected function _setCouponAmount()
	{
		if(isset ($_SESSION['gv_id']))
		{
			$gv_query = xtc_db_query("select coupon_amount from " . TABLE_COUPONS . " where coupon_id = '" .
									 xtc_db_input($_SESSION['gv_id']) . "'");
			$coupon   = xtc_db_fetch_array($gv_query);
			$this->set_content_data('COUPON_AMOUNT2',
									$this->xtcPrice->xtcFormat($coupon['coupon_amount'], true, 0, true));
		}
	}


	protected function _setCoupon()
	{
		if(isset ($_SESSION['cc_id']))
		{
			$this->set_content_data('COUPON_HELP_URL', xtc_href_link(FILENAME_POPUP_COUPON_HELP,
																	 'cID=' . $_SESSION['cc_id'] . '&lightbox_mode=1',
																	 'SSL'));
			$cc_query = xtc_db_query("select coupon_minimum_order from " . TABLE_COUPONS . " where coupon_id = '" .
									 xtc_db_input($_SESSION['cc_id']) . "'");
			$coupon   = xtc_db_fetch_array($cc_query);
			$this->set_content_data('REDEEMED_COUPON_UNDER_MIN_VALUE', $coupon['coupon_minimum_order'] > 0 &&
																	   $coupon['coupon_minimum_order'] >
																	   $_SESSION['cart']->show_total() ? REDEEMED_COUPON_UNDER_MIN_VALUE : '');
		}
		
		if(strpos($_SESSION['info_message'], ERROR_NO_INVALID_REDEEM_GV) !== false)
		{
			$this->set_content_data('ERROR_MESSAGE', ERROR_NO_INVALID_REDEEM_GV);
		}
	}


	protected function _setFormData()
	{
		$this->set_content_data('LINK_ACCOUNT', xtc_href_link('shop.php', 'do=CreateRegistree', 'SSL'));
		$this->set_content_data('FORM_ACTION', xtc_draw_form('gift_coupon',
															 xtc_href_link(FILENAME_SHOPPING_CART, 'action=check_gift',
																		   'NONSSL', true, true, true)));
		$this->set_content_data('FORM_ACTION_URL',
								xtc_href_link(FILENAME_SHOPPING_CART, 'action=check_gift', 'NONSSL', true, true, true));
		$this->set_content_data('INPUT_CODE',
								xtc_draw_input_field('gv_redeem_code', GM_GIFT_INPUT, 'id="gm_gift_input"'));
		$this->set_content_data('INPUT_CODE_NAME', 'gv_redeem_code');
		$this->set_content_data('INPUT_CODE_VALUE', GM_GIFT_INPUT);
		$this->set_content_data('BUTTON_SUBMIT', xtc_image_submit('button_redeem.gif', IMAGE_REDEEM_GIFT));
		$this->set_content_data('language', $_SESSION['language']);
		$this->set_content_data('FORM_END', '</form>');
	}
}