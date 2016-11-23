<?php
/* --------------------------------------------------------------
   DashboardController.inc.php 2016-07-21
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('AdminHttpViewController');

/**
 * Class DashboardController
 *
 * PHP controller class for the dashboard page of the admin section. The statistic results
 * are generated within this class and provided to the frontend through AJAX calls.
 *
 * @category System
 * @package  AdminHttpViewControllers
 */
class DashboardController extends AdminHttpViewController
{
	/**
	 * @var StatisticsService
	 */
	protected $statisticsService;
	
	/**
	 * @var array
	 */
	protected $excludeOrderStatusIds = array(99);
	
	
	/**
	 * Initialize Controller
	 */
	public function init()
	{
		$this->statisticsService = StaticGXCoreLoader::getService('Statistics');
		$this->contentView->set_template_dir(DIR_FS_ADMIN . 'html/content/'); // Set the template directory.
	}
	
	
	public function actionGetStatisticBoxes()
	{
		$interval = $this->_getQueryParameter('interval');
		
		$response = [
			'sales'          => $this->_getSales($interval),
			'online'         => $this->_getUsersOnline(),
			'visitors'       => $this->_getVisitors($interval),
			'orders'         => $this->_getOrdersCount($interval),
			'conversionRate' => $this->_getConversionRate($interval)
		];
		
		return MainFactory::create('JsonHttpControllerResponse', $response);
	}
	
	
	/**
	 * Returns the latest orders.
	 */
	public function actionGetLatestOrders()
	{
		$db   = StaticGXCoreLoader::getDatabaseQueryBuilder();
		$lang = MainFactory::create_object('LanguageTextManager', array('orders', $_SESSION['languages_id']));
		
		include_once(DIR_FS_INC . 'get_payment_title.inc.php');
		
		$db->select('*')
		   ->from('orders')
		   ->join('orders_total', 'orders_total.orders_id = orders.orders_id', 'inner')
		   ->where(array('orders_total.class' => 'ot_total'))
		   ->where_not_in('orders.orders_status', $this->excludeOrderStatusIds)
		   ->limit(5)
		   ->order_by('orders.date_purchased', 'desc');
		
		$data = $db->get()->result_array();
		
		$statuses = $db->get_where('orders_status', array('language_id' => $_SESSION['languages_id']))->result_array();
		
		// Parse order statuses with the "orders.php" logic.
		
		foreach($data as &$row)
		{
			if(!empty($row['payment_method']))
			{
				$row['payment_method'] = @get_payment_title($row['payment_method']);
			}
			
			if($row['orders_status'] == '0')
			{
				$row['orders_status_name'] = $lang->get_text('TEXT_VALIDATING');
			}
			else
			{
				foreach($statuses as $status)
				{
					if($status['orders_status_id'] === $row['orders_status'])
					{
						$row['orders_status_name'] = $status['orders_status_name'];
						break 1;
					}
				}
			}
		}
		
		return MainFactory::create('JsonHttpControllerResponse', array('data' => $data));
	}
	
	
	/**
	 * Returns the amount of users who are currently online
	 *
	 * @return JsonHttpControllerResponse
	 */
	public function actionGetUsersOnline()
	{
		return MainFactory::create('JsonHttpControllerResponse', $this->_getUsersOnline());
	}
	
	
	/**
	 * Get online user statistics.
	 *
	 * @return array
	 */
	protected function _getUsersOnline()
	{
		return array(
			'timespan' => $this->statisticsService->getUsersOnline(),
			'today'    => $this->statisticsService->getUsersOnline()
		);
	}
	
	
	/**
	 * Gets the amount of Visitors in the given timespan
	 *
	 * @return JsonHttpControllerResponse
	 */
	public function actionGetVisitors()
	{
		return MainFactory::create('JsonHttpControllerResponse',
		                           $this->_getVisitors($this->_getQueryParameter('interval')));
	}
	
	
	/**
	 * Get visitors count statistics.
	 *
	 * @param string $interval
	 *
	 * @return array
	 */
	protected function _getVisitors($interval)
	{
		switch($interval)
		{
			case 'week':
				$timespan = $this->statisticsService->getVisitorsLastWeek();
				break;
			
			case 'two_weeks':
				$timespan = $this->statisticsService->getVisitorsLastTwoWeeks();
				break;
			
			case 'month':
				$timespan = $this->statisticsService->getVisitorsLastMonth();
				break;
			
			case 'three_months':
				$timespan = $this->statisticsService->getVisitorsLastThreeMonths();
				break;
			
			case 'six_months':
				$timespan = $this->statisticsService->getVisitorsLastSixMonths();
				break;
			
			case 'year':
				$timespan = $this->statisticsService->getVisitorsLastYear();
				break;
			
			case 'today':
			default:
				$timespan = $this->statisticsService->getVisitorsToday();
				break;
		}
		
		return array(
			'timespan' => $timespan,
			'today'    => $this->statisticsService->getVisitorsToday()
		);
	}
	
	
	/**
	 * Returns the amount of Visitors in the given timespan
	 *
	 * @return JsonHttpControllerResponse
	 */
	public function actionGetNewCustomers()
	{
		switch($this->_getQueryParameter('interval'))
		{
			case 'week':
				$timespan = $this->statisticsService->getNewCustomersLastWeek();
				break;
			
			case 'two_weeks':
				$timespan = $this->statisticsService->getNewCustomersLastTwoWeeks();
				break;
			
			case 'month':
				$timespan = $this->statisticsService->getNewCustomersLastMonth();
				break;
			
			case 'three_months':
				$timespan = $this->statisticsService->getNewCustomersLastThreeMonths();
				break;
			
			case 'six_months':
				$timespan = $this->statisticsService->getNewCustomersLastSixMonths();
				break;
			
			case 'year':
				$timespan = $this->statisticsService->getNewCustomersLastYear();
				break;
			
			case 'today':
			default:
				$timespan = $this->statisticsService->getNewCustomersToday();
				break;
		}
		
		return MainFactory::create('JsonHttpControllerResponse', array(
			'timespan' => $timespan,
			'today'    => $this->statisticsService->getNewCustomersToday()
		));
	}
	
	
	/**
	 * Returns the count of orders in the given timespan
	 *
	 * @return JsonHttpControllerResponse
	 */
	public function actionGetOrdersCount()
	{
		return MainFactory::create('JsonHttpControllerResponse',
		                           $this->_getOrdersCount($this->_getQueryParameter('interval')));
	}
	
	
	/**
	 * Get orders count statistics.
	 *
	 * @param string $interval
	 *
	 * @return array
	 */
	protected function _getOrdersCount($interval)
	{
		switch($interval)
		{
			case 'week':
				$timespan = $this->statisticsService->getOrdersCountLastWeek();
				break;
			
			case 'two_weeks':
				$timespan = $this->statisticsService->getOrdersCountLastTwoWeeks();
				break;
			
			case 'month':
				$timespan = $this->statisticsService->getOrdersCountLastMonth();
				break;
			
			case 'three_months':
				$timespan = $this->statisticsService->getOrdersCountLastThreeMonths();
				break;
			
			case 'six_months':
				$timespan = $this->statisticsService->getOrdersCountLastSixMonths();
				break;
			
			case 'year':
				$timespan = $this->statisticsService->getOrdersCountLastYear();
				break;
			
			case 'today':
			default:
				$timespan = $this->statisticsService->getOrdersCountToday();
				break;
		}
		
		return array(
			'timespan' => $timespan,
			'today'    => $this->statisticsService->getOrdersCountToday()
		);
	}
	
	
	/**
	 * Returns the conversion rate in the given timespan
	 *
	 * @return JsonHttpControllerResponse
	 */
	public function actionGetConversionRate()
	{
		return MainFactory::create('JsonHttpControllerResponse',
		                           $this->_getConversionRate($this->_getQueryParameter('interval')));
	}
	
	
	/**
	 * Get conversion rate statistics.
	 *
	 * @param string $interval
	 *
	 * @return array
	 */
	protected function _getConversionRate($interval)
	{
		switch($interval)
		{
			case 'week':
				$timespan = $this->statisticsService->getConversionRateLastWeek();
				break;
			
			case 'two_weeks':
				$timespan = $this->statisticsService->getConversionRateLastTwoWeeks();
				break;
			
			case 'month':
				$timespan = $this->statisticsService->getConversionRateLastMonth();
				break;
			
			case 'three_months':
				$timespan = $this->statisticsService->getConversionRateLastThreeMonths();
				break;
			
			case 'six_months':
				$timespan = $this->statisticsService->getConversionRateLastSixMonths();
				break;
			
			case 'year':
				$timespan = $this->statisticsService->getConversionRateLastYear();
				break;
			
			case 'today':
			default:
				$timespan = $this->statisticsService->getConversionRateToday();
				break;
		}
		
		return array(
			'timespan' => $timespan,
			'today'    => $this->statisticsService->getConversionRateToday()
		);
	}
	
	
	/**
	 * Returns sales data for the dashboard statistic.
	 *
	 * @return \JsonHttpControllerResponse
	 */
	public function actionGetSalesStatisticsData()
	{
		switch($this->_getQueryParameter('interval'))
		{
			case 'week':
				$timespan = $this->statisticsService->getSalesStatisticsDataLastWeek();
				break;
			case 'two_weeks':
				$timespan = $this->statisticsService->getSalesStatisticsDataLastTwoWeeks();
				break;
			case 'month':
				$timespan = $this->statisticsService->getSalesStatisticsDataLastMonth();
				break;
			case 'three_months':
				$timespan = $this->statisticsService->getSalesStatisticsDataLastThreeMonth();
				break;
			case 'six_months':
				$timespan = $this->statisticsService->getSalesStatisticsDataLastSixMonth();
				break;
			case 'year':
				$timespan = $this->statisticsService->getSalesStatisticsDataLastYear();
				break;
			default:
				$timespan = $this->statisticsService->getSalesStatisticsDataLastThreeMonth();
				break;
		}
		
		return MainFactory::create('JsonHttpControllerResponse', $timespan);
	}
	
	
	protected function _getSalesStatisticsData($interval)
	{
		switch($interval)
		{
			case 'week':
				$timespan = $this->statisticsService->getSalesStatisticsDataLastWeek();
				break;
			case 'two_weeks':
				$timespan = $this->statisticsService->getSalesStatisticsDataLastTwoWeeks();
				break;
			case 'month':
				$timespan = $this->statisticsService->getSalesStatisticsDataLastMonth();
				break;
			case 'three_months':
				$timespan = $this->statisticsService->getSalesStatisticsDataLastThreeMonth();
				break;
			case 'six_months':
				$timespan = $this->statisticsService->getSalesStatisticsDataLastSixMonth();
				break;
			case 'year':
				$timespan = $this->statisticsService->getSalesStatisticsDataLastYear();
				break;
			default:
				$timespan = $this->statisticsService->getSalesStatisticsDataLastThreeMonth();
				break;
		}
		
		return $timespan;
	}
	
	
	/**
	 * Returns order data for the dashboard statistic.
	 *
	 * @return \JsonHttpControllerResponse
	 */
	public function actionGetOrderStatisticsData()
	{
		switch($this->_getQueryParameter('interval'))
		{
			case 'week':
				$timespan = $this->statisticsService->getOrdersStatisticsDataLastWeek();
				break;
			case 'two_weeks':
				$timespan = $this->statisticsService->getOrdersStatisticsDataLastTwoWeek();
				break;
			case 'month':
				$timespan = $this->statisticsService->getOrdersStatisticsDataLastMonth();
				break;
			case 'three_months':
				$timespan = $this->statisticsService->getOrderStatisticsDataLastThreeMonth();
				break;
			case 'six_months':
				$timespan = $this->statisticsService->getOrderStatisticsDataLastSixMonth();
				break;
			case 'year':
				$timespan = $this->statisticsService->getOrderStatisticsDataLastYear();
				break;
			default:
				$timespan = $this->statisticsService->getOrderStatisticsDataLastSixMonth();
				break;
		}
		
		return MainFactory::create('JsonHttpControllerResponse', $timespan);
	}
	
	
	/**
	 * Returns visitor data for the dashboard statistic.
	 *
	 * @return JsonHttpControllerResponse
	 */
	public function actionGetVisitorsStatisticsData()
	{
		switch($this->_getQueryParameter('interval'))
		{
			case 'week':
				$timespan = $this->statisticsService->getVisitorsStatisticsDataLastWeek();
				break;
			case 'two_weeks':
				$timespan = $this->statisticsService->getVisitorsStatisticsDataLastTwoWeeks();
				break;
			case 'month':
				$timespan = $this->statisticsService->getVisitorsStatisticsDataLastMonth();
				break;
			case 'three_months':
				$timespan = $this->statisticsService->getVisitorsStatisticsDataLastThreeMonth();
				break;
			case 'six_months':
				$timespan = $this->statisticsService->getVisitorsStatisticsDataLastSixMonth();
				break;
			case 'year':
				$timespan = $this->statisticsService->getVisitorsStatisticsDataLastYear();
				break;
			default:
				$timespan = $this->statisticsService->getVisitorsStatisticsDataLastThreeMonth();
				break;
		}
		
		return MainFactory::create('JsonHttpControllerResponse', $timespan);
	}
	
	
	/**
	 * Returns new customer data for the dashboard statistic.
	 *
	 * @return JsonHttpControllerResponse
	 */
	public function actionGetNewCustomerStatisticsData()
	{
		switch($this->_getQueryParameter('interval'))
		{
			case 'week':
				$timespan = $this->statisticsService->getNewCustomersStatisticsDataLastWeek();
				break;
			case 'two_weeks':
				$timespan = $this->statisticsService->getNewCustomersStatisticsDataLastTwoWeeks();
				break;
			case 'month':
				$timespan = $this->statisticsService->getNewCustomersStatisticsDataLastMonth();
				break;
			case 'three_months':
				$timespan = $this->statisticsService->getNewCustomersStatisticsDataLastThreeMonth();
				break;
			case 'six_months':
				$timespan = $this->statisticsService->getNewCustomersStatisticsDataLastSixMonth();
				break;
			case 'year':
				$timespan = $this->statisticsService->getNewCustomersStatisticsDataLastYear();
				break;
			default:
				$timespan = $this->statisticsService->getNewCustomersStatisticsDataLastThreeMonth();
				break;
		}
		
		return MainFactory::create('JsonHttpControllerResponse', $timespan);
	}
	
	
	/**
	 * Returns the sales rate in the given timespan
	 *
	 * @return JsonHttpControllerResponse
	 */
	public function actionGetSales()
	{
		switch($this->_getQueryParameter('interval'))
		{
			case 'week':
				$timespan = $this->statisticsService->getSalesLastWeek();
				break;
			
			case 'two_weeks':
				$timespan = $this->statisticsService->getSalesLastTwoWeeks();
				break;
			
			case 'month':
				$timespan = $this->statisticsService->getSalesLastMonth();
				break;
			
			case 'three_months':
				$timespan = $this->statisticsService->getSalesLastThreeMonths();
				break;
			
			case 'six_months':
				$timespan = $this->statisticsService->getSalesLastSixMonths();
				break;
			
			case 'year':
				$timespan = $this->statisticsService->getSalesLastYear();
				break;
			
			case 'today':
			default:
				$timespan = $this->statisticsService->getSalesToday();
				break;
		}
		
		return MainFactory::create('JsonHttpControllerResponse', array(
			'timespan' => $timespan,
			'today'    => $this->statisticsService->getSalesToday()
		));
	}
	
	
	protected function _getSales($interval)
	{
		switch($interval)
		{
			case 'week':
				$timespan = $this->statisticsService->getSalesLastWeek();
				break;
			
			case 'two_weeks':
				$timespan = $this->statisticsService->getSalesLastTwoWeeks();
				break;
			
			case 'month':
				$timespan = $this->statisticsService->getSalesLastMonth();
				break;
			
			case 'three_months':
				$timespan = $this->statisticsService->getSalesLastThreeMonths();
				break;
			
			case 'six_months':
				$timespan = $this->statisticsService->getSalesLastSixMonths();
				break;
			
			case 'year':
				$timespan = $this->statisticsService->getSalesLastYear();
				break;
			
			case 'today':
			default:
				$timespan = $this->statisticsService->getSalesToday();
				break;
		}
		
		return array(
			'timespan' => $timespan,
			'today'    => $this->statisticsService->getSalesToday()
		);
	}
	
	
	/**
	 * Returns the average order value in the given timespan
	 *
	 * @return JsonHttpControllerResponse
	 */
	public function actionGetAverageOrderValue()
	{
		switch($this->_getQueryParameter('interval'))
		{
			case 'week':
				$timespan = $this->statisticsService->getAverageOrderValueLastWeek();
				break;
			
			case 'two_weeks':
				$timespan = $this->statisticsService->getAverageOrderValueLastTwoWeeks();
				break;
			
			case 'month':
				$timespan = $this->statisticsService->getAverageOrderValueLastMonth();
				break;
			
			case 'three_months':
				$timespan = $this->statisticsService->getAverageOrderValueLastThreeMonths();
				break;
			
			case 'six_months':
				$timespan = $this->statisticsService->getAverageOrderValueLastSixMonths();
				break;
			
			case 'year':
				$timespan = $this->statisticsService->getAverageOrderValueLastYear();
				break;
			
			case 'today':
			default:
				$timespan = $this->statisticsService->getAverageOrderValueToday();
				break;
		}
		
		return MainFactory::create('JsonHttpControllerResponse', array(
			'timespan' => $timespan,
			'today'    => $this->statisticsService->getAverageOrderValueToday()
		));
	}
}
