<?php
/* --------------------------------------------------------------
   shop.php 2015-05-21 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

require_once('includes/application_top_main.php');

// includes classes and functions are needed for frontend output
include_once DIR_FS_CATALOG . 'gm/inc/gm_get_privacy_link.inc.php';
include_once DIR_FS_CATALOG . 'gm/inc/gm_get_content_by_group_id.inc.php';
if(!isset($GLOBALS['breadcrumb']))
{
	$GLOBALS['breadcrumb'] = new breadcrumb();
}
include_once DIR_FS_CATALOG . 'includes/classes/boxes.php';
if(!isset($GLOBALS['messageStack']))
{
	$GLOBALS['messageStack'] = new messageStack();
}

$httpService = StaticGXCoreLoader::getService('Http');

$httpContext = $httpService->getHttpContext();
$httpService->handle($httpContext);

