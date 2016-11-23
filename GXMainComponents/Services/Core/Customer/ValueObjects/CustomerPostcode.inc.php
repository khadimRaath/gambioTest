<?php
/* --------------------------------------------------------------
   CustomerPostcode.inc.phpc.php 2015-01-30 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('CustomerPostcodeInterface');

/**
 * Value Object
 *
 * Class CustomerPostcode
 *
 * Represents a customer postcode
 *
 * @category   System
 * @package    Customer
 * @subpackage ValueObjects
 * @implements CustomerPostcodeInterface
 */
class CustomerPostcode implements CustomerPostcodeInterface
{
	/**
	 * Customer's post code.
	 * @var string
	 */
	protected $postcode;


	/**
	 * Constructor for the class CustomerPostcode.
	 *
	 * Validates the length and the data type of the postcode.
	 *
	 * @param string $p_postcode Customer's post code.
	 *
	 * @throws InvalidArgumentException If $p_postcode is not a string.
	 * @throws LengthException If $p_postcode contains more characters than 10.
	 */
	public function __construct($p_postcode)
	{
		if(!is_string($p_postcode))
		{
			throw new InvalidArgumentException('$p_postcode is not a string');
		}

		$dbFieldLength = 10;
		$postcode      = trim($p_postcode);

		if(strlen_wrapper($postcode) > $dbFieldLength)
		{
			throw new LengthException('$postcode is longer than ' . $dbFieldLength . ' characters VARCHAR(10)');
		}

		$this->postcode = $postcode;
	}


	/**
	 * Returns the equivalent string value.
	 * @return string Equivalent string value.
	 */
	public function __toString()
	{
		return $this->postcode;
	}
} 