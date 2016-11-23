<?php
/* --------------------------------------------------------------
   CategoriesSubmenusBoxContentView.inc.php 2014-11-10 gambio
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class CategoriesSubmenusBoxContentView
 */
class CategoriesSubmenusBoxContentView extends ContentView
{
	public $v_customers_status_id		= false;
	public $v_language 					= false;
	public $v_currency 					= false;

	protected $parentCategoriesId		= 0;
	protected $cPath					= null;
	protected $categoriesContentView	= null;
	protected $categoriesAgent			= null;
	protected $languageId				= null;
	protected $categoriesInfoArray		= array();
	protected $html						= '';
	protected $cPathCategoriesInfoArray	= array();
	protected $categoriesParentsArray	= array();
	
	
// ########## CONSTRUCTOR ##########

	/**
	 * 
	 */
	public function __construct()
	{
		parent::__construct();
		$this->set_content_template('module/submenus.html');
		$this->set_caching_enabled(true);
	}
	
	
// ########## GETTER & SETTER ##########

	/**
	 * @param integer $customerStatusId
	 */
	public function setCustomerStatusId($customerStatusId) {
		$this->v_customers_status_id = (int)$customerStatusId;
	}


	/**
	 * @return integer
	 */
	public function getCustomerStatusId() {
		return $this->v_customers_status_id;
	}


	/**
	 * @param $language
	 */
	public function setLanguage($language) {
		$this->v_language = $language;
	}


	/**
	 * @return 
	 */
	public function getLanguage() {
		return $this->v_language;
	}


	/**
	 * @param integer $languageId
	 */
	public function setLanguageId($languageId) {
		$this->languageId = (int)$languageId;
	}


	/**
	 * @return integer
	 */
	public function getLanguageId() {
		return $this->languageId;
	}


	/**
	 * @param $currency
	 */
	public function setCurrency($currency) {
		$this->v_currency = $currency;
	}


	/**
	 * @return
	 */
	public function getCurrency() {
		return $this->v_currency;
	}


	/**
	 * @param string $cPath
	 */
	public function setCPath($cPath) {
		$this->cPath = $cPath;
	}


	/**
	 * @return string
	 */
	public function getCPath() {
		return $this->cPath;
	}


	/**
	 * @param int $categoriesId
	 */
	public function setParentCategoriesId($categoriesId) {
		$this->parentCategoriesId = (int)$categoriesId;
	}


	/**
	 * @return int
	 */
	public function getParentCategoriesId() {
		return $this->parentCategoriesId;
	}

	
// ########## PRIVATE & PROTECTED METHODS ##########

	/**
	 * 
	 */
	public function prepare_data()
	{
		$this->_prepareCache();

		if($this->is_cached() === false)
		{
			$this->_getContentObjects();
			$this->_getCategoriesHtml();
			$this->_generateCPathMarkup();
			$this->_generateParentsIds();
			$this->_assignData();

		}
		elseif (is_object($GLOBALS['coo_debugger']))
		{
			$GLOBALS['coo_debugger']->log('CategoriesSubmenusBoxContentView get_html USE_CACHE', 'SmartyCache');
		}

	}

	
	/**
	 * 
	 */
	protected function _prepareCache()
	{
		$this->clear_cache_id_elements();
		$this->add_cache_id_elements(array(
										 $this->v_customers_status_id,
										 $this->v_language,
										 $this->v_currency,
										 $this->cPath,
										 $this->parentCategoriesId
									 ));
	}


	/**
	 * 
	 */
	protected function _getContentObjects()
	{
		# Categories Submenus
		$this->categoriesContentView = MainFactory::create_object('CategoriesMenuBoxContentView');
		$this->categoriesContentView->set_content_template('module/categories_submenus.html');
		$this->categoriesContentView->set_tree_depth(1);

		$this->categoriesAgent		= MainFactory::create_object('CategoriesAgent', array(), true);
		$this->categoriesInfoArray	= $this->categoriesAgent->get_categories_info_tree($this->parentCategoriesId, $this->languageId, 0);
		if(isset($_GET['cPath']))
		{
			$this->categoriesParentsArray = $this->categoriesAgent->getPartentsIds($_GET['cPath']);
		}
	}


	/**
	 * 
	 */
	protected function _assignData()
	{
		$this->set_content_data('HTML',	$this->html);
	}


	/**
	 * 
	 */
	protected function _getCategoriesHtml()
	{
		$this->html = '';

		foreach( $this->categoriesInfoArray as $value )
		{
			$this->categoriesContentView->setCurrentCategoryId($value['data']['id']);
			$this->html .= $this->categoriesContentView->get_html();
		}
	}

	
	protected function _generateParentsIds()
	{
		$this->html .= '<script type="text/javascript">parentsIds = ' . json_encode($this->categoriesParentsArray) . ';</script>';
	}

	/**
	 * 
	 */
	protected function _generateCPathMarkup()
	{
		if(isset($this->cPath))
		{
			$categoriesArray = explode('_', $this->cPath);

			foreach ($categoriesArray as $value)
			{
				if((int)$value > 0)
				{
					$this->cPathCategoriesInfoArray = $this->categoriesAgent->get_categories_info_tree($value, $this->language, 0);

					foreach ($this->cPathCategoriesInfoArray as $v)
					{
						$this->html .= $this->categoriesContentView->get_html($v['data']['id']);
					}
				}
			}
		}
	}

}