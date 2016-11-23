<?php 
/* --------------------------------------------------------------
   Yoochoose GmbH
   http://www.yoochoose.com
   Copyright (c) 2011 Yoochoose GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   -------------------------------------------------------------- */

    // Does all the steps needed for accessing the database 
    // Only "configure.php" must be included before this file.
    // It is used, if the including of "application_top.php" is not
    // possible. 
    
    define('_VALID_XTC',true);
    define('TABLE_ADMIN_ACCESS', 'admin_access');
    define('TABLE_LANGUAGES', 'languages');
    define('DEFAULT_LANGUAGE', 'en');
    
    
    define('FILENAME_LOGIN', '../login.php');
    define('TABLE_CONFIGURATION', 'configuration');
    define('STORE_DB_TRANSACTIONS', 'false');
    

    require_once(DIR_FS_INC . 'xtc_not_null.inc.php');
    require_once(DIR_FS_INC . 'xtc_get_top_level_domain.inc.php');
    require_once(DIR_FS_CATALOG . 'gm/inc/gm_get_conf.inc.php');
   
    require_once(DIR_FS_ADMIN . DIR_WS_FUNCTIONS . 'database.php');
    require_once(DIR_FS_ADMIN . DIR_WS_FUNCTIONS . 'general.php');
    require_once(DIR_FS_ADMIN . DIR_WS_FUNCTIONS . 'html_output.php');

    // make a connection to the database... now
    xtc_db_connect() or die('Unable to connect to database server!');

    require_once(DIR_FS_CATALOG . 'includes/yoochoose/functions.php');
    require_once(DIR_FS_ADMIN . 'includes/modules/yoochoose/usage_data_common.php');
    
?>