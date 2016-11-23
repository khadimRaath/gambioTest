<?php

/* --------------------------------------------------------------
   ProductsApiV2Controller.inc.php 2016-09-06
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('HttpApiV2Controller');

/**
 * Class ProductsApiV2Controller
 *
 * Provides a gateway to the ProductWriteService and ProductReadService classes, which handle the shop
 * product resources.
 *
 * @category System
 * @package  ApiV2Controllers
 */
class ProductsApiV2Controller extends HttpApiV2Controller
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
	 * Product JSON serializer.
	 * 
	 * @var ProductJsonSerializer
	 */
	protected $productJsonSerializer;

	/**
	 * Product list item JSON serializer.
	 * 
	 * @var ProductListItemJsonSerializer
	 */
	protected $productListItemJsonSerializer;

	/**
	 * Sub resources.
	 * 
	 * @var array
	 */
	protected $subresource;


	/**
	 * Initializes API Controller
	 */
	protected function __initialize()
	{
		$this->productWriteService           = StaticGXCoreLoader::getService('ProductWrite');
		$this->productReadService            = StaticGXCoreLoader::getService('ProductRead');
		$this->productJsonSerializer         = MainFactory::create('ProductJsonSerializer');
		$this->productListItemJsonSerializer = MainFactory::create('ProductListItemJsonSerializer');
		$this->subresource                   = array(
			'links' => 'ProductsLinksApiV2Controller'
		);
	}


	/**
	 * @api        {post} /products Create Product
	 * @apiVersion 2.1.0
	 * @apiName    CreateProduct
	 * @apiGroup   Products
	 *
	 * @apiDescription
	 * Creates a new product record in the system. To see an example usage take a look at
	 * `docs/REST/samples/product-service/create_product.php`
	 *
	 * @apiParamExample {json} Request-Body
	 * {
	 *   "isActive": false,
	 *   "sortOrder": 0,
	 *   "orderedCount": 1,
	 *   "productModel": "ABC123",
	 *   "ean": "",
	 *   "price": 16.7983,
	 *   "discountAllowed": 0,
	 *   "taxClassId": 1,
	 *   "quantity": 998,
	 *   "weight": 0,
	 *   "shippingCosts": 0,
	 *   "shippingTimeId": 1,
	 *   "productTypeId": 1,
	 *   "manufacturerId": 0,
	 *   "isFsk18": false,
	 *   "isVpeActive": false,
	 *   "vpeID": 0,
	 *   "vpeValue": 0,
	 *   "name": {
	 *     "en": "test article",
	 *     "de": "Testartikel"
	 *   },
	 *   "description": {
	 *     "en": "[TAB:Page 1] Test Product Description (Page 1) [TAB: Page 2] Test Product Description (Page 2)",
	 *     "de": "[TAB:Seite 1] Testartikel Beschreibung (Seite 1) [TAB:Seite 2] Testartikel Beschreibung (Seite 2)"
	 *   },
	 *   "shortDescription": {
	 *     "en": "<p>Test product short description.</p>",
	 *     "de": "<p>Testartikel Kurzbeschreibung</p>"
	 *   },
	 *   "keywords": {
	 *     "en": "",
	 *     "de": ""
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
	 *   "url": {
	 *     "en": "",
	 *     "de": ""
	 *   },
	 *   "urlKeywords": {
	 *     "en": "test-article",
	 *     "de": "Testartikel"
	 *   },
	 *   "checkoutInformation": {
	 *     "en": "",
	 *     "de": ""
	 *   },
	 *   "viewedCount": {
	 *     "en": 0,
	 *     "de": 32
	 *   },
	 *   "images": [
	 *     {
	 *       "filename": "artikelbild_1_1.jpg",
	 *       "isPrimary": false,
	 *       "isVisible": true,
	 *       "imageAltText": {
	 *         "en": "",
	 *         "de": ""
	 *       }
	 *     },
	 *     {
	 *       "filename": "artikelbild_1_2.jpg",
	 *       "isPrimary": false,
	 *       "isVisible": true,
	 *       "imageAltText": {
	 *         "en": "",
	 *         "de": ""
	 *       }
	 *     },
	 *     {
	 *       "filename": "artikelbild_1_3.jpg",
	 *       "isPrimary": false,
	 *       "isVisible": true,
	 *       "imageAltText": {
	 *         "en": "",
	 *         "de": ""
	 *       }
	 *     }
	 *   ],
	 *   "settings": {
	 *     "detailsTemplate": "standard.html",
	 *     "optionsDetailsTemplate": "product_options_dropdown.html",
	 *     "optionsListingTemplate": "product_options_dropdown.html",
	 *     "showOnStartpage": false,
	 *     "showQuantityInfo": true,
	 *     "showWeight": false,
	 *     "showPriceOffer": true,
	 *     "showAddedDateTime": false,
	 *     "priceStatus": 0,
	 *     "minOrder": 1,
	 *     "graduatedQuantity": 1,
	 *     "onSitemap": true,
	 *     "sitemapPriority": "0.5",
	 *     "sitemapChangeFrequency": "daily",
	 *     "propertiesDropdownMode": "dropdown_mode_1",
	 *     "startpageSortOrder": 0,
	 *     "showPropertiesPrice": true,
	 *     "usePropertiesCombisQuantity": false,
	 *     "usePropertiesCombisShippingTime": true,
	 *     "usePropertiesCombisWeight": false
	 *   },
	 *   "addonValues": {
	 *     "productsImageWidth": "0",
	 *     "productsImageHeight": "0"
	 *   }
	 * }
	 *
	 * @apiParam {Boolean} isActive Whether the product is active.
	 * @apiParam {Number} sortOrder The sort order of the product.
	 * @apiParam {Number} orderedCount How many times the product was ordered.
	 * @apiParam {String} productModel Product's Model.
	 * @apiParam {String} ean European Article Number.
	 * @apiParam {Number} price Product's Price as float value.
	 * @apiParam {Number} discountAllowed Percentage of the allowed discount as float value.
	 * @apiParam {Number} taxClassId The tax class ID.
	 * @apiParam {Number} quantity Quantity in stock as float value.
	 * @apiParam {Number} weight The weight of the product as float value.
	 * @apiParam {Number} shippingCosts Additional shipping costs as float value.
	 * @apiParam {Number} shippingTimeId Must match a record from the shipping time entries.
	 * @apiParam {Number} productTypeId Must match a record from the product type entries.
	 * @apiParam {Number} manufacturerId Must match the ID of the manufacturer record.
	 * @apiParam {Boolean} isFsk18 Whether the product is FSK18.
	 * @apiParam {Boolean} isVpeActive Whether VPE is active.
	 * @apiParam {Number} vpeID The VPE ID of the product.
	 * @apiParam {Number} vpeValue The VPE value of the product as float value.
	 * @apiParam {Object} name Language specific object with the product's name.
	 * @apiParam {Object} description Language specific object with the product's description.
	 * @apiParam {Object} shortDescription Language specific object with the product's short description.
	 * @apiParam {Object} keywords Language specific object with the product's keywords.
	 * @apiParam {Object} metaTitle Language specific object with the product's meta title.
	 * @apiParam {Object} metaDescription Language specific object with the product's meta description.
	 * @apiParam {Object} metaKeywords Language specific object with the product's meta keywords.
	 * @apiParam {Object} url Language specific object with the product's url.
	 * @apiParam {Object} urlKeywords Language specific object with the product's url keywords.
	 * @apiParam {Object} checkoutInformation Language specific object with the product's checkout information.
	 * @apiParam {Object} viewedCount Language specific object with the product's viewed count.
	 * @apiParam {Array} images Contains the product images information.
	 * @apiParam {String} images.filename The product image file name (provide only the file name and not the whole
	 *           path).
	 * @apiParam {Boolean} images.isPrimary Whether the image is the primary one.
	 * @apiParam {Boolean} images.isVisible Whether the image will be visible.
	 * @apiParam {Object} images.imageAltText Language specific object with the image alternative text.
	 * @apiParam {Object} settings Contains various product settings.
	 * @apiParam {String} settings.detailsTemplate Filename of the details HTML template.
	 * @apiParam {String} settings.optionsDetailsTemplate Filename of the options details HTML template.
	 * @apiParam {String} settings.optionsListingTemplate Filename of the options listing HTML template.
	 * @apiParam {Boolean} settings.showOnStartpage Whether to show the product on startpage.
	 * @apiParam {Boolean} settings.showQuantityInfo Whether to show quantity information.
	 * @apiParam {Boolean} settings.showWeight Whether to show the products weight.
	 * @apiParam {Boolean} settings.showPriceOffer Whether to show price offer.
	 * @apiParam {Boolean} settings.showAddedDateTime Whether to show the creation date-time of the product.
	 * @apiParam {Number} settings.priceStatus Must match a record from the price status entries.
	 * @apiParam {Number} settings.minOrder The minimum order of the product.
	 * @apiParam {Number} settings.graduatedQuantity Product's graduated quantity.
	 * @apiParam {Boolean} settings.onSitemap Whether to include the product in the sitemap.
	 * @apiParam {String} settings.sitemapPriority The sitemap priority (provide a decimal value as a string).
	 * @apiParam {String} settings.sitemapChangeFrequency Possible values can contain the `always`, `hourly`, `daily`,
	 * `weekly`, `monthly`, `yearly`, `never`.
	 * @apiParam {String} settings.propertiesDropdownMode Provide one of the following values: "" >>  Default - all
	 * values are always selectable, `dropdown_mode_1` >> Any order, only possible values are selectable,
	 * `dropdown_mode_2` >> Specified order, only possible values are selectable.
	 * @apiParam {Number} settings.startpageSortOrder The sort order in the startpage.
	 * @apiParam {Boolean} settings.showPropertiesPrice Whether to show properties price.
	 * @apiParam {Boolean} settings.usePropertiesCombisQuantity Whether to use properties combis quantitity.
	 * @apiParam {Boolean} settings.usePropertiesCombisShippingTime Whether to use properties combis shipping time.
	 * @apiParam {Boolean} settings.usePropertiesCombisWeight  Whether to use properties combis weight.
	 * @apiParam {Object} addonValues Contains some extra addon values.
	 * @apiParam {String} addonValues.productsImageWidth The CSS product image width (might contain size metrics).
	 * @apiParam {String} addonValues.productsImageHeight The CSS product image height (might contain size metrics).
	 *
	 * @apiSuccess (Success 201) Response-Body If successful, this method returns a complete Product resource in the
	 * response body.
	 *
	 * @apiError 400-BadRequest The body of the request was empty.
	 * 
	 * @apiErrorExample Error-Response
	 * HTTP/1.1 400 Bad Request
	 * {
	 *   "code": 400,
	 *   "status": "error",
	 *   "message": "The body of the request was empty."
	 * }
	 */
	public function post()
	{
		if($this->_mapResponse($this->subresource))
		{
			return;
		}

		$productJsonString = $this->api->request->getBody();

		if(isset($this->uri[1]) && is_numeric($this->uri[1])) // Duplicate Product
		{
			$productJsonObject = json_decode($productJsonString);

			if($productJsonObject->categoryId === null || !is_numeric($productJsonObject->categoryId))
			{
				$productJsonObject             = new stdClass;
				$productJsonObject->categoryId = 0; // Default category value.
			}

			$productId = $this->productWriteService->duplicateProduct(new IdType($this->uri[1]),
			                                                          new IdType($productJsonObject->categoryId));
		}
		else // Create New Product
		{
			$product   = $this->productJsonSerializer->deserialize($productJsonString);
			$productId = $this->productWriteService->createProduct($product);
		}

		$storedProduct = $this->productReadService->getProductById(new IdType($productId));
		$response      = $this->productJsonSerializer->serialize($storedProduct, false);
		$this->_linkResponse($response);
		$this->_locateResource('products', $productId);
		$this->_writeResponse($response, 201);
	}


	/**
	 * @api        {put} /products/:id Update Product
	 * @apiVersion 2.1.0
	 * @apiName    ProductCategory
	 * @apiGroup   Products
	 *
	 * @apiDescription
	 * Use this method to update an existing product record. Take a look in the POST method for more detailed
	 * explanation on every resource property. To see an example usage consider
	 * `docs/REST/samples/product-service/update_product.php`
	 *
	 * @apiSuccess Response-Body If successful, this method returns the updated Product resource in the response body.
	 *
	 * @apiError 400-BadRequest Product data were not provided.
	 * @apiErrorExample Error-Response (No data)
	 * HTTP/1.1 400 Bad Request
	 * {
	 *   "code": 400,
	 *   "status": "error",
	 *   "message": "Product data were not provided."
	 * }
	 *
	 * @todo Error status code on not found entries should be 404 and not 400.
	 */
	public function put()
	{
		if($this->_mapResponse($this->subresource))
		{
			return;
		}

		if(!isset($this->uri[1]) || !is_numeric($this->uri[1]))
		{
			throw new HttpApiV2Exception('Product record ID was not provided or is invalid: ' . gettype($this->uri[1]),
			                             400);
		}

		$productJsonString = $this->api->request->getBody();

		if(empty($productJsonString))
		{
			throw new HttpApiV2Exception('Product data were not provided.', 400);
		}

		$productId = new IdType($this->uri[1]);

		// Ensure that the product has the correct product id of the request url
		$productJsonString = $this->_setJsonValue($productJsonString, 'id', $productId->asInt());

		$product   = $this->productJsonSerializer->deserialize($productJsonString,
		                                                       $this->productReadService->getProductById($productId));

		$this->productWriteService->updateProduct($product);

		$response = $this->productJsonSerializer->serialize($this->productReadService->getProductById($productId),
		                                                    false);
		$this->_linkResponse($response);
		$this->_writeResponse($response, 200);
	}


	/**
	 * @api        {delete} /products/:id Delete Product
	 * @apiVersion 2.1.0
	 * @apiName    DeleteProduct
	 * @apiGroup   Products
	 *
	 * @apiDescription
	 * Removes a product record from the database. To see an example usage take a look at
	 * `docs/REST/samples/product-service/remove_product.php`
	 *
	 * @apiExample {curl} Delete Product With ID = 24
	 *             curl -X DELETE --user admin@shop.de:12345 http://shop.de/api.php/v2/products/24
	 *
	 * @apiSuccessExample {json} Success-Response
	 * {
	 *   "code": 200,
	 *   "status": "success",
	 *   "action": "delete",
	 *   "resource": "Product",
	 *   "productId": 24
	 * }
	 *
	 * @apiError 400-BadRequest Product record ID was not provided in the resource URL.
	 * @apiErrorExample Error-Response
	 * HTTP/1.1 400 Bad Request
	 * {
	 *   "code": 400,
	 *   "status": "error",
	 *   "message": "Product record ID was not provided in the resource URL."
	 * }
	 */
	public function delete()
	{
		if($this->_mapResponse($this->subresource))
		{
			return;
		}

		// Check if record ID was provided.
		if(!isset($this->uri[1]) || !is_numeric($this->uri[1]))
		{
			throw new HttpApiV2Exception('Product record ID was not provided in the resource URL.', 400);
		}

		// Remove product record from database.
		$this->productWriteService->deleteProductById(new IdType($this->uri[1]));

		// Return response JSON.
		$response = array(
			'code'      => 200,
			'status'    => 'success',
			'action'    => 'delete',
			'resource'  => 'Product',
			'productId' => (int)$this->uri[1]
		);

		$this->_writeResponse($response);
	}


	/**
	 * @api        {get} /products/:id Get Products
	 * @apiVersion 2.1.0
	 * @apiName    GetProduct
	 * @apiGroup   Products
	 *
	 * @apiDescription
	 * Get multiple or a single product records through a GET request. This method supports all the GET parameters
	 * that are mentioned in the "Introduction" section of this documentation. To see an example usage take a look at
	 * `docs/REST/samples/product-service/remove_product.php`
	 *
	 * @apiExample {curl} Get All Products
	 *             curl -i --user admin@shop.de:12345 http://shop.de/api.php/v2/products
	 *
	 * @apiExample {curl} Get Product With ID = 24
	 *             curl -i --user admin@shop.de:12345 http://shop.de/api.php/v2/products/24
	 *
	 * @apiError 404-NotFound Product does not exist.
	 * @apiErrorExample Error-Response
	 * HTTP/1.1 404 Not Found
	 * {
	 *   "code": 404,
	 *   "status": "error",
	 *   "message": "Product does not exist."
	 * }
	 */
	public function get()
	{
		if($this->_mapResponse($this->subresource))
		{
			return;
		}

		if($this->uri[1] && is_numeric($this->uri[1])) // Get Single Record
		{
			try
			{
				$products = array($this->productReadService->getProductById(new IdType($this->uri[1])));
			}
			catch(UnexpectedValueException $e)
			{
				throw new HttpApiV2Exception('Product does not exist.', 404);
			}
		}
		else
		{
			$langParameter = ($this->api->request->get('lang') !== null) ? $this->api->request->get('lang') : 'de';

			$languageCode = new LanguageCode(new NonEmptyStringType($langParameter));

			$products = $this->productReadService->getProductList($languageCode)->getArray();
		}

		$response = array();

		foreach($products as $product)
		{
			if($product instanceof ProductInterface)
			{
				$serialized = $this->productJsonSerializer->serialize($product, false);
			}
			else
			{
				$serialized = $this->productListItemJsonSerializer->serialize($product, false);
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

		// Return single resource to client and not array.
		if(isset($this->uri[1]) && is_numeric($this->uri[1]) && count($response) > 0)
		{
			$response = $response[0];
		}

		$this->_writeResponse($response);
	}
}
