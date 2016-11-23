<?php
/* --------------------------------------------------------------
   gngp_layer_init.inc.php 2014-07-17 gambio
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

require_once(DIR_FS_CATALOG.'GXEngine/GXEngine_init.inc.php');

require_once(DIR_FS_CATALOG.'system/core/BaseClass.inc.php');

require_once(DIR_FS_CATALOG.'system/core/logging/Debugger.inc.php');
require_once(DIR_FS_CATALOG.'system/core/caching/DataCache.inc.php');

require_once(DIR_FS_CATALOG.'system/core/caching/CachedDirectory.inc.php');
require_once(DIR_FS_CATALOG.'system/core/Registry.inc.php');
require_once(DIR_FS_CATALOG.'system/core/ClassRegistry.inc.php');
require_once(DIR_FS_CATALOG.'system/core/ClassOverloadRegistry.inc.php');
require_once(DIR_FS_CATALOG.'system/core/MainFactory.inc.php');
require_once(DIR_FS_CATALOG.'system/core/MainAutoloader.inc.php');

require_once(DIR_FS_CATALOG.'system/core/ContentView.inc.php');
require_once(DIR_FS_CATALOG.'system/core/AjaxHandler.inc.php');

require_once(DIR_FS_CATALOG.'system/core/GMDataObject.inc.php');
require_once(DIR_FS_CATALOG.'system/core/GMDataObjectGroup.inc.php');