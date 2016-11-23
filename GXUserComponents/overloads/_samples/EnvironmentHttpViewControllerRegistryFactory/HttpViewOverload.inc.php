<?php
/* --------------------------------------------------------------
   HttpViewOverload.inc.php 2016-06-24
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class HttpViewOverload
 *
 * This sample overload class demonstrates the addition of new controllers in the HttpService.
 *
 * @see HttpService
 */
class HttpViewOverload extends HttpViewOverload_parent
{
	/**
	 * Overloaded "_addAvailableControllers" method.
	 *
	 * This method will register the HTTP sample controllers that reside in the docs/PHP/samples/http-service directory.
	 *
	 * Notice: You have to copy the sample controller files into the GXEngine/Controllers directory before testing this
	 * overload.
	 *
	 * @param HttpViewControllerRegistryInterface $registry
	 */
	protected function _addAvailableControllers(HttpViewControllerRegistryInterface $registry)
	{
		parent::_addAvailableControllers($registry);
		$registry->set('SampleHttpAction', 'SampleHttpActionController');
		$registry->set('SampleHttpRender', 'SampleHttpRenderController');
		$registry->set('SampleHttpRequest', 'SampleHttpRequestController');
		$registry->set('SampleHttpResponse', 'SampleHttpResponseController');
	}
}
