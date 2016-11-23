<?php
/* --------------------------------------------------------------
   CustomerEmail.inc.php 2016-07-08
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('CustomerEmailInterface');

/**
 * Value Object
 *
 * Class CustomerEmail
 *
 * Represents a customer email
 *
 * @category   System
 * @package    Customer
 * @subpackage ValueObjects
 * @implements CustomerEmailInterface
 */
class CustomerEmail implements CustomerEmailInterface
{
	/**
	 * Customer's E-Mail address.
	 * 
	 * @var string
	 */
	protected $email;


	/**
	 * Constructor of the class CustomerEmail.
	 *
	 * Validates the data type and format of the customer email.
	 *
	 * @param string $p_email                 Customer's E-Mail address.
	 * @param bool   $encodeSpecialCharacters Optional (true), whether to encode the special unicode characters to ASCII.
	 *
	 * @throws InvalidArgumentException If $p_email is not a string.
	 * @throws UnexpectedValueException If $p_email is not a valid e-mail address.
	 */
	public function __construct($p_email, $encodeSpecialCharacters = true)
	{
		if(!is_string($p_email))
		{
			throw new InvalidArgumentException('$p_email is not a string');
		}

		if($encodeSpecialCharacters)
		{
			$punycode = new TrueBV\Punycode(); 
			$email = $punycode->encode($p_email); 
		}
		else 
		{
			$email = $p_email;
		}
		
		if(!filter_var($email, FILTER_VALIDATE_EMAIL))
		{
			throw new UnexpectedValueException('$p_email is not a valid e-mail address: ' . $p_email);
		}
		
		$this->email = trim($p_email); // Store the original string and not the encoded one.
	}


	/**
	 * Returns the equivalent string value.
	 * 
	 * @return string Equivalent string value.
	 */
	public function __toString()
	{
		return $this->email;
	}
} 