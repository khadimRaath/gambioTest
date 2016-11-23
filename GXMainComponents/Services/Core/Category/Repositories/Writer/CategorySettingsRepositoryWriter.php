<?php

/* --------------------------------------------------------------
   CategorySettingsRepositoryWriter.php 2016-06-23
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class CategorySettingsRepositoryWriter
 *
 * This class provides methods for updating particular columns of specific category records in the database.
 * The category settings are stored in the categories table and are more related to display and visibility modes of
 * category related data.
 *
 * @category   System
 * @package    Category
 * @subpackage Repositories
 */
class CategorySettingsRepositoryWriter implements CategorySettingsRepositoryWriterInterface
{
	/**
	 * Database Connection
	 * 
	 * @var CI_DB_query_builder
	 */
	protected $db;
	
	/**
	 * Table name
	 * 
	 * @var string
	 */
	protected $table = 'categories';
	
	
	/**
	 * Customer Status Provider
	 * 
	 * @var CustomerStatusProviderInterface
	 */
	protected $customerStatusProvider;
	
	
	/**
	 * Initialize the category settings repository writer.
	 *
	 * @param CI_DB_query_builder $dbQueryBuilder Database connector.
	 * @param CustomerStatusProviderInterface $customerStatusProvider Customer Status Provider
	 */
	public function __construct(CI_DB_query_builder $dbQueryBuilder,
	                            CustomerStatusProviderInterface $customerStatusProvider)
	{
		$this->db                     = $dbQueryBuilder;
		$this->customerStatusProvider = $customerStatusProvider;
	}
	
	
	/**
	 * Updates a specific category settings entity.
	 *
	 * @param IdType                    $categoryId Category ID.
	 * @param CategorySettingsInterface $settings   Category settings.
	 *
	 * @return CategorySettingsRepositoryWriter Same instance for chained method calls.
	 *                                          
	 * @throws UnexpectedValueException
	 * @throws InvalidArgumentException
	 */
	public function update(IdType $categoryId, CategorySettingsInterface $settings)
	{
		$setArray = array(
			'categories_template'        => $settings->getCategoryListingTemplate(),
			'listing_template'           => $settings->getProductListingTemplate(),
			'products_sorting'           => $settings->getProductSortColumn(),
			'products_sorting2'          => $settings->getProductSortDirection(),
			'gm_sitemap_entry'           => $settings->isSitemapEntry(),
			'gm_priority'                => $settings->getSitemapPriority(),
			'gm_changefreq'              => $settings->getSitemapChangeFreq(),
			'gm_show_attributes'         => $settings->showAttributes(),
			'gm_show_graduated_prices'   => $settings->showGraduatedPrices(),
			'gm_show_qty'                => $settings->showQuantityInput(),
			'gm_show_qty_info'           => $settings->showStock(),
			'show_sub_categories'        => $settings->showSubcategories(),
			'show_sub_categories_images' => $settings->showSubcategoryImages(),
			'show_sub_categories_names'  => $settings->showSubcategoryNames(),
			'show_sub_products'          => $settings->showSubcategoryProducts(),
			'view_mode_tiled'            => $settings->isDefaultViewModeTiled(),
			'show_category_filter'       => $settings->showCategoryFilter(),
			'feature_mode'               => $settings->getFilterSelectionMode(),
			'feature_display_mode'       => $settings->getFilterValueDeactivation()
		);
		
		$customerStatusIds = $this->customerStatusProvider->getCustomerStatusIds();
		
		foreach($customerStatusIds as $customerStatusId)
		{
			$setArray['group_permission_'
			          . $customerStatusId] = (int)$settings->isPermittedCustomerStatus(new IdType($customerStatusId));
		}
		
		$whereArray = array(
			'categories_id' => $categoryId->asInt()
		);
		$this->db->update($this->table, $setArray, $whereArray);
		
		return $this;
	}
}