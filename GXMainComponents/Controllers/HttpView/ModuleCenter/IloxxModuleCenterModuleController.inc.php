<?php
/* --------------------------------------------------------------
  IloxxModuleCenterModuleController.inc.php 2015-09-14
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2015 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
*/

/**
 * Class IloxxModuleCenterModuleController
 * @extends    AbstractModuleCenterModuleController
 * @category   System
 * @package    Modules
 * @subpackage Controllers
 */
class IloxxModuleCenterModuleController extends AbstractModuleCenterModuleController
{
	protected $languageTextManager;

	protected function _init()
	{
		$this->pageTitle   = $this->languageTextManager->get_text('iloxx_title');
		// $this->redirectUrl = xtc_href_link('iloxx.php');
		$this->languageTextManager = MainFactory::create('LanguageTextManager', 'iloxx', $_SESSION['languages_id']);
		$this->contentView->set_template_dir(DIR_FS_ADMIN . 'html/content/module_center/');
	}

	public function actionDefault()
	{
		$content = 'none';
		$title = $this->languageTextManager->get_text('configuration_title');
		$iloxx = MainFactory::create('GMIloxx');
		$parcelServiceReader = MainFactory::create('ParcelServiceReader');
		$templateData = array(
			'page_token'                  => $_SESSION['coo_page_token']->generate_token(),
			'form_action'                 => xtc_href_link('admin.php', 'do=IloxxModuleCenterModule/SaveConfiguration'),
			'form_action_transactionlist' => xtc_href_link('admin.php', 'do=IloxxModuleCenterModule/GetTransactionList'),
			'configuration'               => array(
				'userid'                   => $iloxx->userid,
				'usertoken'                => $iloxx->usertoken,
				'oslabelacquired'          => $iloxx->oslabelacquired,
				'ostracking'               => $iloxx->ostracking,
				'use_weight_options'       => $iloxx->use_weight_options,
				'default_ship_service'     => $iloxx->default_ship_service,
				'default_ship_service_cod' => $iloxx->default_ship_service_cod,
				'parcelservice_id'         => $iloxx->parcelservice_id,
			),
			'orders_status'   => xtc_get_orders_status(),
			'ship_services'   => $iloxx->getShipServices(),
			'parcel_services' => $parcelServiceReader->getAllParcelServices(),
			'gdtl_date'       => date('Y-m-d'),
		);
		$content = $this->_render('iloxx_configuration.html', $templateData);
		return MainFactory::create('AdminPageHttpControllerResponse', $title, $content);
	}

	public function actionSaveConfiguration()
	{
		$_SESSION['coo_page_token']->is_valid($this->_getPostData('page_token'));
		$iloxx = MainFactory::create('GMIloxx');
		$configuration = $this->_getPostData('configuration');
		foreach($configuration as $name => $value)
		{
			$iloxx->$name = $value;
		}
		$redirectUrl = xtc_href_link('admin.php', 'do=IloxxModuleCenterModule');
		return MainFactory::create('RedirectHttpControllerResponse', $redirectUrl);
	}

	public function actionGetTransactionList()
	{
		$_SESSION['coo_page_token']->is_valid($this->_getPostData('page_token'));
		$gdtl_types = array('DPD', 'Grosspaket');
		$gdtl_date = date('Y-m-d', strtotime($this->_getPostData('gdtl_date')));
		$gdtl_type = in_array($this->_getPostData('gdtl_type'), $gdtl_types) ? $this->_getPostData('gdtl_type') : $gdtl_types[0];
		$iloxx = MainFactory::create('GMIloxx');
		$pdfdata = $iloxx->getDailyTransactionList($gdtl_date, $gdtl_type);
		if($pdfdata !== false) {
			header('Content-Type: application/pdf');
			header('Content-Disposition: attachment;filename=tagesabschluss_'.$gdtl_date.'_'.$gdtl_type.'.pdf');
			return MainFactory::create('HttpControllerResponse', $pdfdata);
		}
		else
		{
			return MainFactory::create('AdminPageHttpControllerResponse', 'ERROR', 'Error retrieving transaction list');
		}
	}

}