<?php
/* --------------------------------------------------------------
   OrderOverload.inc.php 2016-06-24
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class OrderOverload
 *
 * This example overload class demonstrates the overloading of the order_ORIGIN class. After enabling this
 * sample create a new order from the shop frontend. It will modify the first order item information and
 * the order summary entry.
 */
class OrderOverload extends OrderOverload_parent
{
	/**
	 * Overloaded Class Constructor
	 *
	 * @param string $orderId (optional)
	 */
	public function __construct($orderId = '')
	{
		parent::__construct($orderId);
		
		$style = 'text-align: center;padding: 25px;margin: 50px  35px;background: #D9EDF7;color: #3187CC;';
		
		echo '
			<div style="' . $style . '">
				<h4>order_ORIGIN overload is used!</h4>
				<p>
					This sample overload will modify the first order item and order total of every new order. 
				</p>
			</div>
		';
	}
	
	
	/**
	 * Overloaded "getOrderData" method.
	 *
	 * This method will change the first order item by setting a new product model name and product price.
	 *
	 * @param int $orderId
	 *
	 * @return array
	 */
	public function getOrderData($orderId)
	{
		$orderData = parent::getOrderData($orderId);
		
		$orderData[0]['PRODUCTS_MODEL']        = 'overload_example_order product model';
		$orderData[0]['PRODUCTS_PRICE']        = '1.000.000,00 EUR';
		$orderData[0]['PRODUCTS_SINGLE_PRICE'] = '12345,00 EUR';
		
		return $orderData;
	}
	
	
	/**
	 * Overloaded "getTotalData" method.
	 *
	 * This method will change the last order total (which is usually the sum value) by setting a random
	 * sum value for the order.
	 *
	 * This sample manipulate the last total item (which is usually the sum value) and sets a random value.
	 *
	 * @param int $orderId
	 *
	 * @return array
	 */
	public function getTotalData($orderId)
	{
		$orderTotalData = parent::getTotalData($orderId);
		
		$randomValue = (float)(mt_rand(1, 100) . '.' . mt_rand(0, 99));
		
		$orderTotalData['data'][count($orderTotalData['data']) - 1]['TEXT'] = '<b>' . $randomValue . ' EUR</b>';
		$orderTotalData['total'] = $randomValue;
		
		return $orderTotalData;
	}
}
