<?php
/* --------------------------------------------------------------
   AddressClass.inc.php 2015-01-29 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Value Object
 *
 * Class AddressClass
 *
 * Represents a customer address
 *
 * @category   System
 * @package    Customer
 * @subpackage ValueObjects
 * @implements AddressClassInterface
 */
class AddressClass implements AddressClassInterface
{
	/**
	 * Address class.
	 * @var string
	 */
	protected $addressClass;


	/**
	 * Constructor of the class AddressClass.
	 *
	 * Validates the data type of the address class.
	 *
	 * @param string $addressClass Address class.
	 *
	 * @throws InvalidArgumentException If $p_city is not a string.
	 */
	public function __construct($addressClass)
	{
		if(!is_string($addressClass))
		{
			throw new InvalidArgumentException('$addressClass is not a string');
		}

		$this->addressClass = $addressClass;
	}


	/**
	 * Returns the equivalent string value.
	 * @return string Equivalent string value.
	 */
	public function __toString()
	{
		return $this->addressClass;
	}
} 