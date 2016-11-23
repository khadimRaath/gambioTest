<?php
/* --------------------------------------------------------------
  MagnalisterModuleCenterModule.inc.php 2015-09-24
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2015 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
*/

/**
 * Class MagnalisterModuleCenterModule
 *
 * @extends    AbstractModuleCenterModule
 * @category   System
 * @package    Modules
 */
class MagnalisterModuleCenterModule extends AbstractModuleCenterModule
{
	protected function _init()
	{
		$this->title       = $this->languageTextManager->get_text('magnalister_title');
		$this->description = $this->languageTextManager->get_text('magnalister_description');
		$this->sortOrder   = 13490;
	}


	/**
	 * Installs the module
	 */
	public function install()
	{
		parent::install();

		$this->magnalisterOrderColumn(true);
		$columnsQuery = $this->db->query('DESCRIBE `admin_access` \'magnalister\'');
		if(!$columnsQuery->num_rows())
		{
			$this->db->query('ALTER TABLE `admin_access` ADD `magnalister` INT( 1 ) NOT NULL DEFAULT \'0\';');
		}

		$this->db->set('magnalister', '1')->where('customers_id', '1')->limit(1)->update('admin_access');
		$this->db->set('magnalister', '1')
		         ->where('customers_id', $_SESSION['customer_id'])
		         ->limit(1)
		         ->update('admin_access');

		$this->db->insert('configuration', array(
			'configuration_key'      => 'MODULE_MAGNALISTER_STATUS',
			'configuration_value'    => 'True',
			'configuration_group_id' => '6',
			'sort_order'             => '1',
			'set_function'           => '',
			'date_added'             => 'NOW()'
		));
		return xtc_href_link('admin.php', 'do=EmbeddedModule/magnalister&update=true');
	}
	
	
	/**
	 * Adds or removes the magnalister column in the orders overview.
	 *
	 * @deprecated This method should be replaced when Gambio provides a generic service method.
	 *             https://tracker.gambio-server.net/issues/48136
	 *
	 * @param bool $blInstall Flag for adding (true) or removing (false) the magnalister column in user_configuration
	 *                        table.
	 */
	protected function magnalisterOrderColumn ($blInstall) {
		$userConfigurationService = StaticGXCoreLoader::getService('UserConfiguration');
		$activeColumns = json_decode(str_replace('\\"', '"', $userConfigurationService->getUserConfiguration(new IdType(1), 'ordersOverviewSettingsColumns')), true);
		if (!empty($activeColumns) && is_array($activeColumns)) {
			$magnaActiveColumns = array();
			if ($blInstall) { // add magnalister column
				foreach ($activeColumns as $iColumn => $sColumn) {
					if ($sColumn == 'magnalister') {// already setted
						$magnaActiveColumns = array();
						break;
					}
					$magnaActiveColumns[] = $sColumn;
					if ($iColumn == 0) {
						$magnaActiveColumns[] = 'magnalister';
					}
				}
			} else if (in_array('magnalister', $activeColumns)) {// remove magnalister column
				$magnaActiveColumns = $activeColumns;
				unset($magnaActiveColumns[array_search('magnalister', $magnaActiveColumns)]);
				$magnaActiveColumns = array_values($magnaActiveColumns); // rebuild index for clean json array
			}
			if (!empty($magnaActiveColumns)) {
				$userConfigurationService->setUserConfiguration(new IdType(1), 'ordersOverviewSettingsColumns', str_replace('"', '\\"', json_encode($magnaActiveColumns)));
			}
		}
	}

	/**
	 * Uninstalls the module
	 */
	public function uninstall()
	{
		parent::uninstall();
		
		$this->magnalisterOrderColumn(false);
		$this->db->query('ALTER TABLE `admin_access` DROP `magnalister`');
		$this->db->where_in('configuration_key', 'MODULE_MAGNALISTER_STATUS')->delete('configuration');
	}
}