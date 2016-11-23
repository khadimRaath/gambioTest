<?php
/* --------------------------------------------------------------
   include_checkout_confirmation.php 2012-02-14 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2012 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   ----------------------------------------------------------

/* -----------------------------------------------------------------------------------------
   Copyright (c) 2011 mediafinanz AG

   This program is free software: you can redistribute it and/or modify
   it under the terms of the GNU General Public License as published by
   the Free Software Foundation, either version 3 of the License, or
   (at your option) any later version.

   This program is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   GNU General Public License for more details.

   You should have received a copy of the GNU General Public License
   along with this program.  If not, see http://www.gnu.org/licenses/.
   ---------------------------------------------------------------------------------------

 * @author Marcel Kirsch
 */
$version = phpversion();
$majorVersion = explode('.', $version);
$majorVersion = intval($majorVersion[0]);

$coo_mediafinanz = MainFactory::create_object('GMDataObject', array('mf_config', array('config_key' => 'clientLicence')));
$t_mediafinanz_licence = $coo_mediafinanz->get_data_value('config_value');

if($majorVersion < 5 || empty($t_mediafinanz_licence)) 
{
    return;
}

// MEDIAFINANZ START
require ('admin/includes/modules/mediafinanz/models/MF/Suspect.php');
require ('admin/includes/modules/mediafinanz/models/MF/Address.php');
require ('admin/includes/modules/mediafinanz/models/MF/Config.php');
require ('admin/includes/modules/mediafinanz/models/MF/Score/Decision.php');


$config = MF_Config::getInstance();
$suspect = MF_Suspect::createSuspect((int)$_SESSION['customer_id']);
$paymentRequestModules = explode(',', $config->getValue('requestOnModules'));

if ( MF_Score_Decision::isScoreActive($suspect) &&
    ($config->getValue('requestType') == 'paymentDepending') &&
    (in_array(strtolower($_POST['payment']), $paymentRequestModules))
   )
{
    // payment depending validation is active!

    $scoreDecision = new MF_Score_Decision();
    $isScoreAllowed = $scoreDecision->isAllowed();
    $redirect = false;

    if ($isScoreAllowed !== true)
    {
        //we shall not get a score, determine if we have to redirect:
        $redirect = !$scoreDecision->showPaymentModules($isScoreAllowed);
    }
    else
    {
        $scoreResult = $suspect->getScoreResult($_SESSION['cart']->total);

        if ((!$scoreResult) && ($config->getValue('allowPaymentWithNoResult') == 0))
        {
            //no score result (error occured), user wants us to redirect to payment page:
            $redirect = true;
        }
        else if (!$suspect->isPositiveScore($scoreResult))
        {
            //bad score, redirect to payment page:
            $redirect = true;
        }
    }


    if ($redirect)
    {
        //redirect to payment modules, if score is bad:
       $_SESSION['badscore'] = true;
       xtc_redirect(xtc_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL'));
    }
}
else if (!MF_Score_Decision::isScoreActive($suspect))
{
    //scoring is deactivated. automatically hide modules:
    $order->customer['payment_unallowed'] = $config->getValue('paymentModules');
}
// MEDIAFINANZ END
?>