<?php

/* --------------------------------------------------------------
   OrderListGeneratorInterface.inc.php 2015-11-05
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface OrderListGeneratorInterface
 *
 * @category   System
 * @package    Order
 * @subpackage Interfaces
 */
interface OrderListGeneratorInterface
{
	/**
	 * Get Order List Items
	 *
	 * Returns an order list item collection.
	 *
	 * @param string|array $conditions Provide a WHERE clause string or an associative array (actually any parameter
	 *                                 that is acceptable by the "where" method of the CI_DB_query_builder method).
	 * @param IntType      $startIndex The start index of the wanted array to be returned (default = null).
	 * @param IntType      $maxCount   Maximum amount of items which should be returned (default = null).
	 * @param StringType   $orderBy    A string which defines how the items should be ordered (default = null).
	 *
	 * @return OrderListItemCollection
	 *
	 * @throws InvalidArgumentException If the result rows contain invalid values.
	 */
	public function getOrderListByConditions($conditions = array(),
	                                         IntType $startIndex = null,
	                                         IntType $maxCount = null,
	                                         StringType $orderBy = null);
	
	
	/**
	 * Filters records by a single keyword string.
	 *
	 * @param StringType      $keyword    Keyword string to be used for searching in order records.
	 * @param IntType|null    $startIndex The start index of the wanted array to be returned (default = null).
	 * @param IntType|null    $maxCount   Maximum amount of items which should be returned (default = null).
	 * @param StringType|null $orderBy    A string which defines how the items should be ordered (default = null).
	 *
	 * @return OrderListItemCollection Order list item collection.
	 */
	public function getOrderListByKeyword(StringType $keyword,
	                                      IntType $startIndex = null,
	                                      IntType $maxCount = null,
	                                      StringType $orderBy = null);
	
	
	/**
	 * Filter order list items by the provided parameters.
	 *
	 * The following slug names need to be used:
	 *
	 *   - number => orders.orders_id
	 *   - customer => orders.customers_lastname orders.customers_firstname
	 *   - group => orders.customers_status_name
	 *   - sum => orders_total.value
	 *   - payment => orders.payment_method
	 *   - shipping => orders.shipping_method
	 *   - countryIsoCode => orders.delivery_country_iso_code_2
	 *   - date => orders.date_purchased
	 *   - status => orders_status.orders_status_name
	 *
	 * @param array           $filterParameters Contains the column slug-names and their values.
	 * @param IntType|null    $startIndex       The start index of the wanted array to be returned (default = null).
	 * @param IntType|null    $maxCount         Maximum amount of items which should be returned (default = null).
	 * @param StringType|null $orderBy          A string which defines how the items should be ordered (default = null).
	 *
	 * @return OrderListItemCollection
	 */
	public function filterOrderList(array $filterParameters,
	                                IntType $startIndex = null,
	                                IntType $maxCount = null,
	                                StringType $orderBy = null);
	
	/**
	 * Get the filtered orders count.
	 *
	 * This number is useful for pagination functionality where the app needs to know the number of the filtered rows.
	 *
	 * @param array $filterParameters
	 *
	 * @return int
	 *
	 * @throws BadMethodCallException
	 */
	public function filterOrderListCount(array $filterParameters);
}
