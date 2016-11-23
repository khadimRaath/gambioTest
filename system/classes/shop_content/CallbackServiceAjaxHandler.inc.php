<?php
/* --------------------------------------------------------------
   CallbackServiceAjaxHandler.inc.php 2016-08-25
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

require_once(DIR_FS_CATALOG . 'gm/classes/JSON.php');

require_once (DIR_FS_INC.'xtc_validate_email.inc.php');
require_once (DIR_FS_INC.'xtc_random_charcode.inc.php');
require_once (DIR_FS_INC.'xtc_php_mail.inc.php');

class CallbackServiceAjaxHandler extends AjaxHandler
{
	function get_permission_status($p_customers_id=NULL)
	{
		return true;
	}

	function proceed()
	{
		$t_output_array = array();
		$t_enable_json_output = false;

		$t_action_request = $this->v_data_array['GET']['action'];

		$coo_captcha = $_SESSION['captcha_object'];

		switch($t_action_request)
		{
			case 'check':
				// name and telephone number exist?
				if(empty($this->v_data_array['GET']['name']) || empty($this->v_data_array['GET']['telephone'])){
					$this->v_output_buffer .= GM_CALLBACK_SERVICE_ERROR . '<br /><br />';
				}
				$this->v_data_array['GET']['recaptcha_response_field'] = urldecode($this->v_data_array['GET']['recaptcha_response_field']);
				$this->v_data_array['GET']['recaptcha_challenge_field'] = urldecode($this->v_data_array['GET']['recaptcha_challenge_field']);
				// vvcode check
				if(!$coo_captcha->is_valid($this->v_data_array['GET'], 'GM_CALLBACK_SERVICE_VVCODE', true)){
					$this->v_output_buffer .= GM_CALLBACK_SERVICE_WRONG_CODE . '<br /><br />';
				}
				
				if(gm_get_conf('GM_CHECK_PRIVACY_CALLBACK') === '1'
				   && gm_get_conf('PRIVACY_CHECKBOX_CALLBACK') === '1'
				   && (!isset($this->v_data_array['GET']['privacy_accepted'])
				       || $this->v_data_array['GET']['privacy_accepted'] !== '1')
				)
				{
					$this->v_output_buffer .= ENTRY_PRIVACY_ERROR . '<br /><br />';
				}
				
				$visual_verify_code = xtc_random_charcode(6);
				$_SESSION['vvcode'] = $visual_verify_code;
				break;
			case 'send':
				if(!empty($this->v_data_array['POST']['telephone']))
				{
					$message = str_replace('%u20AC', 'EUR', xtc_db_prepare_input($this->v_data_array['POST']['message']));
					$text = GM_CALLBACK_SERVICE_MAIL_NAME . xtc_db_prepare_input($this->v_data_array['POST']['name'])
								. "\n". GM_CALLBACK_SERVICE_MAIL_EMAIL . xtc_db_prepare_input($this->v_data_array['POST']['email'])
								. "\n" . GM_CALLBACK_SERVICE_MAIL_TELEPHONE . xtc_db_prepare_input($this->v_data_array['POST']['telephone'])
								. "\n" . GM_CALLBACK_SERVICE_MAIL_TIME . xtc_db_prepare_input($this->v_data_array['POST']['time'])
								. "\n\n" . GM_CALLBACK_SERVICE_MAIL_MESSAGE . "\n" . $message;

					$email = STORE_OWNER_EMAIL_ADDRESS;
					if(!empty($this->v_data_array['POST']['email']) && filter_var($this->v_data_array['POST']['email'], FILTER_VALIDATE_EMAIL))
					{
						$email = $this->v_data_array['POST']['email'];
					}

					// send mail
					xtc_php_mail(CONTACT_US_EMAIL_ADDRESS, CONTACT_US_NAME, CONTACT_US_EMAIL_ADDRESS, CONTACT_US_NAME, '', $email, xtc_db_prepare_input($this->v_data_array['POST']['name']), '', '', GM_CALLBACK_SERVICE_SUBJECT . xtc_db_prepare_input($this->v_data_array['POST']['name'] . ', ' . $this->v_data_array['POST']['telephone']), nl2br(htmlentities_wrapper($text)), html_entity_decode_wrapper($text));

					$this->v_output_buffer = GM_CALLBACK_SERVICE_SUCCESS;
				}
				break;
			default:
				trigger_error('t_action_request not found: '. htmlentities($t_action_request), E_USER_WARNING);
				return false;
		}

		if($t_enable_json_output)
		{
			$coo_json = new Services_JSON(SERVICES_JSON_LOOSE_TYPE);
			$t_output_json = $coo_json->encode($t_output_array);

			$this->v_output_buffer = $t_output_json;
		}

		return true;
	}
}
