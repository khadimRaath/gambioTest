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
   BeezDesk Integration
   --------------------------------------------------------------
*/

class BeezDeskApplicationBottomExtender extends BeezDeskApplicationBottomExtender_parent
{
    function proceed()
    {
        if (defined('MAILBEEZ_MAILHIVE_STATUS') && MAILBEEZ_MAILHIVE_STATUS == 'True') {
            ob_start();
            if (defined('MAILBEEZ_BEEZDESK_CHAT_WIDGET_ID') && MAILBEEZ_BEEZDESK_CHAT_WIDGET_ID != '') {
                // Beezdesk Chat
                $beezdesk_button_id = MAILBEEZ_BEEZDESK_CHAT_WIDGET_ID;
                define('MH_DIR_FS_CATALOG', (substr(DIR_FS_CATALOG, -1) != '/') ? DIR_FS_CATALOG . '/' : DIR_FS_CATALOG);
                if (file_exists(MH_DIR_FS_CATALOG . MH_ROOT_PATH . 'configbeez/config_beezdesk/includes/beezdesk_chat.php')) {
                    include(MH_DIR_FS_CATALOG . MH_ROOT_PATH . 'configbeez/config_beezdesk/includes/beezdesk_chat.php');
                }
                // Beezdesk Chat

                $this->v_output_buffer['BEEZDESK_BOTTOM_CODE'] = ob_get_contents();
                ob_end_clean();
            }
        }
        parent::proceed();
    }
}
