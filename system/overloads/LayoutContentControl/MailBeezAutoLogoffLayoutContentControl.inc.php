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

class MailBeezAutoLogoffLayoutContentControl extends MailBeezAutoLogoffLayoutContentControl_parent
{
	protected function isRequestInWhitelist()
	{
		$whitelist = array('product_reviews_write.php',
		                   'product_reviews.php',
		                   'reviews.php'
		);

		return in_array(basename(gm_get_env_info('SCRIPT_NAME')), $whitelist);
	}

	public function proceed()
	{
	    $parentReturn = parent::proceed();

	    if($this->isRequestInWhitelist()) {
		    if ((defined('MAILBEEZ_MAILHIVE_STATUS') && MAILBEEZ_MAILHIVE_STATUS == 'True')
		        && (defined('MAILBEEZ_REVIEW_ADVANCED_AUTOLOGIN') && MAILBEEZ_REVIEW_ADVANCED_AUTOLOGIN == 'True')) {
			    // MailBeez review advanced autologoff script
			    if (file_exists(MH_DIR_FS_CATALOG . MH_ROOT_PATH . 'mailbeez/review_advanced/includes/autologoff.php')) {
				    include_once(MH_DIR_FS_CATALOG . MH_ROOT_PATH . 'mailbeez/review_advanced/includes/autologoff.php');
			    }
		    }
	    }

        return $parentReturn;
    }
}