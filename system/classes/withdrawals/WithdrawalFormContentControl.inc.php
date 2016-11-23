<?php
/* --------------------------------------------------------------
   WithdrawalFormContentControl.inc.php 2014-06-27 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
  
   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

class WithdrawalFormContentControl extends DataProcessing
{
	public function proceed()
	{
		if(gm_get_conf('WITHDRAWAL_WEBFORM_ACTIVE') != '1' && $_SESSION['customers_status']['customers_status_id'] !== '0')
		{
			$this->set_redirect_url(xtc_href_link('index.php', '', 'SSL'));
			return true;
		}
		
		$coo_withdrawal_control = MainFactory::create_object('WithdrawalControl');

		if(isset($_SESSION['customers_status']['customers_status_id']) 
			&& $_SESSION['customers_status']['customers_status_id'] === '0' 
			&& isset($this->v_data_array['GET']['order_id']) 
			&& trim($this->v_data_array['GET']['order_id']) != '')
		{
			$t_query = 'SELECT
							orders_hash
						FROM
							orders
						WHERE
							orders_id = "' . xtc_db_input($this->v_data_array['GET']['order_id']) . '"';
			$t_result = xtc_db_query($t_query);
			if(xtc_db_num_rows($t_result))
			{
				$t_row = xtc_db_fetch_array($t_result);
				if(trim($t_row['orders_hash']) == '')
				{
					$t_order_hash = md5(time() + mt_rand());
					$sql_data_array = array('orders_hash' => $t_order_hash);

					xtc_db_perform(TABLE_ORDERS, $sql_data_array, 'update', 'orders_id = "' . xtc_db_input($this->v_data_array['GET']['order_id']) . '"');

					$coo_withdrawal_control->set_order_hash($t_order_hash);
				}
				else
				{
					$coo_withdrawal_control->set_order_hash($t_row['orders_hash']);
				}
			}
		}
		elseif(isset($this->v_data_array['GET']['order']) && trim($this->v_data_array['GET']['order']) != '' && (gm_get_conf('WITHDRAWAL_WEBFORM_ACTIVE') == '1' || $_SESSION['customers_status']['customers_status_id'] === '0'))
		{
			$coo_withdrawal_control->set_order_hash($this->v_data_array['GET']['order']);
			$coo_withdrawal_control->set_customer_status_id();
		}

		if(isset($_SESSION['customers_status']['customers_status_id']) && $_SESSION['customers_status']['customers_status_id'] === '0')
		{
			$coo_withdrawal_control->set_customer_status_id((int)$_SESSION['customers_status']['customers_status_id']);	
		}

		$t_withdrawal_data_array = array();
		if(isset($this->v_data_array['POST']['withdrawal_data']))
		{
			$t_withdrawal_data_array = $this->v_data_array['POST']['withdrawal_data'];
			$coo_withdrawal_control->save_withdrawal($t_withdrawal_data_array);
		}

		$t_main_content = $coo_withdrawal_control->get_template('form', $f_withdrawal_data_array);

		$smarty->assign('language', $_SESSION['language']);
		$smarty->assign('main_content', $t_main_content);
		$smarty->caching = 0;
		if(!defined(RM))
		{
			$smarty->loadFilter('output', 'note');
		}
		$smarty->display(CURRENT_TEMPLATE . '/index.html');
		
		
		
		
		
		
		$coo_withdrawal_form_content = MainFactory::create_object('WithdrawalFormContentView');
		$this->v_output_buffer = $coo_withdrawal_form_content->get_html();
		
		return true;
	}
}