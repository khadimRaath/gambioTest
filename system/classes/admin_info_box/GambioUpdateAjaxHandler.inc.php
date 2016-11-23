<?php
/* --------------------------------------------------------------
   GambioUpdateAjaxHandler.inc.php 2012-08-08 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2012 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

require_once(DIR_FS_CATALOG . 'gm/classes/JSON.php');

class GambioUpdateAjaxHandler extends AjaxHandler
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
		$c_server_response = (string)$this->v_data_array['response'];
		
		$coo_json = new Services_JSON(SERVICES_JSON_LOOSE_TYPE);
		$t_response_array = $coo_json->decode($c_server_response);
		
		// todo
		if(is_array($t_response_array))
		{
			if($t_response_array['status'] == 'success')
			{
				$this->v_output_buffer = '<div class="update_message">' . $t_response_array['message'] . '</div>';
			}
			elseif($t_response_array['status'] == 'error')
			{
				$this->v_output_buffer = '<div class="update_error_message">' . $t_response_array['message'] . '</div>';
			}
		}
		
		return true;
	}
}
?>