<?php
/* --------------------------------------------------------------
   WithdrawalSource.inc.php 2014-05-22 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
 */

class WithdrawalSource
{
	public function get_withdrawals($p_offset = 0, $p_limit = 0, $p_order_id = 0)
	{
		$t_withdrawal_array = array();
		$t_offset = (int)$p_offset;
		$t_limit = (int)$p_limit;
		
		$t_limit_sql = '';
		
		if($t_limit !== 0)
		{
			$t_limit_sql = ' LIMIT ' . $t_offset . ', ' . $t_limit;
		}
		
		$t_where_sql = '';
		if(empty($p_order_id) === false && (int)$p_order_id > 0)
		{
			$t_where_sql = ' WHERE order_id LIKE "%' . (int)$p_order_id . '%" ';
		}
		
		$t_query = 'SELECT
						withdrawal_id
					FROM
						withdrawals
					' . $t_where_sql . '
					ORDER BY
						date_created DESC'
					. $t_limit_sql;
		$t_result = xtc_db_query($t_query);
		
		while($t_row = xtc_db_fetch_array($t_result))
		{
			$t_withdrawal_array[$t_row['withdrawal_id']] = MainFactory::create_object('WithdrawalModel', array($t_row['withdrawal_id']));
		}
		
		return $t_withdrawal_array;
	}
	
	/**
	 * @param int $p_order_id
	 *
	 * @return int withdrawals count
	 */
	public function get_withdrawals_count($p_order_id = 0)
	{
		$t_where_sql = '';
		if(empty($p_order_id) === false && (int)$p_order_id > 0)
		{
			$t_where_sql = ' WHERE order_id LIKE "%' . (int)$p_order_id . '%" ';
		}
		
		$t_query = 'SELECT
						COUNT(*) AS cnt
					FROM
						withdrawals
					' . $t_where_sql . '
					ORDER BY
						date_created DESC';
		$t_result = xtc_db_query($t_query);
		$t_row = xtc_db_fetch_array($t_result);
		
		return (int)$t_row['cnt'];
	}
	
	public function get_withdrawal($withdrawal_id)
	{
		$withdrawal_id = (int)$withdrawal_id;
		
		return MainFactory::create_object('WithdrawalModel', array($withdrawal_id));
	}


	/**
	 * @param $p_order_hash
	 *
	 * @return bool|Order
	 */
	public function get_order_by_hash($p_order_hash)
    {
        $t_query = 'SELECT
                    	orders_id
                  	FROM
                  		orders
                  	WHERE
                  		orders_hash = "' . xtc_db_input($p_order_hash) .'";';

        $t_result = xtc_db_query($t_query);

		if(xtc_db_num_rows($t_result) === 1)
		{
			$t_row = xtc_db_fetch_array($t_result);

			require_once DIR_FS_CATALOG . 'includes/classes/order.php';
			$coo_order = new order($t_row['orders_id']);

			return $coo_order;
		}

		return false;
	}
}