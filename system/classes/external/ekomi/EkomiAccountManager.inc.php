<?php
/* --------------------------------------------------------------
   EkomiAccountManager.inc.php 2014-04-02 mb
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/


require_once(DIR_FS_CATALOG . 'gm/classes/lib/nusoap.php');
require_once(DIR_FS_INC . 'xtc_php_mail.inc.php');


class EkomiAccountManager
{
	var $v_wsdl_url = 'http://api.ekomi.de/manage/account.wsdl';
	var $v_coo_soap_client;
	var $v_coo_soap_proxy;

	
	function EkomiAccountManager()
	{
		$this->v_coo_soap_client = new nusoap_client($this->v_wsdl_url, true);
		$this->v_coo_soap_proxy = $this->v_coo_soap_client->getProxy();

		# check connection
		if($this->v_coo_soap_client->getError() || !is_object($this->v_coo_soap_proxy))
		{
			$this->v_coo_soap_proxy = null;
			$coo_ekomi_log = LogControl::get_instance();
			$message = 'Connection to eKomi-API-Server could not be established.';
			$coo_ekomi_log->error($message, 'widgets', 'ekomi_errors', 'error', 'USER ERROR', 0, print_r($this->v_coo_soap_client->getError(), true));
		}
	}


	function account_push(
							$p_account_name,
							$p_url,
							$p_logo,
							$p_desc,
							$p_respon,
							$p_company,
							$p_street,
							$p_address,
							$p_phone,
							$p_fax,
							$p_mail,
							$p_private_mail,
							$p_locale = 'de',
							$p_type = '1',
							$p_product = '47',
							$p_auth = 'gambio_2011_dw7z84i5bfgt645jwti7h8',
							$p_external_id = '',
							$p_private_forename = '',
							$p_private_surname = '',
							$p_private_phone = ''
						)
	{
		$t_success = false;

		if($this->v_coo_soap_proxy !== null)
		{
			# create account
			$t_result = $this->v_coo_soap_proxy->accountPush(
																$p_account_name,
																$p_url,
																$p_logo,
																$p_desc,
																$p_respon,
																$p_company,
																$p_street,
																$p_address,
																$p_phone,
																$p_fax,
																$p_mail,
																$p_private_mail,
																$p_locale,
																$p_type,
																$p_product,
																$p_auth,
																$p_external_id,																
																$p_private_forename,
																$p_private_surname,
																$p_private_phone
															);
			
			if(is_string($t_result))
			{
				$coo_result = json_decode($t_result);

				if(check_data_type($coo_result, 'object'))
				{
					if(isset($coo_result->interface_id) && isset($coo_result->interface_pw))
					{
						# send mail with eKomi login data
						$t_mail_sent = xtc_php_mail(CONTACT_US_EMAIL_ADDRESS, CONTACT_US_NAME, $coo_result->login_mail, $coo_result->login_mail, '', CONTACT_US_EMAIL_ADDRESS, CONTACT_US_NAME, '', '', EKOMI_ACCOUNT_MAIL_SUBJECT, sprintf(EKOMI_ACCOUNT_MAIL_MESSAGE_HTML, $coo_result->login_mail, $coo_result->login_pass), sprintf(EKOMI_ACCOUNT_MAIL_MESSAGE_TXT, $coo_result->login_mail, $coo_result->login_pass));

						if($t_mail_sent)
						{
							# setup configuration
							gm_set_conf('EKOMI_API_ID', $coo_result->interface_id);
							gm_set_conf('EKOMI_API_PASSWORD', $coo_result->interface_pw);
							gm_set_conf('EKOMI_API_STATUS', '1');

							$t_success = true;
						}
						elseif(class_exists('LogControl'))
						{
							$coo_ekomi_log = LogControl::get_instance();
							$message = 'E-mail "' . EKOMI_ACCOUNT_MAIL_SUBJECT . '" could not be sent to "' . $coo_result->login_mail . '"';
							$coo_ekomi_log->error($message, 'widgets', 'ekomi_errors');
						}
					}
				}
			}
			elseif(is_array($t_result) && isset($t_result['faultstring']))
			{
				$t_success = 'mail_is_missing';
			}

			return $t_success;
		}
	}
}
