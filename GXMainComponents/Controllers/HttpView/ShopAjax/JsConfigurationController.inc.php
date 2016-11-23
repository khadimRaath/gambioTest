<?php
/* --------------------------------------------------------------
   JsConfigurationController.inc.php 2016-08-24
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('HttpViewController');

/**
 * Class JsConfigurationController
 *
 * @extends    HttpViewController
 * @category   System
 * @package    HttpViewControllers
 */
class JsConfigurationController extends HttpViewController
{
	public function init()
	{
		// Check page token validity.
		$this->_validatePageToken();
	}
	
	
	/**
	 * Get a configuration value by the provided key.
	 *
	 * @return HttpControllerResponse
	 */
	public function actionGet()
	{
		$keyOrConstant = $this->_getQueryParameter('key');
		$keyIsConstant = defined($keyOrConstant);
		if($keyIsConstant)
		{
			return MainFactory::create('HttpControllerResponse', constant($keyOrConstant));
		}
		
		return MainFactory::create('HttpControllerResponse', gm_get_conf($this->_getQueryParameter('key')));
	}
	
	
	/**
	 * Set a the provided configuration value for the provided key.
	 *
	 * @return HttpControllerResponse
	 */
	public function actionSet()
	{
		gm_set_conf($this->_getPostData('key'), $this->_getPostData('value'));
		
		return MainFactory::create('HttpControllerResponse', $this->_getPostData('value'));
	}
}
