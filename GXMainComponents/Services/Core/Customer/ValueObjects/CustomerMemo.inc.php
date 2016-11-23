<?php

/* --------------------------------------------------------------
   CustomerMemo.inc.php 2016-07-13
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class CustomerMemo
 * 
 * @category   System
 * @package    Customer
 * @subpackage ValueObjects
 */
class CustomerMemo
{
	/**
	 * @var int
	 */
	protected $customerId;
	
	/**
	 * @var string
	 */
	protected $title;

	/**
	 * @var string
	 */
	protected $text;

	/**
	 * @var DateTime
	 */
	protected $creationDate;

	/**
	 * @var int
	 */
	protected $posterId;
	
	
	/**
	 * CustomerMemo constructor.
	 *
	 * @param \IdType     $customerId
	 * @param \StringType $title
	 * @param \StringType $text
	 * @param \DateTime   $creationDate
	 * @param \IdType     $posterId
	 *
	 */
	public function __construct(IdType $customerId,
	                            StringType $title,
	                            StringType $text,
	                            DateTime $creationDate,
	                            IdType $posterId)
	{
		$this->customerId   = $customerId->asInt();
		$this->title        = $title->asString();
		$this->text         = $text->asString();
		$this->creationDate = $creationDate;
		$this->posterId     = $posterId->asInt();
	}


	/**
	 * @return int
	 */
	public function getCustomerId()
	{
		return $this->customerId;
	}


	/**
	 * @return string
	 */
	public function getTitle()
	{
		return $this->title;
	}


	/**
	 * @return string
	 */
	public function getText()
	{
		return $this->text;
	}


	/**
	 * @return DateTime
	 */
	public function getCreationDate()
	{
		return $this->creationDate;
	}


	/**
	 * @return int
	 */
	public function getPosterId()
	{
		return $this->posterId;
	}


	/**
	 * Converted instances returns a string with all properties.
	 * They are separated with an unix linebreak (\n).
	 *
	 * @return string
	 */
	public function __toString()
	{
		return $this->title . "\n" . $this->text . "\n" . $this->creationDate->format('Y-m-d H:i:s');
	}
}