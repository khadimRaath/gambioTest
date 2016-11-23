<?php
/* --------------------------------------------------------------
   gm_get_order_status_list.inc.php 2011-03-29 gambio
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2011 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/
	
	/*
	*	function to get a list of all available order status
	*	@return array
	*/
	function gm_get_order_status_list()
	{
		$t_orders_status_list = false;

		$t_query = xtc_db_query("
								SELECT 
									orders_status_id, 
									orders_status_name 
								FROM " . 
									TABLE_ORDERS_STATUS . " 
								WHERE 
									language_id = '" . $_SESSION['languages_id'] . "' 
								ORDER BY 
									orders_status_id
		");

		if((int)xtc_db_num_rows($t_query) > 0)
		{	
			$t_orders_status_list = array();
			
			while($t_row = xtc_db_fetch_array($t_query)) 
			{
				$t_orders_status_list[] = array(
													'id'	=> $t_row['orders_status_id'], 
													'text'	=> $t_row['orders_status_name']
				);
			}
		}

		return $t_orders_status_list;
	}
?>