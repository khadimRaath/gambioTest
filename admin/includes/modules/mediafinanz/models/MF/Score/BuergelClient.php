<?php

/* --------------------------------------------------------------
   BuergelClient.php 2016-07-04
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
 * Class for buergel person score
 *
 * @author Marcel Kirsch
 * @version 2009-01-07
 *
 */
class MF_Score_BuergelClient implements MF_Score_Interface, MF_Score_PersonInterface
{
    const JUSTIFICATION = 2;

    private $auth;
    private $soapClient;



    /**
     * Creates a new BuergelClient
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
        $url = 'https://soap.mediafinanz.de/buergel200.wsdl';
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
            $result = $this->soapClient->getConCheckScore($this->auth, $suspectArray, self::JUSTIFICATION);

            if (!empty($result->scoreResult))
            {
                if ($result->scoreResult->score == 0)
                {
                    MF_Misc::errorLog($suspect->getCustomerId(), 'Person hat einen Score von 0,0 erhalten');

                    $scoreResult['score']           = 0;
                    $scoreResult['text']            = utf8_decode($result->scoreResult->scoreText);
                    $scoreResult['negativeEntries'] = '';
                    $scoreResult['requestTime']     = time();

                    $this->saveScore($suspect, $scoreResult);

                    return $scoreResult;
                }

                //negative entries:
                $negativeEntry = '';

                if (!empty($result->scoreResult->negativeEntryList))
                {
                    //summarize negative entries:
                    $size = sizeof($result->scoreResult->negativeEntryList);
                    $negativeEntryList = $result->scoreResult->negativeEntryList;
                    for ($i = 0; $i < $size; $i++)
                    {
                        $color = ($i % 2 == 0) ? 'lightgrey' : 'white';

                        $negativeEntry .= '<tr bgcolor="'.$color.'">'
                        . '<td>'.utf8_decode($negativeEntryList[$i]->code).'</td>'
                        . '<td>'.utf8_decode($negativeEntryList[$i]->text).'</td>'
                        . '<td>'.$negativeEntryList[$i]->value.'</td>'
                        . '<td>'.$negativeEntryList[$i]->valueType.'</td>'
                        . '<td>'.$negativeEntryList[$i]->count.'</td>'
                        . '<td>'.$negativeEntryList[$i]->date.'</td></tr>';
                    }
                }

                //build score array:
                $scoreResult['score']           = $result->scoreResult->score;
                $scoreResult['text']            = utf8_decode($result->scoreResult->scoreText);
                $scoreResult['negativeEntries'] = $negativeEntry;
                $scoreResult['requestTime']     = time();

                $this->saveScore($suspect, $scoreResult);

                return $scoreResult;
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

        return (($score['score'] <= $config->getValue('buergel.score')) && ($score['score'] != 0));
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