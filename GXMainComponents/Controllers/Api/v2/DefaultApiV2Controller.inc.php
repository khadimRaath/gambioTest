<?php
/* --------------------------------------------------------------
   DefaultApiV2Controller.inc.php 2016-09-20
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('HttpApiV2Controller');

/**
 * Class DefaultApiV2Controller
 *
 * The default APIv2 controller will be triggered when client consumers hit the "api.php/v2"
 * URI and it will return information about the API.
 *
 * @category System
 * @package  ApiV2Controllers
 */
class DefaultApiV2Controller extends HttpApiV2Controller
{
	public function get()
	{
		$this->_returnHelpResponse();
	}


	public function post()
	{
		$this->_returnHelpResponse();
	}


	public function put()
	{
		$this->_returnHelpResponse();
	}


	public function patch()
	{
		$this->_returnHelpResponse();
	}


	public function delete()
	{
		$this->_returnHelpResponse();
	}


	public function head()
	{
		$this->_returnHelpResponse();
	}


	public function options()
	{
		$this->_returnHelpResponse();
	}


	protected function _returnHelpResponse()
	{
		$apiUrl = GM_HTTP_SERVER . $this->api->request->getRootUri() . '/v2/';

		$resources = [
			'addresses',
			'attachments',
			'categories',
			'category_images',
			'category_icons',
			'countries',
			'customers',
			'emails',
			'orders',
			'product_images',
			'products',
			'zones'
		]; 
		
		$response = []; 
		
		foreach($resources as $resource) 
		{
			$response[$resource] = $apiUrl . $resource;
		}

		$this->_writeResponse($response);
	}
}