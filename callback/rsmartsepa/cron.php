<?php
/* --------------------------------------------------------------
  cron.php 2015-04-27 wem
  Smart Payment Solutions GmbH
  http://www.smart-payment-solutions.de/
  Copyright (c) 2015 Smart Payment Solutions GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
 */

// Include the callback/rsmartsepa/RsmartsepaHelper.php
$helperFilePath = str_replace("\\", '/', dirname(__FILE__) . '/RsmartsepaHelper.php');
require_once($helperFilePath);

// ############################################################################
// Check the action
// ############################################################################
RsmartsepaHelper::cronInit('CRON(rsmartsepa): S T A R T E D');
$rsmartsepa_action = trim(RsmartsepaHelper::getRequestValue('rsmartsepaaction', ''));
$rsmartsepa_cronkey = trim(RsmartsepaHelper::getRequestValue('rsmartsepacronkey', ''));
if($rsmartsepa_action != 'rsmartsepacron' || $rsmartsepa_cronkey == '') {
    RsmartsepaHelper::cronAddMessage('CRON(rsmartsepa): Cron not allowed. Wrong action or key');
    RsmartsepaHelper::cronAddMessage('CRON(rsmartsepa): F I N I S H E D');
    RsmartsepaHelper::textOutput(RsmartsepaHelper::cronGetMessagesAsString(), TRUE);
}
// 2015.09.14: Commented out check of RsmartsepaHelper::getCronKey()
//             because application_top.php is not included here and
//             the calculation will fail.
//             Instead the RsmartsepaHelper::getCronKey() will be calculated
//             in callback.php.
//$rsmartsepa_inicronkey = RsmartsepaHelper::getCronKey();
//if($rsmartsepa_inicronkey == '' || $rsmartsepa_inicronkey == 'undefined') {
//    RsmartsepaHelper::cronAddMessage('CRON(rsmartsepa): Cron not allowed. Invalid key');
//    RsmartsepaHelper::cronAddMessage('CRON(rsmartsepa): F I N I S H E D');
//    RsmartsepaHelper::textOutput(RsmartsepaHelper::cronGetMessagesAsString(), TRUE);
//}
//if($rsmartsepa_cronkey != $rsmartsepa_inicronkey) {
//    RsmartsepaHelper::cronAddMessage('CRON(rsmartsepa): Cron not allowed. Invalid key');
//    RsmartsepaHelper::cronAddMessage('CRON(rsmartsepa): F I N I S H E D');
//    RsmartsepaHelper::textOutput(RsmartsepaHelper::cronGetMessagesAsString(), TRUE);
//}

// Include the callback/rsmartsepa/callback.php
$callbackFilePath = str_replace("\\", '/', dirname(__FILE__) . '/callback.php');
require_once($callbackFilePath);

