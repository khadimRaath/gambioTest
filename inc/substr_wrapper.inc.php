<?php
/* --------------------------------------------------------------
  substr_wrapper.inc.php 2014-02-05 gm
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2014 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
 */

function substr_wrapper($p_string, $p_start, $p_length = null, $p_encoding = 'utf-8')
{
	if(function_exists('mb_substr'))
	{
		$c_length = $p_length;
		
		if($c_length === null)
		{
			if(function_exists('mb_strlen'))
			{
				$c_length = mb_strlen($p_string) - $p_start;
			}
			else
			{
				$c_length = strlen($p_string) - $p_start;
			}
		}

		$t_return = mb_substr($p_string, $p_start, $c_length, $p_encoding);
	}
	elseif($p_length !== null)
	{
		$t_return = substr($p_string, $p_start, $p_length);
	}
	else
	{
		$t_return = substr($p_string, $p_start);
	}

	return $t_return;
}