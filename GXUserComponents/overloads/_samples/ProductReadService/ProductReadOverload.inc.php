<?php
/* --------------------------------------------------------------
   ProductReadOverload.inc.php 2016-06-24
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class ProductReadOverload
 *
 * This sample demonstrates the overloading of ProductReadService.
 *
 * Notice: This service is not currently used within the shop (the integration will come gradually). You can try
 * this overload with the sample files that reside in the docs/PHP/samples/product-service directory.
 *
 * @see ProductReadService
 */
class ProductReadOverload extends ProductReadOverload_parent
{
	/**
	 * Overloaded "getProductById" method.
	 *
	 * @param IdType $productId
	 *
	 * @return StoredProductInterface
	 */
	public function getProductById(IdType $productId)
	{
		$this->_createDebugLog('ProductReadService::getProductById >> Fetch product with ID = ' . $productId->asInt());
		
		return parent::getProductById($productId);
	}
	
	
	/**
	 * Overloaded "getProductList" method.
	 *
	 * @param LanguageCode $languageCode
	 * @param IdType|null  $categoryId
	 * @param IdType|null  $customerStatusLimit
	 *
	 * @return ProductListItemCollection
	 */
	public function getProductList(LanguageCode $languageCode,
	                               IdType $categoryId = null,
	                               IdType $customerStatusLimit = null)
	{
		$this->_createDebugLog('ProductReadService::getProductList >> Fetch product list with category ID = '
		                       . $categoryId->asInt());
		
		return parent::getProductList($languageCode, $categoryId, $customerStatusLimit);
	}
	
	
	/**
	 * Overloaded "getActiveProductList" method.
	 *
	 * Check if the mysqli instance exists. If it exists, log some extra information.
	 * Afterwards, the parent method is invoked and returned.
	 *
	 * @param LanguageCode $languageCode
	 * @param IdType|null  $categoryId
	 * @param IdType|null  $customerStatusLimit
	 *
	 * @return ProductListItemCollection
	 */
	public function getActiveProductList(LanguageCode $languageCode,
	                                     IdType $categoryId = null,
	                                     IdType $customerStatusLimit = null)
	{
		$this->_createDebugLog('ProductReadService::getActiveProductList >> Fetch product list with category ID = '
		                       . $categoryId->asInt());
		
		return parent::getActiveProductList($languageCode, $categoryId, $customerStatusLimit);
	}
	
	
	/**
	 * Overloaded "getProductLinks" method.
	 *
	 * Check if the mysqli instance exists. If it exists, log some extra information.
	 * Afterwards, the parent method is invoked and returned.
	 *
	 * @param IdType $productId
	 *
	 * @return IdCollection
	 */
	public function getProductLinks(IdType $productId)
	{
		$this->_createDebugLog('ProductReadService::getProductLinks >> Fetch product links for product ID = '
		                       . $productId->asInt());
		
		return parent::getProductLinks($productId);
	}
	
	
	/**
	 * Create new debug log entry.
	 *
	 * @param string $message
	 */
	protected function _createDebugLog($message)
	{
		$logControl = LogControl::get_instance();
		$logControl->notice($message);
	}
}
