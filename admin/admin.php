<?php
/* --------------------------------------------------------------
   admin.php 2015-09-28 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

require_once('includes/application_top.php');
$httpService = StaticGXCoreLoader::getService('Http');
$httpContext = $httpService->getHttpContext();
$httpService->handle($httpContext);