<?php
/* --------------------------------------------------------------
   DeleteGuestAccountsAjaxHandler.inc.php 2015-09-30
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

require_once(DIR_FS_CATALOG . 'gm/classes/JSON.php');

class DeleteGuestAccountsAjaxHandler extends AjaxHandler
{
	function get_permission_status($p_customers_id = NULL)
    {
		return true;
    }

	function proceed()
	{
		if($this->get_secure_token() != $this->v_data_array['GET']['token'])
		{
			return false;
		}
		
		$coo_logoff = MainFactory::create_object('LogoffContentControl');
		$coo_logoff->delete_unused_guest_accounts();
		$this->v_output_buffer = 'true';
		
		// add infobox message, if script is called from backend
		if(strpos(gm_get_env_info('SCRIPT_NAME'), DIR_WS_CATALOG . 'admin') !== false)
		{
			$languageTextManager = MainFactory::create_object('LanguageTextManager', array(), true);
			$GLOBALS['messageStack']->add_session($languageTextManager->get_text('guest_accounts_deleted_message', 
																					'admin_info_boxes'), 'success');
		}		
		
		return true;
	}
	
	function get_secure_token()
	{
		$t_token = LogControl::get_secure_token();
		$t_token = md5($t_token);
		
		return $t_token;
	}
}
