<?php
/* --------------------------------------------------------------
   SchufaClient.php 2016-07-04
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------

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
 * Class for schufa person score
 *
 * @author Marcel Kirsch
 * @version 2009-11-21
 *
 */
class MF_Score_SchufaClient implements MF_Score_Interface, MF_Score_PersonInterface
{
    private $auth;
    private $soapClient;



    /**
     * Creates a new SchufaClient
     *
     */
    public function __construct()
    {
        $config = MF_Config::getInstance();

        $licenceKey = md5($config->getValue('applicationLicence').$config->getValue('clientLicence'));

        //set sandbox if set in config:
        $sandbox = ($config->getValue('sandbox') == 0) ? false : true;

        //build auth:
        $auth = array('clientId'   => $config->getValue('clientId'),
                      'licenceKey' => $licenceKey,
                      'sandbox'    => $sandbox);

        $this->auth = $auth;

        $options = array('trace'       => 1,
                         'compression' => true,
                         'exceptions'  => true);

        //create soap client:
        $url = 'https://soap.mediafinanz.de/schufa201.wsdl';
        if($this->_isUrlValid($url))
        {
            $this->soapClient = new SoapClient($url, $options);
        }
    }



    /**
     * Gets a score via soap
     * Returns scoreArray on success and false otherwise
     *
     * @param MF_Suspect $suspect
     * @return mixed
     */
    public function getScore(MF_Suspect $suspect)
    {
        //build suspect array:
        $streetArray = $suspect->getAddress()->getStreetArray();

        $suspectArray = array('firstname'   => $suspect->getFirstname(),
                              'lastname'    => $suspect->getLastname(),
                              'street'      => $streetArray['street'],
                              'houseNumber' => $streetArray['houseNumber'],
                              'postcode'    => $suspect->getAddress()->getPostcode(),
                              'city'        => $suspect->getAddress()->getCity());

        array_walk_recursive($suspectArray, array('MF_Misc', 'toUtf8'));

        try
        {
            //get score for suspect:
            $result = $this->soapClient->getSchufaScore($this->auth, $suspectArray, 'AV');

            //returncode X is returned, if schufa found noone in their database!
            if ($result->returnCode == 'X')
            {
                return false;
            }

            if (!empty($result->scoreResult))
            {
                //normal score response:
                $scoreInformation = $result->scoreResult->score;

                //build score array:
                $scoreResult['score']           = isset($scoreInformation->score) ? $scoreInformation->score : 0;
                $scoreResult['text']            = isset($scoreInformation->text)  ? $scoreInformation->text  : '';

                //persons with score area n, o and p have open negative entries. we handle this as 0 score!
                if (isset($scoreInformation->area) && in_array($scoreInformation->area, array('N', 'O', 'P')))
                {
                    $scoreResult['score'] = 0;
                }

                //entries:
                $entry = '';

                if (!empty($result->scoreResult->entries))
                {
                    //summarize known entries:
                    //::IMPORTANT:: not all entries are negative!
                    $size = sizeof($result->scoreResult->entries);
                    $entryList = $result->scoreResult->entries;
                    for ($i = 0; $i < $size; $i++)
                    {
                        $color = ($i % 2 == 0) ? 'lightgrey' : 'white';

                        $code        = $entryList[$i]->entryCode;
                        $description = isset($entryList[$i]->description) ? $entryList[$i]->description : '';
                        $value       = isset($entryList[$i]->value)       ? $entryList[$i]->value : '';
                        $account     = isset($entryList[$i]->account)     ? $entryList[$i]->account : '';

                        $entry .= '<tr bgcolor="'.$color.'">'
                        . '<td>'.utf8_decode($code).'</td>'
                        . '<td>'.utf8_decode($description).'</td>'
                        . '<td>'.$value.'</td>'
                        . '<td>'.$account.'</td></tr>';
                    }
                }

                $scoreResult['negativeEntries'] = $entry;
                $scoreResult['requestTime']     = time();

                $this->saveScore($suspect, $scoreResult);

                return $scoreResult;
            }
            else if (isset($result->postProcessingNote))
            {
                //deactivated, so this should not be possible:
                return false;
            }
            else if (isset($result->manualRequestRequired))
            {
                $score['score'] = 0;
                $score['text']  = 'Manuelle Anfrage erforderlich: '.$result->manualRequestRequired->informationId;
                $scoreResult['negativeEntries'] = $entry;
                $scoreResult['requestTime']     = time();

                $this->saveScore($suspect, $scoreResult);

                return false;
            }
            else
            {
                //an error occured!
                $errorList = $result->errorList;
                $errorString = implode(', ', $errorList);

                MF_Misc::errorLog($suspect->getCustomerId(), utf8_decode($errorString));

                return false;
            }
        }
        catch (Exception $e)
        {
            MF_Misc::errorLog($suspect->getCustomerId(), utf8_decode($e->getMessage()));
            return false;
        }

    }



    /**
     * Gets an old score out of our database
     * Returns scoreArray on success and false otherwise
     *
     * @param MF_Suspect $suspect
     * @return mixed
     */
    public function getOldScore(MF_Suspect $suspect)
    {
        // First check locale database, if there has been a score result for this user
        $query = xtc_db_query("SELECT
                                   score,
                                   explanation as text,
                                   lastCheck as requestTime,
                                   negativeEntryList as negativeEntries
                               FROM
                                   mf_score_results
                               WHERE
                                   customerId = '" . $suspect->getCustomerId() . "'
                               LIMIT 1");

        $erg = mysqli_fetch_assoc($query);

        return (!empty($erg)) ? $erg : false;
    }



    /**
     * Returns if we already have a valid score for that suspect
     *
     * @param MF_Suspect $suspect
     * @param double $orderTotal
     * @return boolean
     */
    public function hasValidScore(MF_Suspect $suspect, $orderTotal = 0)
    {
        $query = xtc_db_query("SELECT
                                   score,
                                   lastCheck
                               FROM
                                   mf_score_results
                               WHERE
                                   customerId = '"  .$suspect->getCustomerId() . "'
                               LIMIT 1");

        $erg = mysqli_fetch_assoc($query);

        $config = MF_Config::getInstance();

        return (!empty($erg)
                && $erg['lastCheck'] + $config->getValue('recheckSuspect') * 24 * 60 * 60 > time()
                && ($orderTotal <= $config->getValue('orderTotal') || $config->getValue('orderTotal') == 0));
    }



    /**
     * Saves a score in the database
     *
     * @param MF_Suspect $suspect
     * @param array $score
     */
    public function saveScore(MF_Suspect $suspect, $score)
    {
        // store result in db
        xtc_db_query("REPLACE INTO
                                  mf_score_results (customerId, score, explanation, lastCheck, negativeEntryList)
                              VALUES
                                  ('" . $suspect->getCustomerId() . "',
                                   '" . $score['score'] . "',
                                   '" . $score['text'] . "',
                                   '" . $score['requestTime'] . "',
                                   '" . $score['negativeEntries'] . "')");
    }



    /**
     * Returns if the returned score is a positive one
     *
     * @param array $score
     * @return boolean
     */
    public function isPositiveScore($score)
    {
        $config = MF_Config::getInstance();

        return ($score['score'] >= $config->getValue('schufa.score'));
    }
    
    
    /**
     * Checks whether the provided URL is valid or not.
     *
     * @param string $url URL to check
     *
     * @return bool
     */
    protected function _isUrlValid($url)
    {
        try
        {
            $connectChecker = MainFactory::create('ConnectChecker');
            $connectChecker->check_connect($url);
        }
        catch(ConnectCheckerConnectionException $e)
        {
            return false;
        }
        catch(ConnectCheckerCurlMissingException $e)
        {
            return false;
        }
        
        return true;
    }
}