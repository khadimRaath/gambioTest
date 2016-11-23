<?php
class MagnalisterOrdersOverviewController extends MagnalisterOrdersOverviewController_parent {
	protected function _getAssetsArray(){
		$assetsArray = parent::_getAssetsArray();
		if (gm_get_conf('MODULE_CENTER_MAGNALISTER_INSTALLED') == true) {
			$assetsArray[] = MainFactory::create('Asset', DIR_WS_CATALOG.'admin/html/assets/javascript/modules/magnalister/magnalister_order_column.min.js');
		}
		return $assetsArray; 
	}
}
 