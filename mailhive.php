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

require_once('inc/set_mailbeez_env.inc.php');
set_mailbeez_env();

if (file_exists(MH_ROOT_PATH . 'common/main/inc_mailhive.php')) {
    include_once(MH_ROOT_PATH . 'common/main/inc_mailhive.php');
} else {
    ?>
    Please install MailBeez
<?php
}
