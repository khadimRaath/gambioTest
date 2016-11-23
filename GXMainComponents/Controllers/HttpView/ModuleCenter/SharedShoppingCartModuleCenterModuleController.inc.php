<?php
/* --------------------------------------------------------------
   SharedShoppingCartModuleCenterModuleController.inc.php 2016-07-13
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class SharedShoppingCartModuleCenterModuleController
 * 
 * @category   System
 * @package    Modules
 * @subpackage Controllers
 */
class SharedShoppingCartModuleCenterModuleController extends AbstractModuleCenterModuleController
{
    /**
     * Redirects to the configuration page of the module
     */
    protected function _init()
    {
        $this->redirectUrl = xtc_href_link('admin.php', 'do=SharedShoppingCartConfiguration');
    }
}