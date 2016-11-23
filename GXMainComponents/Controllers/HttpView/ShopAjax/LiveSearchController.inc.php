<?php
/* --------------------------------------------------------------
   LiveSearchController.inc.php 2016-05-19
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class LiveSearchController
 * 
 * @extends    HttpViewController
 * @category   System
 * @package    HttpViewControllers
 */
class LiveSearchController extends HttpViewController
{
	/**
	 * @todo get rid of old AjaxHandler
	 * @todo use GET and POST REST-API like      
	 * 
	 * @return HttpControllerResponse
	 */
	public function actionDefault()
	{
		$ajaxHandler = MainFactory::create('LiveSearchAjaxHandler');
		
		$keywords             = !is_null($this->_getPostData('keywords'))
			? (string)$this->_getPostData('keywords')
			: '';
		$categoryId           = !is_null($this->_getPostData('categories_id'))
			? (int)$this->_getPostData('categories_id')
			: 0;
		$includeSubCategories = !is_null($this->_getPostData('inc_subcat'))
			? (int)$this->_getPostData('inc_subcat')
			: 1;

		$getData = array(
			'needle'        => $keywords,
			'categories_id' => $categoryId,
			'inc_subcat'    => $includeSubCategories
		);

		$ajaxHandler->set_data('GET', $getData);
		$ajaxHandler->proceed();

		return MainFactory::create('HttpControllerResponse', $ajaxHandler->get_response());
	}
}