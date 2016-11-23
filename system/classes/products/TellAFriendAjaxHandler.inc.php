<?php
/* --------------------------------------------------------------
   TellAFriendAjaxHandler.inc.php 2016-08-26
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

require_once(DIR_FS_CATALOG . 'gm/classes/JSON.php');

class TellAFriendAjaxHandler extends AjaxHandler
{
	function get_permission_status($p_customers_id=NULL)
	{
		return true;
	}

	function proceed()
	{
		$t_output_array = array();
		$t_enable_json_output = true;

		$t_action_request = $this->v_data_array['GET']['action'];

		switch($t_action_request)
		{
			case 'get_form':
				$t_enable_json_output = false;
				$coo_captcha = MainFactory::create_object('Captcha');
				
				$coo_tell_a_friend = MainFactory::create_object('TellAFriendContentView');
				
				$coo_tell_a_friend->setProductsId($this->v_data_array['GET']['id']);
				$coo_tell_a_friend->setCaptchaObject($_SESSION['captcha_object'] = &$coo_captcha);
				$coo_tell_a_friend->setCustomerId($_SESSION['customer_id']);
				$coo_tell_a_friend->setCustomerFirstName($_SESSION['customer_first_name']);
				$coo_tell_a_friend->setCustomerLastName($_SESSION['customer_last_name']);
				$coo_tell_a_friend->setLanguagesId($_SESSION['languages_id']);
				
				$coo_tell_a_friend->setPost($_POST);
				$coo_tell_a_friend->setName($_POST['name']);
				$coo_tell_a_friend->setEmail($_POST['email']);
				$coo_tell_a_friend->setMessage($_POST['message']);
				
				$coo_tell_a_friend->setPrivacyAccepted((isset($_POST['privacy_accepted']) ? '1' : '0'));
				
				$this->v_output_buffer = $coo_tell_a_friend->get_html();
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