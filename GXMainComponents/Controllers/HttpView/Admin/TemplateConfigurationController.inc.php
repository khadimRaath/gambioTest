<?php
/* --------------------------------------------------------------
   TemplateConfigurationController.inc.php 2016-09-21
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('HttpViewController');

/**
 * Class TemplateConfigurationController
 * @extends    HttpViewController
 * @category   System
 * @package    AdminHttpViewControllers
 */
class TemplateConfigurationController extends AdminHttpViewController
{
	/**
	 * @var CI_DB_query_builder $db
	 */
	protected $db;
	
	/**
	 * @var LanguageTextManager $languageTextManager
	 */
	protected $languageTextManager;

	/**
	 * @var string
	 */
	protected $shopEnvironment;

	/**
	 * @var string
	 */
	protected $styleEditLink;
	
	
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
		$gxCoreLoader = MainFactory::create('GXCoreLoader', MainFactory::create('GXCoreLoaderSettings'));
		$this->db     = $gxCoreLoader->getDatabaseQueryBuilder();
		
		$this->languageTextManager = MainFactory::create('LanguageTextManager', 'template_configuration');
	}
	
	
	/**
	 * Returns the Template Configuration Page
	 *
	 * @return HttpControllerResponse|RedirectHttpControllerResponse
	 */
	public function actionDefault()
	{
		$this->contentView->set_template_dir(DIR_FS_ADMIN . 'html/content/');
		$this->_checkEnvironment();
		
		$html = $this->_render('template_configuration.html', array(
			'MAIN_SHOW_QTY_INFO'                      => gm_get_conf('MAIN_SHOW_QTY_INFO') === 'true',
			'MAIN_SHOW_ATTRIBUTES'                    => gm_get_conf('MAIN_SHOW_ATTRIBUTES') === 'true',
			'MAIN_SHOW_GRADUATED_PRICES'              => gm_get_conf('MAIN_SHOW_GRADUATED_PRICES') === 'true',
			'SHOW_MANUFACTURER_IMAGE_PRODUCT_DETAILS' => gm_get_conf('SHOW_MANUFACTURER_IMAGE_PRODUCT_DETAILS')
			                                             === 'true',
			'SHOW_ADDITIONAL_FIELDS_PRODUCT_DETAILS'  => gm_get_conf('SHOW_ADDITIONAL_FIELDS_PRODUCT_DETAILS')
			                                             === 'true',
			'MAIN_SHOW_QTY'                           => gm_get_conf('MAIN_SHOW_QTY') === 'true',
			'MAIN_VIEW_MODE_TILED'                    => gm_get_conf('MAIN_VIEW_MODE_TILED') === 'true',
			'SHOW_MANUFACTURER_IMAGE_LISTING'         => gm_get_conf('SHOW_MANUFACTURER_IMAGE_LISTING') === 'true',
			'SHOW_PRODUCT_RIBBONS'                    => gm_get_conf('SHOW_PRODUCT_RIBBONS') === 'true',
			'GM_SHOW_FLYOVER'                         => gm_get_conf('GM_SHOW_FLYOVER') === 'true',
			'SHOW_GALLERY'                            => gm_get_conf('SHOW_GALLERY') === 'true',
			'SHOW_ZOOM'                               => gm_get_conf('SHOW_ZOOM') === 'true',
			'CAT_MENU_TOP'                            => gm_get_conf('CAT_MENU_TOP') === 'true',
			'CAT_MENU_LEFT'                           => gm_get_conf('CAT_MENU_LEFT') === 'true',
			'SHOW_SUBCATEGORIES'                      => gm_get_conf('SHOW_SUBCATEGORIES') === 'true',
			'CATEGORY_ACCORDION_EFFECT'               => gm_get_conf('CATEGORY_ACCORDION_EFFECT') === 'true',
			'CATEGORY_DISPLAY_SHOW_ALL_LINK'          => gm_get_conf('CATEGORY_DISPLAY_SHOW_ALL_LINK') === 'true',
			'CATEGORY_UNFOLD_LEVEL'                   => gm_get_conf('CATEGORY_UNFOLD_LEVEL'),
			'SHOW_TOP_LANGUAGE_SELECTION'             => gm_get_conf('SHOW_TOP_LANGUAGE_SELECTION') === 'true',
			'SHOW_TOP_CURRENCY_SELECTION'             => gm_get_conf('SHOW_TOP_CURRENCY_SELECTION') === 'true',
			'SHOW_TOP_COUNTRY_SELECTION'              => gm_get_conf('SHOW_TOP_COUNTRY_SELECTION') === 'true',
			'GM_QUICK_SEARCH'                         => gm_get_conf('GM_QUICK_SEARCH') === 'true',
			'SHOW_FACEBOOK'                           => gm_get_conf('SHOW_FACEBOOK') === 'true',
			'SHOW_TWITTER'                            => gm_get_conf('SHOW_TWITTER') === 'true',
			'SHOW_GOOGLEPLUS'                         => gm_get_conf('SHOW_GOOGLEPLUS') === 'true',
			'SHOW_PINTEREST'                          => gm_get_conf('SHOW_PINTEREST') === 'true',
			'GM_SHOW_WISHLIST'                        => gm_get_conf('GM_SHOW_WISHLIST') === 'true',
			'GM_SPECIALS_STARTPAGE'                   => gm_get_conf('GM_SPECIALS_STARTPAGE'),
			'GM_NEW_PRODUCTS_STARTPAGE'               => gm_get_conf('GM_NEW_PRODUCTS_STARTPAGE'),
			'ENABLE_RATING'                           => gm_get_conf('ENABLE_RATING') === 'true',
			'SHOW_RATING_IN_GRID_AND_LISTING'         => gm_get_conf('SHOW_RATING_IN_GRID_AND_LISTING') === 'true',
			'ALWAYS_SHOW_CONTINUE_SHOPPING_BUTTON'    => gm_get_conf('ALWAYS_SHOW_CONTINUE_SHOPPING_BUTTON') === 'true',
			'SHOP_ENVIRONMENT'                        => $this->shopEnvironment,
			'STYLE_EDIT_LINK'                         => $this->styleEditLink,
			'STYLE_EDIT_SOS_LINK'                     => xtc_href_link('../index.php', 'style_edit_mode=sos'),
			'GM_TELL_A_FRIEND'                        => gm_get_conf('GM_TELL_A_FRIEND') === 'true',
			'USE_UPCOMING_PRODUCT_SWIPER_ON_INDEX'    => gm_get_conf('USE_UPCOMING_PRODUCT_SWIPER_ON_INDEX') === 'true',
			'USE_TOP_PRODUCT_SWIPER_ON_INDEX'         => gm_get_conf('USE_TOP_PRODUCT_SWIPER_ON_INDEX') === 'true',
			'USE_SPECIAL_PRODUCT_SWIPER_ON_INDEX'     => gm_get_conf('USE_SPECIAL_PRODUCT_SWIPER_ON_INDEX') === 'true',
			'USE_NEW_PRODUCT_SWIPER_ON_INDEX'         => gm_get_conf('USE_NEW_PRODUCT_SWIPER_ON_INDEX') === 'true',
		));
	
		$pageTitle = $this->languageTextManager->get_text('HEADING_TITLE');
		return MainFactory::create('AdminPageHttpControllerResponse', $pageTitle, $html);
	}
	
	
	/**
	 * Save shop key
	 *
	 * @return RedirectHttpControllerResponse
	 */
	public function actionStore()
	{
		$this->_store('MAIN_SHOW_QTY_INFO', $this->_getPostData('MAIN_SHOW_QTY_INFO'));
		$this->_store('MAIN_SHOW_ATTRIBUTES', $this->_getPostData('MAIN_SHOW_ATTRIBUTES'));
		$this->_store('MAIN_SHOW_GRADUATED_PRICES', $this->_getPostData('MAIN_SHOW_GRADUATED_PRICES'));
		$this->_store('SHOW_MANUFACTURER_IMAGE_PRODUCT_DETAILS', $this->_getPostData('SHOW_MANUFACTURER_IMAGE_PRODUCT_DETAILS'));
		$this->_store('SHOW_ADDITIONAL_FIELDS_PRODUCT_DETAILS', $this->_getPostData('SHOW_ADDITIONAL_FIELDS_PRODUCT_DETAILS'));
		$this->_store('MAIN_SHOW_QTY', $this->_getPostData('MAIN_SHOW_QTY'));
		$this->_store('MAIN_VIEW_MODE_TILED', $this->_getPostData('MAIN_VIEW_MODE_TILED'));
		$this->_store('SHOW_MANUFACTURER_IMAGE_LISTING', $this->_getPostData('SHOW_MANUFACTURER_IMAGE_LISTING'));
		$this->_store('SHOW_PRODUCT_RIBBONS', $this->_getPostData('SHOW_PRODUCT_RIBBONS'));
		$this->_store('GM_SHOW_FLYOVER', $this->_getPostData('GM_SHOW_FLYOVER'));
		$this->_store('SHOW_GALLERY', $this->_getPostData('SHOW_GALLERY'));
		$this->_store('SHOW_ZOOM', $this->_getPostData('SHOW_ZOOM'));
		$this->_store('CAT_MENU_TOP', $this->_getPostData('CAT_MENU_TOP'));
		$this->_store('CAT_MENU_LEFT', $this->_getPostData('CAT_MENU_LEFT'));
		$this->_store('SHOW_SUBCATEGORIES', $this->_getPostData('SHOW_SUBCATEGORIES'));
		$this->_store('CATEGORY_ACCORDION_EFFECT', $this->_getPostData('CATEGORY_ACCORDION_EFFECT'));
		$this->_store('CATEGORY_DISPLAY_SHOW_ALL_LINK', $this->_getPostData('CATEGORY_DISPLAY_SHOW_ALL_LINK'));
		$this->_store('CATEGORY_UNFOLD_LEVEL', (int)$this->_getPostData('CATEGORY_UNFOLD_LEVEL'));
		$this->_store('SHOW_TOP_LANGUAGE_SELECTION', $this->_getPostData('SHOW_TOP_LANGUAGE_SELECTION'));
		$this->_store('SHOW_TOP_CURRENCY_SELECTION', $this->_getPostData('SHOW_TOP_CURRENCY_SELECTION'));
		$this->_store('SHOW_TOP_COUNTRY_SELECTION', $this->_getPostData('SHOW_TOP_COUNTRY_SELECTION'));
		$this->_store('GM_QUICK_SEARCH', $this->_getPostData('GM_QUICK_SEARCH'));
		$this->_store('SHOW_FACEBOOK', $this->_getPostData('SHOW_FACEBOOK'));
		$this->_store('SHOW_TWITTER', $this->_getPostData('SHOW_TWITTER'));
		$this->_store('SHOW_GOOGLEPLUS', $this->_getPostData('SHOW_GOOGLEPLUS'));
		$this->_store('SHOW_PINTEREST', $this->_getPostData('SHOW_PINTEREST'));
		$this->_store('GM_SHOW_WISHLIST', $this->_getPostData('GM_SHOW_WISHLIST'));
		$this->_store('GM_SPECIALS_STARTPAGE', (int)$this->_getPostData('GM_SPECIALS_STARTPAGE'));
		$this->_store('GM_NEW_PRODUCTS_STARTPAGE', (int)$this->_getPostData('GM_NEW_PRODUCTS_STARTPAGE'));
		$this->_store('ENABLE_RATING', $this->_getPostData('ENABLE_RATING'));
		$this->_store('SHOW_RATING_IN_GRID_AND_LISTING', $this->_getPostData('SHOW_RATING_IN_GRID_AND_LISTING'));
		$this->_store('ALWAYS_SHOW_CONTINUE_SHOPPING_BUTTON', $this->_getPostData('ALWAYS_SHOW_CONTINUE_SHOPPING_BUTTON'));
		$this->_store('GM_TELL_A_FRIEND', $this->_getPostData('GM_TELL_A_FRIEND'));
		$this->_store('USE_UPCOMING_PRODUCT_SWIPER_ON_INDEX', $this->_getPostData('USE_UPCOMING_PRODUCT_SWIPER_ON_INDEX'));
		$this->_store('USE_TOP_PRODUCT_SWIPER_ON_INDEX', $this->_getPostData('USE_TOP_PRODUCT_SWIPER_ON_INDEX'));
		$this->_store('USE_SPECIAL_PRODUCT_SWIPER_ON_INDEX', $this->_getPostData('USE_SPECIAL_PRODUCT_SWIPER_ON_INDEX'));
		$this->_store('USE_NEW_PRODUCT_SWIPER_ON_INDEX', $this->_getPostData('USE_NEW_PRODUCT_SWIPER_ON_INDEX'));

		$url = xtc_href_link('admin.php', 'do=TemplateConfiguration');
		
		return MainFactory::create('RedirectHttpControllerResponse', $url);
	}
	
	
	/**
	 * Update the template configuration values in the database
	 *
	 * @param string $p_key
	 * @param string $p_value
	 */
	protected function _store($p_key, $p_value)
	{
		if($p_value !== null)
		{
			$this->db->set('gm_key', $p_key);
			$this->db->set('gm_value', $p_value);
			$this->db->where('gm_key', $p_key);
			$this->db->replace('gm_configuration');
		}
	}


	protected function _checkEnvironment()
	{
		if(is_dir(DIR_FS_CATALOG . 'StyleEdit3/templates/' . CURRENT_TEMPLATE))
		{
			$this->shopEnvironment = 'StyleEdit3';
			$this->styleEditLink = xtc_href_link('admin.php', 'do=StyleEdit3Authentication');
		}
		elseif(is_dir(DIR_FS_CATALOG.'StyleEdit/') !== false && gm_get_env_info('TEMPLATE_VERSION') < 3)
		{
			$this->shopEnvironment = 'StyleEdit';
			$this->styleEditLink = xtc_href_link('../index.php', 'style_edit_mode=edit');
		}
		else
		{
			if(!isset($_GET['force_config']) || $_GET['force_config'] !== 'true')
			{
				$this->shopEnvironment = 'noStyleEdit';
			}
			elseif(gm_get_env_info('TEMPLATE_VERSION') >= 3) 
			{
				$this->shopEnvironment = 'forceStyleEdit';
				$this->styleEditLink = '#';
			}
		}
	}
}