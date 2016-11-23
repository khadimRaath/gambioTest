<?php
/* --------------------------------------------------------------
	IloxxOrdersOverviewController.inc.php 2016-06-20
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2015 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/

class IloxxOrdersOverviewController extends IloxxOrdersOverviewController_parent
{
	protected function _getAssetsArray()
	{
		$assets = parent::_getAssetsArray();
		if(gm_get_conf('MODULE_CENTER_ILOXX_INSTALLED') == true)
		{
			$assets[] = MainFactory::create('Asset', 'iloxx.lang.inc.php');
			$assets[] = MainFactory::create('Asset', DIR_WS_CATALOG
			                                         . 'admin/html/assets/javascript/modules/iloxx/iloxx.min.js');
		}
		return $assets;
	}
}
