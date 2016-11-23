<?php
/* --------------------------------------------------------------
   function.page_url.php 2010-12-31 mb
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2010 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/
?><?php

function smarty_function_page_url($params, &$smarty)
{
	if(function_exists('gm_get_env_info'))
	{
		return htmlspecialchars_wrapper(gm_get_env_info('REQUEST_URI') );
	}
	else
	{
		return htmlspecialchars_wrapper($_SERVER['REQUEST_URI'] );
	}
}
?>