<?php
/* --------------------------------------------------------------
   GVRedeemContentView.inc.php 2015-05-29 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
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

class GVRedeemContentView extends ContentView
{
	protected $currency;
	protected $customers_status_id;
	protected $coupon_amount;
	protected $error;

	public function __construct()
	{
		parent::__construct();
		
		$this->set_content_template('module/gv_redeem.html');
		$this->set_flat_assigns(true);
	}

	protected function set_validation_rules()
	{
		$this->validation_rules_array['currency']				= array('type' => 'string');
		$this->validation_rules_array['customers_status_id']	= array('type' => 'int');
		$this->validation_rules_array['coupon_amount']			= array('type' => 'double');
		$this->validation_rules_array['error']					= array('type' => 'bool');
	}
	
	public function prepare_data()
	{
		$t_uninitialized_array = $this->get_uninitialized_variables(array('currency',
																		  'customers_status_id',
																		  'coupon_amount',
																		  'error')
		);
		
		if(empty($t_uninitialized_array))
		{
			$xtPrice = new xtcPrice($this->currency, $this->customers_status_id);

			$this->content_array['coupon_amount'] = $xtPrice->xtcFormat($this->coupon_amount, true);
			$this->content_array['error'] = $this->error;
			$this->content_array['CONTINUE_LINK'] = xtc_href_link(FILENAME_DEFAULT);
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
		}
	}
}