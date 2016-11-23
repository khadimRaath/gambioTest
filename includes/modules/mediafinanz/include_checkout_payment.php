<?php
/* --------------------------------------------------------------
   include_checkout_payment.php 2012-02-14 gm
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
require ('admin/includes/modules/mediafinanz/models/MF/DummyError.php');
require ('admin/includes/modules/mediafinanz/models/MF/Score/Decision.php');

$config = MF_Config::getInstance();
$suspect = MF_Suspect::createSuspect((int)$_SESSION['customer_id']);

if (MF_Score_Decision::isScoreActive($suspect))
{
    //automatic solvency check is activated!

    $scoreDecision = new MF_Score_Decision();

    //determine if it is allowed to ask for a score:
    $isScoreAllowed = $scoreDecision->isAllowed();

    if ($isScoreAllowed !== true)
    {
        //we shall not get a score, determine if all payment modules should be displayed:
        if (!$scoreDecision->showPaymentModules($isScoreAllowed))
        {
            //hide payment modules:
            $order->customer['payment_unallowed'] = $config->getValue('paymentModules');
        }

        if ($isScoreAllowed == MF_Score_Decision::DECISION_BAD_SCORE_IN_SESSION)
        {
            //preparation of variables to show an error notice in template block error:
            $GLOBALS['MF_DummyError'] = new MF_DummyError($config->getValue('paymentErrorText'));
            $_GET['payment_error'] = 'MF_DummyError';
            unset($_SESSION['badscore']);
        }
    }
    else
    {
        if (($config->getValue('requestType') == 'always') ||
             MF_Suspect::hasValidScore((int)$_SESSION['customer_id'], $_SESSION['cart']->total))
        {
            //asks for a score or gets an old but valid score from database!
            $scoreResult = $suspect->getScoreResult($_SESSION['cart']->total);

            if ((!$scoreResult) && ($config->getValue('allowPaymentWithNoResult') == 0))
            {
                //no score result (error occured), user wants us to hide payment modules:
                $order->customer['payment_unallowed'] = $config->getValue('paymentModules');
            }
            else if (!$suspect->isPositiveScore($scoreResult))
            {
                //bad score, hide payment modules:
                $order->customer['payment_unallowed'] = $config->getValue('paymentModules');
            }
        }
    }
}
else
{
    //scoring is deactivated. automatically hide modules:
    $order->customer['payment_unallowed'] = $config->getValue('paymentModules');
}
// MEDIAFINANZ END
?>