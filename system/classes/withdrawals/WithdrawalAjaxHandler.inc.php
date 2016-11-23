<?php
/* --------------------------------------------------------------
   WithdrawalAjaxHandler.inc.php 2016-05-03
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class WithdrawalAjaxHandler extends AjaxHandler
{
	function get_permission_status($p_customers_id=NULL)
	{
		if($_SESSION['customers_status']['customers_status_id'] === '0')
		{
			#admins only
			return true;
		}
		return true;
	}

	function proceed()
	{
		$t_output_array = array();
		$t_enable_json_output = false;

		$t_action_request = $this->v_data_array['GET']['action'];

		switch($t_action_request)
		{
			case 'save_withdrawal_order_id':
				$t_enable_json_output = true;
				$withdrawal_id = (int)$this->v_data_array['POST']['withdrawal_id'];
				$order_id = (int)$this->v_data_array['POST']['order_id'];

				if($withdrawal_id > 0 && $this->v_data_array['POST']['order_id'] == (string)$order_id && $_SESSION['coo_page_token']->is_valid($this->v_data_array['POST']['page_token']))
				{
					$withdrawal = MainFactory::create_object('WithdrawalModel', array($withdrawal_id));
					$withdrawal->set_order_id($order_id);
					$withdrawal->save();
					$t_query = 'SELECT
									orders_id
								FROM
									orders
								WHERE
									orders_id = "' . $order_id . '"';
					$t_result = xtc_db_query($t_query);
					if(xtc_db_num_rows($t_result) == 1)
					{
						$t_link = xtc_href_link('orders.php', 'oID=' . $order_id . '&action=edit');
						$t_output_array['order_details_link'] = $t_link;
					}
					else
					{
						$t_output_array['order_details_link'] = '';
					}
					$t_output_array['status'] = 'success';
				}
				else
				{
					$t_output_array['status'] = 'error';
				}
				break;
				
			case 'download_withdrawal_pdf':
				$coo_withdrawal_control = MainFactory::create_object('WithdrawalControl');
				$t_file = $coo_withdrawal_control->get_withdrawal();
				xtc_db_close();
				
				header("Content-Type: application/force-download");
				header("Content-Type: application/octet-stream");
				header('Content-Disposition: attachment; filename="' . $t_file['name'] . '"');
				header("Content-Transfer-Encoding: binary");
				header('Expires: 0');
				header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
				header('Pragma: public');
				readfile($t_file['path']);
				exit(0);
				break;
			
			case 'download_withdrawal_pdf_form':
				$coo_withdrawal_control = MainFactory::create_object('WithdrawalControl');
				$t_file = $coo_withdrawal_control->get_withdrawal_form();
				xtc_db_close();
				
				header("Content-Type: application/force-download");
				header("Content-Type: application/octet-stream");
				header('Content-Disposition: attachment; filename="' . $t_file['name'] . '"');
				header("Content-Transfer-Encoding: binary");
				header('Expires: 0');
				header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
				header('Pragma: public');
				readfile($t_file['path']);
				exit(0);
				break;
			
			case 'download_conditions_of_use_pdf':
				$coo_withdrawal_control = MainFactory::create_object('WithdrawalControl');
				$t_file = $coo_withdrawal_control->get_conditions_of_use();
				xtc_db_close();
				
				header("Content-Type: application/force-download");
				header("Content-Type: application/octet-stream");
				header('Content-Disposition: attachment; filename="' . $t_file['name'] . '"');
				header("Content-Transfer-Encoding: binary");
				header('Expires: 0');
				header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
				header('Pragma: public');
				readfile($t_file['path']);
				exit(0);
				break;
				
			default:
				trigger_error('t_action_request not found: '. htmlentities($t_action_request), E_USER_WARNING);
				return false;
		}

		if($t_enable_json_output)
		{
			$coo_json = MainFactory::create_object('GMJSON', array(false));
			$t_output_json = $coo_json->encode($t_output_array);

			$this->v_output_buffer = $t_output_json;
		}
		return true;
	}
}