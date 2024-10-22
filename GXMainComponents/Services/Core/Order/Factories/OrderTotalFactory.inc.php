<?php

/* --------------------------------------------------------------
   OrderTotalFactory.inc.php 2015-11-03 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('OrderTotalFactoryInterface');

/**
 * Class OrderTotalFactory
 *
 * @category   System
 * @package    Order
 * @subpackage Factories
 */
class OrderTotalFactory implements OrderTotalFactoryInterface
{
	/**
	 * Creates an OrderTotal object.
	 *
	 * @param StringType  $title     The title of the order total item.
	 * @param DecimalType $value     The decimal value of the order total item.
	 * @param StringType  $valueText (optional) The formatted value as text with currency and so on.
	 * @param StringType  $class     (optional) The used order total class (e.g. ot_subtotal).
	 * @param IntType     $sortOrder (optional) The sort order of the order total item.
	 *
	 * @return OrderTotal New order total object.
	 */
	public function createOrderTotal(StringType $title,
	                                 DecimalType $value,
	                                 StringType $valueText = null,
	                                 StringType $class = null,
	                                 IntType $sortOrder = null)
	{
		return MainFactory::create('OrderTotal', $title, $value, $valueText, $class, $sortOrder);
	}
	
	
	/**
	 * Create a StoredOrderTotal object.
	 *
	 * @param IdType $orderTotalId ID of the order total item.
	 *
	 * @return StoredOrderTotal New stored order total object.
	 */
	public function createStoredOrderTotal(IdType $orderTotalId)
	{
		return MainFactory::create('StoredOrderTotal', $orderTotalId);
	}
}