<?php
/* --------------------------------------------------------------
   StatisticsServiceInterface.inc.php 2015-10-05 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface StatisticsServiceInterface
 *
 * @category   System
 * @package    Statistics
 * @subpackage Interfaces
 */
interface StatisticsServiceInterface
{

	/**
	 * Get the amount of users who are currently online
	 *
	 * @return int
	 */
	public function getUsersOnline();


	/**
	 * Gets the amount of visitors of the current date
	 *
	 * @return int
	 */
	public function getVisitorsToday();


	/**
	 * Gets the amount of visitors of the last week
	 *
	 * @return int
	 */
	public function getVisitorsLastWeek();


	/**
	 * Gets the amount of visitors of the last two week
	 *
	 * @return int
	 */
	public function getVisitorsLastTwoWeeks();


	/**
	 * Gets the amount of visitors of the last month
	 *
	 * @return int
	 */
	public function getVisitorsLastMonth();


	/**
	 * Gets the amount of visitors of the last three months
	 *
	 * @return int
	 */
	public function getVisitorsLastThreeMonths();


	/**
	 * Gets the amount of visitors of the last six months
	 *
	 * @return int
	 */
	public function getVisitorsLastSixMonths();


	/**
	 * Gets the amount of visitors of the last year
	 *
	 * @return int
	 */
	public function getVisitorsLastYear();


	/**
	 * Gets the amount of new customers of the current date
	 *
	 * @return int
	 */
	public function getNewCustomersToday();


	/**
	 * Gets the amount of new customers of the last week
	 *
	 * @return int
	 */
	public function getNewCustomersLastWeek();


	/**
	 * Gets the amount of new customers of the last two week
	 *
	 * @return int
	 */
	public function getNewCustomersLastTwoWeeks();


	/**
	 * Gets the amount of new customers of the last month
	 *
	 * @return int
	 */
	public function getNewCustomersLastMonth();


	/**
	 * Gets the amount of new customers of the last three months
	 *
	 * @return int
	 */
	public function getNewCustomersLastThreeMonths();


	/**
	 * Gets the amount of new customers of the last six months
	 *
	 * @return int
	 */
	public function getNewCustomersLastSixMonths();


	/**
	 * Gets the amount of new customers of the last year
	 *
	 * @return int
	 */
	public function getNewCustomersLastYear();


	/**
	 * Gets the orders count of today
	 *
	 * @return int
	 */
	public function getOrdersCountToday();


	/**
	 * Gets the orders count of the last week
	 *
	 * @return int
	 */
	public function getOrdersCountLastWeek();


	/**
	 * Gets the orders count of the last two weeks
	 *
	 * @return int
	 */
	public function getOrdersCountLastTwoWeeks();


	/**
	 * Gets the orders count of the last month
	 *
	 * @return int
	 */
	public function getOrdersCountLastMonth();


	/**
	 * Gets the orders count of the last three months
	 *
	 * @return int
	 */
	public function getOrdersCountLastThreeMonths();


	/**
	 * Gets the orders count of the last six months
	 *
	 * @return int
	 */
	public function getOrdersCountLastSixMonths();


	/**
	 * Gets the orders count of the last year
	 *
	 * @return int
	 */
	public function getOrdersCountLastYear();


	/**
	 * Gets the conversion rate of today as a formatted number (i.e.: 0,27)
	 *
	 * @return string
	 */
	public function getConversionRateToday();


	/**
	 * Gets the conversion rate of the last week as a formatted number (i.e.: 0,27)
	 *
	 * @return string
	 */
	public function getConversionRateLastWeek();


	/**
	 * Gets the conversion rate of the last two weeks as a formatted number (i.e.: 0,27)
	 *
	 * @return string
	 */
	public function getConversionRateLastTwoWeeks();


	/**
	 * Gets the conversion rate of the last month as a formatted number (i.e.: 0,27)
	 *
	 * @return string
	 */
	public function getConversionRateLastMonth();


	/**
	 * Gets the conversion rate of the last three months as a formatted number (i.e.: 0,27)
	 *
	 * @return string
	 */
	public function getConversionRateLastThreeMonths();


	/**
	 * Gets the conversion rate of the last six months as a formatted number (i.e.: 0,27)
	 *
	 * @return string
	 */
	public function getConversionRateLastSixMonths();


	/**
	 * Gets the conversion rate of the last year as a formatted number (i.e.: 0,27)
	 *
	 * @return string
	 */
	public function getConversionRateLastYear();


	/**
	 * Gets the amount of sales of the current date
	 *
	 * @return string
	 */
	public function getSalesToday();


	/**
	 * Gets the amount of sales of the last week
	 *
	 * @return string
	 */
	public function getSalesLastWeek();


	/**
	 * Gets the amount of sales of the last two week
	 *
	 * @return string
	 */
	public function getSalesLastTwoWeeks();


	/**
	 * Gets the amount of sales of the last month
	 *
	 * @return string
	 */
	public function getSalesLastMonth();


	/**
	 * Gets the amount of sales of the last three months
	 *
	 * @return string
	 */
	public function getSalesLastThreeMonths();


	/**
	 * Gets the amount of sales of the last six months
	 *
	 * @return string
	 */
	public function getSalesLastSixMonths();


	/**
	 * Gets the amount of sales of the last year
	 *
	 * @return string
	 */
	public function getSalesLastYear();


	/**
	 * Gets the average order value of today as a formatted price (i.e.: 1.234,56 EUR)
	 *
	 * @return string
	 */
	public function getAverageOrderValueToday();


	/**
	 * Gets the average order value of the last week as a formatted price (i.e.: 1.234,56 EUR)
	 *
	 * @return string
	 */
	public function getAverageOrderValueLastWeek();


	/**
	 * Gets the average order value of the last two weeks as a formatted price (i.e.: 1.234,56 EUR)
	 *
	 * @return string
	 */
	public function getAverageOrderValueLastTwoWeeks();


	/**
	 * Gets the average order value of the last month as a formatted price (i.e.: 1.234,56 EUR)
	 *
	 * @return string
	 */
	public function getAverageOrderValueLastMonth();


	/**
	 * Gets the average order value of the last three months as a formatted price (i.e.: 1.234,56 EUR)
	 *
	 * @return string
	 */
	public function getAverageOrderValueLastThreeMonths();


	/**
	 * Gets the average order value of the last six months as a formatted price (i.e.: 1.234,56 EUR)
	 *
	 * @return string
	 */
	public function getAverageOrderValueLastSixMonths();


	/**
	 * Gets the average order value of the last year as a formatted price (i.e.: 1.234,56 EUR)
	 *
	 * @return string
	 */
	public function getAverageOrderValueLastYear();
}