<?php
/* --------------------------------------------------------------
   CreateGuestController.inc.php 2016-07-13 
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('HttpViewController');

/**
 * Class JsTranslationsController
 * 
 * @extends    HttpViewController
 * @category   System
 * @package    HttpViewControllers
 */
class JsTranslationsController extends HttpViewController
{
	/**
	 * @var LanguageTextManager
	 */
	private $languageTextManager;


	/**
	 * Returns a json encoded language section array.
	 * This method is used by js modules to receive the language values of specific sections.
	 *
	 * Example (Javascript):
	 *  var lang = jse.core.config.get('shopUrl') + '/shop.php?do=JsTranslations&section=shared_shopping_cart'
	 *
	 * The GET-Param 'section' is required.
	 *
	 * @return \JsonHttpControllerResponse
	 */
	public function actionDefault()
	{
		$section = $this->_getQueryParameter('section');
		if(null === $section)
		{
			return new JsonHttpControllerResponse(array('status' => 'error'));
		}

		$this->languageTextManager = MainFactory::create('LanguageTextManager', 'shared_shopping_cart_configuration',
		                                                 (int)$_SESSION['languages_id']);
		$sectionArray              = $this->languageTextManager->get_section_array($section);

		return new JsonHttpControllerResponse($sectionArray);
	}
}