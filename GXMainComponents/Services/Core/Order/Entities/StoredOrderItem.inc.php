<?php

/* --------------------------------------------------------------
   StoredOrderItem.php 2016-03-15
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class StoredOrderItem
 *
 * @category   System
 * @package    Order
 * @subpackage Entities
 */
class StoredOrderItem extends OrderItem implements StoredOrderItemInterface, AddonValueContainerInterface
{
	/**
	 * ID
	 *
	 * @var int
	 */
	protected $id;


	/**
	 * StoredOrderItem constructor
	 *
	 * @param IdType $orderItemId Order item ID.
	 */
	public function __construct(IdType $orderItemId)
	{
		$this->id = $orderItemId->asInt();
		
		// Set empty download information order item collection.
		$this->downloadInformation = MainFactory::create('OrderItemDownloadInformationCollection');

		// Set addon values collection.
		// Note, that there is no setter method for assign the addonValues collection.
		$addonValues       = MainFactory::create('EditableKeyValueCollection', array());
		$this->addonValues = $addonValues;
	}


	/**
	 * Returns ID of the stored order item ID.
	 *
	 * @return int Order item ID.
	 */
	public function getOrderItemId()
	{
		return $this->id;
	}


	/**
	 * Returns the addon value container ID.
	 *
	 * @return int Addon value container ID.
	 */
	public function getAddonValueContainerId()
	{
		return $this->getOrderItemId();
	}
}