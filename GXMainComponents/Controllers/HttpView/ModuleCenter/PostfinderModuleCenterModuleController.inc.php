<?php
/* --------------------------------------------------------------
	PostfinderModuleCenterModuleController.inc.php 2016-04-18
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2015 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/

/**
 * Class PostfinderModuleCenterModuleController
 * @extends    AbstractModuleCenterModuleController
 * @category   System
 * @package    Modules
 * @subpackage Controllers
 */
class PostfinderModuleCenterModuleController extends AbstractModuleCenterModuleController
{
	protected $text;

	protected function _init()
	{
		$this->text        = MainFactory::create('LanguageTextManager', 'postfinder', $_SESSION['languages_id']);
		$this->pageTitle   = $this->text->get_text('module_title');
	}

	public function actionDefault()
	{
		$html = $this->text->get_text('no_configuration');
		return MainFactory::create('AdminPageHttpControllerResponse', $this->text->get_text('configuration_heading'), $html);
	}
}