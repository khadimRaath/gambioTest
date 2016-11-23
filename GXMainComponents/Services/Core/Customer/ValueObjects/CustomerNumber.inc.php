<?php
/* --------------------------------------------------------------
   CustomerNumber.inc.php 2015-01-30 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('CustomerNumberInterface');

/**
 * Value Object
 *
 * Class CustomerNumber
 *
 * Represents a customer number
 *
 * @category   System
 * @package    Customer
 * @subpackage ValueObjects
 * @implements CustomerNumberInterface
 */
class CustomerNumber implements CustomerNumberInterface
{
	/**
	 * Customer's number.
	 * @var string
	 */
	protected $number;


	/**
	 * Constructor of the class CustomerNumber.
	 *
	 * Validates the length and the data type of the customer number.
	 *
	 * @param string $p_number Customer's number.
	 *
	 * @throws InvalidArgumentException If $p_number is not a string.
	 * @throws LengthException If $p_lastname contains more characters than 32.
	 */
	public function __construct($p_number)
	{
		if(!is_string($p_number) && !is_numeric($p_number))
		{
			throw new InvalidArgumentException('$p_number is not a string');
		}

		$dbFieldLength = 32;
		$number        = trim($p_number);

		if(strlen_wrapper($number) > $dbFieldLength)
		{
			throw new LengthException('$number is longer than ' . $dbFieldLength . ' characters VARCHAR(32)');
		}

		$this->number = $number;
	}


	/**
	 * Returns the equivalent string value.
	 * @return string Equivalent string value.
	 */
	public function __toString()
	{
		return $this->number;
	}
}
 