<?php

/* --------------------------------------------------------------
   ProductSettingsRepositoryWriter.inc.php 2016-05-09
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class ProductSettingsRepositoryWriter
 *
 * @category   System
 * @package    Product
 * @subpackage Repositories
 */
class ProductSettingsRepositoryWriter implements ProductSettingsRepositoryWriterInterface
{

	/**
	 * Database connection.
	 *
	 * @var CI_DB_query_builder
	 */
	protected $db;

	/**
	 * Table name.
	 *
	 * @var string
	 */
	protected $table = 'products';
	
	/**
	 * Customer Status Provider
	 *
	 * @var CustomerStatusProviderInterface
	 */
	protected $customerStatusProvider;
	
	
	/**
	 * ProductSettingsRepositoryWriter constructor.
	 *
	 * @param CI_DB_query_builder             $db                     Database connection.
	 * @param CustomerStatusProviderInterface $customerStatusProvider Customer Status Provider
	 */
	public function __construct(CI_DB_query_builder $db,
	                            CustomerStatusProviderInterface $customerStatusProvider)
	{
		$this->db                     = $db;
		$this->customerStatusProvider = $customerStatusProvider;
	}


	/**
	 * Updates product settings by the given product id.
	 *
	 * @param IdType                   $productId ID of product entity.
	 * @param ProductSettingsInterface $settings  Settings entity with values to update.
	 *
	 * @return ProductSettingsRepositoryInterface|$this Same instance for chained method calls.
	 */
	public function update(IdType $productId, ProductSettingsInterface $settings)
	{
		$setArray = array(
			'product_template'                    => $settings->getDetailsTemplate(),
			'options_template'                    => $settings->getOptionsDetailsTemplate(),
			'gm_options_template'                 => $settings->getOptionsListingTemplate(),
			'products_startpage'                  => $settings->showOnStartpage(),
			'products_startpage_sort'             => $settings->getStartpageSortOrder(),
			'gm_show_date_added'                  => $settings->showAddedDateTime(),
			'gm_show_qty_info'                    => $settings->showQuantityInfo(),
			'gm_show_weight'                      => $settings->showWeight(),
			'gm_show_price_offer'                 => $settings->showPriceOffer(),
			'gm_price_status'                     => $settings->getPriceStatus(),
			'gm_min_order'                        => $settings->getMinOrder(),
			'gm_graduated_qty'                    => $settings->getGraduatedQuantity(),
			'gm_sitemap_entry'                    => $settings->isSitemapEntry(),
			'gm_priority'                         => $settings->getSitemapPriority(),
			'gm_changefreq'                       => $settings->getSitemapChangeFreq(),
			'properties_show_price'               => $settings->showPropertiesPrice() ? 'true' : 'false',
			'properties_dropdown_mode'            => $settings->getPropertiesDropdownMode(),
			'use_properties_combis_weight'        => $settings->usePropertiesCombisWeight(),
			'use_properties_combis_quantity'      => $settings->getPropertiesCombisQuantityCheckMode(),
			'use_properties_combis_shipping_time' => $settings->usePropertiesCombisShippingTime()
		);
		
		$customerStatusIds = $this->customerStatusProvider->getCustomerStatusIds();
		
		foreach($customerStatusIds as $customerStatusId)
		{
			$setArray['group_permission_'
			          . $customerStatusId] = (int)$settings->isPermittedCustomerStatus(new IdType($customerStatusId));
		}

		$whereArray = array(
			'products_id' => $productId->asInt()
		);

		$this->db->update($this->table, $setArray, $whereArray);

		return $this;
	}
}