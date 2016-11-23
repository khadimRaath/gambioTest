<?php
/* --------------------------------------------------------------
   function.pre_black_container.php 2013-12-05 mb
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2013 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

function smarty_function_pre_black_container()
{
	$t_output = '';
	
	if((substr_count($_SERVER["SCRIPT_NAME"], 'checkout_') > 0) 
			&& gm_get_conf('GM_LIGHTBOX_CART') == 'true' 
			&& gm_get_conf('GM_LIGHTBOX_CHECKOUT') == 'true')
	{
		$t_output = '<div id="pre_black_container"><div id="pre_black">&nbsp;</div></div>';
	}
	
	return $t_output;
}
