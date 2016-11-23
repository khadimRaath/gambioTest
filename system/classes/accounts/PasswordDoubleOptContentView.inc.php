<?php
/* --------------------------------------------------------------
   PasswordDoubleOptContentView.inc.php 2015-05-29 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------


   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce www.oscommerce.com
   (c) 2003  nextcommerce www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: password_double_opt.php,v 1.0)

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

class PasswordDoubleOptContentView extends ContentView
{
	protected $case;
	protected $email_address = '';
	protected $captcha_html;
	
	public function __construct()
	{
		parent::__construct();
		$this->set_flat_assigns(true);
	}
	
	protected function set_validation_rules()
	{
		// SET VALIDATION RULES
		$this->validation_rules_array['case']			= array('type' => 'string');
		$this->validation_rules_array['email_address']	= array('type' => 'string');
		$this->validation_rules_array['captcha_html']	= array('type' => 'string');
	}

	public function prepare_data()
	{
		$t_uninitialized_array = $this->get_uninitialized_variables(array('case', 'captcha_html'));
		if(empty($t_uninitialized_array))
		{
			$this->content_array['VALIDATION_ACTIVE'] = gm_get_conf('GM_FORGOT_PASSWORD_VVCODE');
			$this->content_array['FORM_ACTION_URL'] = xtc_href_link(FILENAME_PASSWORD_DOUBLE_OPT, 'action=first_opt_in', 'SSL');
			$this->content_array['FORM_METHOD'] = 'post';
			$this->content_array['FORM_ID'] = 'sign';
			$this->content_array['INPUT_EMAIL_NAME'] = 'email';
			$this->content_array['INPUT_EMAIL_VALUE'] = htmlentities_wrapper($this->email_address);
			$this->content_array['GM_CAPTCHA'] = $this->captcha_html;

			switch($this->case)
			{
				case 'first_opt_in':
					$this->content_array['info_message'] = TEXT_LINK_MAIL_SENDED;
					$this->set_content_template('module/password_messages.html');
					break;

				case 'code_error':
					$this->content_array['info_message'] = TEXT_CODE_ERROR;
					$this->content_array['BUTTON_SEND'] = xtc_image_submit('button_send.gif', IMAGE_BUTTON_LOGIN);
					$this->set_content_template('module/password_double_opt_in.html');
					break;

				case 'no_account':
					$this->content_array['info_message'] = TEXT_NO_ACCOUNT;
					$this->set_content_template('module/password_messages.html');
					break;

				case 'double_opt':
					$this->content_array['info_message'] = TEXT_PASSWORD_FORGOTTEN;
					$this->set_content_template('module/password_double_opt_in.html');
					break;
			}
		}
		else
		{
			trigger_error("Variable(s) " . implode(', ', $t_uninitialized_array) . " do(es) not exist in class " . get_class($this) . " or is/are null", E_USER_ERROR);
		}
	}
}