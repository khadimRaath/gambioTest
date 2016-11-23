<?php
/* --------------------------------------------------------------
   StatisticsService.inc.php 2016-03-10
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class StatisticsService
 *
 * Provides the statistic data for the dashboard.
 *
 * @category   System
 * @package    Statistics
 */
class StatisticsService implements StatisticsServiceInterface
{
	/**
	 * @var CI_DB_query_builder
	 */
	protected $db;
	
	/**
	 * @var xtcPrice
	 */
	protected $xtcPrice;
	
	/**
	 * @var int
	 */
	protected $conversionRateDecimalPlaces = 2;
	
	/**
	 * @var array
	 */
	protected $excludeOrderStatusIds = array(99);
	
	
	public function __construct(CI_DB_query_builder $db, xtcPrice $xtcPrice)
	{
		$this->db       = $db;
		$this->xtcPrice = $xtcPrice;
	}
	
	
	// ------------------------------------------------------------------------
	// GET ONLINE USERS
	// ------------------------------------------------------------------------
	
	/**
	 * Gets the amount of users who are currently online.
	 *
	 * @return int
	 */
	public function getUsersOnline()
	{
		$result = $this->db->query('SELECT COUNT(*) AS users_online FROM whos_online')->row_array(1);
		
		return (int)$result['users_online'];
	}
	
	
	
	// ------------------------------------------------------------------------
	// GET VISITORS
	// ------------------------------------------------------------------------
	
	/**
	 * Get visitors data for the last week.
	 *
	 * @return array
	 */
	public function getVisitorsStatisticsDataLastWeek()
	{
		$returnArray = array();
		$dataArray   = array();
		
		for($i = 7; $i > 1; $i--)
		{
			$dataArray[] = $this->_getVisitorsFromDayIntervalHelper($i);
		}
		$dataArray[] = $this->_getVisitorsFromCurrentDayHelper();
		
		$returnArray['type'] = 'day';
		$returnArray['data'] = $dataArray;
		
		return $returnArray;
	}
	
	
	/**
	 * Get visitors data for the last two weeks.
	 *
	 * @return array
	 */
	public function getVisitorsStatisticsDataLastTwoWeeks()
	{
		$returnArray = array();
		$dataArray   = array();
		
		for($i = 14; $i > 1; $i--)
		{
			$dataArray[] = $this->_getVisitorsFromDayIntervalHelper($i);
		}
		$dataArray[] = $this->_getVisitorsFromCurrentDayHelper();
		
		$returnArray['type'] = 'day';
		$returnArray['data'] = $dataArray;
		
		return $returnArray;
	}
	
	
	/**
	 * Get visitors data for the last month.
	 *
	 * @return array
	 */
	public function getVisitorsStatisticsDataLastMonth()
	{
		$returnArray = array();
		$dataArray   = array();
		$dayDiff     = time() - strtotime('-1 months');
		$days        = floor($dayDiff / (60 * 60 * 24));
		
		for($i = $days; $i > 1; $i--)
		{
			$dataArray[] = $this->_getVisitorsFromDayIntervalHelper($i);
		}
		$dataArray[] = $this->_getVisitorsFromCurrentDayHelper();
		
		$returnArray['type'] = 'day';
		$returnArray['data'] = $dataArray;
		
		return $returnArray;
	}
	
	
	/**
	 * Get visitors data for the last three months.
	 *
	 * @return array
	 */
	public function getVisitorsStatisticsDataLastThreeMonth()
	{
		$returnArray = array();
		$dataArray   = array();
		
		for($i = 3; $i > 1; $i--)
		{
			$dataArray[] = $this->_getVisitorsFromMonthIntervalHelper($i);
		}
		$dataArray[] = $this->_getVisitorsFromCurrentMonthHelper();
		
		$returnArray['type'] = 'month';
		$returnArray['data'] = $dataArray;
		
		return $returnArray;
	}
	
	
	/**
	 * Get visitors data for the last six months.
	 *
	 * @return array
	 */
	public function getVisitorsStatisticsDataLastSixMonth()
	{
		$returnArray = array();
		$dataArray   = array();
		
		for($i = 6; $i > 1; $i--)
		{
			$dataArray[] = $this->_getVisitorsFromMonthIntervalHelper($i);
		}
		$dataArray[] = $this->_getVisitorsFromCurrentMonthHelper();
		
		$returnArray['type'] = 'month';
		$returnArray['data'] = $dataArray;
		
		return $returnArray;
	}
	
	
	/**
	 * Get visitors data for the last six months.
	 *
	 * @return array
	 */
	public function getVisitorsStatisticsDataLastYear()
	{
		$returnArray = array();
		$dataArray   = array();
		
		for($i = 12; $i > 1; $i--)
		{
			$dataArray[] = $this->_getVisitorsFromMonthIntervalHelper($i);
		}
		$dataArray[] = $this->_getVisitorsFromCurrentMonthHelper();
		
		$returnArray['type'] = 'month';
		$returnArray['data'] = $dataArray;
		
		return $returnArray;
	}
	
	
	/**
	 * Helper function to fetch visitors data of a month time span.
	 *
	 * @param int $fromInterval
	 *
	 * @return array
	 */
	protected function _getVisitorsFromDayIntervalHelper($fromInterval)
	{
		$query = 'SELECT SUM(gm_counter_visits_total) AS amount 
                  FROM gm_counter_visits 
                  WHERE DATE(gm_counter_date) BETWEEN DATE_SUB(NOW(),INTERVAL ' . $fromInterval
		         . ' DAY) AND DATE_SUB(NOW(),INTERVAL ' . ((int)$fromInterval - 1) . ' DAY)';
		$data  = $this->db->query($query)->row_array(1);
		if(null === $data['amount'])
		{
			$data['amount'] = '0';
		}
		
		return array_merge(array('period' => strtotime('-' . ((int)$fromInterval - 1) . ' days') * 1000), $data);
	}
	
	
	/**
	 * Helper function to fetch visitors data of the current month.
	 *
	 * @return array
	 */
	protected function _getVisitorsFromCurrentDayHelper()
	{
		$query = 'SELECT SUM(gm_counter_visits_total) AS amount 
                  FROM gm_counter_visits 
                  WHERE DATE(gm_counter_date) BETWEEN DATE_SUB(NOW(),INTERVAL 1 DAY) AND NOW()';
		$data  = $this->db->query($query)->row_array(1);
		if(null === $data['amount'])
		{
			$data['amount'] = '0';
		}
		
		return array_merge(array('period' => time() * 1000), $data);
	}
	
	
	/**
	 * Helper function to fetch visitors data of a month time span.
	 *
	 * @param int $fromInterval
	 *
	 * @return array
	 */
	protected function _getVisitorsFromMonthIntervalHelper($fromInterval)
	{
		$query = 'SELECT SUM(gm_counter_visits_total) AS amount 
                  FROM gm_counter_visits 
                  WHERE DATE(gm_counter_date) BETWEEN DATE_SUB(CURDATE(),INTERVAL ' . $fromInterval
		         . ' MONTH) AND DATE_SUB(CURDATE(),INTERVAL ' . ((int)$fromInterval - 1) . ' MONTH)';
		$data  = $this->db->query($query)->row_array(1);
		if(null === $data['amount'])
		{
			$data['amount'] = '0';
		}
		
		return array_merge(array('period' => strtotime('-' . ((int)$fromInterval - 1) . ' months') * 1000), $data);
	}
	
	
	/**
	 * Helper function to fetch visitors data of the current month.
	 *
	 * @return array
	 */
	protected function _getVisitorsFromCurrentMonthHelper()
	{
		$query = 'SELECT SUM(gm_counter_visits_total) AS amount 
                  FROM gm_counter_visits 
                  WHERE DATE(gm_counter_date) BETWEEN DATE_SUB(CURDATE(),INTERVAL 1 MONTH) AND CURDATE()';
		$data  = $this->db->query($query)->row_array(1);
		if(null === $data['amount'])
		{
			$data['amount'] = '0';
		}
		
		return array_merge(array('period' => time() * 1000), $data);
	}
	
	
	/**
	 * Gets the amount of visitors of the current date.
	 *
	 * @return int
	 */
	public function getVisitorsToday()
	{
		$result = $this->db->query('SELECT gm_counter_visits_total AS visitors 
                                            FROM gm_counter_visits 
                                            WHERE DATE(gm_counter_date) = CURDATE() ')->row_array(1);
		
		return (int)$result['visitors'];
	}
	
	
	/**
	 * Gets the amount of visitors of the last week
	 *
	 * @return int
	 */
	public function getVisitorsLastWeek()
	{
		$result = $this->db->query('SELECT SUM(gm_counter_visits_total) AS visitors 
                                  FROM gm_counter_visits 
                                  WHERE DATE_SUB(CURDATE(),INTERVAL 7 DAY) <= DATE(gm_counter_date)')->row_array(1);
		
		return (int)$result['visitors'];
	}
	
	
	/**
	 * Gets the amount of visitors of the last two week
	 *
	 * @return int
	 */
	public function getVisitorsLastTwoWeeks()
	{
		$result = $this->db->query('SELECT SUM(gm_counter_visits_total) AS visitors 
                                  FROM gm_counter_visits 
                                  WHERE DATE_SUB(CURDATE(),INTERVAL 14 DAY) <= DATE(gm_counter_date)')->row_array(1);
		
		return (int)$result['visitors'];
	}
	
	
	/**
	 * Gets the amount of visitors of the last month
	 *
	 * @return int
	 */
	public function getVisitorsLastMonth()
	{
		$result = $this->db->query('SELECT SUM(gm_counter_visits_total) AS visitors 
                                  FROM gm_counter_visits 
                                  WHERE DATE_SUB(CURDATE(),INTERVAL 1 MONTH) <= DATE(gm_counter_date)')->row_array(1);
		
		return (int)$result['visitors'];
	}
	
	
	/**
	 * Gets the amount of visitors of the last three months
	 *
	 * @return int
	 */
	public function getVisitorsLastThreeMonths()
	{
		$result = $this->db->query('SELECT SUM(gm_counter_visits_total) AS visitors 
                                  FROM gm_counter_visits 
                                  WHERE DATE_SUB(CURDATE(),INTERVAL 3 MONTH) <= DATE(gm_counter_date)')->row_array(1);
		
		return (int)$result['visitors'];
	}
	
	
	/**
	 * Gets the amount of visitors of the last six months
	 *
	 * @return int
	 */
	public function getVisitorsLastSixMonths()
	{
		$result = $this->db->query('SELECT SUM(gm_counter_visits_total) AS visitors 
                                  FROM gm_counter_visits 
                                  WHERE DATE_SUB(CURDATE(),INTERVAL 6 MONTH) <= DATE(gm_counter_date)')->row_array(1);
		
		return (int)$result['visitors'];
	}
	
	
	/**
	 * Gets the amount of visitors of the last year
	 *
	 * @return int
	 */
	public function getVisitorsLastYear()
	{
		$result = $this->db->query('SELECT SUM(gm_counter_visits_total) AS visitors 
                                  FROM gm_counter_visits 
                                  WHERE DATE_SUB(CURDATE(),INTERVAL 1 YEAR) <= DATE(gm_counter_date)')->row_array(1);
		
		return (int)$result['visitors'];
	}
	
	// ------------------------------------------------------------------------
	// GET NEW CUSTOMERS
	// ------------------------------------------------------------------------
	
	/**
	 * Get new customer data of the last week.
	 *
	 * @return array
	 */
	public function getNewCustomersStatisticsDataLastWeek()
	{
		$returnArray = array();
		$dataArray   = array();
		
		for($i = 7; $i > 1; $i--)
		{
			$dataArray[] = $this->_getNewCustomersFromDayIntervalHelper($i);
		}
		$dataArray[] = $this->_getNewCustomersFromCurrentDayHelper();
		
		$returnArray['type'] = 'month';
		$returnArray['data'] = $dataArray;
		
		return $returnArray;
	}
	
	
	/**
	 * Get new customer data of the last two weeks.
	 *
	 * @return array
	 */
	public function getNewCustomersStatisticsDataLastTwoWeeks()
	{
		$returnArray = array();
		$dataArray   = array();
		
		for($i = 14; $i > 1; $i--)
		{
			$dataArray[] = $this->_getNewCustomersFromDayIntervalHelper($i);
		}
		$dataArray[] = $this->_getNewCustomersFromCurrentDayHelper();
		
		$returnArray['type'] = 'month';
		$returnArray['data'] = $dataArray;
		
		return $returnArray;
	}
	
	
	/**
	 * Get new customer data of the last month.
	 *
	 * @return array
	 */
	public function getNewCustomersStatisticsDataLastMonth()
	{
		$returnArray = array();
		$dataArray   = array();
		$dayDiff     = time() - strtotime('-1 months');
		$days        = floor($dayDiff / (60 * 60 * 24));
		
		for($i = $days; $i > 1; $i--)
		{
			$dataArray[] = $this->_getNewCustomersFromDayIntervalHelper($i);
		}
		$dataArray[] = $this->_getNewCustomersFromCurrentDayHelper();
		
		$returnArray['type'] = 'month';
		$returnArray['data'] = $dataArray;
		
		return $returnArray;
	}
	
	
	/**
	 * Get new customer data of the last three months.
	 *
	 * @return array
	 */
	public function getNewCustomersStatisticsDataLastThreeMonth()
	{
		$returnArray = array();
		$dataArray   = array();
		
		for($i = 3; $i > 1; $i--)
		{
			$dataArray[] = $this->_getNewCustomersFromMonthIntervalHelper($i);
		}
		$dataArray[] = $this->_getNewCustomersFromCurrentMonthHelper();
		
		$returnArray['type'] = 'month';
		$returnArray['data'] = $dataArray;
		
		return $returnArray;
	}
	
	
	/**
	 * Get new customer data of the last six months.
	 *
	 * @return array
	 */
	public function getNewCustomersStatisticsDataLastSixMonth()
	{
		$returnArray = array();
		$dataArray   = array();
		
		for($i = 6; $i > 1; $i--)
		{
			$dataArray[] = $this->_getNewCustomersFromMonthIntervalHelper($i);
		}
		$dataArray[] = $this->_getNewCustomersFromCurrentMonthHelper();
		
		$returnArray['type'] = 'month';
		$returnArray['data'] = $dataArray;
		
		return $returnArray;
	}
	
	
	/**
	 * Get new customer data of the last year.
	 *
	 * @return array
	 */
	public function getNewCustomersStatisticsDataLastYear()
	{
		$returnArray = array();
		$dataArray   = array();
		
		for($i = 12; $i > 1; $i--)
		{
			$dataArray[] = $this->_getNewCustomersFromMonthIntervalHelper($i);
		}
		$dataArray[] = $this->_getNewCustomersFromCurrentMonthHelper();
		
		$returnArray['type'] = 'month';
		$returnArray['data'] = $dataArray;
		
		return $returnArray;
	}
	
	
	/**
	 * Helper function to fetch new customer data of a day time span.
	 *
	 * @param int $fromInterval
	 *
	 * @return array
	 */
	protected function _getNewCustomersFromDayIntervalHelper($fromInterval)
	{
		$query = 'SELECT COUNT(*) AS amount
                  FROM customers_info 
                  WHERE DATE(customers_info_date_account_created) BETWEEN DATE_SUB(NOW(),INTERVAL ' . $fromInterval
		         . ' DAY) AND DATE_SUB(NOW(),INTERVAL ' . ((int)$fromInterval - 1) . ' DAY)';
		$data  = $this->db->query($query)->row_array(1);
		if(null === $data['amount'])
		{
			$data['amount'] = '0';
		}
		
		return array_merge(array('period' => strtotime('-' . ((int)$fromInterval - 1) . ' days') * 1000), $data);
	}
	
	
	/**
	 * Helper function to fetch new customer data of the current day.
	 *
	 * @return array
	 */
	protected function _getNewCustomersFromCurrentDayHelper()
	{
		$query = 'SELECT COUNT(*) AS amount
                  FROM customers_info 
                  WHERE DATE(customers_info_date_account_created) BETWEEN DATE_SUB(NOW(),INTERVAL 1 DAY) AND NOW()';
		$data  = $this->db->query($query)->row_array(1);
		if(null === $data['amount'])
		{
			$data['amount'] = '0';
		}
		
		return array_merge(array('period' => time() * 1000), $data);
	}
	
	
	/**
	 * Helper function to fetch new customer data of a month time span.
	 *
	 * @param int $fromInterval
	 *
	 * @return array
	 */
	protected function _getNewCustomersFromMonthIntervalHelper($fromInterval)
	{
		$query = 'SELECT COUNT(*) AS amount
                  FROM customers_info 
                  WHERE DATE(customers_info_date_account_created) BETWEEN DATE_SUB(CURDATE(),INTERVAL ' . $fromInterval
		         . ' MONTH) AND DATE_SUB(CURDATE(),INTERVAL ' . ((int)$fromInterval - 1) . ' MONTH)';
		$data  = $this->db->query($query)->row_array(1);
		if(null === $data['amount'])
		{
			$data['amount'] = '0';
		}
		
		return array_merge(array('period' => strtotime('-' . ((int)$fromInterval - 1) . ' months') * 1000), $data);
	}
	
	
	/**
	 * Helper function to fetch new customer data of the current month.
	 *
	 * @return array
	 */
	protected function _getNewCustomersFromCurrentMonthHelper()
	{
		$query = 'SELECT COUNT(*) AS amount
                  FROM customers_info 
                  WHERE DATE(customers_info_date_account_created) BETWEEN DATE_SUB(CURDATE(),INTERVAL 1 MONTH) AND CURDATE()';
		$data  = $this->db->query($query)->row_array(1);
		if(null === $data['amount'])
		{
			$data['amount'] = '0';
		}
		
		return array_merge(array('period' => time() * 1000), $data);
	}
	
	
	/**
	 * Gets the amount of new customers of the current date
	 *
	 * @return int
	 */
	public function getNewCustomersToday()
	{
		$result = $this->db->query('SELECT COUNT(*) AS new_customers 
                                            FROM customers_info 
                                            WHERE DATE(customers_info_date_account_created) = CURDATE()')->row_array(1);
		
		return (int)$result['new_customers'];
	}
	
	
	/**
	 * Gets the amount of new customers of the last week
	 *
	 * @return int
	 */
	public function getNewCustomersLastWeek()
	{
		$result = $this->db->query('SELECT COUNT(*) AS new_customers
                                  FROM customers_info 
                                  WHERE DATE_SUB(CURDATE(),INTERVAL 7 DAY) <= DATE(customers_info_date_account_created)')
		                   ->row_array(1);
		
		return (int)$result['new_customers'];
	}
	
	
	/**
	 * Gets the amount of new customers of the last two week
	 *
	 * @return int
	 */
	public function getNewCustomersLastTwoWeeks()
	{
		$result = $this->db->query('SELECT COUNT(*) AS new_customers
                                  FROM customers_info 
                                  WHERE DATE_SUB(CURDATE(),INTERVAL 14 DAY) <= DATE(customers_info_date_account_created)')
		                   ->row_array(1);
		
		return (int)$result['new_customers'];
	}
	
	
	/**
	 * Gets the amount of new customers of the last month
	 *
	 * @return int
	 */
	public function getNewCustomersLastMonth()
	{
		$result = $this->db->query('SELECT COUNT(*) AS new_customers
                                  FROM customers_info 
                                  WHERE DATE_SUB(CURDATE(),INTERVAL 1 MONTH) <= DATE(customers_info_date_account_created)')
		                   ->row_array(1);
		
		return (int)$result['new_customers'];
	}
	
	
	/**
	 * Gets the amount of new customers of the last three months
	 *
	 * @return int
	 */
	public function getNewCustomersLastThreeMonths()
	{
		$result = $this->db->query('SELECT COUNT(*) AS new_customers
                                  FROM customers_info 
                                  WHERE DATE_SUB(CURDATE(),INTERVAL 3 MONTH) <= DATE(customers_info_date_account_created)')
		                   ->row_array(1);
		
		return (int)$result['new_customers'];
	}
	
	
	/**
	 * Gets the amount of new customers of the last six months
	 *
	 * @return int
	 */
	public function getNewCustomersLastSixMonths()
	{
		$result = $this->db->query('SELECT COUNT(*) AS new_customers
                                  FROM customers_info
                                  WHERE DATE_SUB(CURDATE(),INTERVAL 6 MONTH) <= DATE(customers_info_date_account_created)')
		                   ->row_array(1);
		
		return (int)$result['new_customers'];
	}
	
	
	/**
	 * Gets the amount of new customers of the last year
	 *
	 * @return int
	 */
	public function getNewCustomersLastYear()
	{
		$result = $this->db->query('SELECT COUNT(*) AS new_customers
                                  FROM customers_info
                                  WHERE DATE_SUB(CURDATE(),INTERVAL 1 YEAR) <= DATE(customers_info_date_account_created)')
		                   ->row_array(1);
		
		return (int)$result['new_customers'];
	}
	
	
	// ------------------------------------------------------------------------
	// GET ORDERS COUNT
	// ------------------------------------------------------------------------
	
	/**
	 * Get orders data for the last week.
	 *
	 * @return array
	 */
	public function getOrdersStatisticsDataLastWeek()
	{
		$returnArray = array();
		$dataArray   = array();
		
		for($i = 7; $i > 1; $i--)
		{
			$dataArray[] = $this->_getOrdersFromDayIntervalHelper($i);
		}
		$dataArray[] = $this->_getOrdersFromCurrentDayHelper();
		
		$returnArray['type'] = 'day';
		$returnArray['data'] = $dataArray;
		
		return $returnArray;
	}
	
	
	/**
	 * Get orders data for the last two weeks.
	 *
	 * @return array
	 */
	public function getOrdersStatisticsDataLastTwoWeek()
	{
		$returnArray = array();
		$dataArray   = array();
		
		for($i = 14; $i > 1; $i--)
		{
			$dataArray[] = $this->_getOrdersFromDayIntervalHelper($i);
		}
		$dataArray[] = $this->_getOrdersFromCurrentDayHelper();
		
		$returnArray['type'] = 'day';
		$returnArray['data'] = $dataArray;
		
		return $returnArray;
	}
	
	
	/**
	 * Get orders data for the last month.
	 *
	 * @return array
	 */
	public function getOrdersStatisticsDataLastMonth()
	{
		$returnArray = array();
		$dataArray   = array();
		$dayDiff     = time() - strtotime('-1 months');
		$days        = floor($dayDiff / (60 * 60 * 24));
		
		for($i = $days; $i > 1; $i--)
		{
			$dataArray[] = $this->_getOrdersFromDayIntervalHelper($i);
		}
		$dataArray[] = $this->_getOrdersFromCurrentDayHelper();
		
		$returnArray['type'] = 'day';
		$returnArray['data'] = $dataArray;
		
		return $returnArray;
	}
	
	
	/**
	 * Get order data for the last three months.
	 *
	 * @return array
	 */
	public function getOrderStatisticsDataLastThreeMonth()
	{
		$returnArray = array();
		$dataArray   = array();
		
		for($i = 3; $i > 1; $i--)
		{
			$dataArray[] = $this->_getOrdersFromMonthIntervalHelper($i);
		}
		$dataArray[] = $this->_getOrdersFromCurrentMonthHelper();
		
		$returnArray['type'] = 'month';
		$returnArray['data'] = $dataArray;
		
		return $returnArray;
	}
	
	
	/**
	 * Get order data for the last six months.
	 *
	 * @return array
	 */
	public function getOrderStatisticsDataLastSixMonth()
	{
		$returnArray = array();
		$dataArray   = array();
		
		for($i = 6; $i > 1; $i--)
		{
			$dataArray[] = $this->_getOrdersFromMonthIntervalHelper($i);
		}
		$dataArray[] = $this->_getOrdersFromCurrentMonthHelper();
		
		$returnArray['type'] = 'month';
		$returnArray['data'] = $dataArray;
		
		return $returnArray;
	}
	
	
	/**
	 * Get order data for the last year.
	 *
	 * @return array
	 */
	public function getOrderStatisticsDataLastYear()
	{
		$returnArray = array();
		$dataArray   = array();
		
		for($i = 12; $i > 1; $i--)
		{
			$dataArray[] = $this->_getOrdersFromMonthIntervalHelper($i);
		}
		$dataArray[] = $this->_getOrdersFromCurrentMonthHelper();
		
		$returnArray['type'] = 'month';
		$returnArray['data'] = $dataArray;
		
		return $returnArray;
	}
	
	
	/**
	 * Helper function to fetch order data of a month time span.
	 *
	 * @param int $fromInterval
	 *
	 * @return array
	 */
	protected function _getOrdersFromDayIntervalHelper($fromInterval)
	{
		$query = 'SELECT COUNT(*) AS amount 
                  FROM orders 
                  WHERE
                  orders_status NOT IN (' . implode(',', $this->excludeOrderStatusIds) . ') AND
				  DATE(date_purchased) BETWEEN DATE_SUB(NOW(),INTERVAL ' . $fromInterval
		         . ' DAY) AND DATE_SUB(NOW(),INTERVAL ' . ((int)$fromInterval - 1) . ' DAY)';
		$data  = $this->db->query($query)->row_array(1);
		if(null === $data['amount'])
		{
			$data['amount'] = '0';
		}
		
		return array_merge(array('period' => strtotime('-' . ((int)$fromInterval - 1) . ' days') * 1000), $data);
	}
	
	
	/**
	 * Helper function to fetch order data of the current month.
	 *
	 * @return array
	 */
	protected function _getOrdersFromCurrentDayHelper()
	{
		$query = 'SELECT COUNT(*) AS amount 
                  FROM orders 
                  WHERE
                  orders_status NOT IN (' . implode(',', $this->excludeOrderStatusIds) . ') AND
				  DATE(date_purchased) BETWEEN DATE_SUB(NOW(),INTERVAL 1 DAY) AND NOW()';
		$data  = $this->db->query($query)->row_array(1);
		if(null === $data['amount'])
		{
			$data['amount'] = '0';
		}
		
		return array_merge(array('period' => time() * 1000), $data);
	}
	
	
	/**
	 * Helper function to fetch order data of a month time span.
	 *
	 * @param int $fromInterval
	 *
	 * @return array
	 */
	protected function _getOrdersFromMonthIntervalHelper($fromInterval)
	{
		$query = 'SELECT COUNT(*) AS amount 
                  FROM orders 
                  WHERE
                  orders_status NOT IN (' . implode(',', $this->excludeOrderStatusIds) . ') AND
				  DATE(date_purchased) BETWEEN DATE_SUB(CURDATE(),INTERVAL ' . $fromInterval
		         . ' MONTH) AND DATE_SUB(CURDATE(),INTERVAL ' . ((int)$fromInterval - 1) . ' MONTH)';
		$data  = $this->db->query($query)->row_array(1);
		if(null === $data['amount'])
		{
			$data['amount'] = '0';
		}
		
		return array_merge(array('period' => strtotime('-' . ((int)$fromInterval - 1) . ' months') * 1000), $data);
	}
	
	
	/**
	 * Helper function to fetch order data of the current month.
	 *
	 * @return array
	 */
	protected function _getOrdersFromCurrentMonthHelper()
	{
		$query = 'SELECT COUNT(*) AS amount 
                  FROM orders 
                  WHERE
                  orders_status NOT IN (' . implode(',', $this->excludeOrderStatusIds) . ') AND
				  DATE(date_purchased) BETWEEN DATE_SUB(CURDATE(),INTERVAL 1 MONTH) AND CURDATE()';
		$data  = $this->db->query($query)->row_array(1);
		if(null === $data['amount'])
		{
			$data['amount'] = '0';
		}
		
		return array_merge(array('period' => time() * 1000), $data);
	}
	
	
	/**
	 * Gets the orders count of today
	 *
	 * @return int
	 */
	public function getOrdersCountToday()
	{
		$result = $this->db->query('SELECT COUNT(*) AS orders_count 
									  FROM orders 
									  WHERE 
                                        orders_status NOT IN (' . implode(',', $this->excludeOrderStatusIds) . ') AND
									  	DATE(date_purchased) = CURDATE()')->row_array(1);
		
		return (int)$result['orders_count'];
	}
	
	
	/**
	 * Gets the orders count of the last week
	 *
	 * @return int
	 */
	public function getOrdersCountLastWeek()
	{
		$result = $this->db->query('SELECT COUNT(*) AS orders_count 
									  FROM orders 
									  WHERE
                                        orders_status NOT IN (' . implode(',', $this->excludeOrderStatusIds) . ') AND
									  	DATE_SUB(CURDATE(),INTERVAL 7 DAY) <= DATE(date_purchased)')->row_array(1);
		
		return (int)$result['orders_count'];
	}
	
	
	/**
	 * Gets the orders count of the last two weeks
	 *
	 * @return int
	 */
	public function getOrdersCountLastTwoWeeks()
	{
		$result = $this->db->query('SELECT COUNT(*) AS orders_count 
									  FROM orders 
									  WHERE
                                        orders_status NOT IN (' . implode(',', $this->excludeOrderStatusIds) . ') AND
									  	DATE_SUB(CURDATE(),INTERVAL 14 DAY) <= DATE(date_purchased)')->row_array(1);
		
		return (int)$result['orders_count'];
	}
	
	
	/**
	 * Gets the orders count of the last month
	 *
	 * @return int
	 */
	public function getOrdersCountLastMonth()
	{
		$result = $this->db->query('SELECT COUNT(*) AS orders_count 
                                  FROM orders 
                                  WHERE
                                        orders_status NOT IN (' . implode(',', $this->excludeOrderStatusIds) . ') AND
									  	DATE_SUB(CURDATE(),INTERVAL 1 MONTH) <= DATE(date_purchased)')->row_array(1);
		
		return (int)$result['orders_count'];
	}
	
	
	/**
	 * Gets the orders count of the last three months
	 *
	 * @return int
	 */
	public function getOrdersCountLastThreeMonths()
	{
		$result = $this->db->query('SELECT COUNT(*) AS orders_count 
                                  FROM orders 
                                  WHERE
                                        orders_status NOT IN (' . implode(',', $this->excludeOrderStatusIds) . ') AND
									  	DATE_SUB(CURDATE(),INTERVAL 3 MONTH) <= DATE(date_purchased)')->row_array(1);
		
		return (int)$result['orders_count'];
	}
	
	
	/**
	 * Gets the orders count of the last six months
	 *
	 * @return int
	 */
	public function getOrdersCountLastSixMonths()
	{
		$result = $this->db->query('SELECT COUNT(*) AS orders_count 
                                  FROM orders 
                                  WHERE
                                        orders_status NOT IN (' . implode(',', $this->excludeOrderStatusIds) . ') AND
									  	DATE_SUB(CURDATE(),INTERVAL 6 MONTH) <= DATE(date_purchased)')->row_array(1);
		
		return (int)$result['orders_count'];
	}
	
	
	/**
	 * Gets the orders count of the last year
	 *
	 * @return int
	 */
	public function getOrdersCountLastYear()
	{
		$result = $this->db->query('SELECT COUNT(*) AS orders_count 
                                  FROM orders 
                                  WHERE
                                        orders_status NOT IN (' . implode(',', $this->excludeOrderStatusIds) . ') AND
									  	DATE_SUB(CURDATE(),INTERVAL 1 YEAR) <= DATE(date_purchased)')->row_array(1);
		
		return (int)$result['orders_count'];
	}
	
	
	// ------------------------------------------------------------------------
	// GET CONVERSION RATE
	// ------------------------------------------------------------------------
	
	/**
	 * Gets the conversion rate of today as a formatted number (i.e.: 0,27)
	 *
	 * @return string
	 */
	public function getConversionRateToday()
	{
		return $this->_getConversionRate($this->getOrdersCountToday(), $this->getVisitorsToday());
	}
	
	
	/**
	 * Gets the conversion rate of the last week as a formatted number (i.e.: 0,27)
	 *
	 * @return string
	 */
	public function getConversionRateLastWeek()
	{
		return $this->_getConversionRate($this->getOrdersCountLastWeek(), $this->getVisitorsLastWeek());
	}
	
	
	/**
	 * Gets the conversion rate of the last two weeks as a formatted number (i.e.: 0,27)
	 *
	 * @return string
	 */
	public function getConversionRateLastTwoWeeks()
	{
		return $this->_getConversionRate($this->getOrdersCountLastTwoWeeks(), $this->getVisitorsLastTwoWeeks());
	}
	
	
	/**
	 * Gets the conversion rate of the last month as a formatted number (i.e.: 0,27)
	 *
	 * @return string
	 */
	public function getConversionRateLastMonth()
	{
		return $this->_getConversionRate($this->getOrdersCountLastMonth(), $this->getVisitorsLastMonth());
	}
	
	
	/**
	 * Gets the conversion rate of the last three months as a formatted number (i.e.: 0,27)
	 *
	 * @return string
	 */
	public function getConversionRateLastThreeMonths()
	{
		return $this->_getConversionRate($this->getOrdersCountLastThreeMonths(), $this->getVisitorsLastThreeMonths());
	}
	
	
	/**
	 * Gets the conversion rate of the last six months as a formatted number (i.e.: 0,27)
	 *
	 * @return string
	 */
	public function getConversionRateLastSixMonths()
	{
		return $this->_getConversionRate($this->getOrdersCountLastSixMonths(), $this->getVisitorsLastSixMonths());
	}
	
	
	/**
	 * Gets the conversion rate of the last year as a formatted number (i.e.: 0,27)
	 *
	 * @return string
	 */
	public function getConversionRateLastYear()
	{
		return $this->_getConversionRate($this->getOrdersCountLastYear(), $this->getVisitorsLastYear());
	}
	
	
	/**
	 * Calculates the conversion rate
	 *
	 * @param int $p_ordersCount
	 * @param int $p_visitorsCount
	 *
	 * @return double
	 */
	protected function _calculateConversionRate($p_ordersCount, $p_visitorsCount)
	{
		$conversionRate = 0;
		$ordersCount    = (int)$p_ordersCount;
		$visitorsCount  = (int)$p_visitorsCount;
		
		if($visitorsCount > 0)
		{
			$conversionRate = round($ordersCount / $visitorsCount * 100, $this->conversionRateDecimalPlaces);
		}
		
		return $conversionRate;
	}
	
	
	/**
	 * Returns the conversion rate as a formatted number (i.e.: 0,27)
	 *
	 * @param int $p_ordersCount
	 * @param int $p_visitorsCount
	 *
	 * @return string
	 */
	protected function _getConversionRate($p_ordersCount, $p_visitorsCount)
	{
		$conversionRate = $this->_calculateConversionRate($p_ordersCount, $p_visitorsCount);
		
		$decimalPoint = ',';
		
		if(isset($this->xtcPrice->currencies[$this->xtcPrice->actualCurr]))
		{
			$decimalPoint = $this->xtcPrice->currencies[$this->xtcPrice->actualCurr]['decimal_point'];
		}
		
		$conversionRate = number_format($conversionRate, $this->conversionRateDecimalPlaces, $decimalPoint, '');
		
		return $conversionRate;
	}
	
	
	// ------------------------------------------------------------------------
	// GET SALES
	// ------------------------------------------------------------------------
	
	/**
	 * Gets the amount of sales of the current date
	 *
	 * @return string
	 */
	public function getSalesToday()
	{
		$formattedResult = $this->xtcPrice->xtcFormat($this->_getSalesToday(), true);
		
		return $formattedResult;
	}
	
	
	/**
	 * Gets the amount of sales of the last week
	 *
	 * @return string
	 */
	public function getSalesLastWeek()
	{
		$formattedResult = $this->xtcPrice->xtcFormat($this->_getSalesLastWeek(), true);
		
		return $formattedResult;
	}
	
	
	/**
	 * Gets the amount of sales of the last two week
	 *
	 * @return string
	 */
	public function getSalesLastTwoWeeks()
	{
		$formattedResult = $this->xtcPrice->xtcFormat($this->_getSalesLastTwoWeeks(), true);
		
		return $formattedResult;
	}
	
	
	/**
	 * Gets the amount of sales of the last month
	 *
	 * @return string
	 */
	public function getSalesLastMonth()
	{
		$formattedResult = $this->xtcPrice->xtcFormat($this->_getSalesLastMonth(), true);
		
		return $formattedResult;
	}
	
	
	/**
	 * Gets the amount of sales of the last three months
	 *
	 * @return string
	 */
	public function getSalesLastThreeMonths()
	{
		$formattedResult = $this->xtcPrice->xtcFormat($this->_getSalesLastThreeMonths(), true);
		
		return $formattedResult;
	}
	
	
	/**
	 * Gets the amount of sales of the last six months
	 *
	 * @return string
	 */
	public function getSalesLastSixMonths()
	{
		$formattedResult = $this->xtcPrice->xtcFormat($this->_getSalesLastSixMonths(), true);
		
		return $formattedResult;
	}
	
	
	/**
	 * Gets the amount of sales of the last year
	 *
	 * @return string
	 */
	public function getSalesLastYear()
	{
		$formattedResult = $this->xtcPrice->xtcFormat($this->_getSalesLastYear(), true);
		
		return $formattedResult;
	}
	
	
	/**
	 * Get sales data for the last week.
	 *
	 * @return array
	 */
	public function getSalesStatisticsDataLastWeek()
	{
		$returnArray = array();
		$dataArray   = array();
		
		for($i = 7; $i > 1; $i--)
		{
			$dataArray[] = $this->_getSalesFromDayIntervalHelper($i);
		}
		$dataArray[] = $this->_getSalesFromCurrentDayHelper();
		
		$returnArray['type'] = 'day';
		$returnArray['data'] = $dataArray;
		
		return $returnArray;
	}
	
	
	/**
	 * Get sales data for the last two weeks.
	 *
	 * @return array
	 */
	public function getSalesStatisticsDataLastTwoWeeks()
	{
		$returnArray = array();
		$dataArray   = array();
		
		for($i = 14; $i > 1; $i--)
		{
			$dataArray[] = $this->_getSalesFromDayIntervalHelper($i);
		}
		$dataArray[] = $this->_getSalesFromCurrentDayHelper();
		
		$returnArray['type'] = 'day';
		$returnArray['data'] = $dataArray;
		
		return $returnArray;
	}
	
	
	/**
	 * Get sales data for the last month.
	 *
	 * @return array
	 */
	public function getSalesStatisticsDataLastMonth()
	{
		$returnArray = array();
		$dataArray   = array();
		$dayDiff     = time() - strtotime('-1 months');
		$days        = floor($dayDiff / (60 * 60 * 24));
		
		for($i = $days; $i > 1; $i--)
		{
			$dataArray[] = $this->_getSalesFromDayIntervalHelper($i);
		}
		$dataArray[] = $this->_getSalesFromCurrentDayHelper();
		
		$returnArray['type'] = 'day';
		$returnArray['data'] = $dataArray;
		
		return $returnArray;
	}
	
	
	/**
	 * Get sales data for the last three month.
	 *
	 * @return array
	 */
	public function getSalesStatisticsDataLastThreeMonth()
	{
		$returnArray = array();
		$dataArray   = array();
		
		$dataArray[] = $this->_getSalesFromMonthIntervalHelper(3);
		$dataArray[] = $this->_getSalesFromMonthIntervalHelper(2);
		$dataArray[] = $this->_getSalesFromCurrentMonthHelper();
		
		$returnArray['type'] = 'month';
		$returnArray['data'] = $dataArray;
		
		return $returnArray;
	}
	
	
	/**
	 * Get sales data for the last six month.
	 *
	 * @return array
	 */
	public function getSalesStatisticsDataLastSixMonth()
	{
		$returnArray = array();
		$dataArray   = array();
		
		for($i = 6; $i > 1; $i--)
		{
			$dataArray[] = $this->_getSalesFromMonthIntervalHelper($i);
		}
		$dataArray[] = $this->_getSalesFromCurrentMonthHelper();
		
		$returnArray['type'] = 'month';
		$returnArray['data'] = $dataArray;
		
		return $returnArray;
	}
	
	
	/**
	 * Get sales data for the last year.
	 *
	 * @return array
	 */
	public function getSalesStatisticsDataLastYear()
	{
		$returnArray = array();
		$dataArray   = array();
		
		for($i = 12; $i > 1; $i--)
		{
			$dataArray[] = $this->_getSalesFromMonthIntervalHelper($i);
		}
		$dataArray[] = $this->_getSalesFromCurrentMonthHelper();
		
		$returnArray['type'] = 'month';
		$returnArray['data'] = $dataArray;
		
		return $returnArray;
	}
	
	
	/**
	 * Helper function to fetch sales data of a day time span.
	 *
	 * @param int $fromInterval
	 *
	 * @return array
	 */
	protected function _getSalesFromDayIntervalHelper($fromInterval)
	{
		$query = 'SELECT SUM(ot.value*o.currency_value) AS amount 
				  FROM orders o, orders_total ot 
				  WHERE 
					  o.orders_id = ot.orders_id AND
					  o.orders_status NOT IN (' . implode(',', $this->excludeOrderStatusIds) . ') AND
					  ot.class = "ot_total" AND
					  DATE(o.date_purchased) BETWEEN DATE_SUB(NOW(),INTERVAL ' . $fromInterval
		         . ' DAY) AND DATE_SUB(NOW(),INTERVAL ' . ((int)$fromInterval - 1) . ' DAY)';
		$data  = $this->db->query($query)->row_array(1);
		if(null === $data['amount'])
		{
			$data['amount'] = '0';
		}
		
		return array_merge(array('period' => strtotime('-' . ((int)$fromInterval - 1) . ' days') * 1000), $data);
	}
	
	
	/**
	 * Helper function to fetch sales data of the current day.
	 *
	 * @return array
	 */
	protected function _getSalesFromCurrentDayHelper()
	{
		$query = 'SELECT SUM(ot.value*o.currency_value) AS amount 
				  FROM orders o, orders_total ot 
				  WHERE 
					  o.orders_id = ot.orders_id AND
					  o.orders_status NOT IN (' . implode(',', $this->excludeOrderStatusIds) . ') AND
					  ot.class = "ot_total" AND
					  DATE(o.date_purchased) BETWEEN DATE_SUB(NOW(),INTERVAL 1 DAY) AND NOW()';
		$data  = $this->db->query($query)->row_array(1);
		if(null === $data['amount'])
		{
			$data['amount'] = '0';
		}
		
		return array_merge(array('period' => time() * 1000), $data);
	}
	
	
	/**
	 * Helper function to fetch sales data of a month time span.
	 *
	 * @param int $fromInterval
	 *
	 * @return array
	 */
	protected function _getSalesFromMonthIntervalHelper($fromInterval)
	{
		$query = 'SELECT SUM(ot.value*o.currency_value) AS amount 
				  FROM orders o, orders_total ot 
				  WHERE 
					  o.orders_id = ot.orders_id AND
					  o.orders_status NOT IN (' . implode(',', $this->excludeOrderStatusIds) . ') AND
					  ot.class = "ot_total" AND
					  DATE(o.date_purchased) BETWEEN DATE_SUB(CURDATE(),INTERVAL ' . $fromInterval
		         . ' MONTH) AND DATE_SUB(CURDATE(),INTERVAL ' . ((int)$fromInterval - 1) . ' MONTH)';
		$data  = $this->db->query($query)->row_array(1);
		if(null === $data['amount'])
		{
			$data['amount'] = '0';
		}
		
		return array_merge(array('period' => strtotime('-' . ((int)$fromInterval - 1) . ' months') * 1000), $data);
	}
	
	
	/**
	 * Helper function to fetch sales data of the current month.
	 *
	 * @return array
	 */
	protected function _getSalesFromCurrentMonthHelper()
	{
		$query = 'SELECT SUM(ot.value*o.currency_value) AS amount 
				  FROM orders o, orders_total ot 
				  WHERE 
					  o.orders_id = ot.orders_id AND
					  o.orders_status NOT IN (' . implode(',', $this->excludeOrderStatusIds) . ') AND
					  ot.class = "ot_total" AND
					  DATE(o.date_purchased) BETWEEN DATE_SUB(CURDATE(),INTERVAL 1 MONTH) AND CURDATE()';
		$data  = $this->db->query($query)->row_array(1);
		if(null === $data['amount'])
		{
			$data['amount'] = '0';
		}
		
		return array_merge(array('period' => time() * 1000), $data);
	}
	
	
	/**
	 * Get Sales Today
	 *
	 * @return double
	 */
	protected function _getSalesToday()
	{
		$result = $this->db->query('SELECT SUM(ot.value * o.currency_value) AS sales 
									FROM orders o, orders_total ot 
									WHERE 
										o.orders_id = ot.orders_id AND
					                    o.orders_status NOT IN (' . implode(',', $this->excludeOrderStatusIds) . ') AND
										ot.class = "ot_total" AND
									 	DATE(o.date_purchased) = CURDATE()')->row_array(1);
		
		return (double)$result['sales'];
	}
	
	
	/**
	 * Get Sales Last Week
	 *
	 * @return double
	 */
	protected function _getSalesLastWeek()
	{
		$result = $this->db->query('SELECT SUM(ot.value*o.currency_value) AS sales 
									FROM orders o, orders_total ot 
									WHERE 
										o.orders_id = ot.orders_id AND
					                    o.orders_status NOT IN (' . implode(',', $this->excludeOrderStatusIds) . ') AND
										ot.class = "ot_total" AND
									 	DATE_SUB(CURDATE(),INTERVAL 7 DAY) <= DATE(o.date_purchased)')->row_array(1);
		
		return (double)$result['sales'];
	}
	
	
	/**
	 * Get Sales Last Two Weeks
	 *
	 * @return double
	 */
	protected function _getSalesLastTwoWeeks()
	{
		$result = $this->db->query('SELECT SUM(ot.value*o.currency_value) AS sales 
									FROM orders o, orders_total ot 
									WHERE 
										o.orders_id = ot.orders_id AND
					                    o.orders_status NOT IN (' . implode(',', $this->excludeOrderStatusIds) . ') AND
										ot.class = "ot_total" AND
									 	DATE_SUB(CURDATE(),INTERVAL 14 DAY) <= DATE(o.date_purchased)')->row_array(1);
		
		return (double)$result['sales'];
	}
	
	
	/**
	 * Get Sales Last Month
	 *
	 * @return double
	 */
	protected function _getSalesLastMonth()
	{
		$result = $this->db->query('SELECT SUM(ot.value*o.currency_value) AS sales 
									FROM orders o, orders_total ot 
									WHERE 
										o.orders_id = ot.orders_id AND
					                    o.orders_status NOT IN (' . implode(',', $this->excludeOrderStatusIds) . ') AND
										ot.class = "ot_total" AND
									 	DATE_SUB(CURDATE(),INTERVAL 1 MONTH) <= DATE(o.date_purchased)')->row_array(1);
		
		return (double)$result['sales'];
	}
	
	
	/**
	 * Get Sales Last Three Months
	 *
	 * @return double
	 */
	protected function _getSalesLastThreeMonths()
	{
		$result = $this->db->query('SELECT SUM(ot.value*o.currency_value) AS sales 
									FROM orders o, orders_total ot 
									WHERE 
										o.orders_id = ot.orders_id AND
					                    o.orders_status NOT IN (' . implode(',', $this->excludeOrderStatusIds) . ') AND
										ot.class = "ot_total" AND
									 	DATE_SUB(CURDATE(),INTERVAL 3 MONTH) <= DATE(o.date_purchased)')->row_array(1);
		
		return (double)$result['sales'];
	}
	
	
	/**
	 * Get Sales Last Six Months
	 *
	 * @return double
	 */
	protected function _getSalesLastSixMonths()
	{
		$result = $this->db->query('SELECT SUM(ot.value*o.currency_value) AS sales 
									FROM orders o, orders_total ot 
									WHERE 
										o.orders_id = ot.orders_id AND
					                    o.orders_status NOT IN (' . implode(',', $this->excludeOrderStatusIds) . ') AND
										ot.class = "ot_total" AND
									 	DATE_SUB(CURDATE(),INTERVAL 6 MONTH) <= DATE(o.date_purchased)')->row_array(1);
		
		return (double)$result['sales'];
	}
	
	
	/**
	 * Get Sales Last Year
	 *
	 * @return double
	 */
	protected function _getSalesLastYear()
	{
		$result = $this->db->query('SELECT SUM(ot.value*o.currency_value) AS sales 
									FROM orders o, orders_total ot 
									WHERE 
										o.orders_id = ot.orders_id AND
					                    o.orders_status NOT IN (' . implode(',', $this->excludeOrderStatusIds) . ') AND
										ot.class = "ot_total" AND
									 	DATE_SUB(CURDATE(),INTERVAL 1 YEAR) <= DATE(o.date_purchased)')->row_array(1);
		
		return (double)$result['sales'];
	}
	
	
	// ------------------------------------------------------------------------
	// GET AVERAGE ORDER VALUE
	// ------------------------------------------------------------------------
	
	/**
	 * Gets the average order value of today as a formatted price (i.e.: 1.234,56 EUR).
	 *
	 * @return string
	 */
	public function getAverageOrderValueToday()
	{
		return $this->_getAverageOrderValue($this->_getSalesToday(), $this->getOrdersCountToday());
	}
	
	
	/**
	 * Gets the average order value of the last week as a formatted price (i.e.: 1.234,56 EUR).
	 *
	 * @return string
	 */
	public function getAverageOrderValueLastWeek()
	{
		return $this->_getAverageOrderValue($this->_getSalesLastWeek(), $this->getOrdersCountLastWeek());
	}
	
	
	/**
	 * Gets the average order value of the last two weeks as a formatted price (i.e.: 1.234,56 EUR).
	 *
	 * @return string
	 */
	public function getAverageOrderValueLastTwoWeeks()
	{
		return $this->_getAverageOrderValue($this->_getSalesLastTwoWeeks(), $this->getOrdersCountLastTwoWeeks());
	}
	
	
	/**
	 * Gets the average order value of the last month as a formatted price (i.e.: 1.234,56 EUR).
	 *
	 * @return string
	 */
	public function getAverageOrderValueLastMonth()
	{
		return $this->_getAverageOrderValue($this->_getSalesLastMonth(), $this->getOrdersCountLastMonth());
	}
	
	
	/**
	 * Gets the average order value of the last three months as a formatted price (i.e.: 1.234,56 EUR).
	 *
	 * @return string
	 */
	public function getAverageOrderValueLastThreeMonths()
	{
		return $this->_getAverageOrderValue($this->_getSalesLastThreeMonths(), $this->getOrdersCountLastThreeMonths());
	}
	
	
	/**
	 * Gets the average order value of the last six months as a formatted price (i.e.: 1.234,56 EUR).
	 *
	 * @return string
	 */
	public function getAverageOrderValueLastSixMonths()
	{
		return $this->_getAverageOrderValue($this->_getSalesLastSixMonths(), $this->getOrdersCountLastSixMonths());
	}
	
	
	/**
	 * Gets the average order value of the last year as a formatted price (i.e.: 1.234,56 EUR).
	 *
	 * @return string
	 */
	public function getAverageOrderValueLastYear()
	{
		return $this->_getAverageOrderValue($this->_getSalesLastYear(), $this->getOrdersCountLastYear());
	}
	
	
	/**
	 * Calculates the average order value.
	 *
	 * @param double $p_sales
	 * @param int    $p_ordersCount
	 *
	 * @return double
	 */
	protected function _calculateAverageOrderValue($p_sales, $p_ordersCount)
	{
		$averageOrderValue = 0;
		$sales             = (double)$p_sales;
		$ordersCount       = (int)$p_ordersCount;
		
		if($ordersCount > 0)
		{
			$averageOrderValue = $sales / $ordersCount;
		}
		
		return $averageOrderValue;
	}
	
	
	/**
	 * Returns the conversion rate as a formatted number (i.e.: 0,27).
	 *
	 * @param double $p_sales
	 * @param int    $p_ordersCount
	 *
	 * @return string
	 */
	protected function _getAverageOrderValue($p_sales, $p_ordersCount)
	{
		$averageOrderValue = $this->_calculateAverageOrderValue($p_sales, $p_ordersCount);
		$averageOrderValue = $this->xtcPrice->xtcFormat($averageOrderValue, true);
		
		return $averageOrderValue;
	}
}