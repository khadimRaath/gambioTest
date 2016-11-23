<?php
/* --------------------------------------------------------------
   AccountHistoryInfoContentView.inc.php 2016-02-19
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------


   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(account_history_info.php,v 1.97 2003/05/19); www.oscommerce.com
   (c) 2003	 nextcommerce (account_history_info.php,v 1.17 2003/08/17); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: account_history_info.php 1309 2005-10-17 08:01:11Z mz $)

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

// include needed functions
require_once (DIR_FS_INC . 'xtc_date_short.inc.php');
require_once (DIR_FS_INC . 'xtc_get_all_get_params.inc.php');
require_once (DIR_FS_INC . 'xtc_image_button.inc.php');
require_once (DIR_FS_INC . 'xtc_display_tax_value.inc.php');
require_once (DIR_FS_INC . 'xtc_format_price_order.inc.php');

require_once(DIR_WS_CLASSES . 'order.php');

class AccountHistoryInfoContentView extends ContentView
{
	protected $orderId;
	protected $languageId;
	protected $languageDirectoryName;
	protected $customerId;
	protected $order;

	function __construct($p_orderId, $p_languageId, $p_language, $p_customerId, order $order)
	{
		parent::__construct();
		$this->set_content_template('module/account_history_info.html');
		$this->set_flat_assigns(true);
		$this->set_caching_enabled(false);
		
		$this->setOrderId($p_orderId);
		$this->setLanguageId($p_languageId);
		$this->setLanguageDirectoryName($p_language);
		$this->setCustomerId($p_customerId);
		$this->setOrder($order);
	}

	function prepare_data()
	{
		$this->_assignDeliveryData();
		$this->_assignTrackingCodes();
		$this->_assignOrderProducts();
		$this->_assignOrderTotalData();
		$this->_assignPaymentData();
		$this->_assignHistory();
		$this->_assignDownloads();
		$this->_assignOrderData();
		$this->_assignUrls();
		$this->_assignWithdrawalData();
		
		$this->_assignDeprecated();
	}


	protected function _assignWithdrawalData()
	{
		$coo_language_text_manager = MainFactory::create_object('LanguageTextManager', array('withdrawal', $this->languageId));

		if($this->order->info['abandonment_download'] == 1)
		{
			$this->set_content_data('abandonment_download', $coo_language_text_manager->get_text('text_abandonment_download'));
		}
		
		if($this->order->info['abandonment_service'] == 1)
		{
			$this->set_content_data('abandonment_service', $coo_language_text_manager->get_text('text_abandonment_service'));
		}

		$this->set_content_data('PDF_FORM_URL', xtc_href_link('request_port.php', 'module=ShopContent&amp;action=download&amp;coID=' . gm_get_conf('GM_WITHDRAWAL_CONTENT_ID') . '&amp;withdrawal_form=1'));

		$this->set_content_data('WITHDRAWAL_WEBFORM_ACTIVE', gm_get_conf('WITHDRAWAL_WEBFORM_ACTIVE'));
		$this->set_content_data('WITHDRAWAL_PDF_ACTIVE', gm_get_conf('WITHDRAWAL_PDF_ACTIVE'));
	}


	protected function _assignUrls()
	{
		$this->set_content_data('PRODUCTS_EDIT', xtc_href_link(FILENAME_SHOPPING_CART, '', 'SSL'));
		$this->set_content_data('SHIPPING_ADDRESS_EDIT', xtc_href_link(FILENAME_CHECKOUT_SHIPPING_ADDRESS, '', 'SSL'));
		$this->set_content_data('BILLING_ADDRESS_EDIT', xtc_href_link(FILENAME_CHECKOUT_PAYMENT_ADDRESS, '', 'SSL'));
		$this->set_content_data('BUTTON_PRINT_URL', xtc_href_link(FILENAME_PRINT_ORDER, 'oID='.(int)$this->orderId, 'SSL'));

		if(gm_get_conf('SHOW_ACCOUNT_WITHDRAWAL_LINK') == '1')
		{
			$t_link = generate_withdrawal_link($this->order->info['orders_hash']);
			$this->set_content_data('WITHDRAWAL_LINK', $t_link);
		}
		
		$from_history = preg_match('/page=/i', xtc_get_all_get_params()); // referrer from account_history yes/no
		$back_to = $from_history ? FILENAME_ACCOUNT_HISTORY : FILENAME_ACCOUNT; // if from account_history => return to account_history
		
		$this->set_content_data('BUTTON_BACK_LINK', xtc_href_link($back_to, xtc_get_all_get_params(array('order_id')), 'SSL'));
	}


	protected function _assignDeprecated()
	{
		$from_history = preg_match('/page=/i', xtc_get_all_get_params()); // referrer from account_history yes/no
		$back_to = $from_history ? FILENAME_ACCOUNT_HISTORY : FILENAME_ACCOUNT; // if from account_history => return to account_history
		$this->set_content_data('BUTTON_PRINT', '<a style="cursor:pointer" onclick="javascript:window.open(\''.xtc_href_link(FILENAME_PRINT_ORDER, 'oID='.(int)$this->orderId).'\', \'popup\', \'toolbar=0, width=640, height=600\')"><img src="'.'templates/'.CURRENT_TEMPLATE.'/buttons/'.$this->languageDirectoryName.'/button_print.gif"/></a>', 2);
		$this->set_content_data('BUTTON_BACK', '<a href="' . xtc_href_link($back_to, xtc_get_all_get_params(array('order_id')), 'SSL') . '">' . xtc_image_button('button_back.gif', IMAGE_BUTTON_BACK) . '</a>', 2);

	}
	
	
	protected function _assignOrderData()
	{
		$this->set_content_data('ORDER_NUMBER', $this->orderId);
		$this->set_content_data('ORDER_DATE', xtc_date_long($this->order->info['date_purchased']));
		$this->set_content_data('ORDER_STATUS', $this->order->info['orders_status']);
	}
	
	
	protected function _assignDownloads()
	{
		if(DOWNLOAD_ENABLED == 'true')
		{
			/* @var DownloadsContentView $downloadsContentView */
			$downloadsContentView = MainFactory::create_object('DownloadsContentView');
			$downloadsContentView->setCustomersId($this->customerId);
			$downloadsContentView->setOrderId($this->orderId);
			$html = $downloadsContentView->get_html();
			$this->set_content_data('downloads_content', $html);
		}
	}

	
	protected function _assignHistory()
	{
		$html = '';
		$query = 'SELECT 
						os.orders_status_name, 
						osh.date_added, 
						osh.comments, 
						osh.customer_notified 
					FROM 
						' . TABLE_ORDERS_STATUS . ' os,
						' . TABLE_ORDERS_STATUS_HISTORY . ' osh 
					WHERE 
						osh.orders_id = ' .(int) $this->orderId . ' AND 
						osh.orders_status_id = os.orders_status_id AND 
						os.language_id = ' . (int)$this->languageId . ' 
					ORDER BY osh.date_added';
		$result = xtc_db_query($query);
		
		$historyDataArray = array();
		
		while($row = xtc_db_fetch_array($result))
		{
			$comments = '';
			
			if($row['customer_notified'] != '0')
			{
				$comments = $row['comments'];
			}
			
			$date =  xtc_date_short($row['date_added']);
			
			$html .= '<span class="strong">' . $date . ':</span> ' 
					 . $row['orders_status_name'] . ' '
					 . nl2br(htmlspecialchars_wrapper($comments))
					 . ' <br />';

			$historyDataArray[] = array('date' => $date,
										'status_name' => $row['orders_status_name'],
										'comments' => $comments);
		}
		
		$this->set_content_data('history_data', $historyDataArray);

		// DEPRECATED
		$this->set_content_data('HISTORY_BLOCK', $html, 2);
	}


	protected function _assignPaymentData()
	{
		if($this->order->info['payment_method'] != '' && $this->order->info['payment_method'] != 'no_payment')
		{
			switch($this->order->info['payment_method'])
			{
				case 'billsafe_3_invoice':
				case 'billsafe_3_installment':
					/* @var GMBillSafe $billSafe */
					$billSafe      = MainFactory::create_object('GMBillSafe',
					                                            array($this->order->info['payment_method']));
					$paymentMethod = $billSafe->getPaymentInfo($this->orderId);
					break;
				default:
					/* @var LanguageTextManager $languageTextManager */
					$languageTextManager = MainFactory::create_object('LanguageTextManager', array(), true);
					$languageTextManager->init_from_lang_file('lang/' . $this->languageDirectoryName
					                                          . '/modules/payment/'
					                                          . $this->order->info['payment_method'] . '.php');

					if(defined('MODULE_PAYMENT_' . strtoupper($this->order->info['payment_method']) . '_TEXT_TITLE'))
					{
						$paymentMethod = constant(MODULE_PAYMENT_ . strtoupper($this->order->info['payment_method'])
						                          . _TEXT_TITLE);
					}
					else
					{
						$paymentMethod = $this->order->info['payment_method'];
					}
			}

			$this->set_content_data('PAYMENT_METHOD', $paymentMethod);
		}

		$this->set_content_data('BILLING_LABEL',
		                        xtc_address_format($this->order->billing['format_id'], $this->order->billing, 1, ' ',
		                                           '<br />'));
	}


	protected function _assignOrderTotalData()
	{
		$order_total = $this->order->getTotalData((int)$this->orderId);

		$this->set_content_data('order_total', $order_total['data']);
	}
	

	protected function _assignOrderProducts()
	{
		$this->set_content_data('order_data', $this->order->getOrderData($this->orderId));
	}

	protected function _assignDeliveryData()
	{
		if($this->order->delivery != false)
		{
			$this->set_content_data('DELIVERY_LABEL', xtc_address_format($this->order->delivery['format_id'], $this->order->delivery, 1, ' ', '<br />'));
			
			if($this->order->info['shipping_method'])
			{
				$this->set_content_data('SHIPPING_METHOD', $this->order->info['shipping_method']); 
			}
		}
	}
	

	protected function _assignTrackingCodes()
	{
		/** @var ParcelTrackingCode $coo_parcel_tracking_code_item */
		$coo_parcel_tracking_code_item = MainFactory::create_object('ParcelTrackingCode');
		/** @var ParcelTrackingCodeReader $parcelTrackingCodeReader */
		$parcelTrackingCodeReader = MainFactory::create_object('ParcelTrackingCodeReader');
		
		$t_parcel_tracking_codes_array = $parcelTrackingCodeReader->getTackingCodeItemsByOrderId($coo_parcel_tracking_code_item,
																								  $this->orderId);
		$this->set_content_data('PARCEL_TRACKING_CODES_ARRAY', $t_parcel_tracking_codes_array);
	}


	/**
	 * @param int $p_customerId
	 */
	public function setCustomerId($p_customerId)
	{
		$this->customerId = (int)$p_customerId;
	}


	/**
	 * @return int
	 */
	public function getCustomerId()
	{
		return $this->customerId;
	}


	/**
	 * @param string $p_language
	 */
	public function setLanguageDirectoryName($p_language)
	{
		$this->languageDirectoryName = basename((string)$p_language);
	}


	/**
	 * @return string
	 */
	public function getLanguageDirectoryName()
	{
		return $this->languageDirectoryName;
	}


	/**
	 * @param int $p_languageId
	 */
	public function setLanguageId($p_languageId)
	{
		$this->languageId = (int)$p_languageId;
	}


	/**
	 * @return int
	 */
	public function getLanguageId()
	{
		return $this->languageId;
	}


	/**
	 * @param int $p_orderId
	 */
	public function setOrderId($p_orderId)
	{
		$this->orderId = (int)$p_orderId;
	}


	/**
	 * @return int
	 */
	public function getOrderId()
	{
		return $this->orderId;
	}


	/**
	 * @param order $order
	 */
	public function setOrder(order $order)
	{
		$this->order = $order;	
	}


	/**
	 * @return order
	 */
	public function getOrder()
	{
		return $this->order;
	}
}
