<?php
/* --------------------------------------------------------------
   CustomerFirstname.inc.php 2015-01-30 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('CustomerFirstnameInterface');

/**
 * Value Object
 *
 * Class CustomerFirstname
 *
 * Represents a customer firstname
 *
 * @category   System
 * @package    Customer
 * @subpackage ValueObjects
 * @implements CustomerFirstnameInterface
 */
class CustomerFirstname implements CustomerFirstnameInterface
{
	/**
	 * Customer's first name.
	 * @var string
	 */
	protected $firstname;


	/**
	 * Constructor of the class CustomerFirstname.
	 *
	 * Validates the length and the data type of a customer firstname.
	 *
	 * @param string $p_firstname Customer's first name.
	 *
	 * @throws InvalidArgumentException If $p_firstname is not a string.
	 * @throws LengthException If $p_firstname contains more characters than 64.
	 */
	public function __construct($p_firstname)
	{
		if(!is_string($p_firstname))
		{
			throw new InvalidArgumentException('$p_firstname is not a string');
		}

		$dbFieldLength = 64;
		$firstname     = trim($p_firstname);

		if(strlen_wrapper($firstname) > $dbFieldLength)
		{
			throw new LengthException('$firstname is longer than ' . $dbFieldLength . ' characters VARCHAR(64)');
		}

		$this->firstname = $firstname;
	}


	/**
	 * Returns the equivalent string value.
	 * @return string Equivalent string value.
	 */
	public function __toString()
	{
		return $this->firstname;
	}
}