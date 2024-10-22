<?php
/**
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the GNU General Public License (GPL 2)
 * that is bundled with this package in the file LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Payone to newer
 * versions in the future. If you wish to customize Payone for your
 * needs please refer to http://www.payone.de for more information.
 *
 * @category        Payone
 * @package         Payone_Api
 * @subpackage      Response
 * @copyright       Copyright (c) 2012 <info@noovias.com> - www.noovias.com
 * @author          Matthias Walter <info@noovias.com>
 * @license         <http://www.gnu.org/licenses/> GNU General Public License (GPL 2)
 * @link            http://www.noovias.com
 */

/**
 *
 * @category        Payone
 * @package         Payone_Api
 * @subpackage      Response
 * @copyright       Copyright (c) 2012 <info@noovias.com> - www.noovias.com
 * @license         <http://www.gnu.org/licenses/> GNU General Public License (GPL 2)
 * @link            http://www.noovias.com
 */
abstract class Payone_Api_Response_Authorization_Abstract
    extends Payone_Api_Response_Abstract
{
    /**
     * @var int
     */
    protected $txid = NULL;
    /**
     * @var int
     */
    protected $userid = NULL;
    /**
     * @var string
     */
    protected $protect_result_avs = NULL;
    /**
     * @var string
     */
    protected $clearing_bankaccountholder = NULL;
    /**
     * @var string
     */
    protected $clearing_bankcountry = NULL;
    /**
     * @var string
     */
    protected $clearing_bankaccount = NULL;
    /**
     * @var string
     */
    protected $clearing_bankcode = NULL;
    /**
     * @var string
     */
    protected $clearing_bankiban = NULL;
    /**
     * @var string
     */
    protected $clearing_bankbic = NULL;
    /**
     * @var string
     */
    protected $clearing_bankcity = NULL;
    /**
     * @var string
     */
    protected $clearing_bankname = NULL;

    /**
     * @var string
     */
    protected $mandate_identification = NULL;


    protected $clearing_instructionnote = NULL;
    protected $clearing_reference = NULL;
    protected $clearing_legalnote = NULL;
    protected $clearing_duedate = NULL;
    protected $add_paydata = [];

    /**
     * @param string $clearing_bankaccount
     */
    public function setClearingBankaccount($clearing_bankaccount)
    {
        $this->clearing_bankaccount = $clearing_bankaccount;
    }

    /**
     * @return string
     */
    public function getClearingBankaccount()
    {
        return $this->clearing_bankaccount;
    }

    /**
     * @param string $clearing_bankaccountholder
     */
    public function setClearingBankaccountholder($clearing_bankaccountholder)
    {
        $this->clearing_bankaccountholder = $clearing_bankaccountholder;
    }

    /**
     * @return string
     */
    public function getClearingBankaccountholder()
    {
        return $this->clearing_bankaccountholder;
    }

    /**
     * @param string $clearing_bankbic
     */
    public function setClearingBankbic($clearing_bankbic)
    {
        $this->clearing_bankbic = $clearing_bankbic;
    }

    /**
     * @return string
     */
    public function getClearingBankbic()
    {
        return $this->clearing_bankbic;
    }

    /**
     * @param string $clearing_bankcity
     */
    public function setClearingBankcity($clearing_bankcity)
    {
        $this->clearing_bankcity = $clearing_bankcity;
    }

    /**
     * @return string
     */
    public function getClearingBankcity()
    {
        return $this->clearing_bankcity;
    }

    /**
     * @param string $clearing_bankcode
     */
    public function setClearingBankcode($clearing_bankcode)
    {
        $this->clearing_bankcode = $clearing_bankcode;
    }

    /**
     * @return string
     */
    public function getClearingBankcode()
    {
        return $this->clearing_bankcode;
    }

    /**
     * @param string $clearing_bankcountry
     */
    public function setClearingBankcountry($clearing_bankcountry)
    {
        $this->clearing_bankcountry = $clearing_bankcountry;
    }

    /**
     * @return string
     */
    public function getClearingBankcountry()
    {
        return $this->clearing_bankcountry;
    }

    /**
     * @param string $clearing_bankiban
     */
    public function setClearingBankiban($clearing_bankiban)
    {
        $this->clearing_bankiban = $clearing_bankiban;
    }

    /**
     * @return string
     */
    public function getClearingBankiban()
    {
        return $this->clearing_bankiban;
    }

    /**
     * @param string $clearing_bankname
     */
    public function setClearingBankname($clearing_bankname)
    {
        $this->clearing_bankname = $clearing_bankname;
    }

    /**
     * @return string
     */
    public function getClearingBankname()
    {
        return $this->clearing_bankname;
    }

    /**
     * @param string $protect_result_avs
     */
    public function setProtectResultAvs($protect_result_avs)
    {
        $this->protect_result_avs = $protect_result_avs;
    }

    /**
     * @return string
     */
    public function getProtectResultAvs()
    {
        return $this->protect_result_avs;
    }

    /**
     * @param int $txid
     */
    public function setTxid($txid)
    {
        $this->txid = $txid;
    }

    /**
     * @return int
     */
    public function getTxid()
    {
        return $this->txid;
    }

    /**
     * @param int $userid
     */
    public function setUserid($userid)
    {
        $this->userid = $userid;
    }

    /**
     * @return int
     */
    public function getUserid()
    {
        return $this->userid;
    }

    /**
     * @param string $mandateIdentification
     */
    public function setMandateIdentification($mandateIdentification)
    {
        $this->mandate_identification = $mandateIdentification;
    }

    /**
     * @return string
     */
    public function getMandateIdentification()
    {
        return $this->mandate_identification;
    }

    /* 2016-05 EXTENSIONS BELOW */

    public function setClearingInstructionnote($clearingInstructionnote)
    {
        $this->clearing_instructionnote = $clearingInstructionnote;
    }

    public function getClearingInstructionnote()
    {
        return $this->clearing_instructionnote;
    }

    public function setClearingReference($clearingReference)
    {
        $this->clearing_reference = $clearingReference;
    }

    public function getClearingReference()
    {
        return $this->clearing_reference;
    }

    public function setClearingLegalnote($clearingLegalnote)
    {
        $this->clearing_legalnote = $clearingLegalnote;
    }

    public function getClearingLegalnote()
    {
        return $this->clearing_legalnote;
    }

    public function setClearingDuedate($clearingDuedate)
    {
        $this->clearing_duedate = $clearingDuedate;
    }

    public function getClearingDuedate()
    {
        return $this->clearing_duedate;
    }

    public function setAddPaydata($paydataKey, $value)
    {
        if(is_string($paydataKey) === false || is_string($value) === false)
        {
            throw new Exception('invalid add_paydata input');
        }
        $this->add_paydata[$paydataKey] = $value;
    }

    public function getAddPaydata($paydataKey)
    {
        $value = null;
        if(array_key_exists($paydataKey, $this->add_paydata))
        {
            $value = $this->add_paydata[$paydataKey];
        }
        return $value;
    }

    public function getAllAddPaydata()
    {
        return $this->add_paydata;
    }
}
