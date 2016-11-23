<?php

/* --------------------------------------------------------------
   OrderItemProperty.inc.php 2015-10-28 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/


/**
 * Class StoredOrderItemProperty
 *
 * @category   System
 * @package    Order
 * @subpackage Entities
 */
class StoredOrderItemProperty extends OrderItemProperty implements StoredOrderItemAttributeInterface
{
	/**
	 * Order item attribute ID.
	 *
	 * @var int
	 */
	protected $orderItemAttributeID = 0;
	
	
	/**
	 * StoredOrderItemProperty constructor.
	 *
	 * @param IdType $orderItemAttributeId Order item attribute ID.
	 */
	public function __construct(IdType $orderItemAttributeId)
	{
		$this->orderItemAttributeID = $orderItemAttributeId->asInt();
	}
	
	
	/**
	 * Returns the ID of the stored order item attribute.
	 *
	 * @return int Order item attribute ID.
	 */
	public function getOrderItemAttributeId()
	{
		return $this->orderItemAttributeID;
	}
}