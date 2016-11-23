<?php
/* --------------------------------------------------------------
   DataCacheCheckoutSuccessExtender.inc.php 2014-11-12 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class DataCacheCheckoutSuccessExtender extends DataCacheCheckoutSuccessExtender_parent
{
	function proceed()
	{
		parent::proceed();
		
		$coo_cache = DataCache::get_instance();
		$coo_cache->clear_cache_by_tag('CHECKOUT');
	}
}