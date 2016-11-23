<?php
/* --------------------------------------------------------------
   function.template_setting.php 2016-01-18
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */

/**
 * @param $params
 * @param $smarty
 *
 * @return string
 */
function smarty_function_template_setting($params, &$smarty)
{
	return $GLOBALS['coo_template_control']->findSettingValueByName($params['name']);
}