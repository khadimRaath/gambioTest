<?php
/* --------------------------------------------------------------
   CustomerCountryZoneName.php 2015-01-30 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('CustomerCountryZoneNameInterface');

/**
 * Value Object
 *
 * Class CustomerCountryZoneName
 *
 * Represents a customer country zone name
 *
 * @category   System
 * @package    Customer
 * @subpackage ValueObjects
 * @implements CustomerCountryZoneNameInterface
 */
class CustomerCountryZoneName implements CustomerCountryZoneNameInterface
{
	/**
	 * Customer's country zone name.
	 * @var string
	 */
	protected $name;


	/**
	 * Constructor of the class CustomerCountryZoneName.
	 *
	 * Validates the length and the data type of the customer country zone name.
	 *
	 * @param string $p_name Customer's country zone name.
	 *
	 * @throws InvalidArgumentException If $p_name is not a string.
	 * @throws LengthException If $p_name contains more characters than 32.
	 */
	public function __construct($p_name)
	{
		if(!is_string($p_name))
		{
			throw new InvalidArgumentException('$p_name is not a string');
		}

		$dbFieldLengthName = 32;
		$name              = trim($p_name);

		if(strlen_wrapper($name) > $dbFieldLengthName)
		{
			throw new LengthException('$name is longer than ' . $dbFieldLengthName . ' characters VARCHAR(32)');
		}

		$this->name = $name;
	}


	/**
	 * Returns the equivalent string value.
	 * @return string Equivalent string value.
	 */
	public function __toString()
	{
		return $this->name;
	}
}