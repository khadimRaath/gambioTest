<?php
/* --------------------------------------------------------------
   AccumioClient.php 2016-07-04
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
 * Client for accumio business score
 *
 * @author Marcel Kirsch
 * @version 2009-01-07
 *
 */
class MF_Score_AccumioClient implements MF_Score_Interface, MF_Score_CompanyInterface
{
    const JUSTIFICATION       = 'ABK';
    const CONSENT_DECLARATION = 99;

    private $auth;
    private $soapClient;



    /**
     * Creates a new AccumioClient
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

        //set soap client:
        $url = 'https://soap.mediafinanz.de/accumio200.wsdl';
        if($this->_isUrlValid($url))
        {
            $this->soapClient = new SoapClient($url, $options);
        }
    }



    /**
     * Gets a new score via soap client
     * Returns an scoreArray on success and false otherwise
     *
     * @param MF_Suspect $suspect
     * @return mixed
     */
    public function getScore(MF_Suspect $suspect)
    {
        //build suspect array:
        $streetArray = $suspect->getAddress()->getStreetArray();

        $suspectArray = array('company'     => $suspect->getCompany(),
                              'street'      => $streetArray['street'],
                              'houseNumber' => $streetArray['houseNumber'],
                              'postcode'    => $suspect->getAddress()->getPostcode(),
                              'city'        => $suspect->getAddress()->getCity());

        array_walk_recursive($suspectArray, array('MF_Misc', 'toUtf8'));


        /*
         * ::ATTENTION::
         * We use buergel product getRiskCheckStandardScore at this point,
         * till the whole gambio module is redesigned
         * The old accumio business product in not working anymore!
         */
        $options    = array('trace'       => 1,
                            'compression' => true,
                            'exceptions'  => true);

        $url = 'https://soap.mediafinanz.de/buergel200.wsdl';
        if($this->_isUrlValid($url))
        {
            $soapClient = new SoapClient($url, $options);
        }

        try
        {
            $result     = $soapClient->getRiskCheckStandardScore($this->auth, $suspectArray, 2);

            if (empty($result))
            {
                return false;
            }

            if ($result->scoreResult->score == 0)
            {
                MF_Misc::errorLog($suspect->getCustomerId(), 'Firma hat einen Score von 0 erhalten');

                $scoreResult = array('score'       => 0,
                                     'text'        => 'Firma hat einen Score von 0 erhalten',
                                     'requestTime' => time());

                $this->saveScore($suspect, $scoreResult);

                return $scoreResult;
            }

            //summarize additional information:
            $text = 'Firmenname: '   . utf8_decode($result->scoreResult->name1) . '<br />'
                  . 'Straße: '       . utf8_decode(trim($result->scoreResult->address->street . ' '
                                     . $result->scoreResult->address->houseNumber)) .'<br />'
                  . 'Postleitzahl: ' . $result->scoreResult->address->postcode . '<br />'
                  . 'Ort: '          . utf8_decode($result->scoreResult->address->city) . '<br />'
                  . 'Text: '         . utf8_decode($result->scoreResult->scoreText) . '<br />';

            //build score array:
            $scoreResult = array('score'       => $result->scoreResult->score,
                                 'text'        => $text,
                                 'requestTime' => time());

            $this->saveScore($suspect, $scoreResult);

            return $scoreResult;


//        //this is the old accumio product! ::TODO:: replace with new
//        try
//        {
//            //first ask for the company list to identify the company:
//            $companyList = $this->soapClient->getBusinessSpecialCompanyList($this->auth,
//                                                                            'A1',
//                                                                            $suspectArray,
//                                                                            self::JUSTIFICATION,
//                                                                            self::CONSENT_DECLARATION);
//
//            if (isset($companyList->companyListResult))
//            {
//                $companyList = $companyList->companyListResult;
//
//                if (count($companyList->companyList) == 0)
//                {
//                    //no company found!
//                    return false;
//                }
//
//                $config = MF_Config::getInstance();
//                $minSimilarity = $config->getValue('accumio.minSimilarity');
//
//                $bestHit = $companyList->companyList[0];
//
//                if ($bestHit->similarity < $minSimilarity)
//                {
//                    //found company is not similar enough to company we asked for!
//                    MF_Misc::errorLog($suspect->getCustomerId(), 'Gefundene Firma hatte nur eine Ähnlichkeit von '.$bestHit->similarity.'%');
//                    return false;
//                }
//
//                //get score of found company
//                $result = $this->soapClient->getBusinessSpecialScore($this->auth, $companyList->jobIdentifier, $bestHit->objectId, $bestHit->objectIdType);
//
//                if (empty($result))
//                {
//                    return false;
//                }
//
//                if ($result->scoreResult->classificationCode == 0)
//                {
//                    MF_Misc::errorLog($suspect->getCustomerId(), 'Firma hat einen Score von 0 erhalten');
//
//                    $scoreResult = array('score'       => 0,
//                                         'text'        => 'Firma hat einen Score von 0 erhalten',
//                                         'requestTime' => time());
//
//                    $this->saveScore($suspect, $scoreResult);
//
//                    return $scoreResult;
//                }
//
//                //summarize additional information:
//                $text = 'Firmenname: '   . utf8_decode($result->scoreResult->company) . '<br />'
//                      . 'Straße: '       . utf8_decode(trim($result->scoreResult->address->street . ' '
//                                         . $result->scoreResult->address->houseNumber)) .'<br />'
//                      . 'Postleitzahl: ' . $result->scoreResult->address->postcode . '<br />'
//                      . 'Ort: '          . utf8_decode($result->scoreResult->address->city) . '<br />'
//                      . 'Telefon: '      . $result->scoreResult->phonenumber . '<br/>'
//                      . 'Mobil: '        . $result->scoreResult->mobilePhonenumber . '<br /><br />'
//                      . 'Text: '         . utf8_decode($result->scoreResult->classificationText) . '<br />'
//                      . 'Ähnlichkeit: '  . $bestHit->similarity.'%';
//
//                //build score array:
//                $scoreResult = array('score'       => $result->scoreResult->classificationCode,
//                                     'text'        => $text,
//                                     'requestTime' => time());
//
//                $this->saveScore($suspect, $scoreResult);
//
//                return $scoreResult;
//            }
//            else
//            {
//                //an error occured!
//                $errorList = $companyList->errorList;
//                $errorString = implode(', ', $errorList);
//
//                MF_Misc::errorLog($suspect->getCustomerId(), utf8_decode($errorString));
//
//                return false;
//            }
        }
        catch (Exception $e)
        {
            MF_Misc::errorLog($suspect->getCustomerId(), utf8_decode($e->getMessage()));
            return false;
        }

    }



    /**
     * returns a score out of the database
     * Returns a scoreArray on success and false otherwise
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
                                   lastCheck as requestTime
                               FROM
                                   mf_score_results
                               WHERE
                                   customerId = '" . $suspect->getCustomerId() . "'
                               LIMIT 1");

        $erg = mysqli_fetch_assoc($query);

        return (!empty($erg)) ? $erg : false;
    }



    /**
     * Returs if we already have a valid score for that suspect
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
     * Saves a score into database
     *
     * @param MF_Suspect $suspect
     * @param array $score
     */
    public function saveScore(MF_Suspect $suspect, $score)
    {
        // store result in db
        xtc_db_query("REPLACE INTO
                                  mf_score_results (customerId, score, explanation, lastCheck)
                              VALUES
                                  ('" . $suspect->getCustomerId() . "',
                                   '" . $score['score'] . "',
                                   '" . $score['text'] . "',
                                   '" . $score['requestTime'] . "')");
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

        return (($score['score'] <= $config->getValue('accumio.score')) && ($score['score'] != 0));
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