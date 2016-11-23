<?php

/* --------------------------------------------------------------
   OrderStatusStyles.inc.php 2016-05-06
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class OrderStatusStyles
 *
 * This class works in cooperation with the "admin/html/content/layouts/main/partial/order_status_styles.html" to
 * provide the dynamic styling of the order status labels.
 *
 * @category   System
 * @package    Extensions
 * @subpackage Orders
 */
class OrderStatusStyles
{
	/**
	 * @var CI_DB_query_builder
	 */
	protected $db;
	
	
	/**
	 * OrderStatusStyles constructor.
	 *
	 * @param CI_DB_query_builder $db
	 */
	public function __construct(CI_DB_query_builder $db)
	{
		$this->db = $db;
	}
	
	
	/**
	 * Get the order status styles for the "order_status_styles.html" partial.
	 *
	 * Include the order_status_styles.html in your template and pass in a variable with the "order_status_styles"
	 * name. The template will output a <style> tag with the colors for each status.
	 *
	 * @return array
	 *
	 * @throws UnexpectedValueException
	 * @throws InvalidArgumentException
	 */
	public function getStyles()
	{
		$rows = $this->db->select('orders_status_id, color')
		                 ->from('orders_status')
		                 ->group_by('orders_status_id, color')
		                 ->get()
		                 ->result_array();
		
		$orderStatusStyles = array();
		
		foreach($rows as $row)
		{
			$orderStatusStyles[] = array(
				'id'               => $row['orders_status_id'],
				'color'            => ColorHelper::getLuminance(new StringType($row['color'])) > 143 ? '#333' : '#FFF',
				'background_color' => '#' . $row['color']
			);
		}
		
		return $orderStatusStyles;
	}
}