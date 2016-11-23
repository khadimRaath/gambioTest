<?php
/* --------------------------------------------------------------
   gm_utf8_decode.inc.php 2009-12-07 mb
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2009 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/
?><?php	
function gm_utf8_decode($p_value)
{
	if(is_array($p_value))
	{
		$c_value = array();
		
		foreach($p_value AS $t_key => $t_value)
		{
			$c_value[$t_key] = gm_utf8_decode($t_value);
		}
	}
	else
	{
		$p_value = str_replace('â‚¬', '€', $p_value);
			
		$c_value = '';
		$c_value = utf8_decode($p_value);
	}

	return $c_value;
}
?>