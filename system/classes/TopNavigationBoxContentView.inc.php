<?php
/* --------------------------------------------------------------
  TopNavigationBoxContentView.inc.php 2016-07-19
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2016 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
 */

require_once(DIR_FS_CATALOG . 'gm/inc/gm_prepare_number.inc.php');

class TopNavigationBoxContentView extends ContentView
{
	protected $coo_xtc_price;
	protected $showArrow;
	protected $customersDataArray;
	
	public function __construct()
	{
		parent::__construct();
		$this->set_content_template('boxes/box_top_navigation.html');
		$this->set_caching_enabled(false);
	}

	public function prepare_data()
	{		
		$this->showArrow = 0;

		$this->_setHomeUrl();
		$this->_setAccountUrl();
		$this->_setWishListUrl();
		$this->_setCartUrl();
		$this->_setCurrency();
		$this->_setCustomersData();
		$this->_setLanguageIcon();
		$this->_setAdminUrl();
		$this->_setEditProductUrl();

		# topmenu content
		$this->_setContentBoxLinksData();
	}

	protected function _setHomeUrl()
	{
		$this->set_content_data('HOME_URL', xtc_href_link(FILENAME_DEFAULT, '', 'NONSSL'));
	}
	
	protected function _setAccountUrl()
	{
		if(isset($_SESSION['customer_id']) == false)
		{
			$this->set_content_data('LOGIN_URL', xtc_href_link(FILENAME_LOGIN, '', 'NONSSL'));
		}
		else
		{
			$this->set_content_data('LOGOFF_URL', xtc_href_link(FILENAME_LOGOFF, '', 'NONSSL'));
			$this->set_content_data('ACCOUNT_URL', xtc_href_link(FILENAME_ACCOUNT, '', 'NONSSL'));
		}
	}

	protected function _setWishListUrl()
	{
		$t_gm_show_wishlist = gm_get_conf('GM_SHOW_WISHLIST');
		if($t_gm_show_wishlist == 'true')
		{
			$this->set_content_data('WISHLIST_URL', xtc_href_link(FILENAME_WISHLIST, '', 'NONSSL'));
		}
	}

	protected function _setCartUrl()
	{
		$this->set_content_data('CART_URL', xtc_href_link(FILENAME_SHOPPING_CART, '', 'NONSSL'));
	}

	protected function _setCurrency()
	{
		$showTopCurrencySelection = gm_get_conf('SHOW_TOP_CURRENCY_SELECTION');
		if(count($this->coo_xtc_price->currencies) > 1 && $showTopCurrencySelection == 'true' && strpos(gm_get_env_info('SCRIPT_NAME'), 'checkout') === false)
		{
			$this->set_content_data('CURRENT_CURRENCY', $_SESSION['currency']);
		}

		$this->set_content_data('SHOW_TOP_CURRENCY_SELECTION', ($showTopCurrencySelection == 'true') ? true : false);
	}

	protected function _setCustomersData()
	{
		/*
		if(is_array($this->content_array['customers_data']) == false)
		{
			$this->content_array['customers_data'] = array();
		}
		*/

		$this->_setCustomersDataName();
		$this->_setCustomersDataDiscount();
		$this->_setCustomersDataStatusPublic();
		$this->_setCustomersDataMinMaxOrder();
		$this->_setCustomersDataGender();
		$this->_setCustomersDataGroup();
		$this->_setCustomersDataIcon();
		$this->_setCustomersDataShowArrow();
		
		$this->set_content_data('customers_data', $this->customersDataArray);
	}

	protected function _setCustomersDataName()
	{
		$customerId = array_key_exists('customer_id', $_SESSION) ? $_SESSION['customer_id'] : null;

		if($customerId)
		{
			/** @var CustomerReadService $customerService */
			$customerService = StaticGXCoreLoader::getService('CustomerRead');
			$customer = $customerService->getCustomerById(new IdType((int)$_SESSION['customer_id']));

			if((string)$customer->getFirstname() === '' && (string)$customer->getLastname() === '')
			{
				$this->customersDataArray['FIRST_NAME'] = (string)$customer->getDefaultAddress()->getCompany();
				$this->customersDataArray['LAST_NAME'] = '';
			}
			else
			{
				$this->customersDataArray['FIRST_NAME'] = (string)$customer->getFirstname();
				$this->customersDataArray['LAST_NAME'] = (string)$customer->getLastname();
			}
		}
		else
		{
			$this->customersDataArray['FIRST_NAME'] = '';
			if(isset($_SESSION['customer_first_name']))
			{
				$this->customersDataArray['FIRST_NAME'] = $_SESSION['customer_first_name'];
			}

			$this->customersDataArray['LAST_NAME'] = '';
			if(isset($_SESSION['customer_last_name']))
			{
				$this->customersDataArray['LAST_NAME'] = $_SESSION['customer_last_name'];
			}
		}
	}

	protected function _setCustomersDataDiscount()
	{
		$this->customersDataArray['PRODUCTS_DISCOUNT'] = '';
		if((double)$_SESSION['customers_status']['customers_status_discount'] > 0)
		{
			$this->showArrow = 1;
			$this->customersDataArray['PRODUCTS_DISCOUNT'] = gm_prepare_number($_SESSION['customers_status']['customers_status_discount'], ',');
		}

		$this->customersDataArray['ORDER_DISCOUNT'] = '';
		if((double)$_SESSION['customers_status']['customers_status_ot_discount'] > 0 && $_SESSION['customers_status']['customers_status_ot_discount_flag'] == '1')
		{
			$this->showArrow = 1;
			$this->customersDataArray['ORDER_DISCOUNT'] = gm_prepare_number($_SESSION['customers_status']['customers_status_ot_discount'], ',');
		}
	}

	protected function _setCustomersDataStatusPublic()
	{
		$this->customersDataArray['PUBLIC'] = $_SESSION['customers_status']['customers_status_public'];
	}

	protected function _setCustomersDataMinMaxOrder()
	{
		$this->customersDataArray['MIN_ORDER'] = '';
		if((double)$_SESSION['customers_status']['customers_status_min_order'] > 0)
		{
			$this->showArrow = 1;
			$t_min_order = $this->coo_xtc_price->xtcFormat($_SESSION['customers_status']['customers_status_min_order'], true);
			$this->customersDataArray['MIN_ORDER'] = $t_min_order;
		}

		$this->customersDataArray['MAX_ORDER'] = '';
		if((double)$_SESSION['customers_status']['customers_status_max_order'] > 0)
		{
			$this->showArrow = 1;
			$t_max_order = $this->coo_xtc_price->xtcFormat($_SESSION['customers_status']['customers_status_max_order'], true);
			$this->customersDataArray['MAX_ORDER'] = $t_max_order;
		}
	}

	protected function _setCustomersDataGender()
	{
		$this->customersDataArray['GENDER'] = $_SESSION['customer_gender'];
	}

	protected function _setCustomersDataGroup()
	{
		$this->customersDataArray['ID'] = $_SESSION['customers_status']['customers_status_id'];
		$this->customersDataArray['GROUP'] = $_SESSION['customers_status']['customers_status_name'];
	}

	protected function _setCustomersDataIcon()
	{
		$this->customersDataArray['ICON'] = '';
		if(file_exists('admin/html/assets/images/legacy/icons/' . basename($_SESSION['customers_status']['customers_status_image'])))
		{
			$this->customersDataArray['ICON'] = 'admin/html/assets/images/legacy/icons/' . basename($_SESSION['customers_status']['customers_status_image']);
		}
	}

	protected function _setCustomersDataShowArrow()
	{
		$this->customersDataArray['SHOW_ARROW'] = $this->showArrow;
	}

	protected function _setLanguageIcon()
	{
		if(gm_get_conf('SHOW_TOP_LANGUAGE_SELECTION') == 'true')
		{
			$this->set_content_data('LANGUAGE_ICON', 'lang/' . basename($_SESSION['language']) . '/flag.png');
		}
	}

	protected function _setContentBoxLinksData()
	{
		/* @var ContentBoxContentView $coo_content */
		$coo_content = MainFactory::create_object('ContentBoxContentView');
		$coo_content->setFileFlagName('topmenu_corner');
		$coo_content->setRequestUri($_SERVER['REQUEST_URI']);
		$coo_content->setCustomerStatusId($_SESSION['customers_status']['customers_status_id']);
		$coo_content->setLanguagesId($_SESSION['languages_id']);
		$coo_content->get_html(); #init internal variables. do nothing with return value
		$t_content_array = $coo_content->get_content_array();

		if(is_array($t_content_array['CONTENT_LINKS_DATA']))
		{
			$this->set_content_data('CONTENT_LINKS_DATA', $t_content_array['CONTENT_LINKS_DATA']);
		}

		if(!isset($_SESSION['customer_id']) && gm_get_conf('SHOW_TOP_COUNTRY_SELECTION') === 'true')
		{
			$coo_countries = MainFactory::create_object('Countries', array($_SESSION['languages_id'], true, true));
			$t_country_array = $coo_countries->get_countries_array();
			if(isset($_SESSION['customer_country_iso']))
			{
				$t_country_name = $t_country_array[$_SESSION['customer_country_iso']];
			}
			else
			{
				$t_country_iso_code = $coo_countries->get_iso_code_by_country_id(STORE_COUNTRY);
				$t_country_name = $t_country_array[$t_country_iso_code];
			}
			$this->set_content_data('SHOW_TOP_COUNTRY_SELECTION', 1);
			$this->set_content_data('SELECTED_COUNTRY', $t_country_name);
		}
		else
		{
			$this->set_content_data('SHOW_TOP_COUNTRY_SELECTION', 0);
		}
	}


	protected function _setAdminUrl()
	{
		include DIR_FS_CATALOG . 'release_info.php';

		$this->set_content_data('admin_url', xtc_href_link_admin(FILENAME_START, rawurlencode($gx_version), 'NONSSL'));
	}


	protected function _setEditProductUrl()
	{
		if(isset($GLOBALS['actual_products_id'])) 
		{
			$this->set_content_data('edit_product_url', 'admin/categories.php?cPath='.$GLOBALS['cPath'].'&pID='.$GLOBALS['actual_products_id'].'&action=new_product');
		}
	}

	public function setXtcPrice(xtcPrice $xtcPrice)
	{
		$this->coo_xtc_price = $xtcPrice;
	}
	
}
