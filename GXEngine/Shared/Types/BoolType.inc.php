<?php
/* --------------------------------------------------------------
   BoolType.inc.php 2016-05-09
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class BoolType
 *
 * Shared boolean type class. Use the "asBool" method for getting the plain value.
 *
 * @category   System
 * @package    Shared
 * @subpackage Types
 */
class BoolType
{
	/**
	 * Instance Value
	 *
	 * @var bool
	 */
	protected $value;
	
	
	/**
	 * Class Constructor
	 *
	 * @throws InvalidArgumentException
	 *
	 * @param bool $p_value
	 */
	public function __construct($p_value)
	{
		if(is_bool($p_value))
		{
			$this->value = $p_value;
			
			return;
		}
		
		if(is_string($p_value) && (strtolower($p_value) === 'false' || $p_value === ''))
		{
			$p_value = false;
		}
		elseif(is_string($p_value) && strtolower($p_value) === 'true')
		{
			$p_value = true;
		}
		elseif((!is_bool($p_value) && !is_numeric($p_value))
		       || (is_numeric($p_value) && $p_value != 1 && $p_value != 0)
		)
		{
			throw new InvalidArgumentException('BoolType: Invalid argument value given (expected bool got '
			                                   . gettype($p_value) . ')');
		}
		
		$this->value = (bool)$p_value;
	}
	
	
	/**
	 * Get the instance value as bool.
	 *
	 * @return bool
	 */
	public function asBool()
	{
		return $this->value;
	}
}