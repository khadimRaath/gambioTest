<?php
/* --------------------------------------------------------------
   modifier.shop_url.php 2010-10-08 gambio
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2010 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


function smarty_modifier_shop_url($string)
{
	$t_shop_url = xtc_href_link($string);
    return $t_shop_url;
}