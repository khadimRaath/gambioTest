<?php
/* --------------------------------------------------------------
   CategorySettingsRepositoryReader.inc.php 2016-06-23
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class CategorySettingsRepositoryReader
 * 
 * This class provides methods for fetching particular columns of specific category records in the database.
 * The category settings are stored in the categories table and are more related to display and visibility modes of
 * category related data.
 *
 * @category   System
 * @package    Category
 * @subpackage Repositories
 */
class CategorySettingsRepositoryReader implements CategorySettingsRepositoryReaderInterface
{
	/**
	 * Database Connection
	 * 
	 * @var CI_DB_query_builder
	 */
	protected $db;
	
	/**
	 * Category factory
	 * 
	 * @var CategoryFactoryInterface
	 */
	protected $categoryFactory;
	

	/**
	 * Customer Status Provider
	 * 
	 * @var CustomerStatusProviderInterface
	 */
	protected $customerStatusProvider;
	
	
	/**
	 * CategorySettingsRepositoryReader constructor.
	 *
	 * @param CI_DB_query_builder             $db                     Database connector.
	 * @param CategoryFactoryInterface        $categoryFactory        Category factory.
	 * @param CustomerStatusProviderInterface $customerStatusProvider Customer Status Provider.
	 */
	public function __construct(CI_DB_query_builder $db,
	                            CategoryFactoryInterface $categoryFactory,
	                            CustomerStatusProviderInterface $customerStatusProvider)
	{
		$this->db                     = $db;
		$this->categoryFactory        = $categoryFactory;
		$this->customerStatusProvider = $customerStatusProvider;
	}
	
	
	/**
	 * Returns category settings based on ID given.
	 *
	 * @param IdType $categoryId Category ID.
	 *
	 * @throws UnexpectedValueException if no category record for the provided category ID was found.
	 * @throws InvalidArgumentException
	 *                                  
	 * @return CategorySettingsInterface
	 */
	public function getById(IdType $categoryId)
	{
		$category = $this->db->get_where('categories', array('categories_id' => $categoryId->asInt()))->row_array();
		
		if($category === null)
		{
			throw new UnexpectedValueException('The requested category was not found in the database (ID:'
			                                   . $categoryId->asInt() . ')');
		}
		
		return $this->_createAndReturnCategorySettings($category);
	}
	
	
	/**
	 * Creates a CategorySettings object and returns it.
	 *
	 * @param array $category Fetched Associative category array.
	 *
	 * @return CategorySettings $categorySettings
	 *                          
	 * @throws UnexpectedValueException
	 * @throws InvalidArgumentException
	 */
	protected function _createAndReturnCategorySettings(array $category)
	{
		// Apply the settings to the CategorySettings object.
		$categorySettings = $this->categoryFactory->createCategorySettings();
		$categorySettings->setCategoryListingTemplate(new StringType((string)$category['categories_template']));
		$categorySettings->setProductListingTemplate(new StringType((string)$category['listing_template']));
		$categorySettings->setProductSortColumn(new StringType((string)$category['products_sorting']));
		$categorySettings->setProductSortDirection(new StringType((string)$category['products_sorting2']));
		$categorySettings->setSitemapEntry(new BoolType((bool)$category['gm_sitemap_entry']));
		$categorySettings->setSitemapPriority(new StringType((string)$category['gm_priority']));
		$categorySettings->setSitemapChangeFreq(new StringType((string)$category['gm_changefreq']));
		$categorySettings->setShowAttributes(new BoolType((bool)$category['gm_show_attributes']));
		$categorySettings->setShowGraduatedPrices(new BoolType((bool)$category['gm_show_graduated_prices']));
		$categorySettings->setShowQuantityInput(new BoolType((bool)$category['gm_show_qty']));
		$categorySettings->setShowStock(new BoolType((bool)$category['gm_show_qty_info']));
		$categorySettings->setShowSubcategories(new BoolType((bool)$category['show_sub_categories']));
		$categorySettings->setShowSubcategoryImages(new BoolType((bool)$category['show_sub_categories_images']));
		$categorySettings->setShowSubcategoryNames(new BoolType((bool)$category['show_sub_categories_names']));
		$categorySettings->setShowSubcategoryProducts(new BoolType((bool)$category['show_sub_products']));
		$categorySettings->setDefaultViewModeTiled(new BoolType((bool)$category['view_mode_tiled']));
		$categorySettings->setShowCategoryFilter(new BoolType((bool)$category['show_category_filter']));
		$categorySettings->setFilterSelectionMode(new IntType((int)$category['feature_mode']));
		$categorySettings->setFilterValueDeactivation(new IntType((int)$category['feature_display_mode']));
		
		// Get and apply group permissions.
		$groupPermissionIds = $this->customerStatusProvider->getCustomerStatusIds();
		$this->_setGroupPermissions($category, $groupPermissionIds, $categorySettings);
		
		return $categorySettings;
	}
	
	
	/**
	 * Sets the permitted customers statuses.
	 *
	 * @param array            $category           The fetched category array from the database.
	 * @param array            $groupPermissionIds Array of available group permission.
	 * @param CategorySettings $categorySettings   Object to set the customer statuses.
	 *
	 * @return CategorySettingsRepositoryReader Same instance for chained method calls.
	 *                                          
	 * @throws InvalidArgumentException
	 */
	protected function _setGroupPermissions($category, $groupPermissionIds, $categorySettings)
	{
		foreach($groupPermissionIds as $id)
		{
			if(array_key_exists('group_permission_' . $id, $category))
			{
				$categorySettings->setPermittedCustomerStatus(new IdType($id),
				                                              new BoolType((bool)$category['group_permission_' . $id]));
			}
		}
		
		return $this;
	}
}