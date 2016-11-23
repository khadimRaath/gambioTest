<?php
/* --------------------------------------------------------------
  YoochooseCheckoutSuccessExtender.inc.php 2014-05-07 gm
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2014 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
 */

class YoochooseCheckoutSuccessExtender extends YoochooseCheckoutSuccessExtender_parent
{
	public function proceed()
	{
		parent::proceed();

		if(defined('YOOCHOOSE_ACTIVE') && YOOCHOOSE_ACTIVE)
		{
			include_once(DIR_WS_INCLUDES . 'yoochoose/recommendations.php');
			include_once(DIR_WS_INCLUDES . 'yoochoose/functions.php');
			
			$t_query = 'SELECT 
							products_id, 
							products_quantity, 
							products_price, 
							products_tax 
						FROM ' . TABLE_ORDERS_PRODUCTS . '
						WHERE orders_id = "' . (int)$this->v_data_array['orders_id'] . '"
						ORDER BY orders_products_id';
			$t_last_orders_products_query = xtc_db_query($t_query);
			$t_html = '';
			
			while($t_last_orders_products_array = xtc_db_fetch_array($t_last_orders_products_query))
			{
				$t_tracking_url = getTrackingURL('buy', $t_last_orders_products_array);

				$t_html .= '<img src="' . $t_tracking_url . '" width="0" height="0" alt="" />';
			}
			
			$t_html = '<!-- Yoochoose tracking -->' . $t_html . "\n";

			$this->v_output_buffer['MODULE_yoochoose_checkout_tracking'] = $t_html;
		}
	}
}