<?php

/* --------------------------------------------------------------
   StringType.inc.php 2015-11-04 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class StringType
 *
 * Shared string type class. Use the "asString" method for getting the plain value.
 *
 * @category   System
 * @package    Shared
 * @subpackage Types
 */
class StringType
{
	/**
	 * Instance Value
	 *
	 * @var string
	 */
	protected $value;
	
	
	/**
	 * Class Constructor
	 *
	 * @throws InvalidArgumentException
	 *
	 * @param string $p_value
	 */
	public function __construct($p_value)
	{
		if(!is_string($p_value))
		{
			throw new InvalidArgumentException('StringType: Invalid argument value given (expected string got '
			                                   . gettype($p_value) . '): ' . $p_value);
		}
		
		$this->value = $p_value;
	}
	
	
	/**
	 * Get the instance value as string.
	 *
	 * @return string
	 */
	public function asString()
	{
		return $this->value;
	}
}