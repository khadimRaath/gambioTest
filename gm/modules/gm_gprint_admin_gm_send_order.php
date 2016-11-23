<?php
/* --------------------------------------------------------------
   gm_gprint_admin_gm_send_order.php 2016-06-02
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

require_once(DIR_FS_CATALOG . 'gm/modules/gm_gprint_tables.php');
require_once(DIR_FS_CATALOG . 'gm/classes/GMGPrintConfiguration.php');
require_once(DIR_FS_CATALOG . 'gm/classes/GMGPrintContentManager.php');
    	
$coo_gm_gprint_content_manager = new GMGPrintContentManager();
$coo_gm_gprint_order_data = $coo_gm_gprint_content_manager->get_content_by_orders_id($t_row['orders_id']);

if(isset($coo_gm_gprint_order_data[$t_order_data_values['orders_products_id']]))
{
	if(strpos_wrapper($t_attributes_data, '<br />: <br />') > 0 || strpos_wrapper($t_attributes_data, '<br />:<br />') > 0)
	{
		$t_attributes_data = str_replace('<br />: <br />', '', $t_attributes_data);
		$t_attributes_data = str_replace('<br />:<br />', '', $t_attributes_data);

		if(substr_wrapper($t_attributes_model, -6) == '<br />')
		{
			$t_attributes_model = substr_wrapper($t_attributes_model, 0, -6);
		}
	}
	elseif(strpos_wrapper($t_attributes_data, '<br />: <br />') === 0 || strpos_wrapper($t_attributes_data, '<br />:<br />') === 0)
	{
		$t_attributes_data = str_replace('<br />: <br />', '', $t_attributes_data);
		$t_attributes_data = str_replace('<br />:<br />', '', $t_attributes_data);

		if(substr_wrapper($t_attributes_model, 0, 12) == '<br /><br />')
		{
			$t_attributes_model = substr_wrapper($t_attributes_model, 0, 6);
		}
	}
	elseif(strrpos_wrapper($t_attributes_data, '<br />:') - strlen_wrapper($t_attributes_data) <= -7)
	{
		$t_attributes_data = substr_wrapper($t_attributes_data, 0, strrpos_wrapper($t_attributes_data, '<br />:'));

		if(substr_wrapper($t_attributes_model, -6) == '<br />')
		{
			$t_attributes_model = substr_wrapper($t_attributes_model, 0, -6);
		}
	}
}

$t_attributes_data = str_replace(':', ': ', $t_attributes_data);
$t_attributes_data = str_replace(':  ', ': ', $t_attributes_data);

for($i = 0; $i < count($coo_gm_gprint_order_data[$t_order_data_values['orders_products_id']]); $i++)
{
	$t_attributes_data .= '<br />' . $coo_gm_gprint_order_data[$t_order_data_values['orders_products_id']][$i]['NAME'] . ': ' . $coo_gm_gprint_order_data[$t_order_data_values['orders_products_id']][$i]['VALUE'];
	$t_attributes_model .= '<br />';
}
