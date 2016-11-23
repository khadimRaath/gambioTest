<?php
/* --------------------------------------------------------------
  xtc_db_perform.inc.php 2013-03-04 gm
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2013 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------


  based on:
  (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
  (c) 2002-2003 osCommerce(database.php,v 1.19 2003/03/22); www.oscommerce.com
  (c) 2003	 nextcommerce (xtc_db_perform.inc.php,v 1.3 2003/08/13); www.nextcommerce.org
  (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: xtc_db_perform.inc.php 899 2005-04-29 02:40:57Z hhgag $)

  Released under the GNU General Public License
  --------------------------------------------------------------------------------------- */

function xtc_db_perform($p_table, $p_data_array = array(), $p_action = 'insert', $p_parameters = '', $p_link = 'db_link', $p_quoted_values = true)
{
	$t_result = false;
	$t_quote = '';
	if($p_quoted_values)
	{
		$t_quote = '\'';
	}
	
	reset($p_data_array);

	switch($p_action)
	{
		case 'insert':
			$t_sql = 'INSERT INTO ' . $p_table . ' (';
			
			while(list($t_columns, ) = each($p_data_array))
			{
				$t_sql .= $t_columns . ', ';
			}
			
			$t_sql = substr($t_sql, 0, -2) . ') VALUES (';
			
			reset($p_data_array);
			
			while(list(, $t_value) = each($p_data_array))
			{
				$t_value = (is_Float($t_value) & PHP4_3_10) ? sprintf("%.F", $t_value) : (string)($t_value);
				
				switch($t_value)
				{
					case 'now()':
						$t_sql .= 'NOW(), ';
						break;
					case 'null':
						$t_sql .= 'NULL, ';
						break;
					default:
						if($p_quoted_values)
						{
							$t_value = xtc_db_input($t_value);
						}
						$t_sql .= $t_quote . $t_value . $t_quote . ', ';
						break;
				}
			}
			
			$t_sql = substr($t_sql, 0, -2) . ')';
			
			break;
		
		case 'replace':
			$t_sql = 'REPLACE INTO ' . $p_table . ' (';
			
			while(list($t_columns, ) = each($p_data_array))
			{
				$t_sql .= $t_columns . ', ';
			}
			
			$t_sql = substr($t_sql, 0, -2) . ') VALUES (';
			
			reset($p_data_array);
			
			while(list(, $t_value) = each($p_data_array))
			{
				$t_value = (is_Float($t_value) & PHP4_3_10) ? sprintf("%.F", $t_value) : (string)($t_value);
				
				switch($t_value)
				{
					case 'now()':
						$t_sql .= 'NOW(), ';
						break;
					case 'null':
						$t_sql .= 'NULL, ';
						break;
					default:
						if($p_quoted_values)
						{
							$t_value = xtc_db_input($t_value);
						}
						$t_sql .= $t_quote . $t_value . $t_quote . ', ';
						break;
				}
			}
			
			$t_sql = substr($t_sql, 0, -2) . ')';
			
			break;
		
		case 'update':
			$t_sql = 'UPDATE ' . $p_table . ' SET ';
			
			while(list($t_columns, $t_value) = each($p_data_array))
			{
				$t_value = (is_Float($t_value) & PHP4_3_10) ? sprintf("%.F", $t_value) : (string)($t_value);
				
				switch($t_value)
				{
					case 'now()':
						$t_sql .= $t_columns . ' = NOW(), ';
						break;
					case 'null':
						$t_sql .= $t_columns . ' = NULL, ';
						break;
					default:
						if($p_quoted_values)
						{
							$t_value = xtc_db_input($t_value);
						}
						$t_sql .= $t_columns . ' = ' . $t_quote . $t_value . $t_quote . ', ';
						break;
				}
			}
			
			$t_sql = substr($t_sql, 0, -2) . ' WHERE ' . $p_parameters;
			
			break;
		
		case 'delete':
			$t_sql = 'DELETE FROM ' . $p_table . ' WHERE ' . $p_parameters;
			
			break;
	}

	if(empty($t_sql) == false)
	{
		$t_result = xtc_db_query($t_sql, $p_link);
	}	
	
	return $t_result;
}
