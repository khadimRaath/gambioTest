<?php
/* --------------------------------------------------------------
  SharedShoppingCartModuleCenterModule.inc.php 2016-04-07
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2016 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
*/

/**
 * Class SharedShoppingCartModuleCenterModule
 *
 * @extends    AbstractModuleCenterModule
 * @category   System
 * @package    Modules
 */
class SharedShoppingCartModuleCenterModule extends AbstractModuleCenterModule
{
    /**
     * Initializes the module
     */
    protected function _init()
    {
        $this->title       = $this->languageTextManager->get_text('shared_shopping_cart_title');
        $this->description = $this->languageTextManager->get_text('shared_shopping_cart_description');
    }
}