<?php

/* --------------------------------------------------------------
   IntType.inc.php 2015-11-04 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class IntType
 *
 * Shared int type class. Use the "asInt" method for getting the plain value.
 *
 * @category   System
 * @package    Shared
 * @subpackage Types
 */
class IntType
{
	/**
	 * Instance Value
	 *
	 * @var int
	 */
	protected $value;
	
	
	/**
	 * Class Constructor
	 *
	 * @throws InvalidArgumentException
	 *
	 * @param int $p_value
	 */
	public function __construct($p_value)
	{
		if(!is_numeric($p_value) || (int)$p_value != $p_value || is_float($p_value)
		   || (is_string($p_value) && strpos($p_value, '.'))
		)
		{
			throw new InvalidArgumentException('IntType: Invalid argument value given (expected numeric int got '
			                                   . gettype($p_value) . '): ' . $p_value);
		}
		
		$this->value = (int)$p_value;
	}
	
	
	/**
	 * Get the instance value as int.
	 *
	 * @return int
	 */
	public function asInt()
	{
		return $this->value;
	}
}