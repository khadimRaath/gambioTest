<?php

/* --------------------------------------------------------------
   ProductsLinksApiV2Controller.inc.php 2016-09-20
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('HttpApiV2Controller');

/**
 * Class ProductsLinksApiV2Controller
 *
 * Provides an API interface for managing the product links.
 *
 * Notice: This controller is a sub-resource of the ProductsApiV2Controller.
 *
 * @category System
 * @package  ApiV2Controllers
 */
class ProductsLinksApiV2Controller extends HttpApiV2Controller
{
	/**
	 * Product write service.
	 * 
	 * @var ProductWriteService
	 */
	protected $productWriteService;

	/**
	 * Product read service.
	 * 
	 * @var ProductReadService
	 */
	protected $productReadService;


	/**
	 * Initialize API Controller
	 *
	 * @throws HttpApiV2Exception
	 */
	protected function __initialize()
	{
		if(!isset($this->uri[1]) || !is_numeric($this->uri[1]))
		{
			throw new HttpApiV2Exception('Product record ID was not provided or is invalid: ' . gettype($this->uri[1]),
			                             400);
		}

		$this->productWriteService = StaticGXCoreLoader::getService('ProductWrite');
		$this->productReadService  = StaticGXCoreLoader::getService('ProductRead');
	}


	/**
	 * @api        {post} /products/:id/links Create Product Link
	 * @apiVersion 2.1.0
	 * @apiName    CreateProductLink
	 * @apiGroup   Products
	 *
	 * @apiDescription
	 * Creates a new 'product to category' record in the system.
	 *
	 * @apiParamExample {json} Request-Body
	 * {
	 *   "categoryId": 1
	 * }
	 *
	 * @apiSuccessExample {json} Success-Response
	 * {
	 *   "code": 201,
	 *   "status": "success",
	 *   "action": "create",
	 *   "resource": "ProductLink",
	 *   "productId": 1,
	 *   "categoryId": 1
	 * }
	 */
	public function post()
	{
		$json = json_decode($this->api->request->getBody());

		$productId  = new IdType($this->uri[1]);
		$categoryId = new IdType($json->categoryId);

		$this->productWriteService->linkProduct($productId, $categoryId);

		$response = array(
			'code'       => 201,
			'status'     => 'success',
			'action'     => 'create',
			'resource'      => 'ProductLink',
			'productId'  => (int)$this->uri[1],
			'categoryId' => (int)$json->categoryId
		);

		$this->_writeResponse($response, 201);
	}


	/**
	 * @api        {put} /products/:id/links Update Product Link
	 * @apiVersion 2.1.0
	 * @apiName    UpdateProductLink
	 * @apiGroup   Products
	 *
	 * @apiDescription
	 * Changes an existing 'product to category' link.
	 *
	 * @apiParamExample {json} Request-Body
	 * {
	 *   "oldCategoryId": 1,
	 *   "newCategoryId": 2
	 * }
	 *
	 * @apiSuccessExample {json} Success-Response
	 * {
	 *   "code": 200,
	 *   "status": "success",
	 *   "action": "update",
	 *   "resource": "ProductLink",
	 *   "productId": 1,
	 *   "oldCategoryId": 1,
	 *   "newCategoryId": 2
	 * }
	 */
	public function put()
	{
		$json = json_decode($this->api->request->getBody());

		$productId     = new IdType($this->uri[1]);
		$oldCategoryId = new IdType($json->oldCategoryId);
		$newCategoryId = new IdType($json->newCategoryId);

		$this->productWriteService->changeProductLink($productId, $oldCategoryId, $newCategoryId);

		$response = array(
			'code'          => 200,
			'status'        => 'success',
			'action'        => 'update',
			'resource'      => 'ProductLink',
			'productId'     => (int)$this->uri[1],
			'oldCategoryId' => (int)$json->oldCategoryId,
			'newCategoryId' => (int)$json->newCategoryId
		);

		$this->_writeResponse($response);
	}


	/**
	 * @api        {delete} /products/:id/links Delete Product Link
	 * @apiVersion 2.1.0
	 * @apiName    DeleteProductLink
	 * @apiGroup   Products
	 *
	 * @apiDescription
	 * Deletes an existing 'product to category' link.
	 * If there is no categoryId property set, all the product links will be removed.
	 *
	 * @apiExample {curl} Delete product link for Product with ID = 1 in Category with ID = 1
	 *             curl -X DELETE --user admin@shop.de:12345 http://shop.de/api.php/v2/products/1/links
	 *
	 * @apiSuccessExample {json} Success-Response
	 * {
	 *   "code": 200,
	 *   "status": "success",
	 *   "action": "delete",
	 *   "resource": "ProductLink",
	 *   "productId": 1
	 * }
	 */
	public function delete()
	{
		$json = json_decode($this->api->request->getBody());

		$productId = new IdType($this->uri[1]);

		if($json->categoryId !== null)
		{
			$categoryId = new IdType($json->categoryId);
			$this->productWriteService->deleteProductLink($productId, $categoryId);
		}
		else
		{
			$this->productWriteService->deleteProductLinks($productId);
		}

		$response = array(
			'code'       => 200,
			'status'     => 'success',
			'action'     => 'delete',
			'resource'   => 'ProductLink',
			'productId'  => (int)$this->uri[1]
		);

		$this->_writeResponse($response);
	}


	/**
	 * @api        {get} /products/:id/links Get Product Links
	 * @apiVersion 2.1.0
	 * @apiName    GetProductLink
	 * @apiGroup   Products
	 *
	 * @apiDescription
	 * Get all 'product to category' links for associated with a specific product.
	 *
	 * @apiExample {curl} Get product links for product with ID = 1
	 *             curl -i --user admin@shop.de:12345 http://shop.de/api.php/v2/products/1/links
	 *
	 * @apiSuccessExample {json} Success-Response
	 * [
	 *   0,
	 *   1
	 * ]
	 */
	public function get()
	{
		$productId = new IdType($this->uri[1]);

		$response = array();
		$categoriesArray = $this->productReadService->getProductLinks($productId)->getArray();

		foreach($categoriesArray as $categoryId)
		{
			$response[] = $categoryId->asInt();
		}

		$this->_writeResponse($response);
	}
}
