<?php
/* --------------------------------------------------------------
   LogAjaxHandler.inc.php 2014-03-06 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class LogAjaxHandler extends AjaxHandler
{
	function get_permission_status($p_customers_id=NULL)
	{
		if($_SESSION['customers_status']['customers_status_id'] === '0')
		{
			#admins only
			return true;
		}
		return false;
	}

	function proceed()
	{
		$coo_lang_file_master = MainFactory::create_object('LanguageTextManager', array(), true);
		$coo_lang_file_master->init_from_lang_file('lang/' . basename($_SESSION['language']). '/admin/show_logs.php');
		
		require_once(DIR_FS_ADMIN . 'includes/gm/classes/ShowLogs.php');
		
		$coo_show_logs = new ShowLogs();

		switch($this->v_data_array['GET']['action'])
		{
			case 'show':
				$this->v_output_buffer = $coo_show_logs->get_log($this->v_data_array['POST']['file'],  $this->v_data_array['POST']['page']);

				break;

			case 'clear':
				$this->v_output_buffer = $coo_show_logs->clear_log($this->v_data_array['POST']['file']);

				break;

			case 'delete':
				$this->v_output_buffer = $coo_show_logs->delete_log($this->v_data_array['POST']['file']);

				break;
			
			case 'download':
				$this->v_output_buffer = $coo_show_logs->download_log($this->v_data_array['GET']['file']);
				
				break;
		}		

		return true;
	}
}