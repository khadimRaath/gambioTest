<?php
/* --------------------------------------------------------------
   FilterController.inc.php 2016-09-26
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class FilterController
 *
 * @extends    HttpViewController
 * @category   System
 * @package    HttpViewControllers
 */
class FilterController extends HttpViewController
{
	/**
	 * @todo use GET and POST REST-API like
	 *
	 * @return JsonHttpControllerResponse
	 */
	public function actionDefault()
	{
		$filterContentView = MainFactory::create_object('FilterBoxContentView');
		$filterContentView->set_content_template('boxes/box_filter_form_content.html');
		$filterContentView->setCategoryId($this->_getPostData('feature_categories_id'));
		$filterContentView->setLanguageId($_SESSION['languages_id']);
		$filterContentView->setSelectedValuesArray($this->_getPostData('filter_fv_id'));
		$filterContentView->setPriceStart($this->_getPostData('filter_price_min'));
		$filterContentView->setPriceEnd($this->_getPostData('filter_price_max'));
		$filterContentView->setFilterUrl($this->_getPostData('filter_url'));
		$result = $filterContentView->get_html($this->_getPostData('feature_categories_id'), $_SESSION['languages_id'],
		                                       $this->_getPostData('filter_fv_id'),
		                                       $this->_getPostData('filter_price_min'),
		                                       $this->_getPostData('filter_price_max'),
		                                       $this->_getPostData('filter_url'));
		$result = array(
			'success' => true,
			'content' => array(
				'filter' => array(
					'selector' => 'filterForm',
					'type'     => 'replace',
					'value'    => $result
				)
			)
		);
		
		return MainFactory::create('JsonHttpControllerResponse', $result);
	}
	
	
	/**
	 * @todo use GET and POST REST-API like
	 *
	 * @return JsonHttpControllerResponse
	 */
	public function actionGetListing()
	{
		/** @var ProductListingContentControl $listingContentControl */
		$listingContentControl = MainFactory::create_object('ProductListingContentControl');
		
		$listingContentControl->set_data('GET', $this->_getQueryParametersCollection()->getArray());
		
		$listingContentControl->set_('c_path', $GLOBALS['cPath']);
		
		if(!is_null($this->_getQueryParameter('cat')))
		{
			$listingContentControl->set_('cat', $this->_getQueryParameter('cat'));
		}
		
		if(isset($GLOBALS['cID']))
		{
			$listingContentControl->set_('categories_id', $GLOBALS['cID']);
		}
		
		$listingContentControl->set_('coo_filter_manager', $_SESSION['coo_filter_manager']);
		$listingContentControl->set_('coo_product', $GLOBALS['product']);
		$listingContentControl->set_('currency_code', $_SESSION['currency']);
		$listingContentControl->set_('current_category_id', $GLOBALS['current_category_id']);
		$listingContentControl->set_('current_page', basename($GLOBALS['PHP_SELF']));
		
		if(isset($_SESSION['customer_country_id']))
		{
			$listingContentControl->set_('customer_country_id', $_SESSION['customer_country_id']);
		}
		else
		{
			$listingContentControl->set_('customer_country_id', STORE_COUNTRY);
		}
		
		if(isset($_SESSION['customer_zone_id']))
		{
			$listingContentControl->set_('customer_zone_id', $_SESSION['customer_zone_id']);
		}
		else
		{
			$listingContentControl->set_('customer_zone_id', STORE_ZONE);
		}
		
		$listingContentControl->set_('customers_fsk18_display',
		                             $_SESSION['customers_status']['customers_fsk18_display']);
		$listingContentControl->set_('customers_status_id', $_SESSION['customers_status']['customers_status_id']);
		
		if(!is_null($this->_getQueryParameter('filter_fv_id')))
		{
			$listingContentControl->set_('filter_fv_id', $this->_getQueryParameter('filter_fv_id'));
		}
		
		if(!is_null($this->_getQueryParameter('filter_id')))
		{
			$listingContentControl->set_('filter_id', $this->_getQueryParameter('filter_id'));
		}
		
		if(!is_null($this->_getQueryParameter('filter_price_min')))
		{
			$listingContentControl->set_('filter_price_min', $this->_getQueryParameter('filter_price_min'));
		}
		
		if(!is_null($this->_getQueryParameter('filter_price_max')))
		{
			$listingContentControl->set_('filter_price_max', $this->_getQueryParameter('filter_price_max'));
		}
		
		if(!is_null($this->_getQueryParameter('feature_categories_id')))
		{
			$listingContentControl->set_('feature_categories_id', $this->_getQueryParameter('feature_categories_id'));
		}
		
		if(empty($_SESSION['customers_status']['customers_status_graduated_prices']))
		{
			$listingContentControl->set_('show_graduated_prices', false);
		}
		else
		{
			$listingContentControl->set_('show_graduated_prices', true);
		}
		
		$listingContentControl->set_('languages_id', $_SESSION['languages_id']);
		
		if(isset($_SESSION['last_listing_sql']) == false)
		{
			$_SESSION['last_listing_sql'] = '';
		}
		$listingContentControl->reference_set_('last_listing_sql', $_SESSION['last_listing_sql']);
		
		if(!is_null($this->_getQueryParameter('value_conjunction')))
		{
			$listingContentControl->set_('value_conjunction', $this->_getQueryParameter('value_conjunction'));
		}
		
		$listingContentControl->set_('show_price_tax',
		                             $_SESSION['customers_status']['customers_status_show_price_tax']);
		
		$listingContentControl->init_feature_filter();
		$filterManager = $listingContentControl->get_filter_manager();
		
		if(isset($_GET['reset']))
		{
			$filterManager->reset();
		}
		
		if($listingContentControl->determine_category_depth() === 'top')
		{
			$result = array(
				'success'  => true,
				'redirect' => xtc_href_link(FILENAME_DEFAULT)
			);
			
			return MainFactory::create('JsonHttpControllerResponse', $result);
		}
		elseif(!$filterManager->is_active())
		{
			$result = array(
				'success'  => true,
				'redirect' => GM_HTTP_SERVER . DIR_WS_CATALOG . $_GET['filter_url']
			);
			
			return MainFactory::create('JsonHttpControllerResponse', $result);
		}
		
		$filterSelection = $listingContentControl->get_filter_selection_html_output();
		
		$listingContentControl->setProductListingTemplatePath('snippets/product_listing/product_listing_main.html');
		$listingContentControl->proceed();
		$products = $listingContentControl->get_response();
		
		$listingContentControl->setProductListingTemplatePath('snippets/navigation/pagination_info.html');
		$listingContentControl->proceed();
		$paginationInfo = $listingContentControl->get_response();
		
		$listingContentControl->setProductListingTemplatePath('snippets/product_listing/product_listing_hidden_fields.html');
		$listingContentControl->proceed();
		$hiddenFields = $listingContentControl->get_response();
		
		$listingContentControl->setProductListingTemplatePath('snippets/navigation/pagination.html');
		$listingContentControl->proceed();
		$pagination = $listingContentControl->get_response();
		
		$result = array(
			'success' => true,
			'content' => array(
				'products'       => array(
					'selector' => 'productsContainer',
					'type'     => 'html',
					'value'    => $products
				),
				'filter'         => array(
					'selector' => 'filterSelectionContainer',
					'type'     => 'replace',
					'value'    => $filterSelection
				),
				'pagination'     => array(
					'selector' => 'listingPagination',
					'type'     => 'replace',
					'value'    => $pagination
				),
				'hiddens'        => array(
					'selector' => 'filterHiddenContainer',
					'type'     => 'replace',
					'value'    => $hiddenFields
				),
				'paginationInfo' => array(
					'selector' => 'paginationInfo',
					'type'     => 'replace',
					'value'    => $paginationInfo
				)
			)
		);
		
		return MainFactory::create('JsonHttpControllerResponse', $result);
	}
	
	
	/**
	 * @param mixed $result
	 *
	 * @return array
	 */
	protected function _convertResult($result)
	{
		$result = array(
			'success' => true,
			'content' => array(
				'filter' => array(
					'selector' => 'filterForm',
					'type'     => 'replace',
					'value'    => $result['html']
				)
			)
		);
		
		return $result;
	}
}