<?php
/* --------------------------------------------------------------
  gm_convert_qty.inc.php 2013-12-17 gm
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2013 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
 */

function gm_convert_qty($p_number, $p_force_point = true)
{
	static $t_decimal_point;
	
	$t_formatted_number = $p_number;
	
	if($p_force_point)
	{
		$t_formatted_number = str_replace(',', '.', (string)$t_formatted_number);
	}

	$t_formatted_number = (double)$t_formatted_number;

	if($p_force_point === false)
	{
		if($t_decimal_point === null)
		{
			$t_decimal_point = ',';
			
			$t_sql = 'SELECT decimal_point FROM currencies WHERE code = "' . xtc_db_input($_SESSION['currency']) . '" LIMIT 1';
			$t_result = xtc_db_query($t_sql);
			
			if(xtc_db_num_rows($t_result) == 1)
			{
				$t_result_array = xtc_db_fetch_array($t_result);
				$t_decimal_point = $t_result_array['decimal_point'];
				$t_formatted_number = str_replace('.', $t_decimal_point, $t_formatted_number);
			}
			else
			{
				$t_formatted_number = str_replace('.', $t_decimal_point, $t_formatted_number);
			}
		}
		else
		{
			$t_formatted_number = str_replace('.', $t_decimal_point, $t_formatted_number);
		}
	}

	return $t_formatted_number;
}
