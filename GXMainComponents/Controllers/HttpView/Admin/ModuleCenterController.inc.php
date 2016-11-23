<?php
/* --------------------------------------------------------------
   ModuleCenterController.inc.php 2016-03-24
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('AdminHttpViewController');

/**
 * Class ModuleCenterController
 *
 * @extends    AdminHttpViewController
 * @category   System
 * @package    AdminHttpViewControllers
 */
class ModuleCenterController extends AdminHttpViewController
{
	/**
	 * @var LanguageTextManager $languageTextManager
	 */
	protected $languageTextManager;


	/**
	 * @param HttpContextReaderInterface     $httpContextReader
	 * @param HttpResponseProcessorInterface $httpResponseProcessor
	 * @param ContentViewInterface           $contentView
	 */
	public function __construct(HttpContextReaderInterface $httpContextReader,
	                            HttpResponseProcessorInterface $httpResponseProcessor,
	                            ContentViewInterface $contentView)
	{
		parent::__construct($httpContextReader, $httpResponseProcessor, $contentView);

		$this->languageTextManager = MainFactory::create('LanguageTextManager', 'module_center');
	}


	/**
	 * Returns the Module Center Page
	 *
	 * @return HttpControllerResponse|RedirectHttpControllerResponse
	 */
	public function actionDefault()
	{
		$pageTitle = $this->languageTextManager->get_text('page_title');

		$this->contentView->set_template_dir(DIR_FS_ADMIN . 'html/content/module_center/');
		$html = $this->_render('module_center.html', array(
			'modules' => $this->_getModulesCollection(),
		));

		return MainFactory::create('AdminPageHttpControllerResponse', $pageTitle, $html, null, array('module_center'));
	}


	/**
	 * @return JsonHttpControllerResponse
	 */
	public function actionGetData()
	{
		$module = $this->_findModule($this->_getQueryParameter('module'));

		if($module !== null)
		{
			$payload = array(
				'title'       => $module->getTitle(),
				'name'        => $module->getName(),
				'description' => $module->getDescription(),
				'isInstalled' => $module->isInstalled()
			);

			$response = array('success' => true, 'payload' => $payload);
		}
		else
		{
			$response = array('success' => false);
		}

		return MainFactory::create('JsonHttpControllerResponse', $response);
	}


	/**
	 * Install module
	 *
	 * @return RedirectHttpControllerResponse
	 */
	public function actionStore()
	{
		$module = $this->_findModule($this->_getPostData('module'));
		$url    = xtc_href_link('admin.php', 'do=ModuleCenter');

		if($module !== null)
		{
			$customModuleUrl = $module->install();
			if(xtc_not_null($customModuleUrl))
			{
				$url = $customModuleUrl;
			}
			else
			{
				$url = xtc_href_link('admin.php', 'do=ModuleCenter&module=' . $module->getName());
			}
		}

		return MainFactory::create('RedirectHttpControllerResponse', $url);
	}


	/**
	 * Uninstall module
	 *
	 * @return RedirectHttpControllerResponse
	 */
	public function actionDestroy()
	{
		$module = $this->_findModule($this->_getPostData('module'));
		$url    = xtc_href_link('admin.php', 'do=ModuleCenter');

		if($module !== null)
		{
			$module->uninstall();
			$url = xtc_href_link('admin.php', 'do=ModuleCenter&module=' . $module->getName());
		}

		return MainFactory::create('RedirectHttpControllerResponse', $url);
	}


	/**
	 * @param string $p_moduleName
	 *
	 * @return ModuleCenterModuleInterface|null
	 */
	protected function _findModule($p_moduleName)
	{
		$module = null;

		if(!empty($p_moduleName))
		{
			$moduleName = basename($p_moduleName);

			$languageTextManager = MainFactory::create('LanguageTextManager', 'module_center_module');
			$gxCoreLoader        = MainFactory::create('GXCoreLoader', MainFactory::create('GXCoreLoaderSettings'));
			$db                  = $gxCoreLoader->getDatabaseQueryBuilder();
			$cacheControl        = MainFactory::create_object('CacheControl');

			/**
			 * @var ModuleCenterModuleInterface $module
			 */
			$module = MainFactory::create($moduleName . 'ModuleCenterModule', $languageTextManager, $db, $cacheControl);
		}

		return $module;
	}


	/**
	 * @return ModuleCenterModuleCollection
	 */
	protected function _getModulesCollection()
	{
		$modules      = array();
		$modulesIndex = array();
		$collection   = MainFactory::create('ModuleCenterModuleCollection');

		$mainModuleFiles = glob(DIR_FS_CATALOG . 'GXMainComponents/Modules/*ModuleCenterModule.inc.php');
		$userModuleFiles = glob(DIR_FS_CATALOG . 'GXUserComponents/modules/**/*ModuleCenterModule.inc.php');
		if(is_array($userModuleFiles) == false) $userModuleFiles = array();
		$moduleFiles = array_merge($mainModuleFiles, $userModuleFiles); 
		
		$gxCoreLoader        = MainFactory::create('GXCoreLoader', MainFactory::create('GXCoreLoaderSettings'));
		$db                  = $gxCoreLoader->getDatabaseQueryBuilder();
		$languageTextManager = MainFactory::create('LanguageTextManager', 'module_center_module');
		$cacheControl        = MainFactory::create_object('CacheControl');
		
		if(is_array($moduleFiles))
		{
			foreach($moduleFiles as $file)
			{
				$moduleName = strtok(basename($file), '.');

				if($moduleName === 'AbstractModuleCenterModule')
				{
					continue;
				}

				$module                    = MainFactory::create($moduleName, $languageTextManager, $db, $cacheControl);
				$modules[$moduleName]      = $module;
				$modulesIndex[$moduleName] = $module->getSortOrder();
			}

			asort($modulesIndex, SORT_NUMERIC);
		}

		foreach($modulesIndex as $moduleName => $module)
		{
			$collection->add($modules[$moduleName]);
		}

		return $collection;
	}
}