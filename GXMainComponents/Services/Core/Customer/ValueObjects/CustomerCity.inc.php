<?php
/* --------------------------------------------------------------
   CustomerCity.inc.php 2015-01-30 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('CustomerCityInterface');

/**
 * Value Object
 *
 * Class CustomerCity
 *
 * Represents a customer city
 *
 * @category   System
 * @package    Customer
 * @subpackage ValueObjects
 * @implements CustomerCityInterface
 */
class CustomerCity implements CustomerCityInterface
{
	/**
	 * Customer's city.
	 * @var string
	 */
	protected $city;


	/**
	 * Constructor of the class CustomerCity.
	 *
	 * Validates the length and the data type of the customer city.
	 *
	 * @param string $p_city Customer's city.
	 *
	 * @throws InvalidArgumentException If $p_city is not a string.
	 * @throws LengthException If $p_city contains more characters than 32.
	 */
	public function __construct($p_city)
	{
		if(!is_string($p_city))
		{
			throw new InvalidArgumentException('$p_city is not a string');
		}

		$dbFieldLength = 32;
		$city          = trim($p_city);

		if(strlen_wrapper($city) > $dbFieldLength)
		{
			throw new LengthException('$city is longer than ' . $dbFieldLength . ' characters VARCHAR(32)');
		}
		
		$this->city = $city;
	}


	/**
	 * Returns the equivalent string value.
	 * @return string Equivalent string value.
	 */
	public function __toString()
	{
		return $this->city;
	}
} 