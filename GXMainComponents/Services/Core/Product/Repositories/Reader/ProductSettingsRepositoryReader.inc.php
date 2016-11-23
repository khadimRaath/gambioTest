<?php
/* --------------------------------------------------------------
   ProductSettingsRepositoryReader.inc.php 2016-05-25
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class ProductSettingsRepositoryReader
 *
 * @category   System
 * @package    Product
 * @subpackage Repositories
 */
class ProductSettingsRepositoryReader implements ProductSettingsRepositoryReaderInterface
{
	/**
	 *
	 * @var CI_DB_query_builder
	 */
	protected $db;

	/**
	 * @var ProductFactoryInterface
	 */
	protected $productFactory;
	
	/**
	 * Customer Status Provider
	 *
	 * @var CustomerStatusProviderInterface
	 */
	protected $customerStatusProvider;


	/**
	 * ProductSettingsRepositoryReader Constructor
	 *
	 * @param CI_DB_query_builder             $db                     Database connection.
	 * @param ProductFactoryInterface         $productFactory         Product factory.
	 * @param CustomerStatusProviderInterface $customerStatusProvider Customer Status Provider.
	 */
	public function __construct(CI_DB_query_builder $db,
	                            ProductFactoryInterface $productFactory,
	                            CustomerStatusProviderInterface $customerStatusProvider)
	{
		$this->db                     = $db;
		$this->productFactory         = $productFactory;
		$this->customerStatusProvider = $customerStatusProvider;
	}


	/**
	 * Returns a product settings instance by the given product id.
	 *
	 * @param IdType $productId Id of product entity.
	 *
	 * @throws UnexpectedValueException if the product was not found by the provided ID.
	 *
	 * @return ProductSettingsInterface Entity with product settings for the expected product id.
	 */
	public function getById(IdType $productId)
	{
		$this->db->select('*')->from('products')->where('products_id', $productId->asInt());
		$data = $this->db->get()->row_array();

		if($data === null)
		{
			throw new UnexpectedValueException('The requested Product was not found in database (ID:'
			                                   . $productId->asInt() . ')');
		}

		$product = $this->_createProductByArray($data);

		return $product;
	}


	/**
	 * Creates an empty settings object, gets Data from the database
	 *
	 * @param array $data Product data.
	 *
	 * @throws InvalidArgumentException if the provided data argument is not valid.
	 *
	 * @return GXEngineProduct Same instance for chained method calls.
	 */
	protected function _createProductByArray(array $data)
	{
		$productSettings = $this->productFactory->createProductSettings();

		$productSettings->setDetailsTemplate(new StringType((string)$data['product_template']));
		$productSettings->setGraduatedQuantity(new DecimalType((double)$data['gm_graduated_qty']));
		$productSettings->setSitemapEntry(new BoolType((bool)$data['gm_sitemap_entry']));
		$productSettings->setSitemapPriority(new StringType((string)$data['gm_priority']));
		$productSettings->setSitemapChangeFreq(new StringType((string)$data['gm_changefreq']));
		$productSettings->setMinOrder(new DecimalType((double)$data['gm_min_order']));
		$productSettings->setOptionsDetailsTemplate(new StringType((string)$data['options_template']));
		$productSettings->setOptionsListingTemplate(new StringType((string)$data['gm_options_template']));
		$productSettings->setPriceStatus(new IntType((int)$data['gm_price_status']));
		$productSettings->setPropertiesDropdownMode(new StringType((string)$data['properties_dropdown_mode']));
		$productSettings->setShowAddedDateTime(new BoolType((bool)$data['gm_show_date_added']));
		$productSettings->setShowOnStartpage(new BoolType((bool)$data['products_startpage']));
		$productSettings->setStartpageSortOrder(new IntType((int)$data['products_startpage_sort']));
		$productSettings->setShowPriceOffer(new BoolType((bool)$data['gm_show_price_offer']));
		$productSettings->setShowPropertiesPrice(new BoolType((string)$data['properties_show_price']));
		$productSettings->setShowWeight(new BoolType((bool)$data['gm_show_weight']));
		$productSettings->setPropertiesCombisQuantityCheckMode(new IntType((int)$data['use_properties_combis_quantity']));
		$productSettings->setUsePropertiesCombisShippingTime(new BoolType((string)$data['use_properties_combis_shipping_time']));
		$productSettings->setUsePropertiesCombisWeight(new BoolType((string)$data['use_properties_combis_weight']));
		$productSettings->setShowQuantityInfo(new BoolType((bool)$data['gm_show_qty_info']));
		
		// Get and apply group permissions.
		$groupPermissionIds = $this->customerStatusProvider->getCustomerStatusIds();
		$this->_setGroupPermissions($data, $groupPermissionIds, $productSettings);

		return $productSettings;
	}
	
	
	/**
	 * Sets the permitted customers statuses.
	 *
	 * @param array                    $product            The fetched product array from the database.
	 * @param array                    $groupPermissionIds Array of available group permission.
	 * @param ProductSettingsInterface $productSettings    Object to set the customer statuses.
	 *
	 * @return CategorySettingsRepositoryReader Same instance for chained method calls.
	 */
	protected function _setGroupPermissions($product, $groupPermissionIds, ProductSettingsInterface $productSettings)
	{
		foreach($groupPermissionIds as $id)
		{
			if(array_key_exists('group_permission_' . $id, $product))
			{
				$productSettings->setPermittedCustomerStatus(new IdType($id),
				                                             new BoolType((bool)$product['group_permission_' . $id]));
			}
		}
		
		return $this;
	}
}