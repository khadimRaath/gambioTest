<?php
/* --------------------------------------------------------------
   version_info.php 2012-12-06 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2012 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

include('includes/application_top.php');
include(DIR_FS_CATALOG . 'gm/classes/JSON.php');

$coo_json = new Services_JSON(SERVICES_JSON_LOOSE_TYPE);
$t_shop_versioninfo = 'SHOP_KEY_ERROR';
if(defined('GAMBIO_SHOP_KEY') && isset($_GET['shop_key']) && !empty($_GET['shop_key']) && GAMBIO_SHOP_KEY === $_GET['shop_key'])
{
	$coo_versioninfo = MainFactory::create_object('VersionInfo');
	$t_shop_versioninfo = $coo_versioninfo->get_shop_versioninfo();
}

echo $coo_json->encode($t_shop_versioninfo);