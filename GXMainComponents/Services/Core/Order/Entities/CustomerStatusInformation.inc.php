<?php

/* --------------------------------------------------------------
   CustomerStatusInformation.inc.php 2016-01-20
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class CustomerStatusInformation
 *
 * @category   System
 * @package    Order
 * @subpackage Entities
 */
class CustomerStatusInformation
{
	/**
	 * Customer status ID.
	 * 
	 * @var int
	 */
	protected $statusId;
	
	/**
	 * Status ID.
	 * 
	 * @var string
	 */
	protected $statusName;
	
	/**
	 * Status image.
	 * 
	 * @var string
	 */
	protected $statusImage;
	
	/**
	 * Status discount.
	 * 
	 * @var double
	 */
	protected $statusDiscount;
	
	/**
	 * Is customer a guest?
	 * 
	 * @var bool
	 */
	protected $isGuest;
	
	
	/**
	 * CustomerStatusInformation Constructor
	 *
	 * @param IdType           $statusId       Status ID.
	 * @param StringType|null  $statusName     Status name.
	 * @param StringType|null  $statusImage    Status image.
	 * @param DecimalType|null $statusDiscount Status discount.
	 * @param BoolType|null    $isGuest        Is customer a guest?
	 */
	public function __construct(IdType $statusId,
	                            StringType $statusName = null,
	                            StringType $statusImage = null,
	                            DecimalType $statusDiscount = null,
	                            BoolType $isGuest = null)
	{
		$this->statusId       = $statusId->asInt();
		$this->statusName     = ($statusName !== null) ? $statusName->asString() : '';
		$this->statusImage    = ($statusImage !== null) ? $statusImage->asString() : '';
		$this->statusDiscount = ($statusDiscount !== null) ? $statusDiscount->asDecimal() : 0.0;
		$this->isGuest        = ($isGuest !== null) ? $isGuest->asBool() : true;
	}
	
	
	/**
	 * Returns the status ID.
	 *
	 * @return int Status ID.
	 */
	public function getStatusId()
	{
		return $this->statusId;
	}
	
	
	/**
	 * Returns the status name.
	 *
	 * @return string Status name.
	 */
	public function getStatusName()
	{
		return $this->statusName;
	}
	
	
	/**
	 * Returns the status image.
	 *
	 * @return string Status image.
	 */
	public function getStatusImage()
	{
		return $this->statusImage;
	}
	
	
	/**
	 * Returns status discount.
	 *
	 * @return float Status discount.
	 */
	public function getStatusDiscount()
	{
		return $this->statusDiscount;
	}
	
	
	/**
	 * Checks if customer is a guest.
	 *
	 * @return bool Is customer a guest?
	 */
	public function isGuest()
	{
		return $this->isGuest;
	}
}