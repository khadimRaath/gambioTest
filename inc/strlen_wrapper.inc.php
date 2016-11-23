<?php
/* --------------------------------------------------------------
   strlen_wrapper.inc.php 2014-02-05 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

function strlen_wrapper($p_string, $p_encoding = 'utf-8')
{
	if(function_exists('mb_strlen'))
	{
		$t_return = mb_strlen($p_string, $p_encoding);
	}
	else
	{
		$t_return = strlen($p_string);
	}
	return $t_return;
}