<?php
/* --------------------------------------------------------------
   EkomiSendMailsAjaxHandler.inc.php 2014-03-18 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class EkomiSendMailsAjaxHandler extends AjaxHandler
{
	function get_permission_status($p_customers_id = NULL)
	{
		// no token, no go
		$t_secure_token = LogControl::get_secure_token();
		if(empty($t_secure_token) || $t_secure_token != gm_prepare_string($this->v_data_array['GET']['token'], true))
		{
			return false;
		}

		return true;
	}

	function proceed()
	{
		$coo_ekomi_manager = MainFactory::create_object('EkomiManager', array(gm_get_conf('EKOMI_API_ID'), gm_get_conf('EKOMI_API_PASSWORD')));
		$t_success = $coo_ekomi_manager->send_mails();

		if($t_success)
		{
			$this->v_output_buffer = 'Mails successfully sent.';
		}
		else
		{
			$this->v_output_buffer = 'A failure occured. Check your ekomi_errors-logfile for more information.';
		}

		return true;
	}
}
?>