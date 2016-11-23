<?php

/* --------------------------------------------------------------
	AfterbuyOrdersOverviewController.inc.php 2016-07-07
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2016 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/

class AfterbuyOrdersOverviewController extends AfterbuyOrdersOverviewController_parent
{
    protected function _getAssetsArray()
    {
        $assets = parent::_getAssetsArray();
        if(gm_get_conf('MODULE_CENTER_AFTERBUY_INSTALLED') == true)
        {
            $assets[] = MainFactory::create('Asset', 'admin_buttons.lang.inc.php');
            $assets[] = MainFactory::create('Asset', DIR_WS_CATALOG
                . 'admin/html/assets/javascript/modules/afterbuy/afterbuy.min.js');
        }

        return $assets;
    }
}
