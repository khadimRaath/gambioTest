<?php
/* --------------------------------------------------------------
   modifier.has_children.php 2012-10-22 mb
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2012 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/
?><?php

function smarty_modifier_has_children($t_categories_array)
{
	$t_has_children = false;
	
	if(is_array($t_categories_array))
	{
		foreach($t_categories_array AS $t_category)
		{
			if(isset($t_category['children']) && count($t_category['children']) > 0)
			{
				$t_has_children = true;
			}			
		}
	}
	
	return $t_has_children;
}

?>