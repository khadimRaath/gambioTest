<?php
/* --------------------------------------------------------------
   check_data_type.inc.php 2014-07-30 mb
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

function check_data_type($p_data, $p_type, $p_strict = false, $p_error_level = E_USER_ERROR, $p_object_type = '')
{
	switch(strtolower($p_type))
	{
		case 'int':
			if($p_strict && is_int($p_data))
			{
				return true;
			}
			elseif(!$p_strict && is_numeric($p_data) && (int)$p_data == (double)$p_data)
			{
				return true;
			}
			else
			{
				trigger_error('check_data_type validation failed. Dump: ' . print_r($p_data, true) . ', integer expected, but ' . gettype($p_data) . ' detected', $p_error_level);
				return false;
			}
			break;
		case 'double':
			if($p_strict && is_float($p_data))
			{
				return true;
			}
			elseif(!$p_strict && is_numeric($p_data))
			{
				return true;
			}
			else
			{
				trigger_error('check_data_type validation failed. Dump: ' . print_r($p_data, true) . ', float value expected, but ' . gettype($p_data) . ' detected', $p_error_level);
				return false;
			}
			break;
		case 'string':
			if(is_string($p_data))
			{
				return true;
			}
			else
			{
				trigger_error('check_data_type validation failed. Dump: ' . print_r($p_data, true) . ', string expected, but ' . gettype($p_data) . ' detected', $p_error_level);
				return false;
			}
		case 'array':
			if(is_array($p_data))
			{
				return true;
			}
			else
			{
				trigger_error('check_data_type validation failed. Dump: ' . print_r($p_data, true) . ', array expected, but ' . gettype($p_data) . ' detected', $p_error_level);
				return false;
			}
		case 'bool':
			if(is_bool($p_data))
			{
				return true;
			}
			else
			{
				trigger_error('check_data_type validation failed. Dump: ' . print_r($p_data, true) . ', boolean expected, but ' . gettype($p_data) . ' detected', $p_error_level);
				return false;
			}
		case 'object':
			if($p_object_type != '')
			{
				if($p_data instanceof $p_object_type)
				{
					return true;
				}
				elseif($p_data instanceof $p_object_type .'_ORIGIN')
				{
					return true;
				}
				elseif(get_class($p_data) !== false)
				{
					trigger_error('check_data_type validation failed. Dump: ' . print_r($p_data, true) . ', ' . (string)$p_object_type . '-object expected, but ' . get_class($p_data) . '-object detected', $p_error_level);
					return false;
				}
				else
				{
					trigger_error('check_data_type validation failed. Dump: ' . print_r($p_data, true) . ', ' . (string)$p_object_type . '-object expected, but ' . gettype($p_data) . ' detected', $p_error_level);
					return false;
				}
			}
			
			if(is_object($p_data))
			{
				return true;
			}
			else
			{
				trigger_error('check_data_type validation failed. Dump: ' . print_r($p_data, true) . ', object expected, but ' . gettype($p_data) . ' detected', $p_error_level);
				return false;
			}
		case '':
			trigger_error('check_data_type validation failed. Empty data type.', E_USER_ERROR);
			return false;
		default:
			trigger_error('check_data_type validation failed. Unknown data type: ' . (string)$p_type, E_USER_ERROR);
	}
}