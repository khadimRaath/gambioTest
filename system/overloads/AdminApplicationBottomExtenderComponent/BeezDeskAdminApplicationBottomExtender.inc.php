<?php
/*
  MailBeez Automatic Trigger Email Campaigns
  http://www.mailbeez.com

  Copyright (c) 2010 - 2015 MailBeez

  inspired and in parts based on
  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]

 */

/* --------------------------------------------------------------
   MailBeez Integration
   --------------------------------------------------------------
*/

class BeezDeskAdminApplicationBottomExtender extends BeezDeskAdminApplicationBottomExtender_parent
{
    function proceed()
    {
        parent::proceed();

        if (defined('MAILBEEZ_MAILHIVE_STATUS') && MAILBEEZ_MAILHIVE_STATUS == 'True') {
            if (defined('MAILBEEZ_INSIGHT_VIEW_STATUS') && MAILBEEZ_INSIGHT_VIEW_STATUS == 'True') {
                // BeezDesk
                // BOF: Mailbeez Customer Insight
                define('MH_DIR_FS_CATALOG', (substr(DIR_FS_CATALOG, -1) != '/') ? DIR_FS_CATALOG . '/' : DIR_FS_CATALOG);
                if (file_exists(MH_DIR_FS_CATALOG . MH_ROOT_PATH . 'configbeez/config_customer_insight/includes/admin_footer_include.php')) {
                    include(MH_DIR_FS_CATALOG . MH_ROOT_PATH . 'configbeez/config_customer_insight/includes/admin_footer_include.php');
                }
                // EOF: Mailbeez Customer Insight
                // BeezDesk
            }
        }
    }
}
