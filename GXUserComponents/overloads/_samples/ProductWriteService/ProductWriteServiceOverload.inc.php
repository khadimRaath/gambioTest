<?php
/* --------------------------------------------------------------
   ProductWriteServiceOverload.inc.php 2016-06-24
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class ProductWriteServiceOverload
 *
 * Sample overload for the product write service.
 *
 * @see ProductWriteService
 */
class ProductWriteServiceOverload extends ProductWriteServiceOverload_parent
{
	/**
	 * Overloaded "createProduct" method.
	 *
	 * @param ProductInterface $product
	 *
	 * @return int
	 */
	public function createProduct(ProductInterface $product)
	{
		$newProductId = parent::createProduct($product);
		
		$this->_createDebugLog('ProductWriteServiceService::createProduct >> Created new product with ID = '
		                       . $newProductId);
		
		return $newProductId;
	}
	
	
	/**
	 * Overloaded "updateProduct" method.
	 *
	 * @param StoredProductInterface $product
	 *
	 * @return ProductWriteServiceInterface
	 */
	public function updateProduct(StoredProductInterface $product)
	{
		$this->_createDebugLog('ProductWriteServiceService::updateProduct >> Updated existing product with ID = '
		                       . $product->getProductId());
		
		return parent::updateProduct($product);
	}
	
	
	/**
	 * Overloaded "deleteProductById" method.
	 *
	 * @param IdType $productId
	 *
	 * @return ProductWriteServiceInterface
	 */
	public function deleteProductById(IdType $productId)
	{
		$this->_createDebugLog('ProductWriteServiceService::deleteProductById >> Deleted existing product with ID = '
		                       . $productId->asInt());
		
		return parent::deleteProductById($productId);
	}
	
	
	/**
	 * Overloaded "duplicateProduct" method.
	 *
	 * @param IdType        $productId
	 * @param IdType        $targetCategoryId
	 * @param BoolType|null $duplicateAttributes
	 * @param BoolType|null $duplicateSpecials
	 * @param BoolType|null $duplicateCrossSelling
	 *
	 * @return int
	 */
	public function duplicateProduct(IdType $productId,
	                                 IdType $targetCategoryId,
	                                 BoolType $duplicateAttributes = null,
	                                 BoolType $duplicateSpecials = null,
	                                 BoolType $duplicateCrossSelling = null)
	{
		$this->_createDebugLog('ProductWriteServiceService::duplicateProduct >> Duplicated existing product with ID = '
		                       . $productId->asInt());
		
		return parent::duplicateProduct($productId, $targetCategoryId, $duplicateAttributes, $duplicateSpecials,
		                                $duplicateCrossSelling);
	}
	
	
	/**
	 * Overloaded "linkProduct" method.
	 *
	 * @param IdType $productId
	 * @param IdType $targetCategoryId
	 *
	 * @return ProductWriteServiceInterface
	 */
	public function linkProduct(IdType $productId, IdType $targetCategoryId)
	{
		$this->_createDebugLog('ProductWriteServiceService::linkProduct >> Linked existing product with ID = '
		                       . $productId->asInt());
		
		return parent::linkProduct($productId, $targetCategoryId);
	}
	
	
	/**
	 * Overloaded "changeProductLink" method.
	 *
	 * @param IdType $productId
	 * @param IdType $currentCategoryId
	 * @param IdType $newCategoryId
	 *
	 * @return ProductWriteServiceInterface
	 */
	public function changeProductLink(IdType $productId, IdType $currentCategoryId, IdType $newCategoryId)
	{
		$this->_createDebugLog('ProductWriteServiceService::changeProductLink >> Changed existing link for product with ID = '
		                       . $productId->asInt());
		
		return parent::changeProductLink($productId, $currentCategoryId, $newCategoryId);
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
