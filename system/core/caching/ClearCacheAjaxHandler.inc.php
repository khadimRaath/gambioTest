<?php
/* --------------------------------------------------------------
   ClearCacheAjaxHandler.inc.php 2014-11-12 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class ClearCacheAjaxHandler extends AjaxHandler
{
	function get_permission_status($p_customers_id=NULL)
	{
		if($_SESSION['customers_status']['customers_status_id'] === '0')
		{
			#admins only
			return true;
		}
		return false;
	}

	function proceed()
	{
		$coo_control = MainFactory::create_object('CacheControl');
		$coo_control->clear_data_cache();
		$coo_control->clear_content_view_cache();
		$coo_control->clear_templates_c();

		$coo_control->remove_reset_token();
		
		//clear ADMIN-Cache
		$coo_cache = DataCache::get_instance();
		$coo_cache->clear_cache_by_tag('ADMIN');
		
		$this->v_output_buffer = GM_TOP_MENU_CACHE_EMPTIED;

		return true;
	}
}