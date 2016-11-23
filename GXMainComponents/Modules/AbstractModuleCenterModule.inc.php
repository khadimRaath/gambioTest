<?php
/* --------------------------------------------------------------
  AbstractModuleCenterModule.inc.php 2016-02-18
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2016 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
*/

/**
 * Class AbstractModuleCenterModule
 * @implements  ModuleCenterModuleInterface
 * @category    System
 * @package     Modules
 */
abstract class AbstractModuleCenterModule implements ModuleCenterModuleInterface
{
	/**
	 * @var LanguageTextManager $languageTextManager
	 */
	protected $languageTextManager;

	/**
	 * @var CI_DB_query_builder
	 */
	protected $db;

	/**
	 * @var null|bool $isInstalled
	 */
	protected $isInstalled = null;

	/**
	 * @var string $name
	 */
	protected $name = '';

	/**
	 * @var string $title
	 */
	protected $title = '';

	/**
	 * @var string $description
	 */
	protected $description = '';

	/**
	 * @var int $sortOrder
	 */
	protected $sortOrder = 100000;

	/**
	 * @var CacheControl $cacheControl
	 */
	protected $cacheControl;


	/**
	 * @param LanguageTextManager $languageTextManager
	 * @param CI_DB_query_builder $db
	 * @param CacheControl        $cacheControl
	 */
	public function __construct(LanguageTextManager $languageTextManager,
	                            CI_DB_query_builder $db,
	                            CacheControl $cacheControl)
	{
		$this->languageTextManager = $languageTextManager;
		$this->db                  = $db;
		$this->cacheControl        = $cacheControl;

		$this->_init();
	}


	/**
	 * Initialize the module e.g. set title, description, sort order etc.
	 *
	 * Function will be called in the constructor
	 */
	abstract protected function _init();


	/**
	 * Set module name
	 */
	protected function _setModuleName()
	{
		$moduleName = get_called_class();
		$moduleName = substr($moduleName, 0, strpos($moduleName, 'ModuleCenterModule'));

		$this->name = $moduleName;
	}


	/**
	 * Set isInstalled flag
	 */
	protected function _setIsInstalled()
	{
		$configurationKey  = 'MODULE_CENTER_' . strtoupper($this->getName()) . '_INSTALLED';
		$isInstalledResult = $this->db->select('gm_value')
		                              ->from('gm_configuration')
		                              ->where('gm_key', $configurationKey)
		                              ->get();
		$isInstalled       = $isInstalledResult->row();
		$this->isInstalled = (boolean)$isInstalled->gm_value;
	}


	/**
	 * Installs the module
	 */
	public function install()
	{
		$this->isInstalled = true;
		$this->_store(true);
	}


	/**
	 * Uninstalls the module
	 */
	public function uninstall()
	{
		$this->isInstalled = false;
		$this->_store(false);
	}


	/**
	 * Returns true, if the module is installed. Otherwise false is returned.
	 *
	 * @return bool
	 */
	final public function isInstalled()
	{
		if(is_null($this->isInstalled))
		{
			$this->_setIsInstalled();
		}

		return $this->isInstalled;
	}


	/**
	 * Returns the name of the module
	 *
	 * The name is filtered by a regular expression.
	 * Only alphanumeric characters are allowed.
	 *
	 * @return string
	 */
	final public function getName()
	{
		if(empty($this->name))
		{
			$this->_setModuleName();
		}

		return preg_replace('[\W]', '', $this->name);
	}


	/**
	 * Returns the title of the module
	 *
	 * @return string
	 */
	final public function getTitle()
	{
		return substr(strip_tags($this->title), 0, 30);
	}


	/**
	 * Returns the description of the module
	 *
	 * @return string
	 */
	final public function getDescription()
	{
		return substr(strip_tags($this->description, '<br><i><strong><u>'), 0, 500);
	}


	/**
	 * Returns the sort order of the module
	 *
	 * @return double
	 */
	final public function getSortOrder()
	{
		return (double)$this->sortOrder;
	}


	final protected function _store($installed)
	{
		$installed        = (boolean)$installed;
		$configurationKey = 'MODULE_CENTER_' . strtoupper($this->getName()) . '_INSTALLED';

		$this->db->set('gm_key', $configurationKey);
		$this->db->set('gm_value', (int)$installed);
		$this->db->where('gm_key', $configurationKey);
		$this->db->replace('gm_configuration');

		$this->_clearCache();
	}


	/**
	 * Empty modules cache after module installation
	 */
	final protected function _clearCache()
	{
		$this->cacheControl->clear_data_cache();
	}
}