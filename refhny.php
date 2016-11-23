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

require_once('includes/application_top.php');
require_once('inc/set_mailbeez_env.inc.php');
set_mailbeez_env();

if (file_exists(MH_DIR_FS_CATALOG . MH_ROOT_PATH . 'mailbeez/coupon_referral_honey/includes/inc_refhny.php')) {
    require_once(MH_DIR_FS_CATALOG . MH_ROOT_PATH . 'mailbeez/coupon_referral_honey/includes/inc_refhny.php');
} else {
    ?>
    Please install MailBeez ReferalHoney Module
<?php
}
