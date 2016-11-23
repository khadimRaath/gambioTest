<?php
/* --------------------------------------------------------------
   StoreGXCustomizerOrder.inc.php 2015-11-18
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class StoreGXCustomizerOrder
 */
class StoreGXCustomizerOrder extends StoreGXCustomizerOrder_parent
{
	/**
	 * Store GX-Customizer data of an order item 
	 *
	 * @param IdType             $orderId   Order id.
	 * @param OrderItemInterface $orderItem Order item to add.
	 *
	 * @return int Id of the StoredOrderItem
	 */
	public function addToOrder(IdType $orderId, OrderItemInterface $orderItem)
	{
		$storedOrderItemId = parent::addToOrder($orderId, $orderItem);
		
		$addonValuesArray = $orderItem->getAddonValues()->getArray();
		
		if(isset($addonValuesArray['identifier']))
		{
			$orderManager = new GMGPrintOrderManager();
			$orderManager->save($orderItem->getAddonValue(new StringType('identifier')), $storedOrderItemId);
		}
		
		return $storedOrderItemId;
	}
}