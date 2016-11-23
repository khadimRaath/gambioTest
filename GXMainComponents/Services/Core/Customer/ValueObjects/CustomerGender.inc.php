<?php
/* --------------------------------------------------------------
   CustomerGender.inc.php 2015-01-30 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('CustomerGenderInterface');

/**
 * Value Object
 *
 * Class CustomerGender
 *
 * Represents a customer gender
 *
 * @category   System
 * @package    Customer
 * @subpackage ValueObjects
 * @implements CustomerGenderInterface
 */
class CustomerGender implements CustomerGenderInterface
{
	/**
	 * Customer's gender.
	 * @var string
	 */
	protected $gender;


	/**
	 * Constructor of the class CustomerGender.
	 *
	 * Validates the data type and the entered values of the customer gender.
	 *
	 * @param string $p_gender Customer's gender.
	 *
	 * @throws InvalidArgumentException If $p_gender is not a string.
	 * @throws UnexpectedValueException If $p_gender is not expected string "m" or "f".
	 */
	public function __construct($p_gender)
	{
		if(!is_string($p_gender))
		{
			throw new InvalidArgumentException('$p_gender is not a string');
		}
		
		if(!in_array($p_gender, array('m', 'f', '')))
		{
			throw new UnexpectedValueException('$p_gender is not expected string "m" or "f"');
		}

		$this->gender = $p_gender;
	}


	/**
	 * Returns the equivalent string value.
	 * @return string Equivalent string value.
	 */
	public function __toString()
	{
		return $this->gender;
	}
} 