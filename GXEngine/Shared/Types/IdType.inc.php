<?php
/* --------------------------------------------------------------
   IdType.inc.php 2016-03-01
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class IdType
 *
 * IMPORTANT:
 * When you need to cast an Id object to integer, cast it first to string,
 * because otherwise the following command will return always 1:
 *
 * EXAMPLE:
 * $id = new IdType(948);
 * bad  - (int)$id         >> 1
 * good - (int)(string)$id >> 948
 *
 * @category   System
 * @package    Shared
 * @subpackage Types
 */
class IdType extends IntType implements IdInterface
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
	 * @param int $p_value
	 *
	 * @throws InvalidArgumentException On negative values.
	 */
	public function __construct($p_value)
	{
		parent::__construct($p_value);
		
		if((int)$p_value < 0)
		{
			throw new InvalidArgumentException(__CLASS__
			                                   . ': Invalid argument value given (expected positive integer got '
			                                   . gettype($p_value) . '): ' . $p_value);
		}
	}
	
	
	/**
	 * @deprecated v2.7.1.0 To string method is left for backwards compatibility. Use asInt() method instead.
	 *
	 * @return string
	 */
	public function __toString()
	{
		return (string)$this->value;
	}
}