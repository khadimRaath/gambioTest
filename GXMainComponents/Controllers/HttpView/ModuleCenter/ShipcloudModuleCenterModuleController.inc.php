<?php
/* --------------------------------------------------------------
	ShipcloudModuleCenterModuleController.inc.php 2016-06-07
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2015 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/

/**
 * Controller for shipcloud configuration
 *
 * @extends    AbstractModuleCenterModuleController
 * @category   System
 * @package    Modules
 * @subpackage Controllers
 */
class ShipcloudModuleCenterModuleController extends AbstractModuleCenterModuleController
{
	/**
	 * @var CI_DB_query_builder
	 */
	protected $db;

	/**
	 * @var ShipcloudText
	 */
	protected $shipcloudText;

	/**
	 * @var ShipcloudConfigurationStorage
	 */
	protected $shipcloudConfigurationStorage;


	protected function _init()
	{
		$this->shipcloudText                 = MainFactory::create('ShipcloudText');
		$this->shipcloudConfigurationStorage = MainFactory::create('ShipcloudConfigurationStorage');
		$this->pageTitle                     = $this->shipcloudText->get_text('configuration_heading');
		$gxCoreLoader                        = MainFactory::create('GXCoreLoader',
		                                                           MainFactory::create('GXCoreLoaderSettings'));
		$this->db                            = $gxCoreLoader->getDatabaseQueryBuilder();
	}


	public function actionDefault()
	{
		$userConfigurationService = StaticGXCoreLoader::getService('UserConfiguration');
		$userId                   = new IdType((int)$_SESSION['customer_id']);

		if($this->shipcloudConfigurationStorage->get('mode') == 'sandbox')
		{
			$GLOBALS['messageStack']->add($this->shipcloudText->get_text('warning_sandbox'), 'warning');
		}

		$parcelServiceReader = MainFactory::create('ParcelServiceReader');
		$parcelServices      = $parcelServiceReader->getAllParcelServices();

		$formdata = array(
			'form_action' => xtc_href_link('admin.php', 'do=ShipcloudModuleCenterModule/SaveConfiguration'),
			'user_id'     => $userId,
			'collapsed'   => array(
				'credentials'   => $userConfigurationService->getUserConfiguration($userId, 'shipcloud_config_credentials_collapse'),
				'misc_settings' => $userConfigurationService->getUserConfiguration($userId, 'shipcloud_config_misc_settings_collapse'),
			),
			'configuration' => array(
				'mode'                     => $this->shipcloudConfigurationStorage->get('mode'),
				'api_key_sandbox'          => $this->shipcloudConfigurationStorage->get('api-key/sandbox'),
				'api_key_live'             => $this->shipcloudConfigurationStorage->get('api-key/live'),
				'debug_logging'            => $this->shipcloudConfigurationStorage->get('debug_logging'),
				'from_company'             => $this->shipcloudConfigurationStorage->get('from/company'),
				'from_first_name'          => $this->shipcloudConfigurationStorage->get('from/first_name'),
				'from_last_name'           => $this->shipcloudConfigurationStorage->get('from/last_name'),
				'from_street'              => $this->shipcloudConfigurationStorage->get('from/street'),
				'from_street_no'           => $this->shipcloudConfigurationStorage->get('from/street_no'),
				'from_city'                => $this->shipcloudConfigurationStorage->get('from/city'),
				'from_zip_code'            => $this->shipcloudConfigurationStorage->get('from/zip_code'),
				'from_country'             => $this->shipcloudConfigurationStorage->get('from/country'),
				'from_phone'               => $this->shipcloudConfigurationStorage->get('from/phone'),
				'cod_bank_account_holder'  => $this->shipcloudConfigurationStorage->get('cod-account/bank_account_holder'),
				'cod_bank_name'            => $this->shipcloudConfigurationStorage->get('cod-account/bank_name'),
				'cod_bank_account_number'  => $this->shipcloudConfigurationStorage->get('cod-account/bank_account_number'),
				'cod_bank_code'            => $this->shipcloudConfigurationStorage->get('cod-account/bank_code'),
				'packages'                 => $this->shipcloudConfigurationStorage->get_all_tree('packages'),
				'parcel_service_id'        => $this->shipcloudConfigurationStorage->get('parcel_service_id'),
				'order_status_after_label' => $this->shipcloudConfigurationStorage->get('order_status_after_label'),
				'notify_customer'          => $this->shipcloudConfigurationStorage->get('notify_customer'),
			),
			'boarding_url'         => $this->shipcloudConfigurationStorage->get('boarding_url'),
			'parcel_services'      => $parcelServices,
			'preselected_carriers' => $this->shipcloudConfigurationStorage->get_all_tree('preselected_carriers'),
			'checked_carriers'     => $this->shipcloudConfigurationStorage->get_all_tree('checked_carriers'),
			'orders_statuses'      => $this->getOrdersStatuses(),
			'tab_urls'             => array(
				'default'           => xtc_href_link('admin.php', 'do=ShipcloudModuleCenterModule'),
				'package_templates' => xtc_href_link('admin.php', 'do=ShipcloudModuleCenterModule/PackageTemplates'),
			),
		);

		$carriersCache = MainFactory::create('ShipcloudCarriersCache');
		$formdata['carriers'] = $carriersCache->getCarriers();

		$this->contentView->set_template_dir(DIR_FS_ADMIN . 'html/content/module_center/');
		$html = $this->_render('shipcloud_configuration.html', $formdata);
		$html = $this->shipcloudText->replaceLanguagePlaceholders($html);

		return MainFactory::create('AdminPageHttpControllerResponse', $this->shipcloudText->get_text('configuration_heading'), $html);
	}


	/**
	 * Displays package templates configuration
	 * @return AdminPageHttpControllerResponse
	 */
	public function actionPackageTemplates()
	{
		$packages         = $this->shipcloudConfigurationStorage->get_all_tree('packages');
		$configurationBox = $this->_getConfigurationBox();

		$formdata = array(
			'configuration'     => array(
				'packages'        => $packages,
				'default_package' => $this->shipcloudConfigurationStorage->get('default_package'),
			),
			'tab_urls'          => array(
				'default'           => xtc_href_link('admin.php', 'do=ShipcloudModuleCenterModule'),
				'package_templates' => xtc_href_link('admin.php', 'do=ShipcloudModuleCenterModule/PackageTemplates'),
			),
			'configuration_box' => $configurationBox
		);

		$this->contentView->set_template_dir(DIR_FS_ADMIN . 'html/content/module_center/');
		$html = $this->_render('shipcloud_configuration_package_templates.html', $formdata);
		$html = $this->shipcloudText->replaceLanguagePlaceholders($html);

		return MainFactory::create('AdminPageHttpControllerResponse', $this->shipcloudText->get_text('configuration_heading'), $html);
	}


	/**
	 * Returns HTML for package template ConfigurationBox
	 * @return string
	 */
	protected function _getConfigurationBox()
	{
		$heading = '';
		$this->contentView->set_template_dir(DIR_FS_ADMIN . 'html/content/module_center/');
		$contents       = $this->_render('shipcloud_package_template_configuration_box.html', array());
		$formAttributes = array();
		$buttons        = '<div class="button-set detail-buttons"><button class="btn delete-package-template" onClick="this.blur(); return false;">'
		                  . BUTTON_DELETE . '</button>'
		                  . '<button class="btn btn-primary edit-package-template" onClick="this.blur(); return false;">'
		                  . BUTTON_EDIT . '</button></div>'
		                  . '<div class="button-set form-data-buttons hidden"><button class="btn btn-cancel cancel-package-template" onClick="this.blur(); return false;">'
		                  . BUTTON_CANCEL . '</button>' . '<button class="btn btn-primary save-package-template">'
		                  . BUTTON_SAVE . '</button></div>'
		                  . '<div class="button-set create-form-data-buttons hidden"><button class="btn btn-primary save-package-template">'
		                  . BUTTON_SAVE . '</button></div>'
		                  . '<div class="button-set confirm-delete-buttons hidden"><button class="btn btn-primary confirm-delete-package-template">'
		                  . BUTTON_DELETE . '</button>'
		                  . '<button class="btn btn-cancel cancel-package-template" onClick="this.blur(); return false;">'
		                  . BUTTON_CANCEL . '</button></div>';
		$formIsEditable = '';
		$formAction     = xtc_href_link('admin.php', 'do=ShipcloudModuleCenterModule/SavePackageTemplates');

		$configurationBoxContentView = MainFactory::create_object('ConfigurationBoxContentView');
		$configurationBoxContentView->set_content_data('heading', $heading);
		$configurationBoxContentView->set_content_data('form', $contents);
		$configurationBoxContentView->setFormAttributes($formAttributes);
		$configurationBoxContentView->set_content_data('buttons', $buttons);
		$configurationBoxContentView->setFormEditable($formIsEditable);
		$configurationBoxContentView->setFormAction($formAction);
		$configurationBox = $configurationBoxContentView->get_html();

		return $configurationBox;
	}


	/**
	 * Returns configuration of a package template as identified by the templateId GET parameter
	 * @return JsonHttpControllerResponse
	 */
	public function actionGetPackageTemplate()
	{
		$templateId                    = (int)$this->_getQueryParameter('templateId');
		$packageTemplate               = $this->_getPackageTemplateData($templateId);
		$packageTemplate['is_default'] = $templateId == $this->shipcloudConfigurationStorage->get('default_package');

		return MainFactory::create('JsonHttpControllerResponse', $packageTemplate);
	}


	/**
	 * Deletes a package template as identified by the templateId GET parameter and redirects back to package template configuration
	 * @return RedirectHttpControllerResponse
	 */
	public function actionDeletePackageTemplate()
	{
		$templateId = (int)$this->_getQueryParameter('templateId');
		$this->shipcloudConfigurationStorage->delete_all('packages/' . $templateId);

		return MainFactory::create('RedirectHttpControllerResponse', xtc_href_link('admin.php',
		                                                        'do=ShipcloudModuleCenterModule/PackageTemplates'));;
	}


	/**
	 * Returns template configuration
	 * @param  int
	 * @return array
	 */
	protected function _getPackageTemplateData($templateId)
	{
		return $this->shipcloudConfigurationStorage->get_all('packages/' . $templateId);
	}


	/**
	 * Retrieves a array of order statuses (ids and names as per current session language)
	 * @return array
	 */
	protected function getOrdersStatuses()
	{
		$this->db->where(array('language_id' => $_SESSION['languages_id']));
		$this->db->order_by('orders_status_name ASC');
		$orders_statuses_query = $this->db->get('orders_status');
		$orders_statuses       = $orders_statuses_query->result();

		return $orders_statuses;
	}


	/**
	 * saves package template configuration
	 * @return RedirectHttpControllerResponse
	 */
	public function actionSavePackageTemplates()
	{
		$package = $this->_getPostData('package');
		if(empty($package['id']))
		{
			$package['id'] = $this->shipcloudConfigurationStorage->getMaximumPackageTemplateId() + 1;
		}
		$this->shipcloudConfigurationStorage->set('packages/' . (int)$package['id'] . '/name', $package['name']);
		$this->shipcloudConfigurationStorage->set('packages/' . (int)$package['id'] . '/weight', $package['weight']);
		$this->shipcloudConfigurationStorage->set('packages/' . (int)$package['id'] . '/length', $package['length']);
		$this->shipcloudConfigurationStorage->set('packages/' . (int)$package['id'] . '/width', $package['width']);
		$this->shipcloudConfigurationStorage->set('packages/' . (int)$package['id'] . '/height', $package['height']);

		$defaultPackageTemplate = $this->_getPostData('default_template');
		if($defaultPackageTemplate !== null)
		{
			$this->shipcloudConfigurationStorage->set('default_package', (int)$defaultPackageTemplate);
		}

		return MainFactory::create('RedirectHttpControllerResponse', xtc_href_link('admin.php',
		                                                        'do=ShipcloudModuleCenterModule/PackageTemplates'));
	}


	/**
	 * saves configuration values
	 * @return RedirectHttpControllerResponse
	 */
	public function actionSaveConfiguration()
	{
		$newConfiguration = $this->_getPostData('configuration');
		foreach($newConfiguration as $key => $value)
		{
			$this->shipcloudConfigurationStorage->set($key, $value);
		}
		$preselectionCarriers = $this->_getPostData('preselected_carriers') ?: [];
		$checkedCarriers      = $this->_getPostData('checked_carriers') ?: [];
		$carriers             = $this->shipcloudConfigurationStorage->getCarriers();
		foreach($carriers as $carrier)
		{
			$carrier_selected = in_array($carrier, $preselectionCarriers);
			$this->shipcloudConfigurationStorage->set('preselected_carriers/' . $carrier,
			                                          $carrier_selected ? '1' : '0');
			$carrier_checked = in_array($carrier, $checkedCarriers);
			$this->shipcloudConfigurationStorage->set('checked_carriers/' . $carrier, $carrier_checked ? '1' : '0');
		}
		$GLOBALS['messageStack']->add_session($this->shipcloudText->get_text('configuration_saved'), 'info');

		return MainFactory::create('RedirectHttpControllerResponse', xtc_href_link('admin.php', 'do=ShipcloudModuleCenterModule'));
	}
}