<?php
/*
  MailBeez
  http://www.mailbeez.com

  Copyright (c) 2010 - 2015 MailBeez

  inspired and in parts based on
  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
 */

// loads mailbeez environment
// must also work for page request w/o application_top loaded

function set_mailbeez_env()
{
    if (!defined('MH_DIR_FS_CATALOG') && defined('DIR_FS_CATALOG')) {
        // application_top loaded
        define('MH_DIR_FS_CATALOG', (substr(DIR_FS_CATALOG, -1) != '/') ? DIR_FS_CATALOG . '/' : DIR_FS_CATALOG);
        define('MH_DIR_WS_CATALOG', (substr(DIR_WS_CATALOG, -1) != '/') ? DIR_WS_CATALOG . '/' : DIR_WS_CATALOG);
        $_MH_DIR_FS_CATALOG = MH_DIR_FS_CATALOG;
    } else {
        // request with application_top not loaded
        $_MH_DIR_FS_CATALOG = '';
    }

    // set MH_ROOT_PATH
    if (!defined('MH_ROOT_PATH')) {
        // default location
        $_MH_ROOT_PATH = 'mailhive/';
        $_mh_search_paths = array('mailhive/', 'ext/mailhive/', 'includes/external/mailhive/');

        foreach ($_mh_search_paths as $_MH_ROOT_PATH_TRY) {
            if (file_exists($_MH_DIR_FS_CATALOG . $_MH_ROOT_PATH_TRY . 'cloudbeez/cloudloader_core.php')) {
                $_MH_ROOT_PATH = $_MH_ROOT_PATH_TRY;
                break;
            }
        }

        define('MH_ROOT_PATH', $_MH_ROOT_PATH);
    }
}
