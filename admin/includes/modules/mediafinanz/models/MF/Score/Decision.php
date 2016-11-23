<?php

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

/**
 * Class to handle decision several score decisions
 *
 * @author Marcel Kirsch
 * @version 2008-01-07
 *
 */
class MF_Score_Decision
{
    const DECISION_UNDER_MIN_AMOUNT     = 1;
    const DECISION_OVER_MAX_AMOUNT      = 2;
    const DECISION_BAD_SCORE_IN_SESSION = 3;



    /**
     * Determines if it is ok to ask for a score
     *
     * returns true or the reason for not asking a score (as int value)
     *
     * @return mixed
     */
    public function isAllowed()
    {
        $config = MF_Config::getInstance();

        if ($_SESSION['cart']->total < $config->getValue('minAmountForRequest'))
        {
            return self::DECISION_UNDER_MIN_AMOUNT;
        }

        if (($_SESSION['cart']->total > $config->getValue('maxAmountForRequest')) &&
            ($config->getValue('maxAmountForRequest') != 0))
        {
            return self::DECISION_OVER_MAX_AMOUNT;
        }

        if (isset($_SESSION['badscore']))
        {
            return self::DECISION_BAD_SCORE_IN_SESSION;
        }

        return true;
    }



    /**
     * Determines if it is ok to show payment modules depending on decision of function isAllowed()
     *
     * @param int $decision
     * @return boolean
     */
    public function showPaymentModules($decision)
    {
        $config = MF_Config::getInstance();

        if ($decision == self::DECISION_UNDER_MIN_AMOUNT)
        {
            //total amount is less than minAmount, do not get a score but optional hide some payment modules!
            if ($config->getValue('allowPaymentUnderMinAmount') == 0)
            {
                return false;
            }
        }

        if ($decision == self::DECISION_BAD_SCORE_IN_SESSION)
        {
            return false;
        }

        if ($decision == self::DECISION_OVER_MAX_AMOUNT)
        {
            //total amount is more than maxAmount, do not get a score but optional hide some payment modules!
            if ($config->getValue('allowPaymentOverMaxAmount') == 0)
            {
                return false;
            }
        }

        return true;
    }



    /**
     * Returns wether score for this suspect is active or not
     *
     * @param MF_Suspect $suspect
     * @return bool
     */
    public static function isScoreActive(MF_Suspect $suspect)
    {
        $config = MF_Config::getInstance();

        if (($suspect->isCompany() && ($config->getValue('company.active') == 1)) ||
            (!$suspect->isCompany() && ($config->getValue('person.active') == 1)))
            {
                return true;
            }

        return false;
    }
}