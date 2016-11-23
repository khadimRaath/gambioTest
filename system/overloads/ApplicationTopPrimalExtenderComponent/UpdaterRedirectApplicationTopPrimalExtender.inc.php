<?php
/* --------------------------------------------------------------
   UpdaterRedirectApplicationTopPrimalExtender.inc.php 2015-07-03 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class UpdaterRedirectApplicationTopPrimalExtender extends UpdaterRedirectApplicationTopPrimalExtender_parent
{
	function proceed()
	{
		parent::proceed();

		// do not use gm_get_conf() to avoid caching problems
		$query = 'SELECT `gm_value` FROM `gm_configuration` WHERE `gm_key` = "INSTALLED_VERSION" LIMIT 1';
		$result = xtc_db_query($query);
		if(xtc_db_num_rows($result) == 1)
		{
			$row = xtc_db_fetch_array($result);
			$installedVersion = $row['gm_value'];
		}
		else
		{
			$installedVersion = '';
		}

		include(DIR_FS_CATALOG . 'release_info.php');
		if($gx_version != $installedVersion && $_SESSION['customers_status']['customers_status_id'] === '0')
		{
			$redirectUrl =  DIR_WS_CATALOG . 'gambio_updater';
			if(ENABLE_SSL === true)
			{
				$redirectUrl = HTTPS_SERVER . $redirectUrl;
			}
			xtc_redirect($redirectUrl);
		}
	}
}