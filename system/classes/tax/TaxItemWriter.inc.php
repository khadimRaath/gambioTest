<?php
/* --------------------------------------------------------------
   TaxItemWriter.inc.php 2015-02-24 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class TaxItemWriter
 */
class TaxItemWriter
{
	protected $taxSumItem;

	protected $fields = array('tax_class',
							  'tax_zone',
							  'tax_rate',
							  'currency',
							  'gross',
							  'net',
							  'tax',
							  'order_id',
							  'insert_date',
							  'last_change_datetime'
	);


	/**
	 * @param TaxItem $taxItem
	 *
	 * @return bool|resource
	 */
	public function insertDB(TaxItem $taxItem)
	{

		$queryTemplate = "INSERT INTO `%s`
							SET
							  `tax_class`            = '%s',
							  `tax_zone`             = '%s',
							  `tax_rate`             = '%s',
							  `gross`                = '%s',
							  `net`                  = '%s',
							  `tax`                  = '%s',
							  `currency`             = '%s',
							  `order_id`             = '%s',
							  `insert_date`			 = '%s',
							  `last_change_datetime` = '%s',
							  `tax_description`		 = '%s';
														";

		$insertDate = $taxItem->getInsertDate();
		if($insertDate === null)
		{
			$insertDate = new DateTime();
		}
		
		$lastChangeDate = $taxItem->getLastChangeDatetime();
		if($lastChangeDate === null)
		{
			$lastChangeDate = new DateTime();
		}

		$insertDateString = $insertDate->format('Y-m-d H:i:s');
		$lastChangeDateString = $lastChangeDate->format('Y-m-d H:i:s');
		
		$query = sprintf($queryTemplate,
						 'orders_tax_sum_items',
						 $taxItem->getTaxClass(true),
						 $taxItem->getTaxZone(true),
						 $taxItem->getTaxRate(true),
						 $taxItem->getGross(true),
						 $taxItem->getNet(true),
						 $taxItem->getTax(true),
						 $taxItem->getCurrency(true),
						 $taxItem->getOrderId(true),
						 $insertDateString,
						 $lastChangeDateString,
						 $taxItem->getTaxDescription(true));

		return xtc_db_query($query);
	}


	/**
	 * @param $orderId
	 *
	 * @return bool|resource
	 *
	 */
	public function remove($orderId)
	{
		$orderId = (int)$orderId;
		$return  = false;

		if(!empty($orderId))
		{
			$queryTemplate = 'DELETE FROM `%s` WHERE `order_id` = "%s"';
			$query         = sprintf($queryTemplate, 'orders_tax_sum_items', $orderId);

			$return = xtc_db_query($query);
		}

		return $return;
	}

}