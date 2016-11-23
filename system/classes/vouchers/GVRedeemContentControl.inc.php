<?php
/* --------------------------------------------------------------
   GVRedeemContentControl 2014-03-06 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------


   based on:
   (c) 2000-2001 The Exchange Project (earlier name of osCommerce)
   (c) 2002-2003 osCommerce (gv_redeem.php,v 1.3.2.1 2003/04/18); www.oscommerce.com
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: gv_redeem.php 1034 2005-07-15 15:21:43Z mz $)

   Released under the GNU General Public License
   -----------------------------------------------------------------------------------------
   Third Party contribution:

   Credit Class/Gift Vouchers/Discount Coupons (Version 5.10)
   http://www.oscommerce.com/community/contributions,282
   Copyright (c) Strider | Strider@oscworks.com
   Copyright (c  Nick Stanko of UkiDev.com, nick@ukidev.com
   Copyright (c) Andre ambidex@gmx.net
   Copyright (c) 2001,2002 Ian C Wilson http://www.phesis.org


   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

MainFactory::load_class('DataProcessing');

class GVRedeemContentControl extends DataProcessing
{
	protected $coupon_code;

	public function __construct()
	{
		parent::__construct();
	}

	protected function set_validation_rules()
	{
		$this->validation_rules_array['coupon_code'] = array('type' => 'string');
	}
	
	public function proceed()
	{
		$t_uninitialized_array = $this->get_uninitialized_variables(array('coupon_code'));
		
		if(empty($t_uninitialized_array))
		{
			$error = true;
			$t_sql = 'SELECT
						c.coupon_id,
						c.coupon_amount
					FROM
						' . TABLE_COUPONS . ' c,
						' . TABLE_COUPON_EMAIL_TRACK . ' et
					WHERE
						coupon_code = "' . xtc_db_input($this->coupon_code) . '"
					AND
						c.coupon_id = et.coupon_id'
			;
			$gv_query = xtc_db_query($t_sql);
			if(xtc_db_num_rows($gv_query) > 0)
			{
				$coupon = xtc_db_fetch_array($gv_query);
				$t_sql = 'SELECT
							coupon_id
						FROM
							' . TABLE_COUPON_REDEEM_TRACK . '
						WHERE
							coupon_id = "' . $coupon['coupon_id'] . '"'
				;
				$redeem_query = xtc_db_query($t_sql);
				if(xtc_db_num_rows($redeem_query) == 0)
				{
					// check for required session variables
					$_SESSION['gv_id'] = $coupon['coupon_id'];
					$error = false;
				}
				else
				{
					$error = true;
				}
			}

			if(!$error && isset($_SESSION['customer_id']))
			{
				// Update redeem status
				$t_ip = '';
				if(gm_get_conf('GM_LOG_IP') == '1' && gm_get_conf('GM_CONFIRM_IP') == 0)
				{
					$t_ip = xtc_get_ip_address();
				}
				
				$t_sql = 'INSERT INTO
							' . TABLE_COUPON_REDEEM_TRACK . '
						SET
							coupon_id	= "' . $coupon['coupon_id'] . '",
							customer_id	= "' . $_SESSION['customer_id'] . '",
							redeem_date	= NOW(),
							redeem_ip	= "' . xtc_db_input($t_ip) . '"'
				;
				$gv_query = xtc_db_query($t_sql);
				
				$t_sql = 'UPDATE
							' . TABLE_COUPONS . '
						SET
							coupon_active = "N"
						WHERE
							coupon_id = "' . $coupon['coupon_id'] . '"'
				;
				xtc_db_query($t_sql);
				xtc_gv_account_update($_SESSION['customer_id'], $_SESSION['gv_id']);
				unset($_SESSION['gv_id']);
			}

			$coo_logoff_content = MainFactory::create_object('GVRedeemContentView');
			$coo_logoff_content->set_('currency', $_SESSION['currency']);
			$coo_logoff_content->set_('customers_status_id', $_SESSION['customers_status']['customers_status_id']);
			$coo_logoff_content->set_('coupon_amount', $coupon['coupon_amount']);
			$coo_logoff_content->set_('error', $error);
			$this->v_output_buffer = $coo_logoff_content->get_html();
			
			return true;
		}
		else
		{
			trigger_error("Variable(s) "
						  . implode(', ', $t_uninitialized_array)
						  . " do(es) not exist in class "
						  . get_class($this)
						  . " or are null"
				, E_USER_ERROR
			);
			
			return false;
		}
	}
}