<?php

/* --------------------------------------------------------------
   StoredOrderTotal.inc.php 2015-10-27 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class StoredOrderTotal
 *
 * @category   System
 * @package    Order
 * @subpackage Entities
 */
class StoredOrderTotal extends OrderTotal implements StoredOrderTotalInterface
{
	/**
	 * Order Total ID.
	 *
	 * @var int
	 */
	protected $orderTotalId;
	
	
	/**
	 * StoredOrderTotal constructor.
	 *
	 * @param IdType $orderTotalId Order total ID.
	 */
	public function __construct(IdType $orderTotalId)
	{
		$this->orderTotalId = $orderTotalId->asInt();
	}
	
	
	/**
	 * Returns the ID of the stored order total.
	 *
	 * @return int Stored order total ID.
	 */
	public function getOrderTotalId()
	{
		return $this->orderTotalId;
	}
}