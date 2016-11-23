<?php

/* --------------------------------------------------------------
   NonEmptyStringType.inc.php 2015-11-19
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class NonEmptyStringType
 *
 * Shared string type class. Use the "asString" method for getting the plain value.
 *
 * Notice: The constructor value must not be empty!
 *
 * @category   System
 * @package    Shared
 * @subpackage Types
 */
class NonEmptyStringType extends StringType
{
	/**
	 * Class Constructor
	 *
	 * @param string $p_value Must not be empty.
	 *                        
	 * @throws InvalidArgumentException
	 */
	public function __construct($p_value)
	{
		parent::__construct($p_value);
		
		if(empty($p_value))
		{
			throw new InvalidArgumentException('NonEmptyStringType: Invalid argument value given (expected non-empty '
			                                   . ' string got ' . gettype($p_value) . '): ' . $p_value);
		}
	}
}