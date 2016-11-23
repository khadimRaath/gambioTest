<?php
/* --------------------------------------------------------------
  JanolawModuleCenterModuleController.inc.php 2015-09-14
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2015 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
*/

/**
 * Class JanolawModuleCenterModuleController
 * @extends    AbstractModuleCenterModuleController
 * @category   System
 * @package    Modules
 * @subpackage Controllers
 */
class JanolawModuleCenterModuleController extends AbstractModuleCenterModuleController
{
	/**
	 * @var CI_DB_query_builder
	 */
	protected $db;

	/**
	 * @var array $configurationKeys
	 */
	protected $configurationKeys = array();


	protected function _init()
	{
		$gxCoreLoader = MainFactory::create('GXCoreLoader', MainFactory::create('GXCoreLoaderSettings'));
		$this->db     = $gxCoreLoader->getDatabaseQueryBuilder();

		$this->configurationKeys = array(
			'MODULE_GAMBIO_JANOLAW_STATUS',
			'MODULE_GAMBIO_JANOLAW_USER_ID',
			'MODULE_GAMBIO_JANOLAW_SHOP_ID',
			'MODULE_GAMBIO_JANOLAW_USE_IN_PDF'
		);

		$this->redirectUrl = xtc_href_link('gm_janolaw.php');

		$this->pageTitle = $this->languageTextManager->get_text('janolaw_title');
	}


	/**
	 * Returns an AdminPageHttpControllerResponse with the janolaw configuration template
	 *
	 * @return AdminPageHttpControllerResponse
	 */
	public function actionConfig()
	{
		$this->contentView->set_template_dir(DIR_FS_ADMIN . 'html/content/module_center/');

		$versionInfo = array(
			'version' => 0,
			'multilang' => false,
		);
		if(MODULE_GAMBIO_JANOLAW_STATUS !== 'False')
		{
			$janolaw = MainFactory::create('GMJanolaw');
			$versionInfo = $janolaw->versionCheck();
		}

		$html = $this->_render('janolaw_configuration.html', array(
			'configuration'  => $this->_getConfiguration(),
			'info_page_link' => xtc_href_link('gm_janolaw.php'),
			'version_info' => $versionInfo,
		));

		return MainFactory::create('AdminPageHttpControllerResponse', $this->pageTitle, $html);
	}


	/**
	 * Save janolaw configuration
	 *
	 * @return RedirectHttpControllerResponse
	 */
	public function actionStore()
	{
		$versioninfo_cache_file = DIR_FS_CATALOG.'cache/janolaw-versioninfo.pdc';
		@unlink($versioninfo_cache_file);

		$this->_store($this->_getPostDataCollection());
		$url = xtc_href_link('admin.php', 'do=JanolawModuleCenterModule/Config');

		return MainFactory::create('RedirectHttpControllerResponse', $url);
	}


	/**
	 * Update janolaw configuration in the database
	 *
	 * @param KeyValueCollection $userInputCollection
	 */
	protected function _store(KeyValueCollection $userInputCollection)
	{
		foreach($userInputCollection->getArray() as $configurationKey => $configurationValue)
		{
			$this->db->set('configuration_value', $configurationValue)
			         ->where('configuration_key', $configurationKey)
			         ->update('configuration');
		}
	}


	/**
	 * Loads the janolaw configuration from the database
	 *
	 * @return array $janolawConfiguration
	 */
	protected function _getConfiguration()
	{
		$janolawConfiguration       = array();
		$janolawConfigurationResult = $this->db->select('configuration_key, configuration_value')
		                                       ->from('configuration')
		                                       ->where_in('configuration_key', $this->configurationKeys)
		                                       ->get();
		foreach($janolawConfigurationResult->result() as $row)
		{
			$janolawConfiguration[$row->configuration_key] = $row->configuration_value;
		}

		return $janolawConfiguration;
	}
}