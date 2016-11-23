<?php
/* --------------------------------------------------------------
   TaxItemWriter.inc.php 2015-06-03 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class TaxItemReader
 */
class TaxItemReader
{
	/** @var array */
	protected static $fields = array(
		'tax_class',
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
	/** @var string */
	protected static $tableName = 'orders_tax_sum_items';


	/**
	 * @param $p_orderId
	 *
	 * @return bool
	 */
	public function orderIdIsSaved($p_orderId)
	{
		$response = false;
		/** @var int $orderId */
		$orderId = (int)$p_orderId;

		$query = 'SELECT COUNT(*) AS `counter` FROM `__tableName__` WHERE `order_id` = "__order_id__"';

		$queryVariables = array(
			'__tableName__' => self::$tableName,
			'__order_id__'  => $orderId
		);

		$query = str_replace(array_keys($queryVariables), array_values($queryVariables), $query);

		$result     = xtc_db_query($query);
		$counterRow = xtc_db_fetch_array($result);

		$counter = 1;
		if(array_key_exists('counter', $counterRow))
		{
			$counter = (int)$counterRow['counter'];
		}

		if($counter > 0)
		{
			$response = true;
		}

		return $response;
	}
}