<?php
/* --------------------------------------------------------------
   Yoochoose GmbH
   http://www.yoochoose.com
   Copyright (c) 2011 Yoochoose GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
 */

require_once 'functions.php';

$logged_id = getLoggedUserId();

$user_transrefed = $_SESSION['yoochoose_transferred_user'];

echo "<!-- ".($logged_id?"user logged in":"user not logged in")." | ".($user_transrefed?"already transfered":"not transfered"). "-->\n";

if ( ! $user_transrefed  && ! empty($logged_id)) {
	$trUrl = getTransferURL(getAnonymousUserId(), getLoggedUserId());
	echo "<!-- transfering user -->\n";
	echo '<img src="'.$trUrl.'" width="0" height="0" alt="">';
	 
	$_SESSION['yoochoose_transferred_user'] = true;
}

if ($user_transrefed && empty($logged_id)) {
	echo "<!-- dropping transfered user -->\n";
	
	$_SESSION['yoochoose_transferred_user'] = false;
}

// product tracking is moved to ProductInfoContentView.inc.php
// checkout tracking is moved to CheckoutSuccessContentView.inc.php

?>