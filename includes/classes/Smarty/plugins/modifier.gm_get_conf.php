<?php
/* --------------------------------------------------------------
   modifier.gm_get_conf.php 2015-09-10 tw@gambio
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------

*
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */

function smarty_modifier_gm_get_conf($string)
{
	$output = gm_get_conf($string);
	
	return $output;
}