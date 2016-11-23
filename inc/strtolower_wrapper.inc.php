<?php
/* --------------------------------------------------------------
   strtolower_wrapper.inc.php 2014-02-07 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

function strtolower_wrapper($p_string, $p_encoding = 'utf-8')
{
	if(function_exists('mb_strtolower'))
	{
		$t_strtolower = mb_strtolower($p_string, $p_encoding);
	}
	else
	{
		$t_strtolower = strtolower($p_string);
	}
	
	return $t_strtolower; 
}