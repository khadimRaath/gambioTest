<?php
/* --------------------------------------------------------------
	ProtectedShopsCronAjaxHandler.inc.php 2014-05-26_1650 mabr
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2014 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/

class ProtectedShopsCronAjaxHandler extends AjaxHandler
{
	protected $_coo_ps;
	protected $_operation_mode;

	public function __construct()
	{
		$this->_response = '';
		$this->_coo_ps = MainFactory::create_object('ProtectedShops', array());
	}

	public function get_permission_status($p_customers_id = null)
	{
		if(isset($this->v_data_array['GET']['mode']) && $this->v_data_array['GET']['mode'] == 'frontend')
		{
			$this->_operation_mode = 'frontend';
			return true;
		}
		else
		{
			$this->_operation_mode = 'cron';
			$t_secure_token = FileLog::get_secure_token();
			if(isset($this->v_data_array['GET']['key']) == true && $this->v_data_array['GET']['key'] == $t_secure_token)
			{
				return true;
			}
			else
			{
				$this->v_output_buffer .= $this->_coo_ps->get_text('cron_invalid_key');
				return false;
			}
		}
	}

	public function proceed()
	{
		if($this->_operation_mode == 'frontend')
		{
			$t_last_run = (int)gm_get_conf(ProtectedShops::CFG_PREFIX.'UPDATE_LAST_RUN');
			$t_update_interval = (int)gm_get_conf(ProtectedShops::CFG_PREFIX.'UPDATE_INTERVAL');
			$t_run_now = $t_update_interval > 0 && ((time() - $t_last_run) > $t_update_interval);
			if($t_run_now === true)
			{
				$this->v_output_buffer .= '/* PS update triggered */'."\n";
			}
			else
			{
				$this->v_output_buffer .= '/* PS update triggered; update not required */'."\n";
			}
		}
		else
		{
			$t_run_now = true;
		}

		if($t_run_now === true)
		{
			$t_output = $this->_coo_ps->updateAndUseAll();
			//$t_output = nl2br($t_output);
			if($this->_operation_mode == 'cron')
			{
				header('Content-Type: text/plain');
				$this->v_output_buffer .= $t_output;
			}
			else
			{
				gm_set_conf(ProtectedShops::CFG_PREFIX.'UPDATE_LAST_RUN', time());
			}
		}
		return true;
	}
}
