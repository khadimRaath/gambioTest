<?php

/* --------------------------------------------------------------
   MagnalisterAdminApplicationTopExtender.inc.php 2012-01-16 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2012 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class MagnalisterAdminApplicationTopExtender extends MagnalisterAdminApplicationTopExtender_parent
{
	function proceed()
	{
		parent::proceed();
		
		/* magnalister v1.0.1 */
		$languageTextManager = MainFactory::create('LanguageTextManager',
		                                           'module_center_module',
		                                           (int)$_SESSION['languages_id']);
		$gxCoreLoader        = MainFactory::create('GXCoreLoader', MainFactory::create('GXCoreLoaderSettings'));
		$db                  = $gxCoreLoader->getDatabaseQueryBuilder();
		$cacheControl        = MainFactory::create('CacheControl');
		$magnalisterModule   = MainFactory::create('MagnalisterModuleCenterModule',
		                                           $languageTextManager,
		                                           $db,
		                                           $cacheControl);
		if(!defined('MAGNALISTER_PLUGIN')
		   && $magnalisterModule->isInstalled()
		   && file_exists(DIR_FS_DOCUMENT_ROOT . 'magnaCallback.php')
		)
		{
			ob_start();
			require_once(DIR_FS_DOCUMENT_ROOT . 'magnaCallback.php');
			ob_end_clean();
		}
		/* END magnalister */
	}
}

?>