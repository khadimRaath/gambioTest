<?php
/* --------------------------------------------------------------
  ProductAttributesModuleCenterModule.inc.php 2015-09-14
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2015 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
*/

/**
 * Class ProductAttributesModuleCenterModule
 *
 * @extends    AbstractModuleCenterModule
 * @category   System
 * @package    Modules
 */
class ProductAttributesModuleCenterModule extends AbstractModuleCenterModule
{
	protected function _init()
	{
		$this->title       = $this->languageTextManager->get_text('product_attributes_title');
		$this->description = $this->languageTextManager->get_text('product_attributes_description');
		$this->sortOrder   = 56365;
	}
}