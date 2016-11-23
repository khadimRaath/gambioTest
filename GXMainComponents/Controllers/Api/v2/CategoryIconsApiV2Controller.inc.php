<?php

/* --------------------------------------------------------------
   CategoryIconsApiV2Controller.inc.php 2016-03-07
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('AbstractImagesApiV2Controller');

/**
 * Class CategoryIconsApiV2Controller
 *
 * This controller handles the Category Icon file operations. The clients of the API can list, upload, rename or delete
 * files that exist in the server.
 *
 * @category System
 * @package  ApiV2Controllers
 */
class CategoryIconsApiV2Controller extends AbstractImagesApiV2Controller
{
	/**
	 * Initializes API Controller
	 */
	protected function __initialize()
	{
		$this->writeService = StaticGXCoreLoader::getService('CategoryWrite');
	}


	/**
	 * Returns the absolute path where the image files are located.
	 *
	 * @return string Image folder name.
	 */
	protected function _getImageFolderName()
	{
		return DIR_FS_CATALOG . DIR_WS_IMAGES . 'categories/icons/';
	}


	/**
	 * @api        {post} /category_icons Upload Category Icon
	 * @apiVersion 2.1.0
	 * @apiName    UploadCategoryIcon
	 * @apiGroup   Categories
	 *
	 * @apiDescription
	 * Upload an icon image for the categories. Make this request without the "Content-Type: application/json". Except
	 * from the file the POST request must also contain a "filename" value with the final file name.
	 *
	 * @apiSuccess (Success 201) Response-Body Contains information about the uploaded file.
	 *
	 * @apiSuccessExample {json} Success-Response
	 * {
	 *   "code": 201,
	 *   "status": "success",
	 *   "action": "upload",
	 *   "filename": "my-icon-file.png"
	 * }
	 *
	 * @apiError 400-BadRequest No image file or filename parameter have been provided.
	 * @apiErrorExample Error-Response (No image file)
	 * HTTP/1.1 400 Bad Request
	 * {
	 *   "code": 400,
	 *   "status": "error",
	 *   "message": "No image file was provided."
	 * }
	 *
	 * @apiErrorExample Error-Response (No filename)
	 * HTTP/1.1 400 Bad Request
	 * {
	 *   "code": 400,
	 *   "status": "error",
	 *   "message": "The 'filename' parameter is required and was not provided with the request."
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

		$filename = $this->writeService->importCategoryIconFile($existingFile, $filename);

		// Return success response to client.
		$response = array(
			'code'     => 201,
			'status'   => 'success',
			'action'   => 'upload',
			'filename' => (string)$filename
		);

		$this->_writeResponse($response, 201);
	}


	/**
	 * @api        {put} /category_icons Rename Icon File
	 * @apiVersion 2.1.0
	 * @apiName    RenameIconFile
	 * @apiGroup   Categories
	 *
	 * @apiDescription
	 * Use this method to rename an existing icon file.
	 *
	 * @apiExample {json} Request-Body
	 * {
	 *   "oldFilename": "my-old-icon.png",
	 *   "newFilename": "my-new-icon.png"
	 * }
	 *
	 * @apiSuccess Response-Body Contains information about the executed operation.
	 *
	 * @apiSuccessExample {json} Response-Body
	 * {
	 *   "code": 200,
	 *   "status": "success",
	 *   "action": "rename",
	 *   "oldFilename": "my-old-icon.png",
	 *   "newFilename": "my-new-icon.png"
	 * }
	 *
	 * @apiError 400-BadRequest The body of the request was empty or the request body did not contain the oldFilename or newFilename properties, or their
	 * values were invalid.
	 *
	 * @apiErrorExample Error-Response (Empty request body)
	 * HTTP/1.1 400 Bad Request
	 * {
	 *   "code": 400,
	 *   "status": "error",
	 *   "message": "Category icon data were not provided."
	 * }
	 *
	 * @apiErrorExample Error-Response (Missing parameters)
	 * HTTP/1.1 400 Bad Request
	 * {
	 *   "code": 400,
	 *   "status": "error",
	 *   "message": "This operation requires a JSON object with 'oldFilename' and 'newFilename' properties set."
	 * }
	 */
	public function put()
	{
		$iconJsonString = $this->api->request->getBody();

		if(empty($iconJsonString))
		{
			throw new HttpApiV2Exception('Category icon data were not provided.', 400);
		}

		$iconJsonObject = json_decode($iconJsonString);

		if($iconJsonObject->oldFilename === null || $iconJsonObject->newFilename === null)
		{
			throw new HttpApiV2Exception('This operation requires a JSON object with "oldFilename" and "newFilename" '
			                             . 'properties set. Check the documentation on how to properly use the API.',
			                             400);
		}

		$oldFilename = new FilenameStringType($iconJsonObject->oldFilename);
		$newFilename = new FilenameStringType($iconJsonObject->newFilename);

		$this->writeService->renameCategoryIconFile($oldFilename, $newFilename);

		$response = array(
			'code'        => 200,
			'status'      => 'success',
			'action'      => 'rename',
			'oldFilename' => $iconJsonObject->oldFilename,
			'newFilename' => $iconJsonObject->newFilename
		);

		$this->_writeResponse($response);
	}


	/**
	 * @api        {delete} /category_icon Delete Category Icon
	 * @apiVersion 2.1.0
	 * @apiName    DeleteCategoryIcon
	 * @apiGroup   Categories
	 *
	 * @apiDescription
	 * Removes the category icon file from the server. This method will always provide a successful response even if
	 * the image file was not found.
	 *
	 * @apiExample {json} Request-Body
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
	 * @apiError 400-BadRequest Category icon data were not provided or the request body did not contain the
	 * filename or its value was invalid.
	 * 
	 * @apiErrorExample Error-Response (Empty request body)
	 * HTTP/1.1 400 Bad Request
	 * {
	 *   "code": 400,
	 *   "status": "error",
	 *   "message": "Category icon data were not provided."
	 * }
	 *                  
	 * @apiErrorExample Error-Response (Missing parameters)
	 * HTTP/1.1 400 Bad Request
	 * {
	 *   "code": 400,
	 *   "status": "error",
	 *   "message": "This operation requires a JSON object with 'filename' properties set."
	 * }
	 */
	public function delete()
	{
		$iconJsonString = $this->api->request->getBody();

		if(empty($iconJsonString))
		{
			throw new HttpApiV2Exception('Category icon data were not provided.', 400);
		}

		$iconJsonObject = json_decode($iconJsonString);

		if($iconJsonObject->filename === null)
		{
			throw new HttpApiV2Exception('This operation requires a JSON object with "filename" properties set. '
			                             . 'Check the documentation on how to properly use the API.', 400);
		}

		// The CategoryWriteService will not throw an exception if the image file does not exist.
		$this->writeService->deleteCategoryIconFile(new FilenameStringType($iconJsonObject->filename));

		$response = array(
			'code'     => 200,
			'status'   => 'success',
			'action'   => 'delete',
			'filename' => $iconJsonObject->filename
		);

		$this->_writeResponse($response);
	}


	/**
	 * @api        {get} /category_icon Get Category Icons
	 * @apiVersion 2.1.0
	 * @apiName    GetCategoryIcons
	 * @apiGroup   Categories
	 *
	 * @apiDescription
	 * Returns a list of all category icon files which exists in the server's filesystem through a GET request.
	 *
	 * @apiExample {curl} Get All Category Icons
	 *             curl -i --user admin@shop.de:12345 http://shop.de/api.php/v2/category_icon
	 */
	public function get()
	{
		parent::get();
	}
}
