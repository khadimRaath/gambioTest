<?php
/* --------------------------------------------------------------
   TaxItem.inc.php 2014-12-18 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/


/**
 * Class TaxItem
 */
class TaxItem
{

	/**
	 * @var string
	 */
	protected $taxClass = '';
	/**
	 * @var string
	 */
	protected $taxZone = '';
	/**
	 * @var float
	 */
	protected $taxRate = 0.0;
	/**
	 * @var float
	 */
	protected $gross = 0.0;
	/**
	 * @var float
	 */
	protected $net = 0.0;
	/**
	 * @var float
	 */
	protected $tax = 0.0;
	/**
	 * @var string
	 */
	protected $currency = '';
	/**
	 * @var int
	 */
	protected $orderId     = 0;
	/**
	 * @var string
	 */
	protected $description = '';
	/**
	 * @var DateTime
	 */
	protected $insertDate;
	/**
	 * @var DateTime
	 */
	protected $lastChangeDatetime;


	/**
	 * @param $description
	 */
	public function setTaxDescription($description)
	{
		$this->description = $description;
	}


	/**
	 * @param bool $forDB
	 *
	 * @return string
	 */
	public function getTaxClass($forDB = false)
	{
		return $this->prepareOutput($this->taxClass, $forDB);
	}


	/**
	 * @param string $taxClass
	 */
	public function setTaxClass($taxClass)
	{
		$this->taxClass = $taxClass;
	}


	/**
	 * @param bool $forDB
	 *
	 * @return string
	 */
	public function getTaxZone($forDB = false)
	{
		return $this->prepareOutput($this->taxZone, $forDB);
	}


	/**
	 * @param string $taxZone
	 */
	public function setTaxZone($taxZone)
	{
		$this->taxZone = $taxZone;
	}


	/**
	 * @param bool $forDB
	 *
	 * @return float
	 */
	public function getTaxRate($forDB = false)
	{
		return $this->prepareOutput($this->taxRate, $forDB);
	}


	/**
	 * @param float $taxRate
	 */
	public function setTaxRate($taxRate)
	{
		$this->taxRate = $taxRate;
	}


	/**
	 * @param bool $forDB
	 *
	 * @return string
	 */
	public function getCurrency($forDB = false)
	{
		return $this->prepareOutput($this->currency, $forDB);
	}


	/**
	 * @param string $currency
	 */
	public function setCurrency($currency)
	{
		$this->currency = $currency;
	}


	/**
	 * @param bool $forDB
	 *
	 * @return float
	 */
	public function getGross($forDB = false)
	{
		return $this->prepareOutput($this->gross, $forDB);
	}


	/**
	 * @param float $gross
	 */
	public function setGross($gross)
	{
		$this->gross = $gross;
	}


	/**
	 * @param bool $forDB
	 *
	 * @return float
	 */
	public function getNet($forDB = false)
	{
		return $this->prepareOutput($this->net, $forDB);
	}


	/**
	 * @param float $net
	 */
	public function setNet($net)
	{
		$this->net = $net;
	}


	/**
	 * @param bool $forDB
	 *
	 * @return float
	 */
	public function getTax($forDB = false)
	{
		return $this->prepareOutput($this->tax, $forDB);
	}


	/**
	 * @param float $tax
	 */
	public function setTax($tax)
	{
		$this->tax = $tax;
	}


	/**
	 * @param bool $forDB
	 *
	 * @return int
	 */
	public function getOrderId($forDB = false)
	{
		return $this->prepareOutput($this->orderId, $forDB);
	}


	/**
	 * @param int $orderId
	 */
	public function setOrderId($orderId)
	{
		$this->orderId = $orderId;
	}


	/**
	 * @param bool $forDB
	 *
	 * @return DateTime
	 */
	public function getInsertDate($forDB = false)
	{
		return $this->prepareOutput($this->insertDate, $forDB);
	}


	/**
	 * @param DateTime $insertDate
	 */
	public function setInsertDate(DateTime $insertDate)
	{
		$this->insertDate = $insertDate;
	}


	/**
	 * @param bool $forDB
	 *
	 * @return DateTime
	 */
	public function getLastChangeDatetime($forDB = false)
	{
		return $this->prepareOutput($this->lastChangeDatetime, $forDB);
	}


	/**
	 * @param DateTime $lastChangeDatetime
	 */
	public function setLastChangeDatetime(DateTime $lastChangeDatetime)
	{
		$this->lastChangeDatetime = $lastChangeDatetime;
	}


	/**
	 * @param bool $forDB
	 *
	 * @return string
	 *
	 */
	public function getTaxDescription($forDB = false)
	{
		return $this->prepareOutput($this->description, $forDB);
	}


	/**
	 * @param      $val
	 * @param bool $forDB
	 *
	 * @return string
	 */
	protected function prepareOutput($val, $forDB = false)
	{
		if($forDB)
		{
			$val = xtc_db_input($val);
		}

		return $val;
	}

}