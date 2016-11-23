<?php
/* --------------------------------------------------------------
   TaxSumGroup.inc.php 2014-12-18 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/


/**
 * Class TaxSumGroup
 */
class TaxSumGroup
{

	protected $taxClass                = '';
	protected $taxZone                 = '';
	protected $taxRate                 = 0.0;
	protected $currency                = '';
	protected $exchangeRate            = 1;
	protected $sumGrossOrgCurrency     = 0.0;
	protected $sumNetOrgCurrency       = 0.0;
	protected $sumTaxOrgCurrency       = 0.0;
	protected $sumGrossDefaultCurrency = 0.0;
	protected $sumNetDefaultCurrency   = 0.0;
	protected $sumTaxDefaultCurrency   = 0.0;
	protected $taxDescription          = '';

	/**
	 * @var DateTime
	 */
	protected $insertDate;
	/**
	 * @var DateTime
	 */
	protected $lastChangeDatetime;


	/**
	 * @return string
	 */
	public function getTaxClass()
	{
		return $this->taxClass;
	}


	/**
	 * @return string
	 */
	public function getTaxZone()
	{
		return $this->taxZone;
	}


	/**
	 * @return float
	 */
	public function getTaxRate()
	{
		return $this->taxRate;
	}


	/**
	 * @return string
	 */
	public function getCurrency()
	{
		return $this->currency;
	}


	/**
	 * @return float
	 */
	public function getSumGrossOrgCurrency()
	{
		return $this->sumGrossOrgCurrency;
	}


	/**
	 * @param float $sumGrossOrgCurrency
	 */
	public function setSumGrossOrgCurrency($sumGrossOrgCurrency)
	{
		$this->sumGrossOrgCurrency = $sumGrossOrgCurrency;
	}


	/**
	 * @return float
	 */
	public function getSumNetOrgCurrency()
	{
		return $this->sumNetOrgCurrency;
	}


	/**
	 * @param float $sumNetOrgCurrency
	 */
	public function setSumNetOriginalCurrency($sumNetOrgCurrency)
	{
		$this->sumNetOrgCurrency = $sumNetOrgCurrency;
	}


	/**
	 * @return float
	 */
	public function getSumTaxOrgCurrency()
	{
		return $this->sumTaxOrgCurrency;
	}


	/**
	 * @param float $sumTaxOrgCurrency
	 */
	public function setSumTaxOrgCurrency($sumTaxOrgCurrency)
	{
		$this->sumTaxOrgCurrency = $sumTaxOrgCurrency;
	}


	/**
	 * @return float
	 */
	public function getSumGrossDefaultCurrency()
	{
		return $this->sumGrossDefaultCurrency;
	}


	/**
	 * @param float $sumGrossDefaultCurrency
	 */
	public function setSumGrossDefaultCurrency($sumGrossDefaultCurrency)
	{
		$this->sumGrossDefaultCurrency = $sumGrossDefaultCurrency;
	}


	/**
	 * @return float
	 */
	public function getSumNetDefaultCurrency()
	{
		return $this->sumNetDefaultCurrency;
	}


	/**
	 * @param float $sumNetDefaultCurrency
	 */
	public function setSumNetDefaultCurrency($sumNetDefaultCurrency)
	{
		$this->sumNetDefaultCurrency = $sumNetDefaultCurrency;
	}


	/**
	 * @return float
	 */
	public function getSumTaxDefaultCurrency()
	{
		return $this->sumTaxDefaultCurrency;
	}


	/**
	 * @param float $sumTaxDefaultCurrency
	 */
	public function setSumTaxDefaultCurrency($sumTaxDefaultCurrency)
	{
		$this->sumTaxDefaultCurrency = $sumTaxDefaultCurrency;
	}


	/**
	 * @return DateTime
	 */
	public function getInsertDateTime()
	{
		return $this->insertDate;
	}


	/**
	 * @return DateTime
	 */
	public function getLastChangeDatetime()
	{
		return $this->lastChangeDatetime;
	}


	/**
	 * @param string $taxClass
	 */
	public function setTaxClass($taxClass)
	{
		$this->taxClass = $taxClass;
	}


	/**
	 * @param string $taxZone
	 */
	public function setTaxZone($taxZone)
	{
		$this->taxZone = $taxZone;
	}


	/**
	 * @param float $taxRate
	 */
	public function setTaxRate($taxRate)
	{
		$this->taxRate = $taxRate;
	}


	/**
	 * @param string $currency
	 */
	public function setCurrency($currency)
	{
		$this->currency = $currency;
	}


	/**
	 * @param float $sumGross
	 */
	public function setSumGross($sumGross)
	{
		$this->sumGrossOrgCurrency = $sumGross;
	}


	/**
	 * @param float $sumNet
	 */
	public function setSumNet($sumNet)
	{
		$this->sumNetOrgCurrency = $sumNet;
	}


	/**
	 * @param float $sumTax
	 */
	public function setSumTax($sumTax)
	{
		$this->sumTaxOrgCurrency = $sumTax;
	}


	/**
	 * @param DateTime $insertDate
	 */
	public function setInsertDate(DateTime $insertDate)
	{
		$this->insertDate = $insertDate;
	}


	/**
	 * @param DateTime $lastChangeDatetime
	 */
	public function setLastChangeDatetime(DateTime $lastChangeDatetime)
	{
		$this->lastChangeDatetime = $lastChangeDatetime;
	}


	/**
	 * @return string
	 */
	public function getTaxDescription()
	{
		return $this->taxDescription;
	}


	/**
	 * @param string $taxDescription
	 */
	public function setTaxDescription($taxDescription)
	{
		$this->taxDescription = $taxDescription;
	}


}