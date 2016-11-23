<?php
/* --------------------------------------------------------------
   AdminMenuAjaxHandler.inc.php 2016-05-03
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

require_once(DIR_FS_CATALOG . 'gm/classes/JSON.php');

class AdminMenuAjaxHandler extends AjaxHandler
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
		$t_output_array = array();
		$t_enable_json_output = true;

		$t_action_request = $this->v_data_array['GET']['action'];

		switch($t_action_request)
		{
			case 'get_closed_boxes':
				$result = xtc_db_query('
					SELECT box_key
					FROM gm_admin_boxes
					WHERE
						customers_id 	= "'.$_SESSION['customer_id'].'" AND
						box_status		= "0"
				');
				$t_output_array['box_keys'] = array();
				while(($row = xtc_db_fetch_array($result) )) 
				{
					$t_output_array['box_keys'][] = $row['box_key'];
				}
				break;
			case 'save_box_status':
				$this->gm_set_leftboxes_status($_SESSION['customer_id'], $this->v_data_array['GET']['box_key'], $this->v_data_array['GET']['box_status']);
				break;
			case 'load_favs':
				$result = xtc_db_query('
					SELECT link_key
					FROM gm_admin_favorites
					WHERE
						customers_id = "'.$_SESSION['customer_id'].'"
					ORDER BY
						sort_order ASC,
						favorites_id ASC
				');
				$t_output_array['link_keys'] = array();
				while(($row = xtc_db_fetch_array($result) )) 
				{
					$t_output_array['link_keys'][] = $row['link_key'];
				}
				break;
			case 'save_fav':
				$link_key = addslashes($this->v_data_array['GET']['link_key']);
		
				xtc_db_query('
					DELETE FROM gm_admin_favorites
					WHERE link_key = "'.$link_key.'"
					AND customers_id = "'.$_SESSION['customer_id'].'"
				');
				xtc_db_query('
					INSERT INTO gm_admin_favorites
					SET
						customers_id = "'.$_SESSION['customer_id'].'",
						link_key = "'.$link_key.'"
				');
				break;
			case 'delete_fav':
				$link_key = addslashes($this->v_data_array['GET']['link_key']);

				xtc_db_query('
					DELETE FROM gm_admin_favorites
					WHERE link_key = "'.$link_key.'"
					AND customers_id = "'.$_SESSION['customer_id'].'"
				');
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

	function gm_set_leftboxes_status($customers_id, $box_key, $box_status)
	{
		$customers_id = addslashes($customers_id);
		$box_key = addslashes($box_key);
		$box_status = addslashes($box_status);

		xtc_db_query('
			DELETE FROM gm_admin_boxes
			WHERE
				customers_id 	= "'. $customers_id .'" AND
				box_key			 	= "'. $box_key			.'"
		');

		xtc_db_query('
			INSERT INTO gm_admin_boxes
			SET
				customers_id 	= "'. $customers_id .'",
				box_key			 	= "'. $box_key			.'",
				box_status		= "'. $box_status		.'"
		');
	}
}