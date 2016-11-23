<?php
/* --------------------------------------------------------------
   gm_gprint_order.php 2014-02-06 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

require_once(DIR_FS_CATALOG . 'gm/modules/gm_gprint_tables.php');
require_once(DIR_FS_CATALOG . 'gm/classes/GMGPrintConfiguration.php');
require_once(DIR_FS_CATALOG . 'gm/classes/GMGPrintContentManager.php');

$coo_gm_gprint_content_manager = new GMGPrintContentManager();
$coo_gm_gprint_order_data = $coo_gm_gprint_content_manager->get_content_by_orders_id($oID);

if(!empty($coo_gm_gprint_order_data))
{
	if(strpos_wrapper($attributes_data, '<br />: <br />') > 0 || strpos_wrapper($attributes_data, '<br />:<br />') > 0)
	{
		$attributes_data = str_replace('<br />: <br />', '', $attributes_data);
		$attributes_data = str_replace('<br />:<br />', '', $attributes_data);

		if(substr_wrapper($attributes_model, -6) == '<br />')
		{
			$attributes_model = substr_wrapper($attributes_model, 0, -6);
		}
	}
	elseif(strpos_wrapper($attributes_data, '<br />: <br />') === 0 || strpos_wrapper($attributes_data, '<br />:<br />') === 0)
	{
		$attributes_data = str_replace('<br />: <br />', '', $attributes_data);
		$attributes_data = str_replace('<br />:<br />', '', $attributes_data);

		if(substr_wrapper($attributes_model, 0, 12) == '<br /><br />')
		{
			$attributes_model = substr_wrapper($attributes_model, 6);
		}
	}
	elseif(strrpos_wrapper($attributes_data, '<br />:') !== false && strrpos_wrapper($attributes_data, '<br />:') - strlen_wrapper($attributes_data) <= -7)
	{
		$attributes_data = substr_wrapper($attributes_data, 0, strrpos_wrapper($attributes_data, '<br />:'));

		if(substr_wrapper($attributes_model, -6) == '<br />')
		{
			$attributes_model = substr_wrapper($attributes_model, 0, -6);
		}
	}

	$attributes_data = str_replace(':', ': ', $attributes_data);
	$attributes_data = str_replace(':  ', ': ', $attributes_data);

	for($i = 0; $i < count($coo_gm_gprint_order_data[$order_data_values['orders_products_id']]); $i++)
	{
		$attributes_data .= '<br />' . $coo_gm_gprint_order_data[$order_data_values['orders_products_id']][$i]['NAME'] . ': ' . $coo_gm_gprint_order_data[$order_data_values['orders_products_id']][$i]['VALUE'];
		$attributes_model .= '<br />';
	}
}
