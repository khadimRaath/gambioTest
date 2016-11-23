<?php
/* --------------------------------------------------------------
   CustomerCountryName.php 2015-02-02 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('CustomerCountryNameInterface');

/**
 * Value Object
 *
 * Class CustomerCountryName
 *
 * Represents a customer country name
 *
 * @category   System
 * @package    Customer
 * @subpackage ValueObjects
 * @implements CustomerCountryNameInterface
 */
class CustomerCountryName implements CustomerCountryNameInterface
{
	/**
	 * Customer's country name.
	 * @var string
	 */
	protected $name;


	/**
	 * Constructor of the class CustomerCountryName.
	 *
	 * Validates the length and data type of the customer country name.
	 *
	 * @param string $p_name Customer's country name.
	 *
	 * @throws InvalidArgumentException If $p_name is not a string.
	 * @throws LengthException If $p_name contains more characters than 64.
	 */
	public function __construct($p_name)
	{
		if(!is_string($p_name))
		{
			throw new InvalidArgumentException('$p_name is not a string');
		}

		$dbFieldLengthName = 64;
		$name              = trim($p_name);

		if(strlen_wrapper($name) > $dbFieldLengthName)
		{
			throw new LengthException('$name is longer than ' . $dbFieldLengthName . ' characters VARCHAR(64)');
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