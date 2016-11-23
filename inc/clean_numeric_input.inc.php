<?php
/* --------------------------------------------------------------
   clean_numeric_input.inc.php 2013-12-11 wue
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2013 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

function clean_numeric_input($p_number)
{
	$t_return = 0;
	$t_matches_array = array();
	
	preg_match('/(.*)[^0-9-]{1}([0-9]*)$/', (string)$p_number, $t_matches_array);
	
	if(empty($t_matches_array))
	{
		$t_return = $p_number;
	}
	else
	{
		$t_return = preg_replace('/[^0-9-]/', '', $t_matches_array[1]) . '.' . $t_matches_array[2];
	}
	
	return (double)$t_return;
}