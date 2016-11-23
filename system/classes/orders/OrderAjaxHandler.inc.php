<?php
/* --------------------------------------------------------------
  OrderAjaxHandler.inc.php 2016-05-03
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2016 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
 */

require_once(DIR_FS_CATALOG . 'gm/classes/JSON.php');

class OrderAjaxHandler extends AjaxHandler
{
	function get_permission_status($p_customers_id = NULL)
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
			case 'quantity_checker':
				$t_enable_json_output = false;
				if(is_numeric(xtc_get_prid($this->v_data_array['GET']['id'])))
				{
					$products_id = xtc_get_prid($this->v_data_array['GET']['id']);
					$qty = gm_convert_qty($this->v_data_array['GET']['qty']);

					$get_products_data = xtc_db_query("SELECT gm_min_order, gm_graduated_qty FROM products WHERE products_id = '" . (int)$products_id . "'");
					if(xtc_db_num_rows($get_products_data) == 1)
					{
						$products_data = xtc_db_fetch_array($get_products_data);
						if(empty($products_data['gm_min_order']))
						{
							$products_data['gm_min_order'] = 1;
						}
						if(empty($products_data['gm_graduated_qty']))
						{
							$products_data['gm_graduated_qty'] = 1;
						}
						if($qty < $products_data['gm_min_order'])
						{
							$this->v_output_buffer .= GM_ORDER_QUANTITY_CHECKER_MIN_ERROR_1 . str_replace('.', ',', (double)$products_data['gm_min_order']) . GM_ORDER_QUANTITY_CHECKER_MIN_ERROR_2;
						}
						if($qty > MAX_PRODUCTS_QTY)
						{
							$this->v_output_buffer .= GM_ORDER_QUANTITY_CHECKER_MAX_ERROR_1 . MAX_PRODUCTS_QTY . GM_ORDER_QUANTITY_CHECKER_MAX_ERROR_2;
						}
						$result = $qty / $products_data['gm_graduated_qty'];
						$result = round($result, 4); // workaround for next if-case to avoid calculating failure
						if((int)$result != $result)
						{
							$this->v_output_buffer .= GM_ORDER_QUANTITY_CHECKER_GRADUATED_ERROR_1 . str_replace('.', ',', (double)$products_data['gm_graduated_qty']) . GM_ORDER_QUANTITY_CHECKER_GRADUATED_ERROR_2;
						}
					}
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