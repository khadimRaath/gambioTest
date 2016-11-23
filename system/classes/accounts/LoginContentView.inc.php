<?php
/* --------------------------------------------------------------
   AccountLoginContentView.inc.php 2015-06-19 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------


   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(login.php,v 1.79 2003/05/19); www.oscommerce.com
   (c) 2003      nextcommerce (login.php,v 1.13 2003/08/17); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: login.php 1143 2005-08-11 11:58:59Z gwinger $)

   Released under the GNU General Public License
   -----------------------------------------------------------------------------------------
   Third Party contribution:

   guest account idea by Ingo T. <xIngox@web.de>
   ---------------------------------------------------------------------------------------*/

class LoginContentView extends ContentView
{
	protected $info_message;
	protected $input_mail_value;
	protected $checkout_started_get_param;
	protected $cart_contents_count;

	public function __construct()
	{
		parent::__construct();
		
		$this->set_content_template('module/login.html');
		$this->set_flat_assigns(true);
	}

	protected function set_validation_rules()
	{
		$this->validation_rules_array['info_message']				= array('type' => 'string');
		$this->validation_rules_array['input_mail_value']			= array('type' => 'string');
		$this->validation_rules_array['checkout_started_get_param']	= array('type' => 'string');
		$this->validation_rules_array['cart_contents_count']		= array('type' => 'double');
	}

	public function prepare_data()
	{
		$t_uninitialized_array = $this->get_uninitialized_variables(array('info_message', 'checkout_started_get_param', 'input_mail_value'));
		
		if(empty($t_uninitialized_array))
		{
			$this->get_uninitialized_variables(array('checkout_started_get_param', 'cart_contents_count'));

			$this->content_array['info_message'] = $this->info_message;
			$this->content_array['account_option'] = ACCOUNT_OPTIONS;

			$this->content_array['NEW_ACCOUNT_URL'] = xtc_href_link('shop.php', 'do=CreateRegistree&' . $this->checkout_started_get_param, 'SSL');
			$this->content_array['GUEST_URL'] = xtc_href_link('shop.php', 'do=CreateGuest&' . $this->checkout_started_get_param, 'SSL');

			if($this->checkout_started_get_param != '')
			{
				$this->checkout_started_get_param .= '&';
			}
			$this->content_array['FORM_ID'] = 'login';
			$this->content_array['FORM_ACTION_URL'] = xtc_href_link(FILENAME_LOGIN, $this->checkout_started_get_param . 'action=process', 'SSL');
			$this->content_array['INPUT_MAIL_NAME'] = 'email_address';

			$this->content_array['INPUT_MAIL_VALUE'] = $this->input_mail_value;
			$this->content_array['INPUT_PASSWORD_NAME'] = 'password';
			$this->content_array['LINK_LOST_PASSWORD'] = xtc_href_link(FILENAME_PASSWORD_DOUBLE_OPT, '', 'SSL');
		}
		else
		{
			trigger_error("Variable(s) " . implode(', ', $t_uninitialized_array) . " do(es) not exist in class " . get_class($this) . " or are null", E_USER_ERROR);
		}		
	}
}