<?php

/* --------------------------------------------------------------
   StoredOrderItemAttribute.inc.php 2015-10-27 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class StoredOrderItemAttribute
 *
 * @category   System
 * @package    Order
 * @subpackage Entities
 */
class StoredOrderItemAttribute extends OrderItemAttribute implements StoredOrderItemAttributeInterface
{
	/**
	 * Order item attribute ID.
	 * @var int
	 */
	protected $orderItemAttributeId;
	
	
	/**
	 * StoredOrderItemAttribute constructor.
	 *
	 * @param IdType $orderItemAttributeId IDd of order item attribute.
	 */
	public function __construct(IdType $orderItemAttributeId)
	{
		$this->orderItemAttributeId = $orderItemAttributeId->asInt();
	}
	
	
	/**
	 * Returns the ID of the stored order item attribute.
	 *
	 * @return int Order item attribute ID.
	 */
	public function getOrderItemAttributeId()
	{
		return $this->orderItemAttributeId;
	}
}