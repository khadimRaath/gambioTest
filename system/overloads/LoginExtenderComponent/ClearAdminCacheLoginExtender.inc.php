<?php
/* --------------------------------------------------------------
   ClearAdminCacheLoginExtender.inc.php 2014-11-12 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class ClearAdminCacheLoginExtender extends ClearAdminCacheLoginExtender_parent
{
	function proceed()
	{
		parent::proceed();
		
		$coo_customers = MainFactory::create_object('GMDataObject', array(TABLE_CUSTOMERS, array('customers_id' => $this->v_data_array['customers_id'])));
		
		if($coo_customers->get_data_value('customers_status') === '0')
		{
			//clear ADMIN-Cache
			$coo_cache = DataCache::get_instance();
			$coo_cache->clear_cache_by_tag('ADMIN');
		}
	}
}