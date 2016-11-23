<?php
/* --------------------------------------------------------------
   LoggingAjaxHandler.inc.php 2014-03-13 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class LoggingAjaxHandler extends AjaxHandler
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
		$t_log_group_id = (int)$this->v_data_array['GET']['log_group_id'];
		
		$t_sql = '	SELECT
						name
					FROM
						log_groups
					WHERE
						log_group_id = ' . $t_log_group_id;
		$t_result = xtc_db_query($t_sql);
		$t_row = xtc_db_fetch_array($t_result);
		$t_log_group_name = $t_row['name'];
		
		$this->v_output_buffer = LogControl::get_instance()->get_group_configuration($t_log_group_name, false)->get_configuration_html_form();

		return true;
	}
}