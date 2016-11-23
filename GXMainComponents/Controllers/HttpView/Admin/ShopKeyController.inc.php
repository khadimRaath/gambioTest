<?php
/* --------------------------------------------------------------
   ShopKeyController.inc.php 2016-03-16
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('AdminHttpViewController');

/**
 * Class ShopKeyController
 * @extends    AdminHttpViewController
 * @category   System
 * @package    AdminHttpViewControllers
 */
class ShopKeyController extends AdminHttpViewController
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

		$this->languageTextManager = MainFactory::create('LanguageTextManager', 'shop_key', $_SESSION['languages_id']);
	}


	/**
	 * Returns the Gambio Shop Key Page
	 *
	 * @return HttpControllerResponse|RedirectHttpControllerResponse
	 */
	public function actionDefault()
	{
		$shopKey            = $this->_getShopKey();
		$shopKeyData        = $this->_getShopKeyData($shopKey);
		$shopKeyRequestLink = 'http://www.gambio.de/0n7hb';

		$pageTitle = $this->languageTextManager->get_text('page_title');
		$this->contentView->set_template_dir(DIR_FS_ADMIN . 'html/content/');
		$html = $this->_render('shop_key.html', array(
			'shop_key'              => $shopKey,
			'shop_key_data'         => $shopKeyData,
			'shop_key_request_link' => $shopKeyRequestLink
		));

		return MainFactory::create('AdminPageHttpControllerResponse', $pageTitle, $html, null, array('shop_key'));
	}


	/**
	 * Save shop key
	 *
	 * @return RedirectHttpControllerResponse
	 */
	public function actionStore()
	{
		$this->_store(trim($this->_getPostData('GAMBIO_SHOP_KEY')));
		$url = xtc_href_link('admin.php', 'do=ShopKey');

		return MainFactory::create('RedirectHttpControllerResponse', $url);
	}


	/**
	 * Delete shop key
	 *
	 * @return RedirectHttpControllerResponse
	 */
	public function actionDestroy()
	{
		$this->_store('');
		$url = xtc_href_link('admin.php', 'do=ShopKey');

		return MainFactory::create('RedirectHttpControllerResponse', $url);
	}


	/**
	 * Get the shop key from database
	 *
	 * @return mixed
	 */
	protected function _getShopKey()
	{
		$shopKeyResult = $this->db->select('configuration_value')
		                          ->from('configuration')
		                          ->where('configuration_key', 'GAMBIO_SHOP_KEY')
		                          ->get();
		$shopKey       = $shopKeyResult->row();
		$shopKey       = $shopKey->configuration_value;

		return $shopKey;
	}


	/**
	 * Get the shop key information for the textarea
	 *
	 * @param string $p_shopKey
	 *
	 * @return string
	 */
	protected function _getShopKeyData($p_shopKey)
	{
		include DIR_FS_CATALOG . '/release_info.php';

		$shopKeyData = 'shop_version=' . $gx_version . "\n";
		$shopKeyData .= 'shop_url=' . HTTP_SERVER . DIR_WS_CATALOG . "\n";
		$shopKeyData .= 'shop_key=' . (string)$p_shopKey . "\n";
		$shopKeyData .= 'language=' . $_SESSION['language_code'] . "\n";
		$shopKeyData .= 'server_path=' . rtrim(DIR_FS_CATALOG, '/') . "\n";

		return $shopKeyData;
	}


	/**
	 * Update the shop key in the database
	 *
	 * @param string $p_shopKey
	 */
	protected function _store($p_shopKey)
	{
		$this->db->set('configuration_value', $p_shopKey);
		$this->db->where('configuration_key', 'GAMBIO_SHOP_KEY');
		$this->db->update('configuration');

		$coo_cache = DataCache::get_instance();
		$coo_cache->clear_cache_by_tag('ADMIN');
		
		$this->db->set('gm_value', 1);
		$this->db->set('gm_key', 'CHECK_SHOP_KEY');
		$this->db->where('gm_key', 'CHECK_SHOP_KEY');
		$this->db->replace('gm_configuration');
	}
}