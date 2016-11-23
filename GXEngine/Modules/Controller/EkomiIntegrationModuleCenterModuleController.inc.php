<?php

/**
 * Class EkomiIntegrationModuleCenterModuleController
 *
 * This is a EkomiIntegration overload for the AbstractModuleCenterModuleController.
 * 
 * @see AbstractModuleCenterModuleController
 * 
 * @extends    AbstractModuleCenterModuleController
 * @category   System
 * @package    Modules
 * @author     Sandor Barics <sbarics@ekomi.de>
 */
class EkomiIntegrationModuleCenterModuleController extends AbstractModuleCenterModuleController {

    /**
     * initializes the class properties
     * 
     * @access protected
     */
    protected function _init() {

        // set the page title
        $this->pageTitle = $this->languageTextManager->get_text('ekomiIntegration_title');

        //set the temolate file
        $this->contentView->set_template_dir(DIR_FS_CATALOG . 'GXEngine/Modules/templates');
    }

    /**
     * Accepts the default actions
     * 
     */
    public function actionDefault() {

        $message = '';

        $action = xtc_href_link('admin.php', 'do=' . $_GET['do'] . '&action=save');

        if (isset($_GET['action']) && $_GET['action'] == 'save') {
            $message = $this->_save();

            xtc_redirect(xtc_href_link('admin.php', 'do=' . $_GET['do']));
        } else {
            $message = $_SESSION['flashMessage'];
            $messageType = $_SESSION['messageType'];
            $_SESSION['flashMessage'] = '';
            $_SESSION['messageType'] = '';
        }

        $orderStatuses = xtc_get_orders_status();

        $templateData = array(
            'lang' => $this->languageTextManager,
            'message' => $message,
            'messageType' => $messageType,
            'orderStatuses' => $orderStatuses,
            'actionLink' => $action,
            'hiddenToken' => xtc_draw_hidden_field('page_token', $_SESSION['coo_page_token']->generate_token())
        );

        $content = $this->_render('ekomiIntegration_configuration.html', $templateData);

        return new AdminPageHttpControllerResponse($this->pageTitle, $content);
    }

    /**
     * Saves or updates the configuration settings
     * 
     * @return String The message either saved or not
     * 
     * @access protected
     */
    protected function _save() {

        if ($_SESSION['coo_page_token']->is_valid($_POST['page_token'])) {

            // set the product reviews anabled or not
            if (isset($_POST['productBase'])) {
                gm_set_conf('EKOMIINTEGRATION_PRODUCT_BASE', gm_prepare_string(trim($_POST['productBase'])));
            }

            // Save multiple order states comma separated
            if (isset($_POST['orderStatuses'])) {
                $statuses = $_POST['orderStatuses'];
                if (!empty($statuses)) {
                    $statuses = implode(',', $statuses);
                }
                gm_set_conf('EKOMIINTEGRATION_ORDER_STATUSES', $statuses);
            }

            if (isset($_POST['reviewMod'])) {
                gm_set_conf('EKOMIINTEGRATION_REVIEW_MOD', gm_prepare_string(trim($_POST['reviewMod'])));
            }

            if (!empty($_POST['shopId']) && isset($_POST['shopPassword'])) {
                $shopId = (int) (trim($_POST['shopId']));
                $shopPassword = gm_prepare_string(trim($_POST['shopPassword']));

                $ekomiIntegrationManager = MainFactory::create_object('EkomiIntegrationManager', array($shopId, $shopPassword));

                if ($ekomiIntegrationManager->validateShop()) {
                    gm_set_conf('EKOMIINTEGRATION_SHOPID', $shopId);
                    gm_set_conf('EKOMIINTEGRATION_SHOPPWD', $shopPassword);

                    // set the module enable config
                    if (isset($_POST['enable'])) {
                        gm_set_conf('EKOMIINTEGRATION_ENABLE', gm_prepare_string(trim($_POST['enable'])));
                    }

                    //set the notify message
                    $_SESSION['flashMessage'] = $this->languageTextManager->get_text('ekomiIntegration_config_saved');
                    $_SESSION['messageType'] = 'success';
                } else {
                    //set the config
                    gm_set_conf('EKOMIINTEGRATION_SHOPID', '');
                    gm_set_conf('EKOMIINTEGRATION_SHOPPWD', '');
                    gm_set_conf('EKOMIINTEGRATION_ENABLE', '');

                    //set the notify message
                    $_SESSION['flashMessage'] = $this->languageTextManager->get_text('ekomiIntegration_config_invalid');
                    $_SESSION['messageType'] = 'error';
                }
            } else {
                //set the notify message
                $_SESSION['flashMessage'] = $this->languageTextManager->get_text('ekomiIntegration_config_required');
                $_SESSION['messageType'] = 'error';
            }
        } else {
            //set the notify message
            $_SESSION['flashMessage'] = $this->languageTextManager->get_text('ekomiIntegration_invalid_token');
            $_SESSION['messageType'] = 'error';
        }
    }

}
