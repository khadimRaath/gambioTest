<?php
/* --------------------------------------------------------------
   OrderObjectServiceOverload.inc.php 2016-06-24
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class OrderObjectServiceOverload
 *
 * This sample demonstrates the overloading of the OrderObjectService class. After enabling this sample create
 * a new order from the shop frontend. Then head to the admin log page where there is a new debug log entry about
 * the creation of the new order item.
 *
 * @see OrderObjectService
 */
class OrderObjectServiceOverload extends OrderObjectServiceOverload_parent
{
	/**
	 * Overloaded "createOrderItemObject" method.
	 *
	 * This method will log the creation of the new OrderItem instance.
	 *
	 * @param StringType $name
	 *
	 * @return OrderItemInterface
	 */
	public function createOrderItemObject(StringType $name)
	{
		$logControl = LogControl::get_instance();
		$logControl->notice('OrderObjectService::createOrderItemObject >> Created new OrderItem instance with name = "'
		                    . $name->asString() . '"');
		
		return parent::createOrderItemObject($name);
	}
}
