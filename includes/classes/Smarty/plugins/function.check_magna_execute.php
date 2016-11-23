<?php
/* --------------------------------------------------------------
   function.check_magna_execute.php 2015-03-10 tw@gambio
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

function smarty_function_check_magna_execute($params, Smarty_Internal_Template $template)
{
	if (function_exists('magnaExecute'))
		$template->assign("__ml_found",
		                  magnaExecute(
				                  'magnaGenerateSideNav', 
				                  array ('out' => 'xml'), 
				                  array(), 
				                  MAGNA_WITHOUT_DB_INSTALL | MAGNA_WITHOUT_AUTH | MAGNA_WITHOUT_ACTIVATION
		                  )
		);
}