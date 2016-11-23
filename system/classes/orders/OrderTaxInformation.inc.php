<?php
/* --------------------------------------------------------------
   OrderTaxInformations.inc.php 2016-04-15
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class OrderTaxInformations
 */
class OrderTaxInformation
{
	/**
	 * @var TaxItem
	 */
	protected $taxItem;
	/**
	 * @var TaxItemWriter
	 */
	protected $taxItemWriter;
	/**
	 * @var TaxItemReader
	 */
	protected $taxItemReader;

	protected static $tableOrders      = 'orders';
	protected static $tableOrdersTotal = 'orders_total';
	protected static $tableTaxClass    = 'tax_class';
	protected static $tableTaxRate     = 'tax_rates';
	protected static $tableGeoZones    = 'geo_zones';

	/** @var int */
	protected $orderId = 0;


	/**
	 *
	 */
	public function __construct()
	{
		$this->taxItem       = MainFactory::create_object('TaxItem', array());
		$this->taxItemWriter = MainFactory::create_object('TaxItemWriter', array($this->taxItem));
		$this->taxItemReader = MainFactory::create_object('TaxItemReader', array());
	}


	/**
	 * @param $p_orderId
	 */
	public function saveUnsavedTaxInformation($p_orderId)
	{
		$this->orderId = (int)$p_orderId;

		$isSavedBefore = $this->taxItemReader->orderIdIsSaved($this->orderId);

		if($isSavedBefore === false)
		{
			$this->saveTaxInformation($p_orderId);
		}
	}


	/**
	 * @param int $p_orderId
	 */
	public function saveTaxInformation($p_orderId)
	{
		$this->orderId = (int)$p_orderId;

		/* Get Order information */

		$query = "SELECT
					`orders_id`,
					`title`,
					`text`,
					`value`,
					`class`,
					`sort_order`
					FROM `%s`
					WHERE
						`orders_id` = '%s'";

		$query = sprintf($query, self::$tableOrdersTotal, $this->orderId);

		$result = xtc_db_query($query);

		$taxesInfoArray = array();
		while($taxBasicInformation = xtc_db_fetch_array($result))
		{
			$class = $taxBasicInformation['class'];

			switch($class)
			{
				case'ot_tax':
					$taxesInfoArray[] = array('title' => $taxBasicInformation['title'],
											 'value' => $taxBasicInformation['value']
					);
					break;
			}
		}

		foreach($taxesInfoArray as $taxInfoArray)
		{
			if(is_array($taxInfoArray))
			{
				$taxItem = $this->_prepareTaxInfoDataset($taxInfoArray);
				$this->taxItemWriter->insertDB($taxItem);
			}
		}
	}


	/**
	 * @param array $taxInfoArray
	 *
	 * @return \TaxItem
	 */
	protected function _prepareTaxInfoDataset(array $taxInfoArray = null)
	{
		$taxItem = clone $this->taxItem;

		$taxItem->setLastChangeDatetime(new DateTime());

		if(is_array($taxInfoArray))
		{
			$title = $taxInfoArray['title'];
			$title = substr($title, 6);
			$title = substr($title, 0, -1);
			$taxItem->setTaxDescription($title);
			$taxItem->setTax($taxInfoArray['value']);

			$additionalTaxInfo = $this->_getAdditionalTaxInfo($title);

			$taxItem->setTaxClass($additionalTaxInfo->getTaxClass());
			$taxItem->setTaxRate($additionalTaxInfo->getTaxRate());
			$taxItem->setTaxZone($additionalTaxInfo->getTaxZone());
			$taxItem->setCurrency($additionalTaxInfo->getCurrency());
			$taxItem->setOrderId($this->orderId);

			/* Get gross and net */

			$taxRate = $taxItem->getTaxRate();
			$tax     = $taxItem->getTax();

			if($taxRate > 0)
			{
				$net   = ($tax / $taxRate) * 100;
				$net   = round($net, 4);
				$gross = $net + $tax;

				$taxItem->setNet($net);
				$taxItem->setGross($gross);
			}

			return $taxItem;
		}
	}

	/**
	 * @param string $p_taxDescription
	 *
	 * @return TaxItem
	 */
	protected function _getAdditionalTaxInfo($p_taxDescription)
	{
		$taxItem = clone $this->taxItem;

		$taxClass = $this->_getTaxClass($p_taxDescription);
		$taxZone  = $this->_getTaxZone($p_taxDescription);
		$taxRate  = $this->_getTaxRate($p_taxDescription);
		$currency = $this->_getCurrency();

		$taxItem->setTaxClass($taxClass);
		$taxItem->setTaxZone($taxZone);
		$taxItem->setCurrency($currency);
		$taxItem->setTaxRate($taxRate);

		return $taxItem;
	}


	/**
	 * @param string $p_taxDescription
	 *
	 * @return string
	 */
	protected function _getTaxClass($p_taxDescription)
	{
		$where   = 'tax_description = \'' . xtc_db_input($p_taxDescription) . '\'';
		$taxInfo = $this->_getOneDataset(self::$tableTaxRate, $where);

		$taxClassId = $taxInfo['tax_class_id'];

		$where   = 'tax_class_id = ' . (int)$taxClassId;
		$taxInfo = $this->_getOneDataset(self::$tableTaxClass, $where);

		$taxClass = $taxInfo['tax_class_title'];

		return $taxClass;
	}


	/**
	 * @param $p_taxDescription
	 *
	 * @return mixed
	 */
	protected function _getTaxRate($p_taxDescription)
	{
		$where   = 'tax_description = \'' . xtc_db_input($p_taxDescription) . '\'';
		$taxInfo = $this->_getOneDataset(self::$tableTaxRate, $where);

		$taxRate = $taxInfo['tax_rate'];

		return $taxRate;
	}


	/**
	 * @param $p_taxDescription
	 *
	 * @return string
	 */
	protected function _getTaxZone($p_taxDescription)
	{
		$where   = 'tax_description = \'' . xtc_db_input($p_taxDescription) . '\'';
		$taxInfo = $this->_getOneDataset(self::$tableTaxRate, $where);

		$taxClassId = $taxInfo['tax_zone_id'];

		$where   = 'geo_zone_id = ' . (int)$taxClassId;
		$taxInfo = $this->_getOneDataset(self::$tableGeoZones, $where);

		$taxZone = $taxInfo['geo_zone_name'];

		return $taxZone;

	}


	/**
	 * @return string
	 */
	protected function _getCurrency()
	{
		$orderId = $this->orderId;

		$where   = 'orders_id = ' . (int)$orderId;
		$taxInfo = $this->_getOneDataset(self::$tableOrders, $where);

		$currency = $taxInfo['currency'];

		return $currency;
	}


	/**
	 * @return DateTime
	 */
	protected function _getDateOfPurchase()
	{
		$where = 'orders_id = ' . (int)$this->orderId;
		$orderArray = $this->_getOneDataset(self::$tableOrders, $where);
		
		$dateOfPurchase = new EmptyDateTime($orderArray['date_purchased']);
		
		return $dateOfPurchase;
	}

	/**
	 * @param string $tablename
	 * @param string $where
	 *
	 * @return array
	 */
	protected function _getOneDataset($tablename, $where)
	{
		$query = "SELECT * FROM `%s` WHERE %s";
		$query = sprintf($query, $tablename, $where);

		$result = xtc_db_query($query);

		$oneDataset = xtc_db_fetch_array($result);

		return $oneDataset;
	}
}