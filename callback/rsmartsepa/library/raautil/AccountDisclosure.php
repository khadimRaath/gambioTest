<?php
/* --------------------------------------------------------------
  AccountDisclosure.php 2015-04-24 wem
  Smart Payment Solutions GmbH
  http://www.smart-payment-solutions.de/
  Copyright (c) 2015 Smart Payment Solutions GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
 */

class Raautil_AccountDisclosure {
    
    protected $bankAccountNumber = '';
    protected $bankCode = '';
    protected $accountId = '';
    protected $firstName = '';
    protected $lastName = '';
    protected $address1 = '';
    protected $address2 = '';
    protected $address3 = '';
    protected $zipCode = '';
    protected $city = '';
    protected $country = '';
    protected $msisdn = '';
    protected $male = FALSE;
    protected $dateOfBirth = ''; // dd.mm.yyyy
    
    
    public function __construct() {
        
    } // End constructor
    
    public function setBankAccountNumber($value = '') {
        $value = isset($value) ? (is_string($value) ? trim($value) : '') : '';
        $this->bankAccountNumber = $value;
        return $this;
    } // End setBankAccountNumber
    
    public function getBankAccountNumber() {
        return $this->bankAccountNumber;
    } // End getBankAccountNumber
    
    public function setBankCode($value = '') {
        $value = isset($value) ? (is_string($value) ? trim($value) : '') : '';
        $this->bankCode = $value;
        return $this;
    } // End setBankCode
    
    public function getBankCode() {
        return $this->bankCode;
    } // End getBankCode
    
    public function setAccountId($value = '') {
        $value = isset($value) ? (is_string($value) ? trim($value) : '') : '';
        $this->accountId = $value;
        return $this;
    } // End setAccountId
    
    public function getAccountId() {
        return $this->accountId;
    } // End getAccountId
    
    public function setFirstname($value = '') {
        $value = isset($value) ? (is_string($value) ? trim($value) : '') : '';
        $this->firstName = $value;
        return $this;
    } // End setFirstname
    
    public function getFirstname() {
        return $this->firstName;
    } // End getFirstname

    public function setLastname($value = '') {
        $value = isset($value) ? (is_string($value) ? trim($value) : '') : '';
        $this->lastName = $value;
        return $this;
    } // End setLastname
    
    public function getLastname() {
        return $this->lastName;
    } // End getLastname

    public function setAddress1($value = '') {
        $value = isset($value) ? (is_string($value) ? trim($value) : '') : '';
        $this->address1 = $value;
        return $this;
    } // End setAddress1
    
    public function getAddress1() {
        return $this->address1;
    } // End getAddress1

    public function setAddress2($value = '') {
        $value = isset($value) ? (is_string($value) ? trim($value) : '') : '';
        $this->address2 = $value;
        return $this;
    } // End setAddress2
    
    public function getAddress2() {
        return $this->address2;
    } // End getAddress2
    
    public function setAddress3($value = '') {
        $value = isset($value) ? (is_string($value) ? trim($value) : '') : '';
        $this->address3 = $value;
        return $this;
    } // End setAddress3
    
    public function getAddress3() {
        return $this->address3;
    } // End getAddress3

    public function setZipcode($value = '') {
        $value = isset($value) ? (is_string($value) ? trim($value) : '') : '';
        $this->zipCode = $value;
        return $this;
    } // End setZipcode
    
    public function getZipcode() {
        return $this->zipCode;
    } // End getZipcode

    public function setCity($value = '') {
        $value = isset($value) ? (is_string($value) ? trim($value) : '') : '';
        $this->city = $value;
        return $this;
    } // End setCity
    
    public function getCity() {
        return $this->city;
    } // End getCity

    public function setCountry($value = '') {
        $value = isset($value) ? (is_string($value) ? trim($value) : '') : '';
        $this->country = $value;
        return $this;
    } // End setCountry
    
    public function getCountry() {
        return $this->country;
    } // End getCountry

    public function setMsisdn($value = '') {
        $value = isset($value) ? (is_string($value) ? trim($value) : '') : '';
        $this->msisdn = $value;
        return $this;
    } // End setMsisdn
    
    public function getMsisdn() {
        return $this->msisdn;
    } // End getMsisdn
    
    public function setMale($value = FALSE) {
        if(isset($value)) {
            if($value == TRUE) {
                $this->male = TRUE;
            }
        }
        return $this;
    } // End setMale
    
    public function isMale() {
        return $this->male;
    } // End isMale
    
    public function setDateOfBirth($value = '') {
        $value = isset($value) ? (is_string($value) ? trim($value) : '') : '';
        $this->dateOfBirth = $value;
        return $this;
    } // End setDateOfBirth

    public function getDateOfBirth() {
        return $this->dateOfBirth;
    } // End getDateOfBirth
    
    public function getDateOfBirthTimestamp() {
        // $dateFormatted = @date('d.m.Y', $time);
        $result = 0;
        if(trim($this->dateOfBirth) != '') {
            $time = @strtotime($this->dateOfBirth);
            if($time !== FALSE) {
               $result = $time; 
            }
        }
        return $result;
    } // End getDateOfBirthTimestamp
    
    public function __toString() {
        $result = 'Raautil_AccountDisclosure {' .
                  'accountId="' . $this->getAccountId() . '"' .
                  ', firstName="' . $this->getFirstname() . '"' .
                  ', lastName="' . $this->getLastname() . '"' .
                  ', address1="' . $this->getAddress1() . '"' .
                  ', address2="' . $this->getAddress2() . '"' .
                  ', address3="' . $this->getAddress3() . '"' .
                  ', zipCode="' . $this->getZipcode() . '"' .
                  ', city="' . $this->getCity() . '"' .
                  ', country="' . $this->getCountry() . '"' .
                  ', msisdn="' . $this->getMsisdn() . '"' .
                  ', male="' . ($this->isMale() ? 'true' : 'false') . '"' .
                  ', dateOfBirth="' . $this->getDateOfBirth() . '"' .
                  ', dateOfBirthTS="' . $this->getDateOfBirthTimestamp() . '"' .
                  ', bankAccountNumber="' . $this->getBankAccountNumber() . '"' .
                  ', bankCode="' . $this->getBankCode() . '"' .
                  '}';
        return $result;
    } // End __toString
        
    public static function createFromAccountDisclosureString($acctDisclosure = NULL) {
        if(!isset($acctDisclosure)) {
            return NULL;
        }
        else if(!is_string($acctDisclosure)) {
            return NULL;
        }
        
        $Raautil_AccountDisclosure = NULL;
        $accountData = @json_decode($acctDisclosure);
        if(isset($accountData) && is_object($accountData)) {
            $Raautil_AccountDisclosure = new Raautil_AccountDisclosure();
            $Raautil_AccountDisclosure->setAccountId(isset($accountData->ACCOUNTID) ? strval($accountData->ACCOUNTID) : '');
            $Raautil_AccountDisclosure->setFirstname(isset($accountData->FIRSTNAME) ? strval($accountData->FIRSTNAME) : '');
            $Raautil_AccountDisclosure->setLastname(isset($accountData->LASTNAME) ? strval($accountData->LASTNAME) : '');
            $Raautil_AccountDisclosure->setAddress1(isset($accountData->ADDRESS1) ? strval($accountData->ADDRESS1) : '');
            $Raautil_AccountDisclosure->setAddress2(isset($accountData->ADDRESS2) ? strval($accountData->ADDRESS2) : '');
            $Raautil_AccountDisclosure->setAddress3(isset($accountData->ADDRESS3) ? strval($accountData->ADDRESS3) : '');
            $Raautil_AccountDisclosure->setZipcode(isset($accountData->ZIPCODE) ? strval($accountData->ZIPCODE) : '');
            $Raautil_AccountDisclosure->setCity(isset($accountData->CITY) ? strval($accountData->CITY) : '');
            $Raautil_AccountDisclosure->setCountry(isset($accountData->COUNTRY) ? strval($accountData->COUNTRY) : '');
            $Raautil_AccountDisclosure->setMsisdn(isset($accountData->MSISDN) ? strval($accountData->MSISDN) : '');
            $Raautil_AccountDisclosure->setMale(isset($accountData->MALE) ? $accountData->MALE : 0); // or 1
            $Raautil_AccountDisclosure->setDateOfBirth(isset($accountData->DATEOFBIRTH) ? strval($accountData->DATEOFBIRTH) : '');
            $Raautil_AccountDisclosure->setBankAccountNumber(isset($accountData->BANKACCOUNTNUMBER) ? strval($accountData->BANKACCOUNTNUMBER) : '');
            $Raautil_AccountDisclosure->setBankCode(isset($accountData->BANKCODE) ? strval($accountData->BANKCODE) : '');
        }
        return $Raautil_AccountDisclosure;
    } // End createFromAccountDisclosureString
    
} // End class Raautil_AccountDisclosure

