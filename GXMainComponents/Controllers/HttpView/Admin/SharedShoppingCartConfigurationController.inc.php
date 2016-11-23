<?php
/* --------------------------------------------------------------
   SharedShoppingCartConfigurationController.inc.php 2016-04-07 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('HttpViewController');

/**
 * Class SharedShoppingCartConfigurationController
 *
 * @category System
 * @package HttpViewControllers
 */
class SharedShoppingCartConfigurationController extends HttpViewController
{
    /**
     * @var LanguageTextManager $languageTextManager
     */
    protected $languageTextManager;

    /**
     * @var int $defaultLifePeriod
     */
    protected $defaultLifePeriod = 365;

    /**
     * Initializes the controller
     *
     * @param HttpContextInterface $httpContext
     * @throws LogicException
     */
    public function proceed(HttpContextInterface $httpContext)
    {
        $this->contentView->set_template_dir(DIR_FS_ADMIN . 'html/content/module_center/');
        parent::proceed($httpContext); // proceed http context from parent class
    }
    
    public function actionDefault()
    {
        $this->languageTextManager = MainFactory::create('LanguageTextManager', 'shared_shopping_cart_configuration', (int)$_SESSION['languages_id']);
        $lifePeriod = $this->_getShoppingCartLifePeriod();
        
        $html = $this->_render('shared_shopping_cart_configuration.html',
            array(
                'life_period' => $lifePeriod
            ));
        
        return new AdminPageHttpControllerResponse($this->languageTextManager->get_text('shared_shopping_cart_configuration_title'), $html);
    }

    public function actionStore()
    {
        $this->_storeLifePeriod(new IntType($this->_getPostData('life_period')));
        return new RedirectHttpControllerResponse(xtc_href_link('admin.php', 'do=SharedShoppingCartConfiguration'));
    }

    protected function _getShoppingCartLifePeriod()
    {
        $lifePeriod = gm_get_conf('SHARED_SHOPPING_CART_LIFE_PERIOD');
        if($lifePeriod === null)
        {
            $lifePeriod = $this->defaultLifePeriod;
            $this->_storeLifePeriod(new IntType((int)$lifePeriod));
        }
        $lifePeriod = (int)$lifePeriod;
        
        return $lifePeriod;
    }

    protected function _storeLifePeriod(IntType $lifePeriod)
    {
        gm_set_conf('SHARED_SHOPPING_CART_LIFE_PERIOD', $lifePeriod->asInt());
    }
}