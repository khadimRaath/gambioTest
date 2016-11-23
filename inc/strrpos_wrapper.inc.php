<?php
/* --------------------------------------------------------------
   strrpos_wrapper.inc.php 2014-02-07 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

function strrpos_wrapper($p_haystack, $p_needle, $p_offset = 0, $p_encoding = 'utf-8')
{
	if(function_exists('mb_strrpos'))
	{
		$t_strrpos = mb_strrpos($p_haystack, $p_needle, $p_offset, $p_encoding);
	}
	else
	{
		$t_strrpos = strrpos($p_haystack, $p_needle, $p_offset);
	}
	
	return $t_strrpos; 
}