<?php
/* --------------------------------------------------------------
   ProductNavigatorContentView.inc.php 2016-08-24
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------

   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: product_navigator.php 1292 2005-10-07 16:10:55Z mz $) 

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

/**
 * Class ProductNavigatorContentView
 */
class ProductNavigatorContentView extends ContentView
{
	
	protected $product		        = null;
	protected $categoryId	        = null;
	protected $lastListingSql       = '';
	protected $fsk18DisplayAllowed  = 0;
	protected $customerStatusId     = null;
	protected $languageId           = null;
	protected $assignData           = array();
	protected $boostProducts        = array();
	protected $seoBoost             = null;
	
// ########## CONSTRUCTOR ##########
	
	public function __construct()
	{
		parent::__construct();
		$this->set_content_template('module/product_navigator.html');
		$this->set_flat_assigns(true);
	}
	
// ########## GETTER & SETTER ##########

	/**
	 * @param product 		$p_product		Product object
	 */
	public function setProduct(product $p_product)
	{
		$this->product = $p_product;
	}


	/**
	 * @return product					Product object
	 */
	public function getProduct()
	{
		return $this->product;
	}


	/**
	 * @param	integer		$p_categoryId	Category ID
	 */
	public function setCategoryId($p_categoryId)
	{
		$this->categoryId = (int)$p_categoryId;
	}


	/**
	 * @return	integer						Category ID
	 */
	public function getCategoryId()
	{
		return $this->categoryId;
	}


	/**
	 * @param	string		$p_lastListingSql		Last executed SQL string
	 */
	public function setLastListingSql($p_lastListingSql)
	{
		$this->lastListingSql = (string)$p_lastListingSql;
	}


	/**
	 * @return	string								Last executed SQL string
	 */
	public function getLastListingSql()
	{
		return $this->lastListingSql;
	}


	/**
	 * @return integer
	 */
	public function getFSK18DisplayAllowed()
	{
		return $this->fsk18DisplayAllowed;
	}


	/**
	 * @return integer
	 */
	public function setFSK18DisplayAllowed($p_fsk18DisplayAllowed)
	{
		$this->fsk18DisplayAllowed = (int)$p_fsk18DisplayAllowed;
	}

	/**
	 * @param	integer	$p_customerStatusId 	Customer Status ID
	 */
	public function setCustomerStatusId($p_customerStatusId)
	{
		$this->customerStatusId = (int)$p_customerStatusId;
	}

	/**
	 * @return	integer							Customer Status ID
	 */
	public function getCustomerStatusId()
	{
		return $this->customerStatusId;
	}


	/**
	 * @param	integer		$p_langId			Language ID
	 */
	public function setLanguageId($p_langId)
	{
		$this->languageId = (int)$p_langId;
	}


	/**
	 * @return	integer							Language ID
	 */
	public function getLanguageId()
	{
		return $this->languageId;
	}




// ########## PUBLIC METHODS ##########

	/**
	 * 
	 */
	public function prepare_data()
	{
		$this->seoBoost		= MainFactory::create_object('GMSEOBoost');
		$this->boostProducts 	= $this->seoBoost->boost_products;

		$resultArray = $this->_getProductInfo();
		$this->_prepareAssignData($resultArray['productsArray'], $resultArray['actualKey']);
		$this->_assignData();
	}


	// ########## PRIVATE / PROTECTED METHODS ##########
	/**
	 * @return array		Product info array + actual selected key
	 */
	protected function _getProductInfo()
	{

		$productsQuery	= xtc_db_query($this->_generateSql());
		$i				= 0;
		$result			= array();
		$productArray	= array();
		
		if(xtc_db_num_rows($productsQuery) === 0)
		{
			$productsQuery	= xtc_db_query($this->_generateSql(true));
		}
		
		while ($productsData = xtc_db_fetch_array($productsQuery))
		{
			$productArray[]	= array (
				'pID' => $productsData['products_id'],
				'pName' => $productsData['products_name']
			);

			if ((int)$productsData['products_id'] === (int)$this->product->data['products_id'])
			{
				$result['actualKey'] = $i;
			}

			$i++;
		}
		
		$result['productsArray'] = $productArray;
		
		return $result;
	}


	/**
	 * @param array		$productArray			Product information array
	 * @param int 		$actualKey				Currently selected product
	 */
	protected function _prepareAssignData(array $productArray, $actualKey = 0)
	{
		$this->assignData['PRODUCTS_COUNT'] = count($productArray);
		$size = sizeof($productArray) - 1;

		if ($actualKey > 0)
		{
			$prevId = $actualKey - 1;
			$this->_setUrl($prevId, $productArray, 'PREVIOUS');
		}

		if($actualKey > 1)
		{
			$this->_setUrl(0, $productArray, 'FIRST');
		}

		if ($actualKey < $size)
		{
			$nextId = $actualKey + 1;
			$this->_setUrl($nextId, $productArray, 'NEXT');
		}

		// check if next id = last
		if($actualKey < ($size - 1))
		{
			$this->_setUrl($size, $productArray, 'LAST');
		}
	}


	/**
	 * @return string		SQL for sorting
	 */
	protected function _getCategoriesSortingSql()
	{
		$sql = "SELECT
					products_sorting,
					products_sorting2
				FROM categories
				WHERE categories_id = '" . (int)$this->categoryId . "'";
		
		return $sql;
	}


	/**
	 * @return string		Returns the order statement
	 */
	protected function _getSortOrder()
	{
		$order		 = ' ORDER BY p.products_price ASC';

		$categorySortQuery = xtc_db_query($this->_getCategoriesSortingSql());

		if(xtc_db_num_rows($categorySortQuery) === 1)
		{
			$categorySortData = xtc_db_fetch_array($categorySortQuery);
			if(!empty($categorySortData['products_sorting']))
			{
				$order = ' ORDER BY ' . $categorySortData['products_sorting'] . ' ' . $categorySortData['products_sorting2'];
			}
		}
		
		return $order;
	}


	/**
	 * @param bool $ignoreLastSql
	 * 
	 * @return string		Returns the products sql string
	 */
	protected function _generateSql($ignoreLastSql = false)
	{
		
		if( !empty( $this->lastListingSql ) && !$ignoreLastSql)
		{
			# use last product listing query saved in product_listing.php
			return $this->lastListingSql;
		}

		$condition 	 = '';
		$condition 	.= ($this->fsk18DisplayAllowed === 0) ? ' AND p.products_fsk18 != 1' : '';
		$condition 	.= (GROUP_CHECK === 'true') ? ' AND p.group_permission_' . (int)$this->customerStatusId . ' = 1' : '';
		$order		 = $this->_getSortOrder();
		
		if($ignoreLastSql)
		{
			$cPath = xtc_get_product_path($this->product->pID);
			if($cPath !== '')
			{
				$categoryIds = explode('_', $cPath);
				$this->categoryId = end($categoryIds);
			}
		}
		
		$query		= "SELECT
							pc.products_id,
							pd.products_name
						FROM
							" . TABLE_PRODUCTS_TO_CATEGORIES . " pc,
							" . TABLE_PRODUCTS . " p,
							" . TABLE_PRODUCTS_DESCRIPTION . " pd
						WHERE
							categories_id = '" . (int)$this->categoryId . "'
						AND p.products_id = pc.products_id
						AND p.products_id = pd.products_id
						AND pd.language_id = '" . (int)$this->languageId . "'
						AND p.products_status=1
							" . $condition . $order;

		return $query;
	}


	/**
	 *
	 */
	protected function _assignData()
	{
		foreach($this->assignData as $key => $value) 
		{
			$this->set_content_data($key, $value);
		}
	}


	/**
	 * @param integer	$id				
	 * @param array		$productArray
	 * @param string	$name
	 */
	protected function _setUrl($id, array $productArray, $name)
	{
		$pId    = (int)$productArray[$id]['pID'];
		$pName  = $productArray[$id]['pName'];

		if($this->boostProducts)
		{
			$this->assignData[$name] = $this->seoBoost->get_boosted_product_url($pId);
		}
		else
		{
			$this->assignData[$name] = xtc_href_link(FILENAME_PRODUCT_INFO, xtc_product_link($pId, $pName));
		}
	}

}