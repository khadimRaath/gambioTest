<?php
/* --------------------------------------------------------------
   AdminBoxContentView.inc.php 2015-01-02
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------


   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommercebased on original files FROM OSCommerce CVS 2.2 2002/08/28 02:14:35 www.oscommerce.com
   (c) 2003	 nextcommerce (admin.php,v 1.12 2003/08/13); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: admin.php 1262 2005-09-30 10:00:32Z mz $)

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

// include needed functions
require_once(DIR_FS_INC . 'xtc_image_button.inc.php');

class AdminBoxContentView extends ContentView
{
	protected $cPath = '';
	protected $product;
	protected $contents = '';
	protected $contentsArray = array();
	
	protected $deprecatedBoxEntryCustomers = '';
	protected $deprecatedBoxEntryProducts = '';
	protected $deprecatedBoxEntryReviews = '';
	
	public function __construct()
	{
		parent::__construct();
		$this->set_content_template('boxes/box_admin.html');
		$this->set_caching_enabled(false);
	}
/*
$this->contents 
CONTENT_BOX_ORDERS_CONTENTS
*/

	public function prepare_data()
	{
		$this->_getOrdersStatusValidating();
		$this->_getOrdersStatusValidatingDeprecated();

		$this->_getOrdersStatus();
		$this->_getOrdersStatusDeprecated();
		$this->contents = substr_wrapper($this->contents, 0, -6);

		$this->_setCountCustomers();
		$this->_setCountProducts();
		$this->_setCountReviews();

		$this->_setAdminUrl();
		$this->_setButtonEditProductUrl();
		$this->_setAdminLinkInfo();
		$this->_setOrdersContents();
	}

	protected function _setOrdersContents()
	{
		$this->set_content_data('CONTENT_BOX_TITLE_STATISTICS', BOX_TITLE_STATISTICS);
		$this->set_content_data('CONTENT_BOX_ORDERS_CONTENTS', $this->contents);

		$this->set_content_data('CONTENT_BOX_ORDERS_CONTENTS_ARRAY', $this->contentsArray);
		
		$this->_setOrdersContentsDeprecated();
	}

	protected function _setOrdersContentsDeprecated()
	{
		$t_content = '<strong>' . BOX_TITLE_STATISTICS . '</strong><br />' . $this->contents . '<br />' .
					$this->deprecatedBoxEntryCustomers . '<br />' .
					$this->deprecatedBoxEntryProducts . '<br />' .
					$this->deprecatedBoxEntryReviews . '<br />';
		$this->set_content_data('CONTENT', $t_content, 2);
	}

	protected function _setAdminLinkInfo()
	{
		if($_SESSION['style_edit_mode'] == 'edit')
		{
			$this->set_content_data('ADMIN_LINK_INFO', ADMIN_LINK_INFO_TEXT);
		}
	}

	protected function _setButtonEditProductUrl()
	{
		if($this->product->isProduct())
		{
			$this->set_content_data('BUTTON_EDIT_PRODUCT_URL', 'admin/categories.php?cPath='.$GLOBALS['cPath'].'&pID='.$GLOBALS['actual_products_id'].'&action=new_product');
		}
	}

	protected function _setAdminUrl()
	{
		include DIR_FS_CATALOG . 'release_info.php';
		
		$this->set_content_data('BUTTON_ADMIN_URL', xtc_href_link_admin(FILENAME_START, rawurlencode($gx_version), 'NONSSL'));
	}

	protected function _setCountReviews()
	{
		$t_result = xtc_db_query("SELECT count(*) AS count FROM " . TABLE_REVIEWS);
		$t_reviews_array = xtc_db_fetch_array($t_result);
		$this->deprecatedBoxEntryReviews = BOX_ENTRY_REVIEWS . ' ' . $t_reviews_array['count'];
		$this->set_content_data('CONTENT_BOX_ENTRY_REVIEWS', $this->deprecatedBoxEntryReviews);
	}

	protected function _setCountProducts()
	{
		$t_result = xtc_db_query("SELECT count(*) AS count FROM " . TABLE_PRODUCTS . " where products_status = '1'");
		$t_products_array = xtc_db_fetch_array($t_result);
		$this->deprecatedBoxEntryProducts = BOX_ENTRY_PRODUCTS . ' ' . $t_products_array['count'];
		$this->set_content_data('CONTENT_BOX_ENTRY_PRODUCTS', $this->deprecatedBoxEntryProducts);
	}

	protected function _setCountCustomers()
	{
		$t_result = xtc_db_query("SELECT count(*) AS count FROM " . TABLE_CUSTOMERS);
		$t_customers_array = xtc_db_fetch_array($t_result);
		$this->deprecatedBoxEntryCustomers = BOX_ENTRY_CUSTOMERS . ' ' . $t_customers_array['count'];
		$this->set_content_data('CONTENT_BOX_ENTRY_CUSTOMERS', $this->deprecatedBoxEntryCustomers);
	}

	public function setProduct(product $p_coo_product)
	{
		$this->product = $p_coo_product;
	}

	public function setCPath($p_cPath)
	{
		$this->cPath = (string)$p_cPath;
	}

	protected function _getOrdersStatus()
	{
		$t_result = xtc_db_query("SELECT
										orders_status_name,
										orders_status_id
									FROM " . TABLE_ORDERS_STATUS . "
									WHERE language_id = '" . (int)$_SESSION['languages_id'] . "'");
		while($t_orders_status_array = xtc_db_fetch_array($t_result))
		{
			$t_result2 = xtc_db_query("SELECT count(*) AS count
													FROM " . TABLE_ORDERS . "
													WHERE orders_status = '" . $t_orders_status_array['orders_status_id'] . "'");
			$t_orders_pending_array = xtc_db_fetch_array($t_result2);
			$t_url = "'".xtc_href_link_admin(FILENAME_ORDERS, 'selected_box=customers&status=' . $t_orders_status_array['orders_status_id'], 'NONSSL')."'";
			if($_SESSION['style_edit_mode'] == 'edit')
			{
				$this->contentsArray[] = '<a href="#" onclick="if(confirm(\'' . ADMIN_LINK_INFO_TEXT . '\')){window.location.href='.$t_url.'; return false;} return false;">' . $t_orders_status_array['orders_status_name'] . '</a>: ' . $t_orders_pending_array['count'];
			}
			else
			{
				$this->contentsArray[] = '<a href="#" onclick="window.location.href='.$t_url.'; return false;">' . $t_orders_status_array['orders_status_name'] . '</a>: ' . $t_orders_pending_array['count'];
			}
		}
	}

	protected function _getOrdersStatusDeprecated()
	{
		$t_result = xtc_db_query("SELECT
										orders_status_name,
										orders_status_id
									FROM " . TABLE_ORDERS_STATUS . "
									WHERE language_id = '" . (int)$_SESSION['languages_id'] . "'");
		while($t_orders_status_array = xtc_db_fetch_array($t_result))
		{
			$t_result2 = xtc_db_query("SELECT count(*) AS count
													FROM " . TABLE_ORDERS . "
													WHERE orders_status = '" . $t_orders_status_array['orders_status_id'] . "'");
			$t_orders_pending_array = xtc_db_fetch_array($t_result2);
			$t_url = "'".xtc_href_link_admin(FILENAME_ORDERS, 'selected_box=customers&status=' . $t_orders_status_array['orders_status_id'], 'NONSSL')."'";
			if($_SESSION['style_edit_mode'] == 'edit')
			{
				$this->contents .= '<a href="#" onclick="if(confirm(\'' . ADMIN_LINK_INFO_TEXT . '\')){window.location.href='.$t_url.'; return false;} return false;">' . $t_orders_status_array['orders_status_name'] . '</a>: ' . $t_orders_pending_array['count'] . '<br />';
			}
			else
			{
				$this->contents .= '<a href="#" onclick="window.location.href='.$t_url.'; return false;">' . $t_orders_status_array['orders_status_name'] . '</a>: ' . $t_orders_pending_array['count'] . '<br />';
			}
		}
	}

	protected function _getOrdersStatusValidating()
	{
		$t_orders_status_validating = xtc_db_num_rows(xtc_db_query("SELECT orders_status FROM " . TABLE_ORDERS ." where orders_status ='0'"));
		$t_url = "'".xtc_href_link_admin(FILENAME_ORDERS, 'selected_box=customers&status=0', 'NONSSL')."'";
		if($_SESSION['style_edit_mode'] == 'edit')
		{
			$this->contentsArray[] = '<a href="#" onclick="if(confirm(\'' . ADMIN_LINK_INFO_TEXT . '\')){window.location.href='.$t_url.'; return false;} return false;">' . TEXT_VALIDATING . '</a>: ' . $t_orders_status_validating;
		}
		else
		{
			$this->contentsArray[] = '<a href="#" onclick="window.location.href='.$t_url.'; return false;">' . TEXT_VALIDATING . '</a>: ' . $t_orders_status_validating;
		}
	}
	protected function _getOrdersStatusValidatingDeprecated()
	{
		$t_orders_status_validating = xtc_db_num_rows(xtc_db_query("SELECT orders_status FROM " . TABLE_ORDERS ." where orders_status ='0'"));
		$t_url = "'".xtc_href_link_admin(FILENAME_ORDERS, 'selected_box=customers&status=0', 'NONSSL')."'";
		if($_SESSION['style_edit_mode'] == 'edit')
		{
			$this->contents .= '<a href="#" onclick="if(confirm(\'' . ADMIN_LINK_INFO_TEXT . '\')){window.location.href='.$t_url.'; return false;} return false;">' . TEXT_VALIDATING . '</a>: ' . $t_orders_status_validating . '<br />';
		}
		else
		{
			$this->contents .= '<a href="#" onclick="window.location.href='.$t_url.'; return false;">' . TEXT_VALIDATING . '</a>: ' . $t_orders_status_validating . '<br />';
		}
	}
}