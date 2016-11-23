<?php 
class MagnalisterOrdersOverviewColumns extends MagnalisterOrdersOverviewColumns_parent {
	
	public function __construct() {
		parent::__construct();
		if (gm_get_conf('MODULE_CENTER_MAGNALISTER_INSTALLED') == true) {
			$this->columns[] = MainFactory::create('DataTableColumn')
				->setTitle(new StringType('magnalister'))
				->setName(new StringType('magnalister'))
				->setField(new StringType('orders.orders_id'))
				#->setType(new DataTableColumnType(DataTableColumnType::NUMBER))
			;
		}
	}
}