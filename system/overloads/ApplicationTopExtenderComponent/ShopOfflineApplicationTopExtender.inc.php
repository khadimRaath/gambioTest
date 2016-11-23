<?php
/* --------------------------------------------------------------
   ShopOfflineApplicationTopExtender.inc.php 2015-02-23 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class ShopOfflineApplicationTopExtender extends ShopOfflineApplicationTopExtender_parent
{
	function proceed()
	{
		parent::proceed();

		include(DIR_FS_CATALOG . 'release_info.php');
		$installedVersion = $gx_version;

		// do not use gm_get_conf() to avoid caching problems
		$query = 'SELECT `gm_value` FROM `gm_configuration` WHERE `gm_key` = "INSTALLED_VERSION" LIMIT 1';
		$result = xtc_db_query($query);
		if(xtc_db_num_rows($result) == 1)
		{
			$row = xtc_db_fetch_array($result);
			$installedVersion = $row['gm_value'];
		}
		
		if(gm_get_conf('GM_SHOP_OFFLINE') == 'checked' && $_SESSION['customers_status']['customers_status_id'] != 0 || $gx_version != $installedVersion)
		{
			define('SHOP_OFFLINE', true);
		}
		else
		{
			define('SHOP_OFFLINE', false);
		}
	}
}