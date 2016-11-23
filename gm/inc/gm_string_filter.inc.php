<?php
/* --------------------------------------------------------------
   gm_string_filter.inc.php 2009-11-10 mb
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2009 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/
?><?php

function gm_string_filter($p_string, $p_allowed_characters, $p_negate = false, $p_case_sensitive = false)
{
	$c_allowed_characters = str_replace('/', '\/', $p_allowed_characters);
	$c_allowed_characters = str_replace(']', '\]', $p_allowed_characters);
	
	if($p_negate)
	{
		$t_pattern = '/(.*?)([' . $c_allowed_characters . '])(.*?)/s';
	}
	else
	{
		$t_pattern = '/(.*?)([^' . $c_allowed_characters . '])(.*?)/s';
	}
	
	if(!$p_case_sensitive)
	{
		$t_pattern .= 'i';
	}
	
	if(!is_array($p_string))
	{
		$t_filtered_result = '';
		
		$t_filtered_result = preg_replace($t_pattern, "$1", $p_string);
	}
	else
	{
		$t_filtered_result = array();
		
		foreach($p_string AS $t_key => $t_value)
		{
			if(!is_array($t_value))
			{
				$t_filtered_result[$t_key] = preg_replace($t_pattern, "$1", $t_value);
			}
			else
			{
				$t_filtered_result[$t_key] = gm_string_filter($t_value, $p_allowed_characters, $p_negate, $p_case_sensitive);
			}
		}
	}
		
	return $t_filtered_result;
}

?>