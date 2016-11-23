<?php
/* --------------------------------------------------------------
   NewsletterLinkModuleCenterModule.inc.php 2016-02-18
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class NewsletterLinkModuleCenterModule
 * 
 * @extends    AbstractModuleCenterModule
 * @category   System
 * @package    Modules
 */
class NewsletterLinkModuleCenterModule extends AbstractModuleCenterModule
{
	protected function _init()
	{
		$this->title       = $this->languageTextManager->get_text('newsletter_link_title');
		$this->description = $this->languageTextManager->get_text('newsletter_link_description');
		$this->sortOrder   = 63131;
	}
}