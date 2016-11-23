<?php

/* --------------------------------------------------------------
   ProductImagesApiV2Controller.inc.php 2016-09-20
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('AbstractImagesApiV2Controller');

/**
 * Class ProductImagesApiV2Controller
 *
 * Provides an API interface for managing product images through the ProductWriteService.
 *
 * Notice: This controller IS NOT a sub-resource of the ProductsApiV2Controller.
 *
 * This controller can be reached by using one of the following URIs:
 *
 * - http://shop.de/api.php/v2/product_images
 *
 * or
 *
 * - http://shop.de/api.php/v2/ProductImages
 *
 * Using "productimages" as a resource name will not resolve to this controller.
 *
 * @category System
 * @package  ApiV2Controllers
 */
class ProductImagesApiV2Controller extends AbstractImagesApiV2Controller
{
	/**
	 * Initializes API Controller
	 */
	protected function __initialize()
	{
		$this->writeService = StaticGXCoreLoader::getService('ProductWrite');
	}


	/**
	 * Returns the absolute path where the image files are located.
	 *
	 * @return string
	 */
	protected function _getImageFolderName()
	{
		return DIR_FS_CATALOG . DIR_WS_IMAGES . 'product_images/original_images/';
	}


	/**
	 * @api        {post} /product_images Upload Product Image
	 * @apiVersion 2.1.0
	 * @apiName    UploadProductImage
	 * @apiGroup   Products
	 *
	 * @apiDescription
	 * Uploads an image file for the products. Make this request without the "Content-Type: application/json". Except
	 * from the file the POST request must also contain a "filename" value with the final file name.
	 *
	 * @apiSuccess (Success 201) Response-Body Contains information about the uploaded file.
	 *
	 * @apiSuccessExample {json} Success-Response
	 * {
	 *   "code": 201,
	 *   "status": "success",
	 *   "action": "upload",
	 *   "filename": "my-image-file.png"
	 * }
	 *
	 * @apiError 400-BadRequest No image file was provided.
	 * @apiErrorExample Error-Response
	 * HTTP/1.1 400 Bad Request
	 * {
	 *   "code": 400,
	 *   "status": "error",
	 *   "message": "No image file was provided."
	 * }
	 */
	public function post()
	{
		if(!isset($_FILES) || empty($_FILES))
		{
			throw new HttpApiV2Exception('No image file was provided.', 400);
		}

		$file = array_shift($_FILES);

		$existingFile = new ExistingFile(new NonEmptyStringType($file['tmp_name']));

		if($this->api->request->post('filename') === null)
		{
			throw new HttpApiV2Exception('The "filename" parameter is required and was not provided with the request. '
			                             . 'Check the documentation on how to properly use the API.', 400);
		}

		$filename = new FilenameStringType($this->api->request->post('filename'));

		$filename = $this->writeService->importProductImageFile($existingFile, $filename);

		// Return success response to client.
		$response = array(
			'code'     => 201,
			'status'   => 'success',
			'action'   => 'upload',
			'filename' => $filename
		);

		$this->_writeResponse($response, 201);
	}


	/**
	 * @api        {put} /product_images Rename Image File
	 * @apiVersion 2.1.0
	 * @apiName    RenameImageFile
	 * @apiGroup   Products
	 *
	 * @apiDescription
	 * Use this method to rename an existing image file.
	 *
	 * @apiExample {json} Request-Body
	 * {
	 *   "oldFilename": "my-old-image.png",
	 *   "newFilename": "my-new-image.png"
	 * }
	 *
	 * @apiSuccess Response-Body Contains information about the executed operation.
	 *
	 * @apiSuccessExample {json} Response-Body
	 * {
	 *   "code": 200,
	 *   "status": "success",
	 *   "action": "rename",
	 *   "oldFilename": "my-old-image.png",
	 *   "newFilename": "my-new-image.png"
	 * }
	 *
	 * @apiError 400-BadRequest This operation requires a JSON object with "oldFilename" and "newFilename" properties set.
	 * @apiErrorExample Error-Response
	 * HTTP/1.1 400 Bad Request
	 * {
	 *   "code": 400,
	 *   "status": "error",
	 *   "message": "This operation requires a JSON object with "oldFilename" and "newFilename" properties set. Check the documentation on how to properly use the API."
	 * }
	 */
	public function put()
	{
		$json = json_decode($this->api->request->getBody());

		if($json->oldFilename === null || $json->newFilename === null)
		{
			throw new HttpApiV2Exception('This operation requires a JSON object with "oldFilename" and "newFilename" '
			                             . 'properties set. Check the documentation on how to properly use the API.',
			                             400);
		}

		$oldFilename = new FilenameStringType($json->oldFilename);
		$newFilename = new FilenameStringType($json->newFilename);

		$this->writeService->renameProductImage($oldFilename, $newFilename);

		$response = array(
			'code'        => 200,
			'status'      => 'success',
			'action'      => 'rename',
			'oldFilename' => $json->oldFilename,
			'newFilename' => $json->newFilename
		);

		$this->_writeResponse($response);
	}


	/**
	 * @api        {delete} /product_images Delete Product Image
	 * @apiVersion 2.1.0
	 * @apiName    DeleteProductImage
	 * @apiGroup   Products
	 *
	 * @apiDescription
	 * Remove the product image file from the server. This method will always provide a successful response even if
	 * the image file was not found.
	 *
	 * @apiExample {json} Delete Image
	 * {
	 *   "filename": "file-to-be-deleted.png"
	 * }
	 *
	 * @apiSuccessExample {json} Success-Response
	 * {
	 *   "code": 200,
	 *   "status": "success",
	 *   "action": "delete",
	 *   "filename": "file-to-be-deleted.png"
	 * }
	 *
	 * @apiError 400-BadRequest This operation requires a JSON object with "filename" properties set.
	 * @apiErrorExample Error-Response
	 * HTTP/1.1 400 Bad Request
	 * {
	 *   "code": 400,
	 *   "status": "error",
	 *   "message": "This operation requires a JSON object with "filename" properties set. Check the documentation on how to properly use the API."
	 * }
	 */
	public function delete()
	{
		$json = json_decode($this->api->request->getBody());

		if($json->filename === null)
		{
			throw new HttpApiV2Exception('This operation requires a JSON object with "filename" properties set. '
			                             . 'Check the documentation on how to properly use the API.', 400);
		}

		$this->writeService->deleteProductImage(new FilenameStringType($json->filename));

		$response = array(
			'code'     => 200,
			'status'   => 'success',
			'action'   => 'delete',
			'filename' => $json->filename
		);

		$this->_writeResponse($response);
	}


	/**
	 * @api        {get} /product_images Get Product Images
	 * @apiVersion 2.1.0
	 * @apiName    GetProductImages
	 * @apiGroup   Products
	 *
	 * @apiDescription
	 * Get a list of all product image files which exists in the server's filesystem through a GET request.
	 *
	 * @apiExample {curl} Get All Product Images
	 *             curl -i --user admin@shop.de:12345 http://shop.de/api.php/v2/product_images
	 */
	public function get()
	{
		parent::get();
	}
}
