<?php
/* --------------------------------------------------------------
   CustomerCountryIso3.php 2015-02-02 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('CustomerCountryIso3Interface');

/**
 * Value Object
 *
 * Class CustomerCountryIso3
 *
 * Represents a customer country ISO3 code
 *
 * @category   System
 * @package    Customer
 * @subpackage ValueObjects
 * @implements CustomerCountryIso3Interface
 */
class CustomerCountryIso3 implements CustomerCountryIso3Interface
{
	/**
	 * Customer's ISO-3 country code.
	 * @var string
	 */
	protected $iso3;


	/**
	 * Constructor of the class CustomerCountryIso3.
	 *
	 * Validates the length and data type of the customer country ISO-3 code.
	 *
	 * @param string $p_iso3 Customer's ISO-3 country code.
	 *
	 * @throws InvalidArgumentException If $p_iso3 is not a string.
	 * @throws LengthException If $p_iso3 contains more characters than 3.
	 */
	public function __construct($p_iso3)
	{
		if(!is_string($p_iso3))
		{
			throw new InvalidArgumentException('$p_iso3 is not a string');
		}

		$dbFieldLengthIso3 = 3;
		$iso3              = trim($p_iso3);

		if(strlen_wrapper($iso3) > $dbFieldLengthIso3)
		{
			throw new LengthException('$iso3 is longer than ' . $dbFieldLengthIso3 . ' characters CHAR(3)');
		}

		$this->iso3 = $iso3;
	}


	/**
	 * Returns the equivalent string value.
	 * @return string Equivalent string value.
	 */
	public function __toString()
	{
		return $this->iso3;
	}
}