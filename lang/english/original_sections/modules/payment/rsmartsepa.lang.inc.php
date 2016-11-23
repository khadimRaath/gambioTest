<?php

/* --------------------------------------------------------------
  rsmartsepa.lang.inc.php 2015-04-24 wem
  English language file for the rsmartsepa payment module
  Smart Payment Solutions GmbH
  http://www.smart-payment-solutions.de/
  Copyright (c) 2015 Smart Payment Solutions GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
 */

$t_language_text_section_content_array = array
(
    'MODULE_PAYMENT_RSMARTSEPA_TEXT_TITLE' => 'Payment with the smartphone',
    'MODULE_PAYMENT_RSMARTSEPA_TEXT_DESCRIPTION' => 'Pay safe and easy with your smartphone. You find more information about rSm@rt on our website: <a href="http://www.smart-payment-solutions.de/" target="_blank">www.smart-payment-solutions.de</a>',
    'MODULE_PAYMENT_RSMARTSEPA_STR_SYSTEMREQUIREMENTS' => 'Systemrequirements',
    'MODULE_PAYMENT_RSMARTSEPA_STR_EXISTING' => 'Exists',
    'MODULE_PAYMENT_RSMARTSEPA_STR_MISSING' => 'Missing',
    'MODULE_PAYMENT_RSMARTSEPA_STR_CHECKOUT_MODULETITLE' => 'Payment with the smartphone',
    'MODULE_PAYMENT_RSMARTSEPA_STR_CHECKOUT_DESCRIPTION' => 'With rSm@rt you pay easily and save in whole Europe. ' .
                                                            'For the payment you need the rSm@rt app and an onlinebanking enabled giro account. ' .
                                                            'The payment process will be initiated by scanning the displayed qr-code with the rSm@rt app. ' .
                                                            'You find more information about rSm@rt on our website: <a href="http://www.smart-payment-solutions.de/" target="_blank">www.smart-payment-solutions.de</a>' .
                                                            '<br/><br/>Install the app now:',
    'MODULE_PAYMENT_RSMARTSEPA_STR_CHECKOUT_CONFIRMATION' => 'Keep your rSm@rtLITE or rSm@rtSEPA app ready. ' .
                                                             'When you klick on the <b>Payment</b> button a qr-code will be displayed, ' .
                                                             'that you have to scan and commit with the app.',
    'MODULE_PAYMENT_RSMARTSEPA_STR_ORDERNUMBER' => 'Order Number',
    'MODULE_PAYMENT_RSMARTSEPA_STR_SELLER' => 'Seller',
    'MODULE_PAYMENT_RSMARTSEPA_STR_AMOUNT' => 'Amount',
    'MODULE_PAYMENT_RSMARTSEPA_STR_SCANTEXT' => 'Start your rSmart app, tap on the symbol "Transfer" and scan the displayed qr-code',
    'MODULE_PAYMENT_RSMARTSEPA_STR_CANCEL' => 'Cancel',
    'MODULE_PAYMENT_RSMARTSEPA_STR_SIMULATEMATCH' => 'Simulate Success',
    'MODULE_PAYMENT_RSMARTSEPA_STR_SIMULATEFAILURE' => 'Simulate Failure',
    'MODULE_PAYMENT_RSMARTSEPA_STR_SIMULATION' => 'Simulation',
    'MODULE_PAYMENT_RSMARTSEPA_STR_ERROR_CREATETRANSACTION' => 'Payment with rSm@rt failed!',
    'MODULE_PAYMENT_RSMARTSEPA_STR_REASON' => 'Reason',
    'MODULE_PAYMENT_RSMARTSEPA_STR_PAYMENTSUCCESS' => 'Payment with rSm@rt was successful',
    'MODULE_PAYMENT_RSMARTSEPA_STR_PAYMENTFAILED' => 'Payment with rSm@rt failed!',
    'MODULE_PAYMENT_RSMARTSEPA_STR_PAYMENTCANCELLED' => 'Payment with rSm@rt was cancelled!',
    'MODULE_PAYMENT_RSMARTSEPA_STR_PREVIEWLINKTITLE' => 'Preview in a new browser tab without javascript functionality',
    'MODULE_PAYMENT_RSMARTSEPA_STR_TEMPLATETESTDESCRIPTION' => 'Test different browser window sizes to see, how the template changes. If you are ready close the rrowser tab',
    'MODULE_PAYMENT_RSMARTSEPA_STR_TOUCHTEXT1' => 'If you have the rSmartSEPA app installed, clicking on this link',
    'MODULE_PAYMENT_RSMARTSEPA_STR_TOUCHTEXT2' => 'will launch the app.',
    'MODULE_PAYMENT_RSMARTSEPA_STR_TOUCHTEXT3' => 'Otherwise the browser will try to open a non existing site.',
    'MODULE_PAYMENT_RSMARTSEPA_STR_TOUCHTEXT4' => 'Do you really want to follow this link?',
    'MODULE_PAYMENT_RSMARTSEPA_STR_INVALID_TERMINALDATA' => 'Invalid configuration. Reason:',
    'MODULE_PAYMENT_RSMARTSEPA_STR_PAYSAVEINEUROPE' => 'Pay realtime, easy and save in whole Europe.',
    'MODULE_PAYMENT_RSMARTSEPA_STR_MATCHFLAGSET' => 'The MATCH simulationflag has been set. The next pollcycle will react on it.',
    'MODULE_PAYMENT_RSMARTSEPA_STR_FAILUREFLAGSET' => 'Das FAILURE simulationflag has been set. The next pollcycle will react on it.',
    'MODULE_PAYMENT_RSMARTSEPA_STR_PAYMENTINVALIDHASH' => 'The payment with rSm@rt was stopped because of an error!',
    'MODULE_PAYMENT_RSMARTSEPA_STR_PAYMENTCANCELFAILED' => 'The payment process could not more be removed on the payment server!',
    
    'MODULE_PAYMENT_RSMARTSEPA_STATUS_TITLE' => 'rSm@rt',
    'MODULE_PAYMENT_RSMARTSEPA_STATUS_DESC' => 'Activate rSm@rt?',
    'MODULE_PAYMENT_RSMARTSEPA_SORT_ORDER_TITLE' => 'Display order',
    'MODULE_PAYMENT_RSMARTSEPA_SORT_ORDER_DESC' => 'Display order. The smalles integer will be shown first',
    'MODULE_PAYMENT_RSMARTSEPA_ZONE_TITLE' => 'Payment zone',
    'MODULE_PAYMENT_RSMARTSEPA_ZONE_DESC' => 'If you select a zone, this payment type will only be avialable in this zone',
    'MODULE_PAYMENT_RSMARTSEPA_ALLOWED_TITLE' => 'Allowed Zones',
    'MODULE_PAYMENT_RSMARTSEPA_ALLOWED_DESC' => 'Enter particular zones that are allowed for this payment type. (e.g. AT,DE (if the field is empty all zones are allowed))',
    'MODULE_PAYMENT_RSMARTSEPA_ORDER_STATUS_ID_ERROR_TITLE' => 'Order state for payment error',
    'MODULE_PAYMENT_RSMARTSEPA_ORDER_STATUS_ID_ERROR_DESC' => 'The state that should be set if the payment failed.<br/><b>Recommended: "rsmartsepa Error"</b>',
    'MODULE_PAYMENT_RSMARTSEPA_ORDER_STATUS_ID_OK_TITLE' => 'Order state for successful payment',
    'MODULE_PAYMENT_RSMARTSEPA_ORDER_STATUS_ID_OK_DESC' => 'The state that should be set if the payment was successful.<br/><b>Recommended: "rsmartsepa Paid"</b>',
    'MODULE_PAYMENT_RSMARTSEPA_ORDER_STATUS_ID_TEMP_TITLE' => 'Temporary order state',
    'MODULE_PAYMENT_RSMARTSEPA_ORDER_STATUS_ID_TEMP_DESC' => 'This state will be set when the payment will be initiated.<br/><b>Recommended: "rsmartsepa Pending"</b>',
    'MODULE_PAYMENT_RSMARTSEPA_TERMINALDATA_URI_TITLE' => '<hr/><u>TERMINAL DATA</u><br/><br/>Terminal URI',
    'MODULE_PAYMENT_RSMARTSEPA_TERMINALDATA_URI_DESC' => 'You find this value in your terminal on the rSm@rt seller portal.',
    'MODULE_PAYMENT_RSMARTSEPA_TERMINALDATA_SELLERID_TITLE' => 'Seller ID (sellerId)',
    'MODULE_PAYMENT_RSMARTSEPA_TERMINALDATA_SELLERID_DESC' => 'You find this value in your project on the rSm@rt seller portal.',
    'MODULE_PAYMENT_RSMARTSEPA_TERMINALDATA_KEY_TITLE' => 'Seller Key (key)',
    'MODULE_PAYMENT_RSMARTSEPA_TERMINALDATA_KEY_DESC' => 'You find this value in your project on the rSm@rt seller portal.',
    'MODULE_PAYMENT_RSMARTSEPA_TERMINALDATA_PROVIDERID_TITLE' => 'Provider ID (providerId)',
    'MODULE_PAYMENT_RSMARTSEPA_TERMINALDATA_PROVIDERID_DESC' => 'You find this value in your terminal on the rSm@rt seller portal.',
    'MODULE_PAYMENT_RSMARTSEPA_TERMINALDATA_COUNTRYID_TITLE' => 'Countrycode (countryId)',
    'MODULE_PAYMENT_RSMARTSEPA_TERMINALDATA_COUNTRYID_DESC' => 'Please enter a 2 charcter country code . (e.g. de)',
    'MODULE_PAYMENT_RSMARTSEPA_TERMINALDATA_SALESPOINTID_TITLE' => 'Salespoint (salesPointId)',
    'MODULE_PAYMENT_RSMARTSEPA_TERMINALDATA_SALESPOINTID_DESC' => 'You find this value in your terminal on the rSm@rt seller portal.',
    'MODULE_PAYMENT_RSMARTSEPA_TERMINALDATA_DESCRIPTION_TITLE' => 'Terminaldescription (description)',
    'MODULE_PAYMENT_RSMARTSEPA_TERMINALDATA_DESCRIPTION_DESC' => 'You find this value in your terminal on the rSm@rt seller portal.',
    'MODULE_PAYMENT_RSMARTSEPA_SIMULATIONMODE_TITLE' => '<hr/>Activate simulationsmode ?',
    'MODULE_PAYMENT_RSMARTSEPA_SIMULATIONMODE_DESC' => 'The simulationsmode is for test purpose without smartphone. IMPORTANT: The order state will be set on success, failure or cancelled anyway.',
    'MODULE_PAYMENT_RSMARTSEPA_DEBUGMODE_TITLE' => 'Debug Mode',
    'MODULE_PAYMENT_RSMARTSEPA_DEBUGMODE_DESC' => 'When active, a javascript debug overlay will be shown in payment screen',
);