<?php
/* --------------------------------------------------------------
   modifier.xtc_href_link.php 2010-12-12 gambio
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2010 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------

*
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */

function smarty_modifier_xtc_href_link($page, $parameters = '', $connection = 'NONSSL', $add_session_id = true, $search_engine_safe = true)
{
	$t_output = xtc_href_link($page, $parameters, $connection, $add_session_id, $search_engine_safe);
	return $t_output;
}

/* vim: set expandtab: */