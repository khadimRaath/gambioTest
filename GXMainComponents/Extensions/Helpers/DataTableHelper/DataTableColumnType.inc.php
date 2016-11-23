<?php

/* --------------------------------------------------------------
   DataTableColumnType.inc.php 2016-05-02
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class DataTableColumnType
 *
 * There are cases where each column is treated differently depending its data type. 
 *
 * @category   System
 * @package    Extensions
 * @subpackage Helpers
 */
class DataTableColumnType extends StringType
{
	// Supported column types. 
	const DATE   = 'date';
	const NUMBER = 'number';
	const STRING = 'string';
	
	
	/**
	 * DataTableColumnType constructor.
	 *
	 * @param string $value
	 *
	 * @throws InvalidArgumentException
	 */
	public function __construct($value)
	{
		parent::__construct($value);
		
		$type = strtoupper($value); // 'DATE', 'STRING' or 'NUMBER'
		
		if(!defined('self::' . $type))
		{
			throw new InvalidArgumentException('Invalid DataTableColumnType provided: ' . $value);
		}
	}
}