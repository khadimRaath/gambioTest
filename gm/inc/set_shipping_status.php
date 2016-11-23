<?php
/* --------------------------------------------------------------
   set_shipping_status.php 2013-12-05 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2013 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/*
 * set the shipping status based on the number of items
 * 
 * @param int $p_product_id Product id
 * @param int $p_combi_id Combi id (optional)
 * @return bool true:OK
 */
function set_shipping_status($p_product_id, $p_combi_id = false)
{
	// check if the configuration is set to change the shipping status
	$auto_shipping_status = gm_get_conf('GM_AUTO_SHIPPING_STATUS');
	if($auto_shipping_status == 'false' || ACTIVATE_SHIPPING_STATUS == 'false' || STOCK_LIMITED == 'false' || STOCK_CHECK == 'false')
	{
		return true;
	}
	
	// get a product ID from a product ID with attributes
	$c_product_id = false;
	$t_product_id = (int)xtc_get_prid($p_product_id);
	$c_product_quantity = 0;
	$c_product_shipping_status_id = 0;
	
	$c_combi_id = false;
	$t_combi_id = false;
	
	$t_use_combis_shipping_time = false;
	$t_use_combis_quantity = false;
	
	$t_query = 'SELECT 
					products_shippingtime,
					products_quantity,
					use_properties_combis_quantity,
					use_properties_combis_shipping_time
				FROM
					' . TABLE_PRODUCTS . '
				WHERE
					products_id = "' . $t_product_id . '"';
	
	$t_product_query = xtc_db_query($t_query);
	if(xtc_db_num_rows($t_product_query) == 1)
	{
		$t_row = xtc_db_fetch_array($t_product_query);
		$c_product_id = $t_product_id;
		$c_product_quantity = $t_row['products_quantity'];
		$c_product_shipping_status_id = $t_row['products_shippingtime'];
		$t_use_combis_shipping_time = $t_row['use_properties_combis_shipping_time'];
		$t_use_combis_quantity = $t_row['use_properties_combis_quantity'];
	}
	
	if((int)$p_combi_id > 0)
	{
		$t_combi_id = (int)$p_combi_id;
		$t_query = 'SELECT 
						combi_shipping_status_id,
						combi_quantity
					FROM
						products_properties_combis
					WHERE 
						products_properties_combis_id = "' . $t_combi_id . '"';
		$t_combi_query = xtc_db_query($t_query);
		if(xtc_db_num_rows($t_combi_query) == 1)
		{
			$t_row = xtc_db_fetch_array($t_combi_query);
			$c_combi_id = $t_combi_id;
			
			if($t_use_combis_shipping_time == 0 && (($t_use_combis_quantity == 0 && ATTRIBUTE_STOCK_CHECK == 'true') || $t_use_combis_quantity == 2))
			{
				return true;
			}
			
			if($t_use_combis_shipping_time == 1 && (($t_use_combis_quantity == 0 && ATTRIBUTE_STOCK_CHECK == 'false') || $t_use_combis_quantity == 1))
			{
				return true;
			}
			if($t_use_combis_quantity == 3)
			{
				return true;
			}
			
			if($t_use_combis_shipping_time == 1)
			{
				$c_product_shipping_status_id = $t_row['combi_shipping_status_id'];
				$c_product_quantity = $t_row['combi_quantity'];
			}
		}
	}
	
	if($c_product_id != false)
	{
		// get the next shipping status
		$t_query = 'SELECT 
						shipping_status_id
					FROM 
						' . TABLE_SHIPPING_STATUS . '
					WHERE 
						shipping_quantity >= "' . xtc_db_input($c_product_quantity) . '"
					ORDER BY 
						shipping_quantity ASC
					LIMIT 1';
		$t_new_shipping_status_query = xtc_db_query($t_query);
		if(xtc_db_num_rows($t_new_shipping_status_query) == 1)
		{
			$t_new_shipping_status_values = xtc_db_fetch_array($t_new_shipping_status_query);
			$t_shipping_status_id_new = $t_new_shipping_status_values['shipping_status_id'];

			// update product shipping status
			if($t_shipping_status_id_new != $c_product_shipping_status_id)
			{
				if($c_combi_id != false && $t_use_combis_shipping_time == 1)
				{
					$t_query = 'UPDATE
									products_properties_combis
								SET
									combi_shipping_status_id = "' . xtc_db_input($t_shipping_status_id_new) . '"
								WHERE
									products_properties_combis_id = "' . $c_combi_id . '"';
				}
				else
				{
					$t_query = 'UPDATE
									' . TABLE_PRODUCTS . '
								SET
									products_shippingtime = "' . xtc_db_input($t_shipping_status_id_new) . '"
								WHERE
									products_id = "' . $c_product_id . '"';
				}
				xtc_db_query($t_query);
			}
		}
	}
	return true;
}