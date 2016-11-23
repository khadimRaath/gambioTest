<?php

/*
  MailBeez Automatic Trigger Email Campaigns
  http://www.mailbeez.com

  Copyright (c) 2010 - 2015 MailBeez

  inspired and in parts based on
  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/

class MailBeezApplicationBottomExtender extends MailBeezApplicationBottomExtender_parent
{
    function proceed()
    {
        if (defined('MAILBEEZ_MAILHIVE_STATUS') && MAILBEEZ_MAILHIVE_STATUS == 'True') {

            define('MH_DIR_FS_CATALOG', (substr(DIR_FS_CATALOG, -1) != '/') ? DIR_FS_CATALOG . '/' : DIR_FS_CATALOG);

            ob_start();
            // MailBeez
            if (defined('MAILBEEZ_CRON_SIMPLE_STATUS') && MAILBEEZ_CRON_SIMPLE_STATUS == 'True') {
                if (file_exists(MH_DIR_FS_CATALOG . MH_ROOT_PATH . 'configbeez/config_cron_simple/includes/cron_simple_inc.php')) {
                    include(MH_DIR_FS_CATALOG . MH_ROOT_PATH . 'configbeez/config_cron_simple/includes/cron_simple_inc.php');
                }
            }
            if (defined('MAILBEEZ_CRON_ADVANCED_STATUS') && MAILBEEZ_CRON_ADVANCED_STATUS == 'True') {
                if (file_exists(MH_DIR_FS_CATALOG . MH_ROOT_PATH . 'configbeez/config_cron_advanced/includes/cron_advanced_inc.php')) {
                    include(MH_DIR_FS_CATALOG . MH_ROOT_PATH . 'configbeez/config_cron_advanced/includes/cron_advanced_inc.php');
                }
            }
            // - MailBeez

            // MailBeez BigData Tracking
            if (file_exists(DIR_FS_CATALOG . MH_ROOT_PATH . 'configbeez/config_ezako/includes/eztracker.php')) {
                include(DIR_FS_CATALOG . MH_ROOT_PATH . 'configbeez/config_ezako/includes/eztracker.php');
            }
            // MailBeez BigData Tracking

            $this->v_output_buffer['MAILBEEZ_BOTTOM_CODE'] = ob_get_contents();
            ob_end_clean();
        }

        parent::proceed();

    }
}
