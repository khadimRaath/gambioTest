<?php

/* --------------------------------------------------------------
   EmailStringType.inc.php 2016-07-08
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class EmailStringType
 *
 * Shared email string type class. Use the "asString" method for getting the plain value.
 *
 * @category   System
 * @package    Shared
 * @subpackage Types
 */
class EmailStringType extends NonEmptyStringType
{
	/**
	 * Class Constructor
	 *
	 * @param string $p_email
	 * 
	 * @throws UnexpectedValueException
	 * @throws InvalidArgumentException
	 */
	public function __construct($p_email, $encodeSpecialCharacters = true)
	{
		parent::__construct($p_email);
		
		if($encodeSpecialCharacters === true)
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
	}
}