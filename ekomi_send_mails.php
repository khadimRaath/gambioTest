<?php
/* --------------------------------------------------------------
   ekomi_send_mails.php 2014-03-18 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

require_once('includes/application_top.php');

// no token, no go
$t_secure_token = LogControl::get_secure_token();
if(empty($t_secure_token) || $t_secure_token != gm_prepare_string($_GET['token'], true))
{
	xtc_db_close();
	
	die();
}

$coo_ekomi_manager = MainFactory::create_object('EkomiManager', array(gm_get_conf('EKOMI_API_ID'), gm_get_conf('EKOMI_API_PASSWORD')));
$t_success = $coo_ekomi_manager->send_mails();

if($t_success)
{
	echo 'Mails successfully sent.';
}
else
{
	echo 'A failure occured. Check your ekomi_errors-logfile for more information.';
}

xtc_db_close();