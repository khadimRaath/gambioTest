<?php
/* --------------------------------------------------------------
   AccountHistoryContentView.inc.php 2015-07-22 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------


   based on: 
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(account_history_info.php,v 1.97 2003/05/19); www.oscommerce.com 
   (c) 2003	 nextcommerce (account_history_info.php,v 1.17 2003/08/17); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: account_history.php 1309 2005-10-17 08:01:11Z mz $)

   Released under the GNU General Public License 
   ---------------------------------------------------------------------------------------*/

// include needed functions
require_once (DIR_FS_INC . 'xtc_count_customer_orders.inc.php');
require_once (DIR_FS_INC . 'xtc_date_long.inc.php');
require_once (DIR_FS_INC . 'xtc_image_button.inc.php');
require_once (DIR_FS_INC . 'xtc_get_all_get_params.inc.php');

class AccountHistoryContentView extends ContentView
{
	protected $customerId;
	protected $languageId;
	protected $page;
	
	public function __construct()
	{
		parent::__construct();
		$this->set_content_template('module/account_history.html');
		$this->set_flat_assigns(true);
		$this->set_caching_enabled(false);
	}

	public function prepare_data()
	{
		$orderContentArray = array();
		$orders_total = xtc_count_customer_orders();
		
		if($orders_total > 0)
		{
			$historySplitPageResult = $this->_getSplitPageResult($this->_buildQuery(), $this->page, MAX_DISPLAY_ORDER_HISTORY);
			$result = xtc_db_query($historySplitPageResult->sql_query);

			while($orderDataArray = xtc_db_fetch_array($result))
			{
				$products_result = xtc_db_query($this->_buildCountQuery($orderDataArray['orders_id']));
				$countArray = xtc_db_fetch_array($products_result);

				$orderContentArray[] = $this->_buildOrderDataArray($orderDataArray, $countArray['count']);
			}

			$this->set_content_data('SPLIT_BAR', '
					<div class="smallText" style="clear:both;"><div style="float:left;">'.$historySplitPageResult->display_count(TEXT_DISPLAY_NUMBER_OF_ORDERS).'</div>
					<div align="right">'.TEXT_RESULT_PAGE.' '.$historySplitPageResult->display_links(MAX_DISPLAY_PAGE_LINKS, xtc_get_all_get_params(array ('page', 'info', 'x', 'y'))).'</div>
					</div>');
		}

		$this->set_content_data('order_content', $orderContentArray);
		$this->set_content_data('BUTTON_BACK', '<a href="'.xtc_href_link(FILENAME_ACCOUNT, '', 'SSL').'">'.xtc_image_button('button_back.gif', IMAGE_BUTTON_BACK).'</a>');
		$this->set_content_data('BUTTON_BACK_LINK', xtc_href_link(FILENAME_ACCOUNT, '', 'SSL'));
	}


	/**
	 * @param array $orderData
	 * @param int   $p_count
	 *
	 * @return array
	 */
	protected function _buildOrderDataArray(array $orderData, $p_count)
	{
		$orderDataArray = array ('ORDER_ID' => $orderData['orders_id'],
								 'ORDER_STATUS' => $orderData['orders_status_name'],
								 'ORDER_DATE' => xtc_date_long($orderData['date_purchased']),
								 'ORDER_PRODUCTS' => (int)$p_count,
								 'ORDER_TOTAL' => strip_tags($orderData['order_total']),
								 'ORDER_BUTTON' => '<a href="'.xtc_href_link(FILENAME_ACCOUNT_HISTORY_INFO, 'page='.(empty($this->page) ? "1" : (int)$this->page) .'&order_id='.$orderData['orders_id'], 'SSL').'">'.xtc_image_button('small_view.gif', SMALL_IMAGE_BUTTON_VIEW).'</a>',
								 'BUTTON_URL' => xtc_href_link(FILENAME_ACCOUNT_HISTORY_INFO, 'page='.(empty($this->page) ? "1" : (int)$this->page) .'&order_id='.$orderData['orders_id'], 'SSL'));

		return $orderDataArray;
	}


	/**
	 * @param int $p_order_id
	 *
	 * @return string
	 */
	protected function _buildCountQuery($p_order_id)
	{
		$query = "SELECT COUNT(*) AS count FROM " . TABLE_ORDERS_PRODUCTS . " WHERE orders_id = '" . (int)$p_order_id . "'";
		
		return $query;
	}


	/**
	 * @param string $p_query
	 * @param int $p_page
	 * @param int $p_limit
	 *
	 * @return splitPageResults
	 */
	protected function _getSplitPageResult($p_query, $p_page, $p_limit)
	{
		$splitPageResults = new splitPageResults($p_query, $p_page, $p_limit);
		
		return $splitPageResults;
	}


	/**
	 * @return string
	 */
	protected function _buildQuery()
	{
		$query = "SELECT 
						o.orders_id, 
						o.date_purchased, 
						o.delivery_name, 
						o.billing_name, 
						ot.text as order_total, 
						s.orders_status_name 
					FROM 
						" . TABLE_ORDERS . " o, 
						" . TABLE_ORDERS_TOTAL . " ot, 
						" . TABLE_ORDERS_STATUS . " s,
						" . TABLE_CUSTOMERS_INFO . " ci
					WHERE 
						o.customers_id = '" . (int)$this->customerId . "' AND 
						o.orders_id = ot.orders_id AND 
						ot.class = 'ot_total' AND 
						o.orders_status = s.orders_status_id AND 
						s.language_id = '" . (int)$this->languageId . "' AND
						o.customers_id = ci.customers_info_id AND
						o.date_purchased >= ci.customers_info_date_account_created
					ORDER BY orders_id DESC";
		
		return $query;
	}


	/**
	 * @param int $p_customerId
	 */
	public function setCustomerId($p_customerId)
	{
		$this->customerId = (int)$p_customerId;
	}


	/**
	 * @param int $p_languageId
	 */
	public function setlanguageId($p_languageId)
	{
		$this->languageId = (int)$p_languageId;
	}


	/**
	 * @param string $p_page
	 */
	public function setPage($p_page)
	{
		$this->page = (string)$p_page;
	}
}
