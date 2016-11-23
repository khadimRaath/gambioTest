<?php
/* --------------------------------------------------------------
   modifier.detect_page.php 2015-10-27 tw@gambio
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

function smarty_modifier_recursive_array_search($string, $needle)
{
	foreach($string as $key => $value)
	{
		$current_key = $key;
		if($needle === $value OR (is_array($value)
		                          && smarty_modifier_recursive_array_search($value, $needle) !== false)
		)
		{
			return $current_key;
		}
	}
	
	return false;
}