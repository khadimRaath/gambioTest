<?php
/* --------------------------------------------------------------
   AdminLayoutHttpControllerResponse.inc.php 2016-07-26
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('HttpControllerResponse');

/**
 * Class AdminLayoutHttpControllerResponse
 *
 * This class will be used for rendering the new Admin pages which must be explicitly written in
 * templates. These templates can extend any of the existing admin layouts by themselves.
 *
 * Child controllers can you the "init" method to initialize their dependencies
 *
 * @category System
 * @package  Http
 * @extends  HttpControllerResponse
 */
class AdminLayoutHttpControllerResponse extends HttpControllerResponse
{
	/**
	 * Page Title
	 *
	 * @var string
	 */
	protected $title;
	
	/**
	 * Template Path
	 *
	 * @var string
	 */
	protected $template;
	
	/**
	 * Template data.
	 *
	 * @var KeyValueCollection
	 */
	protected $data;
	
	/**
	 * Page Assets
	 *
	 * Provide paths or filenames to JavaScript, CSS or PHP Translation files.
	 *
	 * @var AssetCollectionInterface
	 */
	protected $assets;
	
	/**
	 * Content Sub Navigation
	 *
	 * The sub navigation will be displayed under the header and can redirect to similar pages.
	 *
	 * @var ContentNavigationCollectionInterface
	 */
	protected $contentNavigation;
	
	/**
	 * ContentView instance.
	 *
	 * Used for parsing the Smarty templates.
	 *
	 * @var ContentView
	 */
	protected $contentView;
	
	
	/**
	 * AdminLayoutHttpViewController constructor.
	 *
	 * @param NonEmptyStringType                        $title             Page title.
	 * @param ExistingFile                              $template          Template absolute path.
	 * @param KeyValueCollection|null                   $data              A key-value collection containing the data
	 *                                                                     to be used by the template.
	 * @param AssetCollectionInterface|null             $assets            Page assets (js, css, translations etc).
	 * @param ContentNavigationCollectionInterface|null $contentNavigation Sub content navigation (key-value
	 *                                                                     collection).
	 * @param ContentView|null                          $contentView       Provide a custom content view class if
	 *                                                                     needed.
	 */
	public function __construct(NonEmptyStringType $title,
	                            ExistingFile $template,
	                            KeyValueCollection $data = null,
	                            AssetCollectionInterface $assets = null,
	                            ContentNavigationCollectionInterface $contentNavigation = null,
	                            ContentView $contentView = null)
	{
		$this->title             = $title->asString();
		$this->template          = $template->getFilePath();
		$this->data              = $data;
		$this->assets            = $assets;
		$this->contentNavigation = $contentNavigation;
		$this->contentView       = (!empty($contentView)) ? $contentView : MainFactory::create('ContentView');
		$this->_render();
	}
	
	
	/**
	 * Render the provided template.
	 *
	 * Hint: Override this method to change the rendering algorithm.
	 *
	 * @throws InvalidArgumentException
	 * @throws UnexpectedValueException
	 */
	protected function _render()
	{
		$this->contentView->set_flat_assigns(true);
		$this->contentView->set_escape_html(true);
		$this->contentView->set_template_dir(dirname($this->template));
		$this->contentView->set_content_template(basename($this->template));
		
		// Set content data. 
		if($this->data !== null)
		{
			$content = array(); // Content array
			foreach($this->data->getArray() as $key => $value)
			{
				$content[$key] = $value;
			}
			$this->contentView->set_content_data('content', $content); 
		}
		
		// Set $_SESSION
		$this->contentView->set_content_data('_SESSION', $_SESSION);
		
		// Set Base URL
		$this->contentView->set_content_data('base_url', rtrim(HTTP_SERVER . DIR_WS_CATALOG, '/'));
		
		// Set Environment & File Suffix
		$environment = file_exists(DIR_FS_CATALOG . '.dev-environment') ? 'development' : 'production';
		$this->contentView->set_content_data('environment', $environment);
		$this->contentView->set_content_data('suffix', $environment === 'production' ? '.min' : '');
		
		// Set Template Directory Path 
		$this->contentView->set_content_data('template_dir', DIR_FS_ADMIN . 'html/content');
		
		// Set Page Title
		$this->contentView->set_content_data('page_title', $this->title);
		
		// Set Language Code 
		$this->contentView->set_content_data('language_code', $_SESSION['language_code']);
		
		// Set Shop Version
		$this->contentView->set_content_data('shop_version', gm_get_conf('INSTALLED_VERSION'));

		// Set Shop offline flag
		$this->contentView->set_content_data('shop_offline', gm_get_conf('GM_SHOP_OFFLINE') !== '');
		
		// Set Page Token
		$this->contentView->set_content_data('page_token', $_SESSION['coo_page_token']->generate_token());
		
		// Set Cache Token
		$this->contentView->set_content_data('cache_token', MainFactory::create('CacheTokenHelper')->getCacheToken());
		
		// Set FontAwesome Fallback
		$fontAwesomePath = DIR_FS_ADMIN . 'html/assets/fonts/font-awesome/FontAwesome.otf';
		$this->contentView->set_content_data('fontawesome_fallback', !file_exists($fontAwesomePath));
		
		// Set JavaScript Translations
		$translations = $this->_getTranslations();
		$this->contentView->set_content_data('translations', json_encode($translations));
		
		// Set Main Menu Data 
		$adminMenuContentView = MainFactory::create('AdminMenuContentView');
		$adminMenuContentView->setCustomerId($_SESSION['customer_id']);
		$this->contentView->set_content_data('menu_entries', $adminMenuContentView->prepare_data());

		// Set the initial menu state
		$userConfigurationService = StaticGXCoreLoader::getService('UserConfiguration');
		$menuVisibility = $userConfigurationService->getUserConfiguration(new IdType((int)$_SESSION['customer_id']), 'menuVisibility');
		$this->contentView->set_content_data('menu_visibility', $menuVisibility);
		
		// Get recent search area. 
		$recentSearchArea = $userConfigurationService->getUserConfiguration(new IdType((int)$_SESSION['customer_id']),
		                                                                    'recentSearchArea');
		$this->contentView->set_content_data('recent_search_area', $recentSearchArea); 

		// Set Shop Key Information
		$this->contentView->set_content_data('shop_key_state', (bool)gm_get_conf('SHOP_KEY_VALID'));
		
		// Set Language Information
		$db               = StaticGXCoreLoader::getDatabaseQueryBuilder();
		$languageProvider = MainFactory::create('LanguageProvider', $db);
		$languages        = array();
		foreach($languageProvider->getCodes()->getArray() as $code)
		{
			$languages[] = array(
				'code' => $code->asString(),
				'name' => $languageProvider->getDirectoryByCode($code)
			);
		}
		asort($languages); // "asort" - german language needs to be first on the array
		$this->contentView->set_content_data('languages', $languages);
		
		// Set Content Navigation
		if($this->contentNavigation !== null)
		{
			$this->contentView->set_content_data('content_navigation', $this->contentNavigation->getArray());
		}
		
		// Set Page Assets
		if($this->assets !== null)
		{
			$scripts = $this->assets->getHtml(new StringType(Asset::JAVASCRIPT));
			$this->contentView->set_content_data('dynamic_script_assets', $scripts); 
			$styles  = $this->assets->getHtml(new StringType(Asset::CSS));
			$this->contentView->set_content_data('dynamic_style_assets', $styles);
		}
		
		// Set Initial Messages
		$this->_setInitialMessages();
		
		// Set message stack data. 
		$this->contentView->set_content_data('message_stack', $GLOBALS['messageStack']->get_messages());
		
		echo $this->contentView->get_html();
	}
	
	
	/**
	 * Get the default and assets translations.
	 *
	 * Hint: Override this method to fetch different default translations.
	 *
	 * @return array
	 */
	protected function _getTranslations()
	{
		$translations                     = ($this->assets !== null) ? $this->assets->getTranslations() : array();
		$languageTextManager              = MainFactory::create('LanguageTextManager', 'general',
		                                                        $_SESSION['languages_id']);
		$translations['general']          = $languageTextManager->get_section_array('general');
		$translations['buttons']          = $languageTextManager->get_section_array('buttons');
		$translations['messages']         = $languageTextManager->get_section_array('messages');
		$translations['admin_labels']     = $languageTextManager->get_section_array('admin_labels');
		$translations['admin_general']    = $languageTextManager->get_section_array('admin_general');
		$translations['admin_info_boxes'] = $languageTextManager->get_section_array('admin_info_boxes');
		
		return $translations;
	}
	
	
	/**
	 * Set initial messages for new admin layout. 
	 */
	protected function _setInitialMessages()
	{
		$languageTextManager = MainFactory::create('LanguageTextManager', 'admin_general', $_SESSION['languages_id']);
		$contentArray        = $this->contentView->get_content_array();

		if(file_exists(DIR_FS_CATALOG . 'gambio_installer'))
		{
			// Installer directory still exists error message.
			$installerMessage = sprintf($languageTextManager->get_text('WARNING_INSTALL_DIRECTORY_EXISTS',
			                                                           'general'), substr(DIR_WS_CATALOG, 0, -1));
			
			$GLOBALS['messageStack']->add($installerMessage, 'error');
		}

		if($contentArray['environment'] === 'development')
		{
			$GLOBALS['messageStack']->add($languageTextManager->get_text('TEXT_DEV_ENVIRONMENT_WARNING',
			                                                             'admin_general'), 'warning');
		}
	}
}