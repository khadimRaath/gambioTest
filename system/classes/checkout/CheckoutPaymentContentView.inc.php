<?php
/* --------------------------------------------------------------
   CheckoutPaymentContentView.inc.php 2016-07-26
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------


   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(checkout_payment.php,v 1.110 2003/03/14); www.oscommerce.com
   (c) 2003	 nextcommerce (checkout_payment.php,v 1.20 2003/08/17); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: checkout_payment.php 1325 2005-10-30 10:23:32Z mz $)

   Released under the GNU General Public License
   -----------------------------------------------------------------------------------------
   Third Party contributions:
   agree_conditions_1.01        	Autor:	Thomas Plänkers (webmaster@oscommerce.at)

   Customers Status v3.x  (c) 2002-2003 Copyright Elari elari@free.fr | www.unlockgsm.com/dload-osc/ | CVS : http://cvs.sourceforge.net/cgi-bin/viewcvs.cgi/elari/?sortby=date#dirlist

   Credit Class/Gift Vouchers/Discount Coupons (Version 5.10)
   http://www.oscommerce.com/community/contributions,282
   Copyright (c) Strider | Strider@oscworks.com
   Copyright (c  Nick Stanko of UkiDev.com, nick@ukidev.com
   Copyright (c) Andre ambidex@gmx.net
   Copyright (c) 2001,2002 Ian C Wilson http://www.phesis.org

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

// include needed functions
require_once(DIR_FS_INC . 'xtc_address_label.inc.php');
require_once(DIR_FS_INC . 'xtc_get_address_format_id.inc.php');

class CheckoutPaymentContentView extends ContentView
{
	protected $address_book_id;
	protected $comments;
	protected $coo_order;
	protected $coo_order_total;
	protected $coo_payment;
	protected $customer_id;
	protected $customers_status_id;
	protected $error_message;
	protected $language;
	protected $languages_id;
	protected $selected_payment_method;
	protected $style_edit_active;
	protected $cart_product_array;

	public function __construct()
	{
		parent::__construct();
		
		$this->set_content_template('module/checkout_payment.html');
		$this->set_flat_assigns(true);
	}


	public function prepare_data()
	{
		$t_uninitialized_array = $this->get_uninitialized_variables(array(	'address_book_id',
																			  'customer_id',
																			  'customers_status_id',
																			  'language',
																			  'languages_id',
																			  'style_edit_active',
																			  'error_message',
																			  'comments',
																			  'coo_payment',
																			  'coo_order',
																			  'coo_order_total'));
		if(empty($t_uninitialized_array))
		{
			$this->_assignErrorMessage();
			$this->_assignUrls();
			$this->_assignAddress();
			$this->_assignVoucherData();
			$this->_assignGiftData();
			$this->_assignComment();
			$this->_assignConditions();
			$this->_assignWithdrawal();
			$this->_assignLightboxData();
			$this->_assignStyleEditFlag();
			$this->_assignPaymentBlock();
			$this->_assignAmazonData();

			$this->_assignDeprecated();
		}
		else
		{
			trigger_error("Variable(s) " . implode(', ', $t_uninitialized_array) . " do(es) not exist in class " . get_class($this) . " or is/are null", E_USER_ERROR);
		}
	}


	protected function _assignUrls()
	{
		$buttonBackUrl = xtc_href_link(FILENAME_CHECKOUT_SHIPPING, '', 'SSL');
		if ($this->coo_order->content_type == 'virtual' || ($this->coo_order->content_type == 'virtual_weight') || ($_SESSION['cart']->count_contents_virtual() == 0))
		{
			$buttonBackUrl = xtc_href_link(FILENAME_SHOPPING_CART, '', 'SSL');
		}

		$this->set_content_data('BUTTON_BACK_URL', $buttonBackUrl);
		$this->set_content_data('FORM_ACTION_URL', xtc_href_link(FILENAME_CHECKOUT_CONFIRMATION, '', 'SSL'));
	}


	protected function _assignVoucherData()
	{
		if($this->coo_order->info['total'] <= 0)
		{
			$this->set_content_data('GV_COVER', 'true');
		}
	}


	protected function _assignGiftData()
	{
		if(ACTIVATE_GIFT_SYSTEM == 'true')
		{
			$orderTotalModules = $this->coo_order_total;
			$this->set_content_data('module_gift', $orderTotalModules->credit_selection());
		}
	}


	protected function _assignComment()
	{
		$this->set_content_data('COMMENTS_NAME', 'comments');
		$this->set_content_data('COMMENTS_WRAP', 'soft');
		$this->set_content_data('COMMENTS_VALUE', $this->comments);
		$this->set_content_data('COMMENTS_HIDDEN_NAME', 'comments_added');
		$this->set_content_data('COMMENTS_HIDDEN_VALUE', 'YES');
	}


	protected function _assignConditions()
	{
		//check if display conditions on checkout page is true
		if(gm_get_conf('GM_SHOW_CONDITIONS') == 1)
		{
			$result = xtc_db_query($this->_buildConditionsQuery());
			$contentDataArray = xtc_db_fetch_array($result);

			if($contentDataArray['content_file'] != '' && file_exists(DIR_FS_CATALOG . 'media/content/' . basename($contentDataArray['content_file'])))
			{
				$this->set_content_data('AGB_IFRAME', 1);
				$this->set_content_data('AGB_IFRAME_URL', GM_HTTP_SERVER . DIR_WS_CATALOG . 'media/content/' . basename($contentDataArray['content_file']));
			}
			else
			{
				$conditionsArray = array('CLASS' => 'agb_container',
											'NAME' => 'conditions_text', 
											'TEXT' => $contentDataArray['content_text']);
				$this->set_content_data('conditions_data', $conditionsArray);
			}

			$main = new main();
			$this->set_content_data('AGB_LINK', $main->getContentLink(3, MORE_INFO));
		}

		$this->set_content_data('SHOW_CONDITIONS_CHECKBOX', gm_get_conf('GM_CHECK_CONDITIONS'));
	}


	protected function _assignDeprecatedConditions()
	{
		//check if display conditions on checkout page is true
		if(gm_get_conf('GM_SHOW_CONDITIONS') == 1)
		{
			$result = xtc_db_query($this->_buildConditionsQuery());
			$contentDataArray = xtc_db_fetch_array($result);

			if($contentDataArray['content_file'] != '' && file_exists(DIR_FS_CATALOG . 'media/content/' . basename($contentDataArray['content_file'])))
			{
				$conditions = '<iframe SRC="' . GM_HTTP_SERVER . DIR_WS_CATALOG . 'media/content/' . basename($contentDataArray['content_file']) . '" width="100%" height="300"></iframe>';
			}
			else
			{
				$conditions = '<textarea class="agb_textarea" name="conditions_text" readonly="readonly">' . trim(strip_tags($contentDataArray['content_text'])) . '</textarea>';
			}

			$this->set_content_data('AGB', $conditions, 2);
		}

		if(gm_get_conf('GM_CHECK_CONDITIONS') == 1)
		{
			$this->set_content_data('CHECKBOX_AGB', '<input type="checkbox" value="conditions" name="conditions" id="conditions" />', 2);
		}
	}


	/**
	 * @return string
	 */
	protected function _buildConditionsQuery()
	{
		if(GROUP_CHECK == 'true')
		{
			$groupCheck = " AND group_ids LIKE '%c_" . $this->customers_status_id . "_group%' ";
		}

		$query = "SELECT
						content_title,
						content_heading,
						content_text,
						content_file
					FROM " . TABLE_CONTENT_MANAGER . "
					WHERE 
						content_group = '3' 
						" . $groupCheck . " AND
						languages_id = '" . (int)$this->languages_id . "'";
		
		return $query;
	}


	/**
	 * @return string
	 */
	protected function _buildWithdrawalQuery()
	{
		if(GROUP_CHECK == 'true')
		{
			$groupCheck = " AND group_ids LIKE '%c_" . $this->customers_status_id . "_group%' ";
		}

		if((int)gm_get_conf('GM_WITHDRAWAL_CONTENT_ID') === 3889895)
		{
			$checkQuery = 'SELECT
									content_title,
									content_heading,
									content_text,
									content_file
								FROM 
									' . TABLE_CONTENT_MANAGER . '
								WHERE
									content_group = ' . (int)gm_get_conf('GM_WITHDRAWAL_CONTENT_ID') . '
								AND
									languages_id = ' . $this->languages_id . ' ' . $groupCheck;
			$checkResult = xtc_db_query($checkQuery);
			$row = xtc_db_fetch_array($checkResult);

			if($row['content_file'] == false)
			{
				$result = xtc_db_query('SELECT file_flag FROM cm_file_flags WHERE file_flag_name = "withdrawal"');
				
				if(xtc_db_num_rows($result))
				{
					$row = xtc_db_fetch_array($result);
					$withdrawalFileFlag = $row['file_flag'];
					$query = 'SELECT
									content_title,
									content_heading,
									content_text,
									content_file
								FROM 
									' . TABLE_CONTENT_MANAGER . '
								WHERE
									file_flag = ' . $withdrawalFileFlag . '
								AND
									content_status = 1
								AND
									languages_id = ' . (int)$this->languages_id . ' ' . $groupCheck . '
								ORDER BY
									sort_order';
				}
				else
				{
					$query = $checkQuery;
				}
				
			}
			else
			{
				$query = $checkQuery;
			}
		}
		else
		{
			$query = 'SELECT
							content_title,
							content_heading,
							content_text,
							content_file
						FROM 
							' . TABLE_CONTENT_MANAGER . '
						WHERE
							content_group = ' . (int)gm_get_conf('GM_WITHDRAWAL_CONTENT_ID') . '
						AND
							languages_id = ' . (int)$this->languages_id . ' ' . $groupCheck;
		}
		
		return $query;
	}


	protected function _assignWithdrawal()
	{
		//check if display withdrawal on checkout page is true
		if(gm_get_conf('GM_SHOW_WITHDRAWAL') == 1)
		{
			$result = xtc_db_query($this->_buildWithdrawalQuery());

			$withdrawalsArray = array();

			while($row = xtc_db_fetch_array($result))
			{
				if($row['content_file'] != '' && file_exists(DIR_FS_CATALOG . 'media/content/' . basename($row['content_file'])))
				{
					$withdrawalArray = array('data' => array('IFRAME_URL' => GM_HTTP_SERVER . DIR_WS_CATALOG . 'media/content/' . basename($row['content_file']),
														  'HEADING' => $row['content_heading']),
										  'data_type' => 'iframe'
					);
				}
				else
				{
					$withdrawalArray = array('data' => array('CLASS' => 'withdrawal_textarea',
														  'NAME' => 'withdrawal_text',
														  'HEADING' => $row['content_heading'],
														  'TEXT' => $row['content_text']),
										  'data_type' => 'content'
					);
				}
				$withdrawalsArray[] = $withdrawalArray;
			}

			$this->set_content_data('SHOW_WITHDRAWAL', gm_get_conf('GM_SHOW_WITHDRAWAL'));
			$this->set_content_data('withdrawal_array', $withdrawalsArray);
		}

		$this->set_content_data('SHOW_CHECKBOX_WITHDRAWAL', gm_get_conf('GM_CHECK_WITHDRAWAL'));

		$this->_assignAbandonmentFlags();
	}	
	
	
	protected function _assignDeprecated()
	{
		$buttonBackUrl = xtc_href_link(FILENAME_CHECKOUT_SHIPPING, '', 'SSL');
		if ($this->coo_order->content_type == 'virtual' || ($this->coo_order->content_type == 'virtual_weight') || ($_SESSION['cart']->count_contents_virtual() == 0))
		{
			$buttonBackUrl = xtc_href_link(FILENAME_SHOPPING_CART, '', 'SSL');
		}
		
		$this->set_content_data('FORM_ACTION', xtc_draw_form('checkout_payment', xtc_href_link(FILENAME_CHECKOUT_CONFIRMATION, '', 'SSL'), 'post'), 2);
		$this->set_content_data('BUTTON_ADDRESS', '<a href="' . xtc_href_link(FILENAME_CHECKOUT_PAYMENT_ADDRESS, '', 'SSL') . '">' . xtc_image_button('button_change_address.gif', IMAGE_BUTTON_CHANGE_ADDRESS) . '</a>', 2);
		$this->set_content_data('BUTTON_BACK', '<a href="' . $buttonBackUrl . '"><img src="templates/' . CURRENT_TEMPLATE . '/buttons/' . $this->language . '/backgr.gif" /></a>', 2);
		$this->set_content_data('BUTTON_CONTINUE', xtc_image_submit('contgr.gif', IMAGE_BUTTON_CONTINUE), 2);
		$this->set_content_data('FORM_END', '</form>', 2);

		$this->set_content_data('COMMENTS', xtc_draw_textarea_field('comments', 'soft', '', '', $this->comments, 'class="comments_textarea"') . xtc_draw_hidden_field('comments_added', 'YES'), 2);

		$this->_assignDeprecatedConditions();

		if(gm_get_conf('GM_CHECK_WITHDRAWAL') == 1)
		{
			$this->set_content_data('CHECKBOX_WITHDRAWAL', '<input type="checkbox" value="withdrawal" name="withdrawal" id="withdrawal" />', 2);
		}
	}
	

	protected function _assignAbandonmentFlags()
	{
		$showAbandonmentDownload = false;
		$showAbandonmentService  = false;

		foreach($this->cart_product_array as $productArray)
		{
			if($productArray['product_type'] == '2')
			{
				$showAbandonmentDownload = true;
			}

			if($productArray['product_type'] == '3')
			{
				$showAbandonmentService = true;
			}
		}

		if($showAbandonmentDownload)
		{
			$this->set_content_data('show_abandonment_download', 'true');
		}

		if($showAbandonmentService)
		{
			$this->set_content_data('show_abandonment_service', 'true');
		}
	}


	protected function _assignLightboxData()
	{
		$this->set_content_data('LIGHTBOX', gm_get_conf('GM_LIGHTBOX_CHECKOUT'));
		$this->set_content_data('LIGHTBOX_CLOSE', xtc_href_link(FILENAME_DEFAULT, '', 'NONSSL'));
	}


	protected function _assignStyleEditFlag()
	{
		if($this->style_edit_active)
		{
			$this->set_content_data('STYLE_EDIT', 1);
		}
		else
		{
			$this->set_content_data('STYLE_EDIT', 0);
		}
	}


	protected function _assignPaymentBlock()
	{
		/* @var CheckoutPaymentModulesContentView $paymentModulesView */
		$paymentModulesView = MainFactory::create_object('CheckoutPaymentModulesContentView');
		$paymentModulesView->set_('coo_order', $this->coo_order);
		$paymentModulesView->set_('coo_payment', $this->coo_payment);

		if($this->selected_payment_method !== null)
		{
			$paymentModulesView->set_('selected_payment_method', $this->selected_payment_method);
		}

		$html = $paymentModulesView->get_html();
		$this->set_content_data('PAYMENT_BLOCK', $html);
	}


	protected function _assignAmazonData()
	{
		if(empty($_SESSION['amazonadvpay_order_ref_id']) !== true)
		{
			$this->set_content_data('amazonadvpay_active', 1);
			$this->set_content_data('amazon_checkout_payment', '<div id="walletWidgetDiv"></div>');
		}
	}


	protected function _assignAddress()
	{
		$this->set_content_data('ADDRESS_LABEL',
								xtc_address_label($this->customer_id, $this->address_book_id, true, ' ', '<br />'));
	}


	protected function _assignErrorMessage()
	{
		$this->set_content_data('error', $this->error_message);
	}


	/**
	 * @param int $p_addressBookId
	 */
	public function set_address_book_id($p_addressBookId)
	{
		$this->address_book_id = (int)$p_addressBookId;
	}


	/**
	 * @return int
	 */
	public function get_address_book_id()
	{
		return $this->address_book_id;
	}


	/**
	 * @param string $p_comments
	 */
	public function set_comments($p_comments)
	{
		$this->comments = (string)$p_comments;
	}


	/**
	 * @return mixed
	 */
	public function get_comments()
	{
		return $this->comments;
	}


	/**
	 * @param order $order
	 */
	public function set_coo_order(order $order)
	{
		$this->coo_order = $order;
	}


	/**
	 * @return order
	 */
	public function get_coo_order()
	{
		return $this->coo_order;
	}


	/**
	 * @param order_total $orderTotal
	 */
	public function set_coo_order_total(order_total $orderTotal)
	{
		$this->coo_order_total = $orderTotal;
	}


	/**
	 * @return order_total
	 */
	public function get_coo_order_total()
	{
		return $this->coo_order_total;
	}


	/**
	 * @param payment $payment
	 */
	public function set_coo_payment(payment $payment)
	{
		$this->coo_payment = $payment;
	}


	/**
	 * @return payment
	 */
	public function get_coo_payment()
	{
		return $this->coo_payment;
	}


	/**
	 * @param int $p_customerId
	 */
	public function set_customer_id($p_customerId)
	{
		$this->customer_id = (int)$p_customerId;
	}


	/**
	 * @return int
	 */
	public function get_customer_id()
	{
		return $this->customer_id;
	}


	/**
	 * @param int $p_customersStatusId
	 */
	public function set_customers_status_id($p_customersStatusId)
	{
		$this->customers_status_id = (int)$p_customersStatusId;
	}


	/**
	 * @return int
	 */
	public function get_customers_status_id()
	{
		return $this->customers_status_id;
	}


	/**
	 * @param string $p_errorMessage
	 */
	public function set_error_message($p_errorMessage)
	{
		$this->error_message = (string)$p_errorMessage;
	}


	/**
	 * @return string
	 */
	public function get_error_message()
	{
		return $this->error_message;
	}


	/**
	 * @param string $p_language
	 */
	public function set_language($p_language)
	{
		$this->language = basename((string)$p_language);
	}


	/**
	 * @return string
	 */
	public function get_language()
	{
		return $this->language;
	}


	/**
	 * @param int $p_languageId
	 */
	public function set_languages_id($p_languageId)
	{
		$this->languages_id = (int)$p_languageId;
	}


	/**
	 * @return int
	 */
	public function get_languages_id()
	{
		return $this->languages_id;
	}


	/**
	 * @param string $p_paymentMethod
	 */
	public function set_selected_payment_method($p_paymentMethod)
	{
		$this->selected_payment_method = $p_paymentMethod;
	}


	/**
	 * @return string
	 */
	public function get_selected_payment_method()
	{
		return $this->selected_payment_method;
	}


	/**
	 * @param bool $p_isStyleEditActive
	 */
	public function set_style_edit_active($p_isStyleEditActive)
	{
		$this->style_edit_active = (bool)$p_isStyleEditActive;
	}


	/**
	 * @return bool
	 */
	public function get_style_edit_active()
	{
		return $this->style_edit_active;
	}


	/**
	 * @param array $cartProductsArray
	 */
	public function set_cart_product_array(array $cartProductsArray)
	{
		$this->cart_product_array = $cartProductsArray;
	}


	/**
	 * @return array
	 */
	public function get_cart_product_array()
	{
		return $this->cart_product_array;
	}
}