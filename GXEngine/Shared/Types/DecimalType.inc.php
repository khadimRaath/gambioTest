<?php

/* --------------------------------------------------------------
   DecimalType.inc.php 2015-11-04 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class DecimalType
 *
 * Shared decimal type class. Use the "asDecimal" method for getting the plain value.
 *
 * @category   System
 * @package    Shared
 * @subpackage Types
 */
class DecimalType
{
	/**
	 * Instance Value
	 *
	 * @var float
	 */
	protected $value;
	
	
	/**
	 * Class Constructor
	 *
	 * @throws InvalidArgumentException
	 *
	 * @param float $p_value
	 */
	public function __construct($p_value)
	{
		if(!is_numeric($p_value) || (float)$p_value != $p_value)
		{
			throw new InvalidArgumentException('DecimalType: Invalid argument value given (expected float numeric got '
			                                   . gettype($p_value) . '): ' . $p_value);
		}
		
		$this->value = (float)$p_value;
	}
	
	
	/**
	 * Get the instance value as decimal.
	 *
	 * @return float
	 */
	public function asDecimal()
	{
		return $this->value;
	}
}