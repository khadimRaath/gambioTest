<?php
/* --------------------------------------------------------------
  NewsletterLinkModuleCenterModuleController.inc.php 2016-02-18
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2016 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
*/

/**
 * Class NewsletterLinkModuleCenterModuleController
 * @extends    AbstractModuleCenterModuleController
 * @category   System
 * @package    Modules
 * @subpackage Controllers
 */
class NewsletterLinkModuleCenterModuleController extends AbstractModuleCenterModuleController
{
	protected function _init()
	{
		$this->pageTitle   = $this->languageTextManager->get_text('newsletter_link_title');
		$this->redirectUrl = xtc_href_link('module_newsletter.php');
	}
}