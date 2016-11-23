<?php
/* --------------------------------------------------------------
  PrintOrderContentView.inc.php 2016-03-02
  http://www.gambio.de
  Copyright (c) 2014 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------


  based on:
  (c) 2003	 nextcommerce (print_order.php,v 1.5 2003/08/24); www.nextcommerce.org
  (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: print_order.php 1185 2005-08-26 15:16:31Z mz $)

  Released under the GNU General Public License
  --------------------------------------------------------------------------------------- */

// include needed functions
require_once (DIR_FS_INC . 'xtc_get_order_data.inc.php');
require_once (DIR_FS_INC . 'xtc_get_attributes_model.inc.php');

class PrintOrderContentView extends ContentView
{
	protected $coo_language_text_manager;
	protected $order_id = 0;
	protected $customer_id = 0;
	protected $language = 'german';
	protected $coo_order;

	public function __construct()
	{
		parent::__construct();
		$this->set_flat_assigns(true);
		$this->coo_language_text_manager = MainFactory::create_object('LanguageTextManager', array(), true);
		$this->set_content_template('module/print_order.html');
		$this->set_caching_enabled(false);
	}

	protected function set_validation_rules()
	{
		// SET VALIDATION RULES
		$this->validation_rules_array['coo_language_text_manager']	= array('type' => 'object',
																			'object_type' => 'LanguageTextManager');
		$this->validation_rules_array['order_id']					= array('type' => 'int');
		$this->validation_rules_array['customer_id']				= array('type' => 'int');
		$this->validation_rules_array['language']					= array('type' => 'string');
		$this->validation_rules_array['coo_order']					= array('type' => 'object',
																			'object_type' => 'order');
	}

	public function prepare_data()
	{
		// CHECK IF CUSTOMER IS ALLOWED TO VIEW THE ORDER
		$t_query = 'SELECT
						customers_id
					FROM
						' . TABLE_ORDERS . '
					WHERE
						orders_id = "' . $this->order_id . '"';
		$t_result = xtc_db_query($t_query);
		if(xtc_db_num_rows($t_result) > 0)
		{
			$t_row = xtc_db_fetch_array($t_result);
			if($this->customer_id == $t_row['customers_id'])
			{
				// GET ORDER DATA
				include_once (DIR_WS_CLASSES . 'order.php');
				// CREATE ORDER OBJECT
				$this->coo_order = new order($this->order_id);

				// ADD DATA
				$this->add_data();
			}
			else
			{
				// NO PERMISSION TO VIEW THE ORDER
				$this->content_array['ERROR'] = 'You are not allowed to view this order!';
			}
		}
		else
		{
			// NO ORDER FOUND
			$this->content_array['ERROR'] = 'No order found...';
		}
	}

	protected function add_data()
	{
		$this->add_address_data();
		$this->add_order_data();
		$this->add_logo();
	}

	protected function add_address_data()
	{
		// ADDRESS DATA
		$this->set_content_data('address_label_customer', xtc_address_format($this->coo_order->customer['format_id'], $this->coo_order->customer, 1, '', '<br />'));
		$this->set_content_data('address_label_shipping', xtc_address_format($this->coo_order->delivery['format_id'], $this->coo_order->delivery, 1, '', '<br />'));
		$this->set_content_data('address_label_payment', xtc_address_format($this->coo_order->billing['format_id'], $this->coo_order->billing, 1, '', '<br />'));
	}

	protected function add_order_data()
	{
		// ORDER ID
		$this->content_array['oID'] = $this->order_id;

		// PAYMENT
		if($this->coo_order->info['payment_method'] != '' && $this->coo_order->info['payment_method'] != 'no_payment')
		{
			include_once(DIR_FS_INC.'get_payment_title.inc.php');
			$t_payment_method = get_payment_title($this->coo_order->info['payment_method']);
			$this->content_array['PAYMENT_METHOD'] = $t_payment_method;
		}

		// CUSTOMER CSID
		$this->content_array['csID'] = $this->coo_order->customer['csID'];

		// COMMENT
		$this->content_array['COMMENT'] = $this->coo_order->info['comments'];

		// DATE
		$this->content_array['DATE'] = xtc_date_long($this->coo_order->info['date_purchased']);

		// ORDER TOTAL
		$t_order_total = $this->coo_order->getTotalData($this->order_id);
		$this->content_array['order_data'] = $this->coo_order->getOrderData($this->order_id);
		$this->content_array['order_total'] = $t_order_total['data'];
	}

	protected function add_logo()
	{
		// LOGO MAIL
		$gm_logo_mail = MainFactory::create_object('GMLogoManager', array("gm_logo_mail"));
		if($gm_logo_mail->logo_use == '1')
		{
			$this->content_array['gm_logo_mail'] = $gm_logo_mail->get_logo();
		}
	}
}