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

require_once(DIR_FS_INC . 'set_mailbeez_env.inc.php');
set_mailbeez_env();

class MailBeezAnalyticsClicktrackerApplicationTopExtender extends MailBeezAnalyticsClicktrackerApplicationTopExtender_parent
{
    function proceed()
    {
        if (defined('MAILBEEZ_MAILHIVE_STATUS') && MAILBEEZ_MAILHIVE_STATUS == 'True') {
            if (file_exists(MH_DIR_FS_CATALOG . MH_ROOT_PATH . 'includes/clicktracker.php')) {
                include_once(MH_DIR_FS_CATALOG . MH_ROOT_PATH . 'includes/clicktracker.php');
            }
        }

        parent::proceed();
    }
}
