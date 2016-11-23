<?php
/* --------------------------------------------------------------
   TaxSumManagerReader.inc.php 2015-02-24 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class TaxSumManagerReader
 */
class TaxSumManagerReader
{

	/**
	 * @var DateTime
	 */
	protected $startDate;
	/**
	 * @var DateTime
	 */
	protected $endDate;
	/**
	 * @var TaxSumGroup
	 */
	protected $taxSumGroup;
	/**
	 * @var array
	 */
	protected $taxSumItemsArray = array();
	/**
	 * @var array
	 */
	protected $orderStatusArray = array();
	/**
	 * @var string
	 */
	protected $paymentModule = '';


	/**
	 * @param TaxSumGroup $p_taxSumGroup
	 */
	public function __construct(TaxSumGroup $p_taxSumGroup)
	{
		$this->taxSumGroup = $p_taxSumGroup;
	}


	/**
	 * @param DateTime $p_startDate
	 * @param DateTime $p_endDate
	 * @param array    $orderStatusArray
	 * @param string   $p_paymentModule
	 *
	 * @return array
	 */
	public function getAllTaxSumInfo(DateTime $p_startDate, DateTime $p_endDate, array $orderStatusArray = array(), $p_paymentModule = '')
	{

		$this->startDate = $p_startDate;
		$this->endDate   = $p_endDate;
		
		foreach($orderStatusArray as &$orderId)
		{
			$orderId = (int)$orderId;
		}
		
		$this->orderStatusArray = $orderStatusArray;
		
		if(!empty($p_paymentModule))
		{
			$this->paymentModule = (string)$p_paymentModule;
		}

		$this->buildTaxSumItemsArray();

		return $this->taxSumItemsArray;

	}


	/**
	 * @return DateTime
	 */
	public function getStartDate()
	{
		return $this->startDate;
	}


	/**
	 * @param DateTime $startDate
	 */
	public function setStartDate(DateTime $startDate)
	{
		$this->startDate = $startDate;
	}


	/**
	 * @return DateTime
	 */
	public function getEndDate()
	{
		return $this->endDate;
	}


	/**
	 * @param DateTime $endDate
	 */
	public function setEndDate(DateTime $endDate)
	{
		$this->endDate = $endDate;
	}


	protected function buildTaxSumItemsArray()
	{
		$result = $this->readFromDB();

		while($dataset = xtc_db_fetch_array($result))
		{
			$taxSumGroup = clone $this->taxSumGroup;
			$taxSumGroup->setTaxZone($dataset['t_zone']);
			$taxSumGroup->setTaxClass($dataset['t_class']);
			$taxSumGroup->setCurrency($dataset['t_currency']);
			$taxSumGroup->setTaxRate($dataset['t_rate']);
			$taxSumGroup->setSumNetOriginalCurrency($dataset['t_sum_net_org_currency']);
			$taxSumGroup->setSumGrossOrgCurrency($dataset['t_sum_gross_org_currency']);
			$taxSumGroup->setSumTaxOrgCurrency($dataset['t_sum_tax_org_currency']);
			$taxSumGroup->setSumNetDefaultCurrency($dataset['t_sum_net_default_currency']);
			$taxSumGroup->setSumGrossDefaultCurrency($dataset['t_sum_gross_default_currency']);
			$taxSumGroup->setSumTaxDefaultCurrency($dataset['t_sum_tax_default_currency']);

			$this->taxSumItemsArray[] = $taxSumGroup;
		}
	}


	/**
	 * @return bool|resource
	 */
	protected function readFromDB()
	{
		$wherePart = '';

		if(empty($this->orderStatusArray) && empty($this->paymentModule))
		{
			$queryTemplate = "SELECT
								`tax_zone` AS `t_zone`,
								`tax_class` AS `t_class`,
								`tax_rate` AS `t_rate`,
								`currency` AS `t_currency`,
								`insert_date` AS `t_insert_date`,
								 SUM(`net`)	AS `t_sum_net_org_currency`,
								 SUM(`gross`) AS `t_sum_gross_org_currency`,
								 SUM(`tax`)	AS `t_sum_tax_org_currency`
							  FROM orders_tax_sum_items
							  WHERE `insert_date` >= '%s' AND `insert_date` <= '%s'
							  GROUP BY `t_zone`, `t_class`, `t_rate`, `t_currency`
							  ORDER BY `t_zone` ASC";
		}
		else
		{

			if(!empty($this->orderStatusArray))
			{
				$wherePart .= ' AND o.orders_status IN (' . implode(',', $this->orderStatusArray) . ') ';
			}

			if(!empty($this->paymentModule))
			{
				$wherePart .= ' AND o.payment_method = "' . xtc_db_input($this->paymentModule) . '" ';
			}
			
			$queryTemplate = "SELECT
								ot.`tax_zone` AS `t_zone`,
								ot.`tax_class` AS `t_class`,
								ot.`tax_rate` AS `t_rate`,
								ot.`currency` AS `t_currency`,
								ot.`insert_date` AS `t_insert_date`,
								 SUM(ot.`net`)	AS `t_sum_net_org_currency`,
								 SUM(ot.`gross`) AS `t_sum_gross_org_currency`,
								 SUM(ot.`tax`)	AS `t_sum_tax_org_currency`
							  FROM 
								orders_tax_sum_items ot,
								orders o
							  WHERE 
							  	ot.`insert_date` >= '%s' AND ot.`insert_date` <= '%s' AND
							  	ot.order_id = o.orders_id 
							  	%s
							  GROUP BY `t_zone`, `t_class`, `t_rate`, `t_currency`
							  ORDER BY `t_zone` ASC";
		}

		$startDate = $this->startDate->format('Y-m-d');
		$endDate   = $this->endDate->format('Y-m-d');

		$query = sprintf($queryTemplate, $startDate, $endDate, $wherePart);

		$result = xtc_db_query($query);

		return $result;
	}

}