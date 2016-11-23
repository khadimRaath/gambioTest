<?php
/* --------------------------------------------------------------
   DownloadsContentView.inc.php 2014-10-29 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------


   based on: 
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(downloads.php,v 1.2 2003/02/12); www.oscommerce.com 
   (c) 2003	 nextcommerce (downloads.php,v 1.6 2003/08/13); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: downloads.php 896 2005-04-27 19:22:59Z mz $)

   Released under the GNU General Public License 
   ---------------------------------------------------------------------------------------*/

// include the needed functions
if (!function_exists('xtc_date_long')) {
	/** @noinspection PhpIncludeInspection */
	require_once (DIR_FS_INC.'xtc_date_long.inc.php');
}

if (!function_exists('xtc_date_short')) {
	/** @noinspection PhpIncludeInspection */
	require_once (DIR_FS_INC.'xtc_date_short.inc.php');
}


/**
 * Class DownloadsContentView
 */
class DownloadsContentView extends ContentView
{

	protected $orderId;
	protected $customersId;

	protected $orderStatus;
	protected $lastOrder;
	protected $downloadOrderStatus_array = array();
	protected $downloadsResult;


	/**
	 *
	 */
	public function __construct()
	{
		parent::__construct();
		$this->set_content_template('module/downloads.html');
		$this->set_flat_assigns(true);
		$this->set_caching_enabled(false);
	}


	public function prepare_data()
	{
		$this->build_html = false;
		$this->_buildOrderInfos();
		$this->_assignDownloads();
	}

	protected function _buildOrderInfos()
	{

		if(!strstr(gm_get_env_info('PHP_SELF'), FILENAME_ACCOUNT_HISTORY_INFO))
		{
			// Get last order id for checkout_success
			$ordersQuery = xtc_db_query('
											SELECT
											  `orders_id`, `orders_status`
											FROM
											' . TABLE_ORDERS . '
											WHERE
											  customers_id = "' . $this->customersId . '"
											ORDER BY
											  orders_id
											DESC LIMIT 1');

			$orders      = xtc_db_fetch_array($ordersQuery);
			$lastOrder   = $orders['orders_id'];
			$orderStatus = $orders['orders_status'];
		}
		else
		{
			$lastOrder   = $this->orderId;
			$ordersQuery = xtc_db_query('SELECT orders_status FROM ' . TABLE_ORDERS . ' WHERE orders_id = "' . $lastOrder . '"');
			$orders      = xtc_db_fetch_array($ordersQuery);
			$orderStatus = $orders['orders_status'];
		}

		$downloadOrderStatus_array = explode('|', DOWNLOAD_MIN_ORDERS_STATUS);

		$this->orderStatus               = $orderStatus;
		$this->lastOrder                 = $lastOrder;
		$this->downloadOrderStatus_array = $downloadOrderStatus_array;

	}


	/**
	 * @return string
	 */
	protected function _buildQueryDownloadableProducts() {
		$query = 'SELECT
					  date_format(`o`.`date_purchased`, "%Y-%m-%d")
						AS `date_purchased_day`,
					  `opd`.`download_maxdays`, `op`.`products_name`, `opd`.`orders_products_download_id`, `opd`.`orders_products_filename`,
					  `opd`.`download_count`, `opd`.`download_maxdays`, `o`.`abandonment_download`,
					  UNIX_TIMESTAMP(`o`.`date_purchased`)
						AS `date_purchased_unix`,
					  UNIX_TIMESTAMP(now())
						AS `time`
					FROM ' .TABLE_ORDERS. ' o, ' .TABLE_ORDERS_PRODUCTS. ' op, ' .TABLE_ORDERS_PRODUCTS_DOWNLOAD.' opd
					WHERE o.customers_id = "' . $this->customersId .'"
					AND o.orders_id = "' . $this->lastOrder .'"
					AND o.orders_id = op.orders_id
					AND op.orders_products_id = opd.orders_products_id
					AND opd.orders_products_filename != ""';

		return $query;
	}

	/**
	 *
	 */
	protected function _getResultFromDB()
	{
		// Now get all downloadable products in that order

		$query          = $this->_buildQueryDownloadableProducts();
		$downloadsResult = xtc_db_query($query);

		return $downloadsResult;
	}

	protected function _assignDownloads()
	{

		$dl_array = array();

		$downloadsResult = $this->_getResultFromDB();

		if(!in_array($this->orderStatus, $this->downloadOrderStatus_array))
		{
			$this->set_content_data('dl_prevented', 'true');
		}

		if(xtc_db_num_rows($downloadsResult) > 0)
		{
			$this->build_html = true;
			$jj = 0;
			//<!-- list of products -->
			while($downloads = xtc_db_fetch_array($downloadsResult))
			{
				// MySQL 3.22 does not have INTERVAL
				list ($dtYear, $dtMonth, $dtDay) = explode('-', $downloads['date_purchased_day']);
				$downloadTimestamp    = mktime(23, 59, 59, $dtMonth, $dtDay + $downloads['download_maxdays'], $dtYear);
				$downloadExpiry       = date('Y-m-d H:i:s', $downloadTimestamp);
				$downloadDelayMessage = $this->get_download_delay_message($downloads['date_purchased_unix'],
																		  $downloads['time'],
																		  $downloads['abandonment_download']);

				//<!-- left box -->
				// The link will appear only if:
				// - Download remaining count is > 0, AND
				// - The file is present in the DOWNLOAD directory, AND EITHER
				// - No expiry date is enforced (maxdays == 0), OR
				// - The expiry date is not reached
				if(($downloads['download_count'] > 0) &&
				   (file_exists(DIR_FS_DOWNLOAD . $downloads['orders_products_filename'])) &&
				   (($downloads['download_maxdays'] == 0) || ($downloadTimestamp > $downloads['time'])) &&
				   in_array($this->orderStatus, $this->downloadOrderStatus_array)
				)
				{
					$dl_array[$jj]['download_link'] = '<a href="' . xtc_href_link(FILENAME_DOWNLOAD,
																				  'order=' . $this->lastOrder . '&id=' .
																				  $downloads['orders_products_download_id']) .
													  '">' . $downloads['products_name'] . '</a>';
					$dl_array[$jj]['pic_link']      = xtc_href_link(FILENAME_DOWNLOAD,
																	'order=' . $this->lastOrder . '&id=' .
																	$downloads['orders_products_download_id']);
					$dl_array[$jj]['delay_message'] = $downloadDelayMessage;
				}
				else
				{
					$dl_array[$jj]['download_link'] = $downloads['products_name'];
				}
				//<!-- right box -->
				$dl_array[$jj]['date']       = xtc_date_long($downloadExpiry);
				$dl_array[$jj]['date_short'] = xtc_date_short($downloadExpiry);
				$dl_array[$jj]['count']      = $downloads['download_count'];
				$jj++;
			}
		}
		$this->set_content_data('dl', $dl_array);

	}


	/**
	 * @param     $p_date_purchased
	 * @param     $p_time
	 * @param int $p_withdrawal_right_abandoned
	 *
	 * @return string
	 */
	protected function get_download_delay_message($p_date_purchased, $p_time, $p_withdrawal_right_abandoned = 0)
	{
		$t_output = '';

		if($p_withdrawal_right_abandoned == 1)
		{
			$t_download_abandonment_time = gm_get_conf('DOWNLOAD_DELAY_FOR_ABANDONMENT_OF_WITHDRAWL_RIGHT');
		}
		else
		{
			$t_download_abandonment_time = gm_get_conf('DOWNLOAD_DELAY_WITHOUT_ABANDONMENT_OF_WITHDRAWL_RIGHT');
		}

		$t_time_until_download_allowed = ($p_date_purchased + $t_download_abandonment_time) - $p_time;

		if($t_time_until_download_allowed > 0) {
			/** @var $coo_download_delay DownloadDelay */
			$coo_download_delay = MainFactory::create_object('DownloadDelay');
			$coo_download_delay->convert_seconds_to_days($t_time_until_download_allowed);

			$t_days = $coo_download_delay->get_delay_days();
			$t_hours = $coo_download_delay->get_delay_hours();
			$t_minutes = $coo_download_delay->get_delay_minutes();
			$t_seconds = $coo_download_delay->get_delay_seconds();

			$coo_text_mgr = MainFactory::create_object('LanguageTextManager', array('withdrawal', $_SESSION['languages_id']) );
			/** @var $coo_text_time_left DownloadTimerStringOutput */
			$coo_text_time_left = MainFactory::create_object('DownloadTimerStringOutput', array(
				$t_days,
				$t_hours,
				$t_minutes,
				$t_seconds,
				$coo_text_mgr
			));

			$t_output = $coo_text_time_left->get_msg();
		}

		return $t_output;
	}


	/**
	 * @return int
	 */
	public function getOrderId()
	{
		return $this->orderId;
	}


	/**
	 * @param int $orderId
	 */
	public function setOrderId($orderId)
	{
		if(check_data_type($orderId, 'int')){
			$this->orderId = (int)$orderId;
		};

	}


	/**
	 * @return int
	 */
	public function getCustomersId()
	{
		return $this->customersId;
	}


	/**
	 * @param int $customersId
	 */
	public function setCustomersId($customersId)
	{
		if(check_data_type($customersId, 'int'))
		{
			$this->customersId = $customersId;
		};
	}
}