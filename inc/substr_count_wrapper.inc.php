<?php
/* --------------------------------------------------------------
   substr_count_wrapper.inc.php 2014-02-07 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

function substr_count_wrapper($p_haystack, $p_needle, $p_encoding = 'utf-8')
{
	if(function_exists('mb_substr_count'))
	{
		$t_substr_count = mb_substr_count($p_haystack, $p_needle, $p_encoding);
	}
	else
	{
		$t_substr_count = substr_count($p_haystack, $p_needle);
	}
	
	return $t_substr_count; 
}
