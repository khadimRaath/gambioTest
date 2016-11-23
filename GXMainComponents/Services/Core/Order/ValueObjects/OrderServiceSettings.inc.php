<?php
/* --------------------------------------------------------------
   OrderServiceSettings.inc.php 2016-01-20
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('OrderServiceSettingsInterface');

/**
 * Value Object
 *
 * Class OrderServiceSettings
 *
 * Represents the default settings of an order
 *
 * @category   System
 * @package    Order
 * @subpackage ValueObjects
 * @implements OrderServiceSettingsInterface
 */
class OrderServiceSettings implements OrderServiceSettingsInterface
{
	/**
	 * The default Id of an order status
	 * 
	 * @var int
	 */
	protected $defaultOrderStatusId;
	
	/**
	 * The default Id of a customer status
	 * 
	 * @var int
	 */
	protected $defaultCustomerStatusId;
	
	/**
	 * The default Id of a guest status id
	 * 
	 * @var int
	 */
	protected $defaultGuestStatusId;
	
	
	/**
	 * Constructor of the class CustomerServiceSettings
	 *
	 * Sets default order status id from constant
	 */
	public function __construct()
	{
		$this->defaultOrderStatusId    = (int)DEFAULT_ORDERS_STATUS_ID;
		$this->defaultCustomerStatusId = (int)DEFAULT_CUSTOMERS_STATUS_ID;
		$this->defaultGuestStatusId    = (int)DEFAULT_CUSTOMERS_STATUS_ID_GUEST;
	}
	
	
	/**
	 * Returns the default order status ID.
	 * 
	 * @return int Default order status ID.
	 */
	public function getDefaultOrderStatusId()
	{
		return $this->defaultOrderStatusId;
	}
	
	
	/**
	 * Returns the default customer status ID.
	 * 
	 * @return int Default customer status ID.
	 */
	public function getDefaultCustomerStatusId()
	{
		return $this->defaultCustomerStatusId;
	}
	
	
	/**
	 * Returns the default guest status ID.
	 * 
	 * @return int Default guest status ID
	 */
	public function getDefaultGuestStatusId()
	{
		return $this->defaultGuestStatusId;
	}
}
 