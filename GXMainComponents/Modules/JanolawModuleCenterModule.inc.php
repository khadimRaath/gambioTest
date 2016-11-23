<?php
/* --------------------------------------------------------------
  JanolawModuleCenterModule.inc.php 2015-09-14
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2015 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
*/

/**
 * Class JanolawModuleCenterModule
 *
 * @extends    AbstractModuleCenterModule
 * @category   System
 * @package    Modules
 */
class JanolawModuleCenterModule extends AbstractModuleCenterModule
{
	/**
	 * @var array $configurationKeys
	 */
	protected $configurationKeys = array();


	protected function _init()
	{
		$this->title       = $this->languageTextManager->get_text('janolaw_title');
		$this->description = $this->languageTextManager->get_text('janolaw_description');
		$this->sortOrder   = 68184;

		$this->configurationKeys = array(
			'MODULE_GAMBIO_JANOLAW_STATUS',
			'MODULE_GAMBIO_JANOLAW_USER_ID',
			'MODULE_GAMBIO_JANOLAW_SHOP_ID',
			'MODULE_GAMBIO_JANOLAW_USE_IN_PDF'
		);
	}


	/**
	 * Installs the module
	 */
	public function install()
	{
		parent::install();

		foreach($this->_getDefaultConfigurationData() as $configuration)
		{
			$this->db->insert('configuration', $configuration);
		}
	}


	/**
	 * Uninstalls the module
	 */
	public function uninstall()
	{
		parent::uninstall();

		$this->db->where_in('configuration_key', $this->configurationKeys)->delete('configuration');
	}


	/**
	 * Get array of default janolaw configuration
	 *
	 * @return array
	 */
	protected function _getDefaultConfigurationData()
	{
		return array(
			array(
				'configuration_key'      => 'MODULE_GAMBIO_JANOLAW_SHOP_ID',
				'configuration_value'    => '12345',
				'configuration_group_id' => '6',
				'sort_order'             => '1',
				'set_function'           => '',
				'date_added'             => 'NOW()'
			),
			array(
				'configuration_key'      => 'MODULE_GAMBIO_JANOLAW_USER_ID',
				'configuration_value'    => '12345',
				'configuration_group_id' => '6',
				'sort_order'             => '1',
				'set_function'           => '',
				'date_added'             => 'NOW()'
			),
			array(
				'configuration_key'      => 'MODULE_GAMBIO_JANOLAW_STATUS',
				'configuration_value'    => 'True',
				'configuration_group_id' => '6',
				'sort_order'             => '1',
				'set_function'           => '',
				'date_added'             => 'NOW()'
			),
			array(
				'configuration_key'      => 'MODULE_GAMBIO_JANOLAW_USE_IN_PDF',
				'configuration_value'    => 'True',
				'configuration_group_id' => '6',
				'sort_order'             => '2',
				'set_function'           => '',
				'date_added'             => 'NOW()'
			)
		);
	}
}