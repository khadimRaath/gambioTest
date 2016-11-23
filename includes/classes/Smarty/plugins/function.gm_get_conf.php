<?php
/* --------------------------------------------------------------
   function.gm_get_conf.php 2015-03-10 tw@gambio
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

function smarty_function_gm_get_conf($params, $smarty)
{
	echo gm_get_conf($params['get']);
}