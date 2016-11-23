<?php
/* --------------------------------------------------------------
   CustomerHouseNumber.inc.php 2016-04-08
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('CustomerHouseNumberInterface');

/**
 * Value Object
 *
 * Class CustomerHouseNumber
 *
 * Represents a house number
 *
 * @category   System
 * @package    Customer
 * @subpackage ValueObjects
 * @implements CustomerHouseNumberInterface
 */
class CustomerHouseNumber implements CustomerHouseNumberInterface
{
	/**
	 * House number.
	 * @var string
	 */
	protected $houseNumber;
	
	
	/**
	 * Constructor of the class CustomerHouseNumber.
	 *
	 * Validates the length and the data type of a house number.
	 *
	 * @param string $houseNumber House number.
	 *
	 * @throws InvalidArgumentException If $houseNumber is not a string.
	 * @throws LengthException If $houseNumber contains more than 64 characters.
	 */
	public function __construct($houseNumber)
	{
		if(!is_string($houseNumber))
		{
			throw new InvalidArgumentException('$houseNumber is not a string');
		}
		
		$dbFieldLength = 64;
		$houseNumber   = trim($houseNumber);
		
		if(strlen_wrapper($houseNumber) > $dbFieldLength)
		{
			throw new LengthException('$houseNumber is longer than ' . $dbFieldLength . ' characters VARCHAR('
			                          . $dbFieldLength . ')');
		}
		
		$this->houseNumber = $houseNumber;
	}
	
	
	/**
	 * Returns the equivalent string value.
	 * @return string Equivalent string value.
	 */
	public function __toString()
	{
		return $this->houseNumber;
	}
}