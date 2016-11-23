<?php
/* --------------------------------------------------------------
   gm_get_language.inc.php 2014-10-01
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/*
*	-> function to get content values by group id
*/
function gm_get_language()
{
	static $t_languages_array;
	
	if($t_languages_array === null)
	{
		$t_languages_array = array();

		$t_result = xtc_db_query("	SELECT
									*
								FROM
									languages
								ORDER by
									sort_order, 
									languages_id
								");

		while($t_result_array = xtc_db_fetch_array($t_result))
		{
			$t_languages_array[$t_result_array['languages_id']] = $t_result_array;
		}
	}

	return $t_languages_array;
}
