<?php
/* --------------------------------------------------------------
   CheckoutSuccessContentView.inc.php 2016-02-19
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------


   based on: 
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(checkout_success.php,v 1.48 2003/02/17); www.oscommerce.com 
   (c) 2003	 nextcommerce (checkout_success.php,v 1.14 2003/08/17); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: checkout_success.php 896 2005-04-27 19:22:59Z mz $)

   Released under the GNU General Public License
   -----------------------------------------------------------------------------------------
   Third Party contribution:

   Credit Class/Gift Vouchers/Discount Coupons (Version 5.10)
   http://www.oscommerce.com/community/contributions,282
   Copyright (c) Strider | Strider@oscworks.com
   Copyright (c  Nick Stanko of UkiDev.com, nick@ukidev.com
   Copyright (c) Andre ambidex@gmx.net
   Copyright (c) 2001,2002 Ian C Wilson http://www.phesis.org


   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

class CheckoutSuccessContentView extends ContentView
{
	protected $language;
	protected $order_id;
	protected $customer_id;
	protected $nc_checkout_success_info;
	
	public function __construct()
	{
		parent::__construct();
		
		$this->set_content_template('module/checkout_success.html');
		$this->set_flat_assigns(true);
		$this->set_caching_enabled(false);
	}


	public function prepare_data()
	{
		$this->_assignUrls();
		$this->_assignNcSuccessInfo();
		$this->_assignLightboxData();
		$this->_assignDownloadData();

		$this->_assignDeprecated();
	}


	protected function _assignDeprecated()
	{
		$this->set_content_data('FORM_ACTION', xtc_draw_form('order',
															 xtc_href_link(FILENAME_CHECKOUT_SUCCESS, 'action=update',
																		   'SSL')), 2);
		$this->set_content_data('BUTTON_CONTINUE', xtc_image_submit('contgr.gif', IMAGE_BUTTON_CONTINUE), 2);
		$this->set_content_data('BUTTON_PRINT',
								'<img src="' . 'templates/' . CURRENT_TEMPLATE . '/buttons/' . $this->language .
								'/button_print.gif" style="cursor:hand" onclick="window.open(\'' .
								xtc_href_link(FILENAME_PRINT_ORDER, 'oID=' . $this->order_id) .
								'\', \'popup\', \'toolbar=0, width=640, height=600\')" />', 2);
		$this->set_content_data('FORM_END', '</form>', 2);
	}


	protected function _assignUrls()
	{
		$this->set_content_data('FORM_ACTION_URL', xtc_href_link(FILENAME_CHECKOUT_SUCCESS, 'action=update', 'SSL'));
		$this->set_content_data('BUTTON_PRINT_URL', xtc_href_link(FILENAME_PRINT_ORDER, 'oID=' . $this->order_id, 'SSL'));
		$this->set_content_data('LOGOFF_URL', xtc_href_link(FILENAME_LOGOFF, '', 'NONSSL'));

		// GV Code Start
		$gv_query = xtc_db_query("SELECT amount FROM " . TABLE_COUPON_GV_CUSTOMER . " WHERE customer_id='" . (int)$this->customer_id . "'");
		if($gv_result = xtc_db_fetch_array($gv_query))
		{
			if($gv_result['amount'] > 0)
			{
				$this->set_content_data('GV_SEND_LINK', xtc_href_link(FILENAME_GV_SEND, '', 'SSL'));
			}
		}
		// GV Code End
	}


	protected function _assignNcSuccessInfo()
	{
		if($this->nc_checkout_success_info)
		{
			$this->set_content_data('NC_SUCCESS_INFO', $this->nc_checkout_success_info);
		}
	}


	protected function _assignLightboxData()
	{
		$this->set_content_data('LIGHTBOX', gm_get_conf('GM_LIGHTBOX_CHECKOUT'));
		$this->set_content_data('LIGHTBOX_CLOSE', xtc_href_link(FILENAME_DEFAULT, '', 'NONSSL'));
	}


	protected function _assignDownloadData()
	{
		if(DOWNLOAD_ENABLED == 'true')
		{
			$coo_downloads_content_view = MainFactory::create_object('DownloadsContentView');
			$coo_downloads_content_view->setCustomersId($this->customer_id);
			$coo_downloads_content_view->setOrderId($this->order_id);
			$t_downloads_html = $coo_downloads_content_view->get_html();
			$this->set_content_data('downloads_content', $t_downloads_html);
		}
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
	 * @param int $p_orderId
	 */
	public function set_order_id($p_orderId)
	{
		$this->order_id = (int)$p_orderId;
	}


	/**
	 * @return int
	 */
	public function get_order_id()
	{
		return $this->order_id;
	}

	/**
	 * @param string $p_nc_checkout_success_info
	 */
	public function set_nc_checkout_success_info($p_nc_checkout_success_info)
	{
		if (is_null($p_nc_checkout_success_info))
		{
			return;
		}
		if(check_data_type($p_nc_checkout_success_info, 'string'))
		{
			$this->nc_checkout_success_info = $p_nc_checkout_success_info;
		}
	}

	/**
	 * @return string
	 */
	public function get_nc_checkout_success_info()
	{
		return $this->nc_checkout_success_info;
	}
}