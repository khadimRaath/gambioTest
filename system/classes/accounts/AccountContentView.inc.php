<?php
/* --------------------------------------------------------------
  AccountContentView.inc.php 2016-02-18
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2016 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------


  based on:
  (c) 2000-2001 The Exchange Project (earlier name of osCommerce)
  (c) 2002-2003 osCommerce (account.php,v 1.59 2003/05/19); www.oscommerce.com
  (c) 2003      nextcommerce (account.php,v 1.12 2003/08/17); www.nextcommerce.org
  (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: account.php 1124 2005-07-28 08:50:04Z mz $)

  Released under the GNU General Public License
  --------------------------------------------------------------------------------------- */

// include needed functions
require_once(DIR_FS_INC . 'xtc_count_customer_orders.inc.php');
require_once(DIR_FS_INC . 'xtc_date_short.inc.php');
require_once(DIR_FS_INC . 'xtc_date_long.inc.php');
require_once(DIR_FS_INC . 'xtc_get_path.inc.php');
require_once(DIR_FS_INC . 'xtc_get_product_path.inc.php');
require_once(DIR_FS_INC . 'xtc_get_products_name.inc.php');
require_once(DIR_FS_INC . 'xtc_get_products_image.inc.php');

class AccountContentView extends ContentView
{
	protected $customer_id;
	protected $is_guest = false;
	protected $customer_data_array;
	protected $tracking_data_array;
	protected $products_history_array;
	protected $order_history_array;
	protected $mail_content_array;
	protected $languages_id;
	protected $coo_product;
	protected $coo_message_stack;
	protected $post_action = '';
	protected $post_content = '';
	protected $coo_smarty;
	
	public function AccountContentView()
	{
		parent::__construct();
		
		$this->set_content_template('module/account.html');
		$this->set_flat_assigns(true);
		$this->set_caching_enabled(false);
	}
	
	protected function set_validation_rules()
	{
		$this->validation_rules_array['customer_id']			= array('type' 			=> 'int');
		$this->validation_rules_array['languages_id']			= array('type' 			=> 'int');
		$this->validation_rules_array['post_action']			= array('type' 			=> 'string');
		$this->validation_rules_array['post_content']			= array('type' 			=> 'string');
		$this->validation_rules_array['is_guest']				= array('type' 			=> 'bool');
		$this->validation_rules_array['customer_data_array']	= array('type' 			=> 'array');
		$this->validation_rules_array['mail_content_array']		= array('type' 			=> 'array');
		$this->validation_rules_array['tracking_data_array']	= array('type' 			=> 'array');
		$this->validation_rules_array['products_history_array']	= array('type' 			=> 'array');
		$this->validation_rules_array['order_history_array']	= array('type' 			=> 'array');
		$this->validation_rules_array['coo_message_stack']		= array('type' 			=> 'object',
																		'object_type'	=> 'messageStack');
		$this->validation_rules_array['coo_smarty']				= array('type' 			=> 'object',
																		'object_type' 	=> 'Smarty');
		$this->validation_rules_array['coo_product']			= array('type' 			=> 'object',
																		'object_type' 	=> 'product');
	}
	
	public function prepare_data()
	{
		$t_uninitialized_array = $this->get_uninitialized_variables(array('customer_id',
																		  'languages_id',
																		  'coo_product',
																		  'coo_message_stack',
																		  'tracking_data_array')
		);

		if(empty($t_uninitialized_array))
		{
			$this->coo_smarty = new Smarty();

			if($this->post_action == 'gm_delete_account')
			{
				$this->load_customer_data();
				$this->create_admin_email_data();
				$this->send_admin_email();
			}

			$this->load_products_history();
			$this->load_order_history();

			$this->add_error_messages();
			$this->add_data();
		}
		else
		{
			trigger_error("Variable(s) " . implode(', ', $t_uninitialized_array) . " do(es) not exist in class " . get_class($this) . " or is/are null", E_USER_ERROR);
		}

		$showRating = false;
		if(gm_get_conf('ENABLE_RATING') === 'true' && gm_get_conf('SHOW_RATING_IN_GRID_AND_LISTING') === 'true')
		{
			$showRating = true;
		}
		$this->content_array['showRating'] = $showRating;
	}
	
	protected function add_error_messages()
	{
		if($this->coo_message_stack->size('account') > 0)
		{
			$this->content_array['error_message'] = $this->coo_message_stack->output('account');
		}
		if($this->post_action == 'gm_delete_account')
		{
			$this->content_array['error_message'] = GM_SEND;
		}
	}
	
	protected function add_data()
	{
		if($this->is_guest)
		{
			$this->content_array['NO_GUEST'] = 0;
		}
		else
		{
			$this->content_array['NO_GUEST'] = 1;
		}

		$this->content_array['LINK_DELETE_ACCOUNT'] = xtc_href_link('gm_account_delete.php', '', 'SSL');

		$this->content_array['LINK_EDIT'] = xtc_href_link(FILENAME_ACCOUNT_EDIT, '', 'SSL');
		$this->content_array['LINK_ADDRESS'] = xtc_href_link(FILENAME_ADDRESS_BOOK, '', 'SSL');
		$this->content_array['LINK_PASSWORD'] = xtc_href_link(FILENAME_ACCOUNT_PASSWORD, '', 'SSL');
		$this->content_array['LINK_LOGOFF'] = xtc_href_link(FILENAME_LOGOFF, '', 'SSL');
		
		if(gm_get_conf('MODULE_CENTER_NEWSLETTERLINK_INSTALLED') === '1')
		{
			$this->content_array['LINK_NEWSLETTER'] = xtc_href_link(FILENAME_NEWSLETTER, '', 'SSL');
		}
		
		$this->content_array['LINK_ALL'] = xtc_href_link(FILENAME_ACCOUNT_HISTORY, '', 'SSL');
		$this->content_array['order_content'] = $this->order_history_array;
		$this->content_array['products_history'] = $this->products_history_array;
		// BOF GM_MOD:
		$this->content_array['TRUNCATE_PRODUCTS_NAME'] = gm_get_conf('TRUNCATE_PRODUCTS_NAME');
	}

	protected function load_customer_data()
	{
		$this->customer_data_array = array();
		if(isset($this->customer_id) && is_numeric($this->customer_id))
		{
			$t_select = "SELECT
							*
						FROM 
							" . TABLE_CUSTOMERS . "
						WHERE
							customers_id = '" . $this->customer_id . "'
			";
			$t_result = xtc_db_query($t_select);
			if(xtc_db_num_rows($t_result))
			{
				$this->customer_data_array = xtc_db_fetch_array($t_result);
			}
		}
	}
	
	protected function create_admin_email_data()
	{
		$t_customer = $this->customer_data_array['customers_firstname']
					  . ' '
					  . $this->customer_data_array['customers_lastname']
					  . ' '
					  . $this->customer_data_array['customers_email_address']
		;
		$this->mail_content_array['CUSTOMER'] = $t_customer;
		$this->mail_content_array['NOTIFY_COMMENTS'] = htmlentities_wrapper($this->post_content);
		// bof gm
		$coo_gm_logo_manager = MainFactory::create_object('GMLogoManager', array("gm_logo_mail"));
		if($coo_gm_logo_manager->logo_use == '1')
		{
			$this->mail_content_array['gm_logo_mail'] = $coo_gm_logo_manager->get_logo();
		}
	}
	
	protected function send_admin_email()
	{
		foreach($this->mail_content_array as $t_key => $t_value)
		{
			$this->coo_smarty->assign($t_key, $t_value);
		}

		// eof gm
		$html_mail = fetch_email_template($this->coo_smarty, 'delete_account_mail', 'html');
		$txt_mail = fetch_email_template($this->coo_smarty, 'delete_account_mail', 'txt');

		// send mail to admin
		xtc_php_mail(EMAIL_SUPPORT_ADDRESS,
					 EMAIL_SUPPORT_NAME,
					 EMAIL_SUPPORT_ADDRESS,
					 STORE_NAME,
					 EMAIL_SUPPORT_FORWARDING_STRING,
					 $this->customer_data_array['customers_email_address'],
					 $this->customer_data_array['customers_firstname'] . ' ' . $this->customer_data_array['customers_lastname'],
					 '',
					 '',
					 GM_SUBJECT,
					 $html_mail,
					 $txt_mail
		);
	}
	
	protected function load_products_history()
	{
		$this->products_history_array = array();
		$coo_seo_boost = MainFactory::create_object('GMSEOBoost');
		$i = 0;
		$max = count($this->tracking_data_array['products_history']);

		while($i < $max)
		{
			$t_select = "SELECT 
							* 
						FROM 
							" . TABLE_PRODUCTS . " p, 
							" . TABLE_PRODUCTS_DESCRIPTION . " pd 
						WHERE 
							p.products_id = pd.products_id 
							AND pd.language_id='" . $this->languages_id . "' 
							AND p.products_status = '1' 
							AND p.products_id = '" . $this->tracking_data_array['products_history'][$i] . "'
			";
			$product_history_query = xtc_db_query($t_select);
			$history_product = xtc_db_fetch_array($product_history_query);
			$cpath = xtc_get_product_path($this->tracking_data_array['products_history'][$i]);
			if($history_product['products_status'] != 0)
			{
				/* bof gm */
				$gm_seo_cat = explode('_', $cpath);
				if($coo_seo_boost->boost_categories)
				{
					$gm_seo_cat_link = xtc_href_link($coo_seo_boost->get_boosted_category_url(end($gm_seo_cat), $this->languages_id));
				}
				else
				{
					$gm_seo_cat_link = xtc_href_link(FILENAME_DEFAULT, xtc_category_link(end($gm_seo_cat)));
				}
				$history_product = array_merge($history_product, array('cat_url' => $gm_seo_cat_link));
				/* eof gm */

				$this->products_history_array[] = $this->coo_product->buildDataArray($history_product);
			}
			$i ++;
		}
	}
	
	protected function load_order_history()
	{
		$this->order_history_array = array();
		if(xtc_count_customer_orders() > 0)
		{
			$t_select = "SELECT
							o.orders_id,
							o.date_purchased,
							o.delivery_name,
							o.delivery_country,
							o.billing_name,
							o.billing_country,
							ot.text as order_total,
							s.orders_status_name
						FROM
							" . TABLE_ORDERS . " o, 
							" . TABLE_ORDERS_TOTAL . " ot, 
							" . TABLE_ORDERS_STATUS . " s,
							" . TABLE_CUSTOMERS_INFO . " ci
						WHERE
							o.customers_id = '" . $this->customer_id . "'
							AND o.orders_id = ot.orders_id
							AND ot.class = 'ot_total'
							AND o.orders_status = s.orders_status_id
							AND s.language_id = '" . $this->languages_id . "'
							AND o.customers_id = ci.customers_info_id 
							AND o.date_purchased >= ci.customers_info_date_account_created
						ORDER BY 
							orders_id DESC
						LIMIT 2
			";
			$orders_query = xtc_db_query($t_select);

			while($orders = xtc_db_fetch_array($orders_query))
			{
				$t_order_array = array(
					'ORDER_ID' => $orders['orders_id'],
					'ORDER_DATE' => xtc_date_short($orders['date_purchased']),
					'ORDER_STATUS' => $orders['orders_status_name'],
					'ORDER_TOTAL' => $orders['order_total'],
					'ORDER_LINK' => xtc_href_link(FILENAME_ACCOUNT_HISTORY_INFO, 'order_id=' . $orders['orders_id'], 'SSL'),
					'ORDER_BUTTON' => '<a href="' . xtc_href_link(FILENAME_ACCOUNT_HISTORY_INFO, 'order_id=' . $orders['orders_id'], 'SSL') . '">' . xtc_image_button('small_view.gif', SMALL_IMAGE_BUTTON_VIEW) . '</a>',
					'ORDER_BUTTON_LINK' => xtc_href_link(FILENAME_ACCOUNT_HISTORY_INFO, 'order_id=' . $orders['orders_id'], 'SSL'),
					'downloads_data' => $this->get_download_by_orders_id($orders['orders_id'])
				);
				$this->order_history_array[] = $t_order_array;
			}
		}
	}

	protected function get_download_by_orders_id($p_orders_id)
	{
		$c_orders_id = (int)$p_orders_id;

		$t_downloads_array = array();

		$t_sql = '
				SELECT
					orders_status
				FROM 
					' . TABLE_ORDERS . '
				WHERE 
					orders_id = "' . $c_orders_id . '"
		';
		$t_query = xtc_db_query($t_sql);
		if(xtc_db_num_rows($t_query))
		{
			$t_order_array = xtc_db_fetch_array($t_query);
			$t_order_status = $t_order_array['orders_status'];

			$t_download_order_status_array = explode('|', DOWNLOAD_MIN_ORDERS_STATUS);

			if(is_array($t_download_order_status_array) && in_array($t_order_status, $t_download_order_status_array))
			{
				$t_sql = "SELECT
								date_format(o.date_purchased, '%Y-%m-%d') AS date_purchased_day,
								op.products_name,
								opd.orders_products_download_id,
								opd.orders_products_filename,
								opd.download_count,
								opd.download_maxdays,
								o.abandonment_download,
								UNIX_TIMESTAMP(o.date_purchased) as date_purchased_unix
							FROM
								" . TABLE_ORDERS . " o,
								" . TABLE_ORDERS_PRODUCTS . " op,
								" . TABLE_ORDERS_PRODUCTS_DOWNLOAD . " opd
							WHERE
								o.customers_id = '" . $this->customer_id . "' AND
								o.orders_id = '" . $c_orders_id . "' AND
								o.orders_id = op.orders_id AND
								op.orders_products_id = opd.orders_products_id AND
								opd.orders_products_filename != ''
				";
				$t_query = xtc_db_query($t_sql);
				if(xtc_db_num_rows($t_query) > 0)
				{
					$i = 0;
					while($t_downloads_data_array = xtc_db_fetch_array($t_query))
					{
						list($t_year, $t_month, $t_day) = explode('-', $t_downloads_data_array['date_purchased_day']);
						$t_download_timestamp = mktime(23, 59, 59, $t_month, $t_day + $t_downloads_data_array['download_maxdays'], $t_year);
						$t_download_expiry = date('Y-m-d H:i:s', $t_download_timestamp);

						if(($t_downloads_data_array['download_count'] > 0) 
						   && empty($t_downloads_data_array['orders_products_filename']) == false 
						   && file_exists(DIR_FS_DOWNLOAD . basename($t_downloads_data_array['orders_products_filename'])) 
						   && ($t_downloads_data_array['download_maxdays'] == 0 || $t_download_timestamp > time()) 
						   && in_array($t_order_status, $t_download_order_status_array))
						{
							$t_downloads_array[$i]['LINK'] = xtc_href_link(FILENAME_DOWNLOAD, 'order=' . $c_orders_id . '&id=' . $t_downloads_data_array['orders_products_download_id']);
						}
						
						$t_downloads_array[$i]['DELAY_MESSAGE'] = $this->get_download_delay_message($t_downloads_data_array['date_purchased_unix'], $t_downloads_data_array['abandonment_download']);
						$t_downloads_array[$i]['PRODUCTS_NAME'] = $t_downloads_data_array['products_name'];
						$t_downloads_array[$i]['DATE'] = xtc_date_long($t_download_expiry);
						$t_downloads_array[$i]['DATE_SHORT'] = xtc_date_short($t_download_expiry);
						$t_downloads_array[$i]['COUNT'] = $t_downloads_data_array['download_count'];
						$i++;
					}
				}
			}
		}

		return $t_downloads_array;
	}
	
	protected function get_download_delay_message($p_date_purchased, $p_withdrawal_right_abandoned = 0)
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

		$t_time_until_download_allowed = ($p_date_purchased + $t_download_abandonment_time) - time();

		if($t_download_abandonment_time > 0 && $t_time_until_download_allowed > 0)
		{
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
}