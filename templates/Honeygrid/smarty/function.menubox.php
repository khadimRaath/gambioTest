<?php
/* --------------------------------------------------------------
   function.menubox.php 2016-01-07
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

function smarty_function_menubox($params, &$smarty)
{
	$position = $GLOBALS['coo_template_control']->get_menubox_position($params['name']);
	
	// get box content
	$assignedVars = $smarty->getTemplateVars();
	foreach($assignedVars as $title => $content)
	{
		if($title === $position)
		{
			return $content;
		}
	}

	return '';
}
