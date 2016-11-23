<?php
/* --------------------------------------------------------------
   function.html_shop_offline.php 2015-05-13 mb
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/
?><?php

function smarty_function_html_shop_offline()
{
	$t_output = '<br/><br/><br/>'. gm_get_conf('GM_SHOP_OFFLINE_MSG') .'<br/><br/><br/>';
	$t_output .= "\n<!-- shop mode: offline -->\n";
	return $t_output;
}