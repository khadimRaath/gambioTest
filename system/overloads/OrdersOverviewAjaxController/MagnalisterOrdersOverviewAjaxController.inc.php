<?php 
class MagnalisterOrdersOverviewAjaxController extends MagnalisterOrdersOverviewAjaxController_parent {
	protected function _getTableData() {
		$tableData = parent::_getTableData();
		if (gm_get_conf('MODULE_CENTER_MAGNALISTER_INSTALLED') == true) {
			foreach ($tableData as &$row) {
				if (function_exists('magnaExecute')) {
					$row['magnalister'] = magnaExecute('magnaRenderOrderPlatformIcon', array('oID' => $row['DT_RowId']), array('order_details.php'));
				} else {
					$row['magnalister'] = '&mdash;';
				}
			}
		}
		return $tableData;
	}
	
}