<?php
/* --------------------------------------------------------------
   MissingTaxSumImporter.inc.php 2014-12-22 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class MissingTaxSumImporter
 */
class MissingTaxSumImporter
{
	/**
	 * import missing tax data into database
	 */
	public function import()
	{
		$missingOrderIdsArray = $this->getMissingOrderIds();

		if(sizeof($missingOrderIdsArray) > 0)
		{
			/* @var OrderTaxInformation $orderTaxInformation */
			$orderTaxInformation = MainFactory::create_object('OrderTaxInformation');

			foreach($missingOrderIdsArray as $orderId)
			{
				$orderTaxInformation->saveTaxInformation($orderId);
			}
		}
	}


	/**
	 * @return array of orderIds
	 */
	protected function getMissingOrderIds()
	{
		$missingOrderIdsArray = array();

		$sql = '
			SELECT
				o.orders_id AS orders_order_id,
				ot.order_id AS tax_order_id
			FROM
				orders AS o LEFT JOIN orders_tax_sum_items ot ON (o.orders_id = ot.order_id)
			WHERE
				ot.order_id IS NULL
		';
		$result = xtc_db_query($sql);

		while(($row = xtc_db_fetch_array($result) ))
		{
			$missingOrderIdsArray[] = $row['orders_order_id'];
		}
		return $missingOrderIdsArray;
	}

}
 