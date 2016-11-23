<?php
/* --------------------------------------------------------------
   CustomerLastname.inc.php 2015-01-30 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('CustomerLastnameInterface');

/**
 * Value Object
 *
 * Class CustomerLastname
 *
 * Represents a customer lastname
 *
 * @category   System
 * @package    Customer
 * @subpackage ValueObjects
 * @implements CustomerLastnameInterface
 */
class CustomerLastname implements CustomerLastnameInterface
{
	/**
	 * Customer's last name.
	 * @var string
	 */
	protected $lastname;


	/**
	 * Constructor for the class CustomerLastname.
	 *
	 * Validates the length and the data type of the customer last name.
	 *
	 * @param string $p_lastname Customer's last name.
	 *
	 * @throws InvalidArgumentException If $p_lastname is not a string.
	 * @throws LengthException If $p_lastname contains more characters than 64.
	 */
	public function __construct($p_lastname)
	{
		if(!is_string($p_lastname))
		{
			throw new InvalidArgumentException('$p_lastname is not a string');
		}

		$dbFieldLength = 64;
		$lastname      = trim($p_lastname);

		if(strlen_wrapper($lastname) > $dbFieldLength)
		{
			throw new LengthException('$lastname is longer than ' . $dbFieldLength . ' characters VARCHAR(64)');
		}

		$this->lastname = trim($p_lastname);
	}


	/**
	 * Returns the equivalent string value.
	 * @return string Equivalent string value.
	 */
	public function __toString()
	{
		return $this->lastname;
	}
} 