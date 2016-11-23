<?php
/* --------------------------------------------------------------
   parse_str_wrapper.inc.php 2014-02-18 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

function parse_str_wrapper($p_encoded_string, &$p_result_array = array())
{
	if(function_exists('mb_parse_str'))
	{
		return mb_parse_str($p_encoded_string, $p_result_array);
	}

	return parse_str($p_encoded_string, $p_result_array);
}