<?php
/* --------------------------------------------------------------
   strtoupper_wrapper.inc.php 2014-02-07 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

function strtoupper_wrapper($p_string, $p_encoding = 'utf-8')
{
	if(function_exists('mb_strtoupper'))
	{
		$t_strtoupper = mb_strtoupper($p_string, $p_encoding);
	}
	else
	{
		$t_strtoupper = strtoupper($p_string);
	}
	
	return $t_strtoupper; 
}