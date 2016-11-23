<?php
/*
  MailBeez Automatic Trigger Email Campaigns
  http://www.mailbeez.com

  Copyright (c) 2010 - 2014 MailBeez

  inspired and in parts based on
  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]

 */


///////////////////////////////////////////////////////////////////////////////
///																			 //
///                 MailBeez Core file - do not edit                         //
///                                                                          //
///////////////////////////////////////////////////////////////////////////////

require('includes/application_top.php');

require_once(DIR_FS_INC . 'set_mailbeez_env.inc.php');
set_mailbeez_env();


if (isset($_POST['cloudloader_mode']) || isset($_GET['cloudloader_mode'])) {
    // installer entrypoint
    if (file_exists(MH_DIR_FS_CATALOG . MH_ROOT_PATH . 'cloudbeez/dev_environment.php')) {
        include(MH_DIR_FS_CATALOG . MH_ROOT_PATH . 'cloudbeez/dev_environment.php');
    }
    require_once(MH_DIR_FS_CATALOG . MH_ROOT_PATH . 'cloudbeez/cloudloader/bootstrap/inc_mailbeez.php');
} else {
    if (file_exists(MH_DIR_FS_CATALOG . MH_ROOT_PATH . 'common/main/inc_mailbeez.php')) {
        // mailbeez installed
        require_once(MH_DIR_FS_CATALOG . MH_ROOT_PATH . 'common/main/inc_mailbeez.php');
    } else {
        // not yet installed, load installer
        if (file_exists(MH_DIR_FS_CATALOG . MH_ROOT_PATH . 'common/local/devsettings.php')) {
            include(MH_DIR_FS_CATALOG . MH_ROOT_PATH . 'common/local/devsettings.php');
        }
        // Please install MailBeez
        if (defined('MAILBEEZ_INSTALLER_DISABLED') && MAILBEEZ_INSTALLER_DISABLED) {
            echo "installer disabled";
        } else {
            require_once(MH_DIR_FS_CATALOG . MH_ROOT_PATH . 'cloudbeez/cloudloader/bootstrap/inc_cloudloader_core_bootstrap.php');
        }
    }
}
