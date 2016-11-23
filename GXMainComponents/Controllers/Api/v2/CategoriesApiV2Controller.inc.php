<?php

/* --------------------------------------------------------------
  CategoriesApiV2Controller.inc.php 2016-09-06
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2016 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
*/

MainFactory::load_class('HttpApiV2Controller');

/**
 * Class CategoriesApiV2Controller
 *
 * Provides a gateway to the CategoryWriteService and CategoryReadService classes, which handle the shop category
 * resources.
 *
 * @category   System
 * @package    ApiV2Controllers
 */
class CategoriesApiV2Controller extends HttpApiV2Controller
{
	/**
	 * Category write service.
	 * 
	 * @var CategoryWriteService
	 */
	protected $categoryWriteService;

	/**
	 * Category read service.
	 * 
	 * @var CategoryReadService
	 */
	protected $categoryReadService;

	/**
	 * Category JSON serializer.
	 * 
	 * @var CategoryJsonSerializer
	 */
	protected $categoryJsonSerializer;

	/**
	 * Category list item JSON serializer.
	 * 
	 * @var CategoryListItemJsonSerializer
	 */
	protected $categoryListItemJsonSerializer;


	/**
	 * Initialize API Controller
	 */
	protected function __initialize()
	{
		$this->categoryWriteService           = StaticGXCoreLoader::getService('CategoryWrite');
		$this->categoryReadService            = StaticGXCoreLoader::getService('CategoryRead');
		$this->categoryJsonSerializer         = MainFactory::create('CategoryJsonSerializer');
		$this->categoryListItemJsonSerializer = MainFactory::create('CategoryListItemJsonSerializer');
	}


	/**
	 * @api        {post} /categories Create Category
	 * @apiVersion 2.1.0
	 * @apiName    CreateCategory
	 * @apiGroup   Categories
	 *
	 * @apiDescription
	 * Creates new category in the system. To see an example usage take a look at
	 * `docs/REST/samples/category-service/create_category.php`
	 *
	 * @apiParamExample {json} Request-Body
	 * {
	 *   "parentId": 0,
	 *   "isActive": true,
	 *   "sortOrder": 0,
	 *   "name": {
	 *     "en": "test category",
	 *     "de": "Testkategorie"
	 *   },
	 *   "headingTitle": {
	 *     "en": "test category",
	 *     "de": "Testkategorie"
	 *   },
	 *   "description": {
	 *     "en": "<p>test category description</p>",
	 *     "de": "<p>Testkategorie Beschreibung</p>"
	 *   },
	 *   "metaTitle": {
	 *     "en": "",
	 *     "de": ""
	 *   },
	 *   "metaDescription": {
	 *     "en": "",
	 *     "de": ""
	 *   },
	 *   "metaKeywords": {
	 *     "en": "",
	 *     "de": ""
	 *   },
	 *   "urlKeywords": {
	 *     "en": "test-category",
	 *     "de": "Testkategorie"
	 *   },
	 *   "icon": "item_ltr.gif",
	 *   "image": "",
	 *   "imageAltText": {
	 *     "en": "",
	 *     "de": ""
	 *   },
	 *   "settings": {
	 *     "categoryListingTemplate": "categorie_listing.html",
	 *     "productListingTemplate": "product_listing_v1.html",
	 *     "sortColumn": "p.products_price",
	 *     "sortDirection": "ASC",
	 *     "onSitemap": true,
	 *     "sitemapPriority": "0.5",
	 *     "sitemapChangeFrequency": "daily",
	 *     "showAttributes": false,
	 *     "showGraduatedPrice": false,
	 *     "showQuantity": true,
	 *     "showQuantityInfo": false,
	 *     "showSubCategories": true,
	 *     "showSubCategoryImages": true,
	 *     "showSubCategoryNames": true,
	 *     "showSubCategoryProducts": false,
	 *     "isViewModeTiled": false,
	 *     "showCategoryFilter": false, 
	 *     "filterSelectionMode": 0, 
	 *     "filterValueDeactivation": 0,
	 *     "groupPermissions": [
	 *       {
	 *         "id": "0",
	 *         "isPermitted": false
	 *       },
	 *       {
	 *         "id": "1",
	 *         "isPermitted": false
	 *       },
	 *       {
	 *         "id": "2",
	 *         "isPermitted": false
	 *       },
	 *       {
	 *         "id": "3",
	 *         "isPermitted": false
	 *       }
	 *     ]
	 *   }
	 * }
	 *
	 * @apiParam {Number} parentId The ID of the parent category (use 0 if there is no parent category).
	 * @apiParam {Boolean} isActive Whether the category is active.
	 * @apiParam {Number} sortOrder Category's sort order starts from 0.
	 * @apiParam {Object} name Multi-language object with the category's name.
	 * @apiParam {Object} headingTitle Multi-language object with the category's title.
	 * @apiParam {Object} description Multi-language object with the category's description.
	 * @apiParam {Object} metaTitle Multi-language object with the category's meta title.
	 * @apiParam {Object} metaDescription Multi-language object with the category's meta description.
	 * @apiParam {Object} metaKeywords Multi-language object with the category's meta keywords.
	 * @apiParam {Object} urlKeywords Multi-language object with the category's meta URL keywords.
	 * @apiParam {String} icon The category icon filename.
	 * @apiParam {String} image The category image filename.
	 * @apiParam {Object} imageAltText Multi-language object with image alt text.
	 * @apiParam {Object} settings Contains the category settings.
	 * @apiParam {String} settings.categoryListingTemplate Provide a category listing template
	 *           (`categorie_listing.html`).
	 * @apiParam {String} settings.productListingTemplate Provide a product listing template
	 *           (`product_listing_v1.html`).
	 * @apiParam {String} settings.sortColumn The name of the products column that will be used to sort the products.
	 * @apiParam {String} settings.sortDirection Provide `ASC` or `DESC`
	 * @apiParam {Boolean} settings.onSitemap Whether the category appears on sitemap.
	 * @apiParam {String} settings.sitemapPriority A numerical string value that defines the priority.
	 * @apiParam {String} settings.sitemapChangeFrequency Possible values can contain the `always`, `hourly`, `daily`,
	 * `weekly`, `monthly`, `yearly`, `never`.
	 * @apiParam {Boolean} settings.showAttributes Show attributes flag.
	 * @apiParam {Boolean} settings.showGraduatedPrice Show graduated price flag.
	 * @apiParam {Boolean} settings.showQuantity Show quantity flag.
	 * @apiParam {Boolean} settings.showQuantityInfo Show quantity information flag.
	 * @apiParam {Boolean} settings.showSubCategories Show sub categories flag.
	 * @apiParam {Boolean} settings.showSubCategoryImages Show sub category images flag.
	 * @apiParam {Boolean} settings.showSubCategoryNames Show sub category names flag.
	 * @apiParam {Boolean} settings.showSubCategoryProducts Show sub category products flag.
	 * @apiParam {Boolean} settings.isViewModeTiled Whether the category view mode is tiled.
	 * @apiParam {Boolean} settings.showCategoryFilter Whether to show the category filter.
	 * @apiParam {Number} settings.filterSelectionMode Filter selection mode value.
	 * @apiParam {Number} settings.filterValueDeactivation Filter value deactivation mode value. 
	 * @apiParam {Array} settings.groupPermissions Contains objects that have info about the customer group
	 *           permissions.
	 * @apiParam {Number} settings.groupPermissions.id The customer group permissions.
	 * @apiParam {Boolean} settings.groupPermissions.isPermitted Whether the current group is permitted to view the
	 *           category.
	 *
	 * @apiSuccess (Success 201) Response-Body If successful, this method returns a complete Category resource in the
	 * response body.
	 * 
	 * @apiError 400-BadRequest Category data were not provided.
	 * 
	 * @apiErrorExample Error-Response
	 * HTTP/1.1 400 Bad Request
	 * {
	 *   "code": 400,
	 *   "status": "error",
	 *   "message": "Category data were not provided."
	 * }
	 */
	public function post()
	{
		$categoryJsonString = $this->api->request->getBody();

		if(empty($categoryJsonString))
		{
			throw new HttpApiV2Exception('Category data were not provided.', 400);
		}

		if(isset($this->uri[1]) && is_numeric($this->uri[1])) // Duplicate Category
		{
			$categoryJsonObject = json_decode($categoryJsonString);

			if($categoryJsonObject->parentId === null || !is_numeric($categoryJsonObject->parentId))
			{
				$categoryJsonObject           = new stdClass;
				$categoryJsonObject->parentId = 0; // Default category value.
			}

			$categoryId = $this->categoryWriteService->duplicateCategory(new IdType($this->uri[1]),
			                                                             new IdType($categoryJsonObject->parentId));
		}
		else // Create New Category
		{
			$category   = $this->categoryJsonSerializer->deserialize($categoryJsonString);
			$categoryId = $this->categoryWriteService->createCategory($category);
		}

		$storedCategory = $this->categoryReadService->getCategoryById(new IdType($categoryId));
		$response       = $this->categoryJsonSerializer->serialize($storedCategory, false);
		$this->_linkResponse($response);
		$this->_locateResource('categories', $categoryId);
		$this->_writeResponse($response, 201);
	}


	/**
	 * @api        {put} /categories/:id Update Category
	 * @apiVersion 2.1.0
	 * @apiName    UpdateCategory
	 * @apiGroup   Categories
	 *
	 * @apiDescription
	 * Use this method to update an existing category record. Take a look in the POST method for more detailed
	 * explanation on every resource property. To see an example usage take a look at
	 * `docs/REST/samples/category-service/update_category.php`
	 *
	 * @apiSuccess Response-Body If successful, this method returns the updated Category resource in the response body.
	 *
	 * @apiError 400-BadRequest Category record ID was not provided or is invalid.
	 * @apiError 400-BadRequest Category data were not provided.
	 *           
	 * @apiErrorExample Error-Response (Missing or invalid ID)
	 * HTTP/1.1 400 Bad Request
	 * {
	 *   "code": 400,
	 *   "status": "error",
	 *   "message": "Category record ID was not provided or is invalid."
	 * }
	 *
	 * @apiErrorExample Error-Response (Empty request body)
	 * HTTP/1.1 400 Bad Request
	 * {
	 *   "code": 400,
	 *   "status": "error",
	 *   "message": "Category data were not provided."
	 * }
	 */
	public function put()
	{
		if(!isset($this->uri[1]) || !is_numeric($this->uri[1]))
		{
			throw new HttpApiV2Exception('Category record ID was not provided or is invalid: ' . gettype($this->uri[1]),
			                             400);
		}

		$categoryJsonString = $this->api->request->getBody();

		if(empty($categoryJsonString))
		{
			throw new HttpApiV2Exception('Category data were not provided.', 400);
		}

		$categoryId = new IdType($this->uri[1]);

		// Ensure that the category has the correct category id of the request url
		$categoryJsonString = $this->_setJsonValue($categoryJsonString, 'id', $categoryId->asInt());

		$category   = $this->categoryJsonSerializer->deserialize($categoryJsonString,
		                                                         $this->categoryReadService->getCategoryById($categoryId));

		$this->categoryWriteService->updateCategory($category);

		$response = $this->categoryJsonSerializer->serialize($category, false);
		$this->_linkResponse($response);
		$this->_writeResponse($response, 200);
	}


	/**
	 * @api        {delete} /categories/:id Delete Category
	 * @apiVersion 2.1.0
	 * @apiName    DeleteCategory
	 * @apiGroup   Categories
	 *
	 * @apiDescription
	 * Removes a category record from the database. The products that are assigned to this category will not
	 * be removed. To see an example usage take a look at
	 * `docs/REST/samples/category-service/remove_category.php`
	 *
	 * @apiExample {curl} Delete Category With ID = 57
	 *             curl -X DELETE --user admin@shop.de:12345 http://shop.de/api.php/v2/categories/57
	 *
	 * @apiSuccessExample {json} Success-Response
	 * {
	 *   "code": 200,
	 *   "status": "success",
	 *   "action": "delete",
	 *   "resource": "Category",
	 *   "categoryId": 57
	 * }
	 *
	 * @apiError 400-BadRequest Category record ID was not provided in the resource URL.
	 *           
	 * @apiErrorExample Error-Response
	 * HTTP/1.1 400 Bad Request
	 * {
	 *   "code": 400,
	 *   "status": "error",
	 *   "message": "Category record ID was not provided in the resource URL."
	 * }
	 */
	public function delete()
	{
		// Check if record ID was provided.
		if(!isset($this->uri[1]) || !is_numeric($this->uri[1]))
		{
			throw new HttpApiV2Exception('Category record ID was not provided in the resource URL.', 400);
		}

		// Remove category record from database.
		$this->categoryWriteService->deleteCategoryById(new IdType($this->uri[1]));

		// Return response JSON.
		$response = array(
			'code'       => 200,
			'status'     => 'success',
			'action'     => 'delete',
			'resource'   => 'Category',
			'categoryId' => (int)$this->uri[1]
		);

		$this->_writeResponse($response);
	}


	/**
	 * @api        {get} /categories/:id Get Categories
	 * @apiVersion 2.1.0
	 * @apiName    GetCategory
	 * @apiGroup   Categories
	 *
	 * @apiDescription
	 * Get multiple or a single category records through a GET request. This method supports all the GET parameters
	 * that are mentioned in the "Introduction" section of this documentation. To see an example usage take a look at
	 * `docs/REST/samples/category-service/fetch_category.php`
	 *
	 * @apiExample {curl} Get All Categories
	 *             curl -i --user admin@shop.de:12345 http://shop.de/api.php/v2/categories
	 *
	 * @apiExample {curl} Get Category With ID = 57
	 *             curl -i --user admin@shop.de:12345 http://shop.de/api.php/v2/categories/57
	 * 
	 * @apiExample {curl} Get Children of Category With ID = 23
	 *             curl -i --user admin@shop.de:12345 http://shop.de/api.php/v2/categories/23/children
	 *
	 * @apiError 404-NotFound Category does not exist.
	 *           
	 * @apiErrorExample Error-Response
	 * HTTP/1.1 404 Not Found
	 * {
	 *   "code": 404,
	 *   "status": "error",
	 *   "message": "Category does not exist."
	 * }
	 */
	public function get()
	{
		// Parse customer status limit GET parameter.
		$customerStatusLimit = null;
		if($this->api->request->get('customer_status_limit') !== null)
		{
			$customerStatusLimit = new IdType($this->api->request->get('customer_status_limit'));
		}

		// Parse language code GET parameter.
		$languageParameter = ($this->api->request->get('lang') !== null) ? $this->api->request->get('lang') : 'de';
		$languageCode      = new LanguageCode(new NonEmptyStringType($languageParameter));

		// Fetch the response data through the CategoryReadService.
		if(isset($this->uri[1]) && is_numeric($this->uri[1]))
		{
			if(isset($this->uri[2]) && $this->uri[2] === 'children') // Get Category Children
			{
				$categories = $this->categoryReadService->getCategoryList($languageCode, new IdType($this->uri[1]),
				                                                          $customerStatusLimit)->getArray();
			}
			else // Get Single Record
			{
				try
				{
					$categories = array($this->categoryReadService->getCategoryById(new IdType($this->uri[1])));
				}
				catch(UnexpectedValueException $e)
				{
					throw new HttpApiV2Exception('Category does not exist.', 404);
				}
			}
		}
		else // Get All Categories
		{
			$categories = $this->categoryReadService->getCategoryList($languageCode, null, $customerStatusLimit)
			                                        ->getArray();
		}

		// Prepare the response array.
		$response = array();

		foreach($categories as $category)
		{
			if($category instanceof CategoryInterface)
			{
				$serialized = $this->categoryJsonSerializer->serialize($category, false);
			}
			else
			{
				$serialized = $this->categoryListItemJsonSerializer->serialize($category, false);
			}

			$response[] = $serialized;
		}

		if($this->api->request->get('q') !== null)
		{
			$this->_searchResponse($response, $this->api->request->get('q'));
		}

		$this->_sortResponse($response);
		$this->_paginateResponse($response);
		$this->_minimizeResponse($response);
		$this->_linkResponse($response);

		// Return single resource to client and not array (if needed).
		if(isset($this->uri[1]) && is_numeric($this->uri[1]) && !isset($this->uri[2]) && count($response) > 0)
		{
			$response = $response[0];
		}

		$this->_writeResponse($response);
	}
}
