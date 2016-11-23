<?php
/* --------------------------------------------------------------
   create_account.php 2015-06-22 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

$checkoutStartedParam = '';
if(isset($_GET['checkout_started']) && $_GET['checkout_started'] === '1')
{
	$checkoutStartedParam = '&checkout_started=' . $_GET['checkout_started'];
}

header('Location: ./shop.php?do=CreateRegistree' . $checkoutStartedParam);