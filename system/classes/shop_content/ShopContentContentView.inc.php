<?php
/* --------------------------------------------------------------
  ShopContentContentView.php 2016-08-25
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2016 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------


  based on:
  (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
  (c) 2002-2003 osCommerce(conditions.php,v 1.21 2003/02/13); www.oscommerce.com
  (c) 2003	 nextcommerce (shop_content.php,v 1.1 2003/08/19); www.nextcommerce.org
  (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: shop_content.php 1303 2005-10-12 16:47:31Z mz $)

  Released under the GNU General Public License
  --------------------------------------------------------------------------------------- */

// include needed functions
require_once(DIR_FS_INC . 'xtc_image_button.inc.php');

class ShopContentContentView extends ContentView
{
	protected $coo_seo_boost;
	protected $content_group_id = 0;
	protected $content_id;
	protected $content_heading;
	protected $content_text;
	protected $content_file;
	protected $error_message;
	protected $action;
	protected $captcha;
	protected $withdrawal_content;
	protected $subject;
	protected $name;
	protected $email_address;
	protected $message_body;
	protected $file_flag_name;

	public function __construct()
	{
		parent::__construct();
		
		$this->set_template_dir(DIR_FS_CATALOG . 'templates/' . CURRENT_TEMPLATE . '/module/');
		$this->set_flat_assigns(false);
	}

	protected function set_validation_rules()
	{
		// SET VAlIDATION RULES
		$this->validation_rules_array['content_group_id']	= array('type' => 'int');
		$this->validation_rules_array['content_id']			= array('type' => 'int');
		$this->validation_rules_array['content_heading']	= array('type' => 'string');
		$this->validation_rules_array['content_text']		= array('type' => 'string');
		$this->validation_rules_array['content_file']		= array('type' => 'string');
		$this->validation_rules_array['error_message']		= array('type' => 'string');
		$this->validation_rules_array['action']				= array('type' => 'string');
		$this->validation_rules_array['subject']			= array('type' => 'string');
		$this->validation_rules_array['name']				= array('type' => 'string');
		$this->validation_rules_array['email_address']		= array('type' => 'string');
		$this->validation_rules_array['message_body']		= array('type' => 'string');
		$this->validation_rules_array['withdrawal_content']	= array('type' => 'string');
		$this->validation_rules_array['file_flag_name']		= array('type' => 'string');
		$this->validation_rules_array['captcha']			= array('type' => 'object',
																	'object_type' => 'Captcha');
		$this->validation_rules_array['coo_seo_boost']	= array('type' => 'object',
																  'object_type' => 'GMSEOBoost');
	}

	public function prepare_data()
	{
		$t_uninitialized_array = $this->get_uninitialized_variables(array('captcha'));
		if(empty($t_uninitialized_array))
		{
			$this->set_content_data('error_message', $this->error_message);

			if($this->content_group_id == 7)
			{
				$this->set_content_template('contact_us.html');
				$this->add_contact_us_data();
			}
			elseif($this->content_group_id == 14)
			{
				$this->set_content_template('content.html');
				$this->set_content_data('HIDE_BOTTOM', true);
				$this->add_content_data();
			}
			elseif($this->content_group_id == 3889891)
			{
				$this->set_content_template('content.html');
				$this->add_shipping_and_payment_conditions_data();
			}
			elseif($this->content_group_id == gm_get_conf('GM_WITHDRAWAL_CONTENT_ID'))
			{
				$this->set_content_template('content.html');
				$this->add_withdrawal_content_data();
			}
			elseif($this->file_flag_name == 'withdrawal')
			{
				$this->set_content_template('content.html');
				$this->add_withdrawal_text();
			}
			else
			{
				$this->set_content_template('content.html');
				$this->add_content_data();
			}
		}
		else
		{
			trigger_error("Variable(s) " . implode(', ', $t_uninitialized_array) . " do(es) not exist in class " . get_class($this) . " or is/are null", E_USER_ERROR);
		}
	}
	
	protected function get_file_output()
	{
		ob_start();
		if(strpos($this->content_file, '.txt'))
		{
			echo '<pre>';
		}

		include (DIR_FS_CATALOG . 'media/content/' . $this->content_file);

		if(strpos($this->content_file, '.txt'))
		{
			echo '</pre>';
		}
		$t_content_body = ob_get_contents();
		ob_end_clean();
		
		return $t_content_body;
	}
	
	protected function add_contact_us_data()
	{
		$this->set_content_data('CONTACT_HEADING', $this->content_heading);
		if(isset($this->action) && ($this->action == 'success'))
		{
			$this->set_content_data('success', '1');
		}
		else
		{
			if($this->content_file != '' && file_exists(DIR_FS_CATALOG . 'media/content/' . basename($this->content_file)))
			{
				$contact_content = $this->get_file_output();
			}
			else
			{
				$contact_content = $this->content_text;
			}

			$this->set_content_data('CONTACT_CONTENT', $contact_content);
			$this->set_content_data('FORM_ID', 'contactus');
			$this->set_content_data('FORM_ACTION_URL', xtc_href_link(FILENAME_CONTENT, 'action=send&coID=' . $this->content_group_id, 'NONSSL', true, true, true));
			$this->set_content_data('FORM_METHOD', 'post');
			$this->set_content_data('INPUT_NAME_NAME', 'name');
			$this->set_content_data('INPUT_NAME_VALUE', htmlentities_wrapper($this->name));
			$this->set_content_data('INPUT_EMAIL_NAME', 'email');
			$this->set_content_data('INPUT_EMAIL_VALUE', htmlentities_wrapper($this->email_address));
			$this->set_content_data('INPUT_SUBJECT_NAME', 'subject');
			$this->set_content_data('INPUT_SUBJECT_VALUE', $this->subject);
			$this->set_content_data('INPUT_TEXT_NAME', 'message_body');
			$this->set_content_data('INPUT_TEXT_VALUE', htmlentities_wrapper($this->message_body));
			
			//start captcha 
			$this->set_content_data('GM_CONTACT_VVCODE', gm_get_conf('GM_CONTACT_VVCODE'));
			$this->set_content_data('GM_CAPTCHA', $this->captcha->get_html());
			//end captcha
			
			$this->set_content_data('INPUT_PRIVACY_VALUE', $this->privacy_accepted);
			$this->set_content_data('GM_PRIVACY_LINK', gm_get_privacy_link('GM_CHECK_PRIVACY_CONTACT'));
			$this->set_content_data('show_privacy_checkbox', gm_get_conf('PRIVACY_CHECKBOX_CONTACT'));
		}
	}
	
	protected function add_shipping_and_payment_conditions_data()
	{
		if($this->content_file != '')
		{
			$content_body = $this->get_file_output();
		}
		else
		{
			$coo_shipping_and_payment_matrix_content_view = MainFactory::create_object('ShippingAndPaymentMatrixContentView');
			$coo_shipping_and_payment_matrix_content_view->set_content_data('heading', $this->content_heading);
			$t_content = $coo_shipping_and_payment_matrix_content_view->get_html();

			$content_body = str_replace('{$shipping_and_payment_matrix}', $t_content, $this->content_text);
		}

		$this->set_content_data('CONTENT_HEADING', $this->content_heading);
		$this->set_content_data('CONTENT_BODY', $content_body);
		$this->set_content_data('BUTTON_CONTINUE', '<a href="javascript:history.back(1)">'.xtc_image_button('button_back.gif', IMAGE_BUTTON_BACK).'</a>');
	}
	
	protected function add_withdrawal_content_data()
	{
		$content_body = '';

		if ($this->content_file != '' && file_exists(DIR_FS_CATALOG . 'media/content/' . basename($this->content_file)))
		{
			$content_body = $this->get_file_output();
		}
		else
		{
			$content_body = str_replace('{$WITHDRAWAL_TEXT}', $this->withdrawal_content, $this->content_text);

			if(gm_get_conf('WITHDRAWAL_WEBFORM_ACTIVE') == '1')
			{
				$content_body = str_replace('{* withdrawal_form_link_start *}', '', $content_body);
				$content_body = str_replace('{* withdrawal_form_link_end *}', '', $content_body);
			}
			else
			{
				$content_body = preg_replace('/\{\* withdrawal_form_link_start \*\}.*\{\* withdrawal_form_link_end \*\}/s', '', $content_body);
			}

			if(gm_get_conf('WITHDRAWAL_PDF_ACTIVE') == '1')
			{
				$content_body = str_replace('{* withdrawal_pdf_link_start *}', '', $content_body);
				$content_body = str_replace('{* withdrawal_pdf_link_end *}', '', $content_body);
			}
			else
			{
				$content_body = preg_replace('/\{\* withdrawal_pdf_link_start \*\}.*\{\* withdrawal_pdf_link_end \*\}/s', '', $content_body);
			}

			if(gm_get_conf('WITHDRAWAL_WEBFORM_ACTIVE') == '0' && gm_get_conf('WITHDRAWAL_PDF_ACTIVE') == '0')
			{
				$content_body = preg_replace('/\{\* withdrawal_form_start \*\}.*\{\* withdrawal_form_end \*\}/s', '', $content_body);
			}
			else
			{
				$content_body = str_replace('{* withdrawal_form_start *}', '', $content_body);
				$content_body = str_replace('{* withdrawal_form_end *}', '', $content_body);
			}

			$t_withdrawal_content_id = gm_get_conf('GM_WITHDRAWAL_CONTENT_ID');
			$content_body = str_replace('{$PDF_URL}', xtc_href_link('request_port.php', 'module=ShopContent&amp;action=download&amp;coID=' . $t_withdrawal_content_id . ''), $content_body);
			$content_body = str_replace('{$PDF_FORM_URL}', xtc_href_link('request_port.php', 'module=ShopContent&amp;action=download&amp;coID=' . $t_withdrawal_content_id . '&amp;withdrawal_form=1'), $content_body);
			$content_body = str_replace('{$WEBFORM_URL}', xtc_href_link('withdrawal.php', '', 'SSL'), $content_body);

			$t_sef_parameter = '';
			if(SEARCH_ENGINE_FRIENDLY_URLS == 'true')
			{
				$t_sef_parameter = '&content=' . xtc_cleanName($this->content_title);
			}

			if($this->coo_seo_boost->boost_content)
			{
				$t_content_url = xtc_href_link($this->coo_seo_boost->get_boosted_content_url($this->coo_seo_boost->get_content_id_by_content_group(3889895), $_SESSION['languages_id']));
			}
			else
			{
				$t_content_url = xtc_href_link(FILENAME_CONTENT, 'coID=3889895' . $t_sef_parameter);
			}

			$content_body = str_replace('{$PAGE_URL}', $t_content_url, $content_body);
		}

		$this->set_content_data('HIDE_BOTTOM', false);
		$this->set_content_data('CONTENT_HEADING', $this->content_heading);
		$this->set_content_data('CONTENT_BODY', $content_body);
		$this->set_content_data('BUTTON_CONTINUE', '<a href="javascript:history.back(1)">'.xtc_image_button('button_back.gif', IMAGE_BUTTON_BACK).'</a>');
	}
	
	protected function add_withdrawal_text()
	{
		if($this->content_file != '' && file_exists(DIR_FS_CATALOG . 'media/content/' . basename($this->content_file)))
		{
			$content_body = $this->get_file_output();
		}
		else
		{
			$content_body = $this->content_text;
		}
		$this->set_content_data('CONTENT_HEADING', '');
		$this->set_content_data('HIDE_BOTTOM', true);
		$this->set_content_data('CONTENT_BODY', '<strong>' . $this->content_heading . '</strong><br /><br />' . $content_body);
		$this->set_content_data('BUTTON_CONTINUE', '');
	}
	
	protected function add_content_data()
	{
		if($this->content_file != '' && file_exists(DIR_FS_CATALOG . 'media/content/' . basename($this->content_file)))
		{
			$content_body = $this->get_file_output();
			$this->set_content_data('file', $content_body);
		}
		else
		{
			$content_body = $this->content_text;
		}
		$this->set_content_data('CONTENT_HEADING', $this->content_heading);
		$this->set_content_data('CONTENT_BODY', $content_body);

		$this->set_content_data('BUTTON_CONTINUE', '<a href="javascript:history.back(1)">' . xtc_image_button('button_back.gif', IMAGE_BUTTON_BACK) . '</a>');
	}
}
