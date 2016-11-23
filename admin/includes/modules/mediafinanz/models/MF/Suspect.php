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
 * Suspect Class
 *
 * @author Marcel Kirsch
 * @version 2009-01-07
 */

class MF_Suspect
{
    private $customerId;
    private $company;
    private $sex;
    private $firstname;
    private $lastname;
    private $emailAddress;
    private $phone;
    private $isCompany = false;
    private $dateOfBirth;



    /**
     * Address
     *
     * @var address
     */
    private $address;



    /**
     * Contains class with score funtions
     *
     * @var MF_Score_Interface
     */
    private $scoreClass;



    /**
     * Constructor
     *
     * @param string $customerId
     * @param string $company
     * @param string $sex
     * @param string $firstname
     * @param string $lastname
     * @param string $email
     * @param string $phone
     * @param string $vatId
     * @param MF_Address $address
     * @param string $dateOfBirth
     */
    public function __construct($customerId, $company, $sex, $firstname, $lastname, $email, $phone, $vatId, $address, $dateOfBirth = '')
    {
        require_once ('Score/Interface.php');
        require_once ('Score/PersonInterface.php');
        require_once ('Score/CompanyInterface.php');
        require_once ('Misc.php');

        $this->customerId   = $customerId;
        $this->company      = $company;
        $this->sex          = $sex;
        $this->firstname    = $firstname;
        $this->lastname     = $lastname;
        $this->emailAddress = $email;
        $this->phone        = $phone;
        $this->address      = $address;
        $this->dateOfBirth  = $dateOfBirth;

        //try to find out if suspect is a company:
        if ((strlen($company) > 0) || (strlen($vatId) > 0))
        {
            $this->isCompany = true;
            $this->company = (strlen($company) > 0) ? $company : $firstname.' '.$lastname;
            $this->sex = 'c';
        }

        $config = MF_Config::getInstance();

        //load default scoreClass for this type of suspect:
        $scoreClass = ($this->isCompany) ? $config->getValue('defaultCompanyScore') : $config->getValue('defaultPersonScore');
        require_once(str_replace('_', '/', substr($scoreClass, 3)) . '.php');

        $this->scoreClass = new $scoreClass();

        //validate scoreclass:
        $validScoreClass = $this->isValidScoreClass();

        if ($validScoreClass !== true)
        {
            $message = implode(', ', $validScoreClass);
            MF_Misc::errorLog($customerId, $message);
            throw new Exception($message);
        }
    }



    /**
     * Function to create a suspect
     * ::CAUTION:: This function uses the customer information to create the suspect.
     *
     * @param int $customerId
     * @return MF_Suspect
     */
    public static function createSuspect($customerId)
    {
        // Fetch customer info from database
        $subjectQuery = xtc_db_query('SELECT
                                            IFNULL(entry_company, "") as entry_company,
                                            customers_gender as sex,
                                            customers_email_address as email,
                                            customers_telephone as phone,
                                            entry_firstname,
                                            entry_lastname,
                                            entry_street_address,
                                            entry_postcode,
                                            entry_city,
                                            countries_iso_code_2 as country,
                                            customers_dob,
                                            IFNULL(customers_vat_id, "") as customers_vat_id
                                      FROM
                                            address_book
                                      INNER JOIN
                                            customers
                                      ON
                                            (customers.customers_default_address_id = address_book.address_book_id)
                                      JOIN  countries
                                      ON entry_country_id = countries_id
                                      WHERE
                                            customers.customers_id = '.(int) xtc_db_input($customerId).'
                                      LIMIT 1');

        $subjectErg = mysqli_fetch_assoc($subjectQuery);

        if ($subjectErg)
        {
            //suspect found!
            $address = new MF_Address(utf8_decode($subjectErg['entry_street_address']),
                                      utf8_decode($subjectErg['entry_city']),
                                      utf8_decode($subjectErg['entry_postcode']),
                                      utf8_decode($subjectErg['country']));

            $suspect = new MF_Suspect($customerId,
                                      utf8_decode($subjectErg['entry_company']),
                                      utf8_decode($subjectErg['sex']),
                                      utf8_decode($subjectErg['entry_firstname']),
                                      utf8_decode($subjectErg['entry_lastname']),
                                      utf8_decode($subjectErg['email']),
                                      utf8_decode($subjectErg['phone']),
                                      utf8_decode($subjectErg['customers_vat_id']),
                                      $address,
                                      substr($subjectErg['customers_dob'], 0, 10));

            return $suspect;
        }

        return false;
    }



    /**
     * Returns the Suspect by the given order ID
     * ::CAUTION:: This function uses the billing values of the order
     *
     * @param int $orderId
     * @return MF_Suspect
     */
    public static function createSuspectByOrder($orderId)
    {
        // Fetch complete customer information from order
        $orderQuery = xtc_db_query('SELECT
                                        IFNULL(customers_company, "") AS company,
                                        customers_id,
                                        customers_email_address AS email,
                                        customers_telephone AS phone,
                                        billing_firstname AS firstname,
                                        billing_lastname AS lastname,
                                        billing_street_address AS street,
                                        billing_postcode AS postcode,
                                        billing_city AS city,
                                        billing_country_iso_code_2 as country,
                                        IFNULL(customers_vat_id, "") as vat_id
                                    FROM
                                        orders
                                    WHERE
                                        orders_id = '.(int) xtc_db_input($orderId).'
                                    LIMIT 1');

        $result = mysqli_fetch_assoc($orderQuery);

        if ($result)
        {
            //suspect found!
            $address = new MF_Address($result['street'],
                                      $result['city'],
                                      $result['postcode'],
                                      $result['country']);

            $suspect = new MF_Suspect($result['customers_id'],
                                      $result['company'],
                                      '@',
                                      $result['firstname'],
                                      $result['lastname'],
                                      $result['email'],
                                      $result['phone'],
                                      $result['vat_id'],
                                      $address,
                                      '');

            return $suspect;
        }

        return false;
    }



    public function setLastname($lastname)
    {
        $this->lastname = $lastname;
    }



    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;
    }



    public function getAddress()
    {
        return $this->address;
    }



    public function getSex()
    {
        return $this->sex;
    }



    public function getFirstname()
    {
        return $this->firstname;
    }



    public function getLastname()
    {
        return $this->lastname;
    }



    public function getCompany()
    {
        return $this->company;
    }



    public function getCustomerId()
    {
        return $this->customerId;
    }



    public function getPhone()
    {
        return $this->phone;
    }



    public function getEmailAddress()
    {
        return $this->emailAddress;
    }



    /**
     * Returns current scoreclass
     *
     * @return MF_Score_Interface
     */
    public function getScoreClass()
    {
        return $this->scoreClass;
    }



    public function isCompany()
    {
        return $this->isCompany;
    }



    /**
     * Returns Score Result of the Suspect
     * If there is one in the DB, this one will be returned,
     * otherwise MF is asked for a new score
     *
     * @return array
     */
    public function getScoreResult($orderTotal = 0)
    {
        $config = MF_Config::getInstance();
        $scoreClass = $this->scoreClass;

        if ($scoreClass->hasValidScore($this, $orderTotal))
        {
            //there still is a valid score, no need to ask mf!
            $scoreResult = $scoreClass->getOldScore($this);
        }
        else
        {
            //get score by mf:
            $scoreResult = $scoreClass->getScore($this);
        }

        return $scoreResult;
    }



    /**
     * Returns if a valid score for this suspect exists
     *
     * @param int $customerId
     * @param double $orderTotal
     * @return boolean
     */
    public static function hasValidScore($customerId, $orderTotal = 0)
    {
        $suspect = self::createSuspect($customerId);
        $scoreClass = $suspect->getScoreClass();

        return $scoreClass->hasValidScore($suspect, $orderTotal);
    }



    /**
     * Validates the score class
     * Returns true if scoreclass is valid and array with error strings otherwise
     *
     * @return mixed
     */
    private function isValidScoreClass()
    {
        $message = array();
        $valid   = true;

        if (!is_a($this->scoreClass, 'MF_Score_Interface'))
        {
            $message[] = 'Die angegebene Scoreklasse ist nicht vom Typ MF_Score_Interface';
            $valid = false;
        }

        if ($this->isCompany && (!is_a($this->scoreClass, 'MF_Score_CompanyInterface')))
        {
            $message[] = 'Die angegebene Scoreklasse ist nicht vom Typ MF_Score_CompanyInterface';
            $valid = false;
        }

        if (!$this->isCompany && (!is_a($this->scoreClass, 'MF_Score_PersonInterface')))
        {
            $message[] = 'Die angegebene Scoreklasse ist nicht vom Typ MF_Score_PersonInterface';
            $valid = false;
        }

        return ($valid) ? $valid : $message;
    }



    /**
     * Returns if the returned score is a positive one
     *
     * @param array $score
     * @return boolean
     */
    public function isPositiveScore($score)
    {
        return $this->scoreClass->isPositiveScore($score);
    }
}

?>