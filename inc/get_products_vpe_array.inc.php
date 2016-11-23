<?php
/* --------------------------------------------------------------
   get_products_vpe_array.inc.php 2012-07-23 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2012 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

require_once(DIR_FS_INC . 'xtc_get_vpe_name.inc.php');
require_once(DIR_FS_INC . 'xtc_get_prid.inc.php');

function get_products_vpe_array($p_products_id, $p_price = false, $p_options_values_array = array(), $p_combis_id = 0)
{
	$t_vpe_price_array = array();
	
	$c_products_id = (int)xtc_get_prid($p_products_id);
	
	$c_options_values_array = (array)$p_options_values_array;
	
	// if p_products_id contains attributes data and c_options_values_array is empty, write data into c_options_values_array
	if(strpos($p_products_id, '{') !== false && empty($c_options_values_array))
	{
		$t_start_pos = strpos($p_products_id, '{');
		$t_end_pos = strlen($p_products_id);
		if(strpos($p_products_id, 'x') !== false)
		{
			$t_end_pos = strpos($p_products_id, 'x')-1;
		}
		
		$t_attributes_string = substr($p_products_id, $t_start_pos, $t_end_pos);
		
		$t_attributes_array = explode('{', str_replace('}', '{', $t_attributes_string));
		
		for($i = 1; $i < count($t_attributes_array); $i = $i+2)
		{
			$c_options_values_array[$t_attributes_array[$i]] = $t_attributes_array[$i+1];
		}
	}
	
	$c_combis_id = (int)$p_combis_id;
	// if p_products_id contains combis_id and c_combis_id is empty, write combis_id into c_combis_id
	if($c_combis_id == 0 && strpos($p_products_id, 'x') !== false)
	{
		$c_combis_id = (int)substr($p_products_id, strpos($p_products_id, 'x')+1);		
	}
	
	$coo_xtcPrice = new xtcPrice($_SESSION['currency'], $_SESSION['customers_status']['customers_status_id']);
	$coo_product = new product($c_products_id);
	
	if(is_numeric($p_price))
	{
		$c_price = (double)$p_price;
	}
	else // get products price by products_id if price parameter p_price is not set 
	{
		$c_price = (double)$coo_xtcPrice->xtcGetPrice($coo_product->data['products_id'], false, 1, $coo_product->data['products_tax_class_id'], $coo_product->data['products_price'], 1);
	}	
	
	$t_vpe_array = array();
	
	// get products vpe data
	if(!empty($coo_product->data['products_vpe']) && (double)$coo_product->data['products_vpe_value'] > 0 && $coo_product->data['products_vpe_status'] == '1')
	{
		$t_vpe_array['products_vpe_id'] = $coo_product->data['products_vpe'];
		$t_vpe_array['vpe_value'] = (double)$coo_product->data['products_vpe_value'];
	}
			
	// get products attributes vpe data (overwriting products vpe data)
	// key: options_id
	// value: options_value_id
	foreach($c_options_values_array AS $t_options_id => $t_options_values_id)
	{
		$c_options_id = (int)$t_options_id;
		$c_options_values_id = (int)$t_options_values_id;

		$t_sql = "SELECT
						products_vpe_id,
						gm_vpe_value AS vpe_value
					FROM
						" . TABLE_PRODUCTS_ATTRIBUTES . "
					WHERE
						products_id = '" . $c_products_id . "' AND
						options_id = '" . $c_options_id . "' AND
						options_values_id = '" . $c_options_values_id . "' AND
						products_vpe_id > 0 AND
						gm_vpe_value > 0
					LIMIT 1";

		$t_result = xtc_db_query($t_sql);

		if(xtc_db_num_rows($t_result) == 1)
		{
			$t_vpe_array = xtc_db_fetch_array($t_result);
		}
	}
	
	// get properties vpe data (overwriting products attributes vpe data)
	if($c_combis_id > 0)
	{
		$coo_properties_data_agent = MainFactory::create_object('PropertiesDataAgent');
		$t_properties_vpe_array = $coo_properties_data_agent->get_properties_combis_vpe_details($c_combis_id, $_SESSION['languages_id']);
		
		if((double)$t_properties_vpe_array['vpe_value'] > 0)
		{
			$t_vpe_array = $t_properties_vpe_array;
		}
	}
	
	// calculate vpe price and set vpe text
	if(!empty($t_vpe_array) && $c_price > 0 && (double)$t_vpe_array['vpe_value'] > 0)
	{
		$t_vpe_name = xtc_get_vpe_name($t_vpe_array['products_vpe_id']);
		
		$t_vpe_price_array = array();
		$t_vpe_price_array['products_vpe_id'] = $t_vpe_array['products_vpe_id'];
		$t_vpe_price_array['vpe_value'] = (double)$t_vpe_array['vpe_value'];
		$t_vpe_price_array['vpe_price'] = $c_price * (1 / (double)$t_vpe_array['vpe_value']);
		$t_vpe_price_array['vpe_text'] = $coo_xtcPrice->xtcFormat($c_price * (1 / (double)$t_vpe_array['vpe_value']), true) . TXT_PER . $t_vpe_name;
		if(!isset($t_vpe_price_array['products_vpe_name']))
		{
			$t_vpe_price_array['products_vpe_name'] = $t_vpe_name;
		}
	}
	
	
	return $t_vpe_price_array;
}
?>