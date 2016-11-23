<?php
/* --------------------------------------------------------------
   GXEngineProduct.inc.php 2016-03-14
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class GXEngineProduct
 *
 * @category   System
 * @package    Product
 * @subpackage Entities
 */
class GXEngineProduct implements ProductInterface
{
	/**
	 * Product settings.
	 *
	 * @var ProductSettingsInterface
	 */
	protected $settings;

	/**
	 * Determines whether this product is active.
	 *
	 * @var boolean
	 */
	protected $active = false;

	/**
	 * Sort order.
	 *
	 * @var int
	 */
	protected $sortOrder = 0;

	/**
	 * Available DateTime.
	 *
	 * @var EmptyDateTime
	 */
	protected $availableDateTime;

	/**
	 * Added DateTime.
	 *
	 * @var DateTime
	 */
	protected $addedDateTime;

	/**
	 * Last modified DateTime.
	 *
	 * @var DateTime
	 */
	protected $lastModifiedDateTime;

	/**
	 * Viewed count.
	 *
	 * @var EditableKeyValueCollection
	 */
	protected $viewedCount;

	/**
	 * Ordered count.
	 *
	 * @var int
	 */
	protected $orderedCount = 0;

	/**
	 * Names collection.
	 *
	 * @var EditableKeyValueCollection
	 */
	protected $names;

	/**
	 * Description collection.
	 *
	 * @var EditableKeyValueCollection
	 */
	protected $descriptions;

	/**
	 * Short description collection.
	 *
	 * @var EditableKeyValueCollection
	 */
	protected $shortDescriptions;

	/**
	 * Keyword collection.
	 *
	 * @var EditableKeyValueCollection
	 */
	protected $keywords;

	/**
	 * Meta title collection.
	 *
	 * @var EditableKeyValueCollection
	 */
	protected $metaTitles;

	/**
	 * Meta description collection.
	 *
	 * @var EditableKeyValueCollection
	 */
	protected $metaDescriptions;

	/**
	 * Meta keywords collection.
	 *
	 * @var EditableKeyValueCollection
	 */
	protected $metaKeywords;

	/**
	 * URL.
	 *
	 * @var EditableKeyValueCollection
	 */
	protected $url;

	/**
	 * URL keywords.
	 *
	 * @var EditableKeyValueCollection
	 */
	protected $urlKeywords;
	
	/**
	 * URL rewrites.
	 *
	 * @var UrlRewriteCollection
	 */
	protected $urlRewrites;

	/**
	 * Checkout information collection.
	 *
	 * @var EditableKeyValueCollection
	 */
	protected $checkoutInformation;

	/**
	 * Product model.
	 *
	 * @var string
	 */
	protected $productModel = '';

	/**
	 * EAN.
	 *
	 * @var string
	 */
	protected $ean = '';

	/**
	 * Price.
	 *
	 * @var float
	 */
	protected $price = 0.00;

	/**
	 * Tax class ID.
	 *
	 * @var int
	 */
	protected $taxClassId = 0;

	/**
	 * Quantity.
	 *
	 * @var float
	 */
	protected $quantity = 0.00;

	/**
	 * Weight.
	 *
	 * @var float
	 */
	protected $weight = 0.00;

	/**
	 * Discount allowed.
	 *
	 * @var float
	 */
	protected $discountAllowed = 0.00;

	/**
	 * Shipping costs.
	 *
	 * @var float
	 */
	protected $shippingCosts = 0.00;

	/**
	 * Shipping time ID.
	 *
	 * @var int
	 */
	protected $shippingTimeId = 0;

	/**
	 * Product type ID.
	 *
	 * @var int
	 */
	protected $productTypeId = 0;

	/**
	 * Manufacturer ID.
	 *
	 * @var int
	 */
	protected $manufacturerId = 0;

	/**
	 * Is FSK 18?
	 *
	 * @var bool
	 */
	protected $fsk18 = false;

	/**
	 * Is VPE active?
	 *
	 * @var bool
	 */
	protected $vpeActive = false;

	/**
	 * VPE ID.
	 *
	 * @var int
	 */
	protected $vpeId = 0;

	/**
	 * VPE value.
	 *
	 * @var float
	 */
	protected $vpeValue = 0.00;

	/**
	 * Addons collection.
	 *
	 * @var EditableKeyValueCollection
	 */
	protected $addonValues;

	/**
	 * Image container.
	 *
	 * @var ProductImageContainer
	 */
	protected $imageContainer;


	/**
	 * GXEngineProduct constructor.
	 *
	 * @param ProductSettingsInterface $settings Product settings.
	 */
	public function __construct(ProductSettingsInterface $settings)
	{
		$this->settings = $settings;

		$this->setAvailableDateTime(new EmptyDateTime());

		$this->setAddedDateTime(new DateTime());

		$this->setLastModifiedDateTime(new DateTime());

		$this->imageContainer = MainFactory::create('ProductImageContainer');

		$this->names = MainFactory::create('EditableKeyValueCollection', array());

		$this->viewedCount = MainFactory::create('EditableKeyValueCollection', array());

		$this->url = MainFactory::create('EditableKeyValueCollection', array());

		$this->descriptions = MainFactory::create('EditableKeyValueCollection', array());

		$this->shortDescriptions = MainFactory::create('EditableKeyValueCollection', array());

		$this->keywords = MainFactory::create('EditableKeyValueCollection', array());

		$this->metaTitles = MainFactory::create('EditableKeyValueCollection', array());

		$this->metaDescriptions = MainFactory::create('EditableKeyValueCollection', array());

		$this->metaKeywords = MainFactory::create('EditableKeyValueCollection', array());

		$this->urlKeywords = MainFactory::create('EditableKeyValueCollection', array());
		
		$this->urlRewrites = MainFactory::create('UrlRewriteCollection', array());

		$this->checkoutInformation = MainFactory::create('EditableKeyValueCollection', array());

		$this->addonValues = MainFactory::create('EditableKeyValueCollection', array());
	}


	/**
	 * Is Active
	 *
	 * Checks if a product is active.
	 *
	 * @return bool Product status.
	 */
	public function isActive()
	{
		return $this->active;
	}


	/**
	 * Get Sort Order
	 *
	 * Returns an integer which represents a specific sort order.
	 *
	 * @return int The sort order.
	 */
	public function getSortOrder()
	{
		return $this->sortOrder;
	}


	/**
	 * Get Available Date Time
	 *
	 * Returns the available date time of the product.
	 *
	 * @return DateTime The available date time.
	 */
	public function getAvailableDateTime()
	{
		return $this->availableDateTime;
	}


	/**
	 * Get Added Date Time
	 *
	 * Returns the added date time of the product.
	 *
	 * @return DateTime The added date time.
	 */
	public function getAddedDateTime()
	{
		return $this->addedDateTime;
	}


	/**
	 * Get Last Modified Date Time
	 *
	 * Returns the last modified date time.
	 *
	 * @return DateTime The last modified date time.
	 */
	public function getLastModifiedDateTime()
	{
		return $this->lastModifiedDateTime;
	}


	/**
	 * Get View Count
	 *
	 * Returns the current view count of the product, depending on the provided language code.
	 *
	 * @throws InvalidArgumentException if the language code is not valid.
	 *
	 * @param LanguageCode $language The language code of the language to be returned.
	 *
	 * @return int The current view count.
	 */
	public function getViewedCount(LanguageCode $language)
	{
		return $this->viewedCount->getValue($language->asString());
	}


	/**
	 * Get Ordered Count
	 *
	 * Returns the ordered count of the product.
	 *
	 * @return int The ordered count.
	 */
	public function getOrderedCount()
	{
		return $this->orderedCount;
	}


	/**
	 * Get Product Settings.
	 *
	 * Returns the product settings.
	 *
	 * @return ProductSettingsInterface
	 */
	public function getSettings()
	{
		return $this->settings;
	}


	/**
	 * Get Name
	 *
	 * Returns the name of the product, depending on the provided language code.
	 *
	 * @throws InvalidArgumentException if the language code is not valid.
	 *
	 * @param LanguageCode $language The language code of the name to return.
	 *
	 * @return string The name of the product.
	 */
	public function getName(LanguageCode $language)
	{
		return $this->names->getValue($language->asString());
	}


	/**
	 * Get Description
	 *
	 * Returns the description of the product, depending on the provided language code.
	 *
	 * @throws InvalidArgumentException if the language code is not valid.
	 *
	 * @param LanguageCode $language The language code of the description to return.
	 *
	 * @return string The description of the product.
	 */
	public function getDescription(LanguageCode $language)
	{
		return $this->descriptions->getValue($language->asString());
	}


	/**
	 * Get Short Description
	 *
	 * Returns the short description of the product, depending on the provided language code.
	 *
	 * @throws InvalidArgumentException if the language code is not valid.
	 *
	 * @param LanguageCode $language The language code of the short description to return.
	 *
	 * @return string The short description of the product.
	 */
	public function getShortDescription(LanguageCode $language)
	{
		return $this->shortDescriptions->getValue($language->asString());
	}


	/**
	 * Get Keywords
	 *
	 * Returns the keywords of the product, depending on the provided language code.
	 *
	 * @throws InvalidArgumentException if the language code is not valid.
	 *
	 * @param LanguageCode $language The language code of the keywords to return.
	 *
	 * @return string The keywords of the product.
	 */
	public function getKeywords(LanguageCode $language)
	{
		return $this->keywords->getValue($language->asString());
	}


	/**
	 * Get Meta Title
	 *
	 * Returns the meta title of the product, depending on the provided language code.
	 *
	 * @throws InvalidArgumentException if the language code is not valid.
	 *
	 * @param LanguageCode $language The language code of the meta title to return.
	 *
	 * @return string The meta title of the product.
	 */
	public function getMetaTitle(LanguageCode $language)
	{
		return $this->metaTitles->getValue($language->asString());
	}


	/**
	 * Get Meta Description
	 *
	 * Returns the meta description of the product, depending on the provided language code.
	 *
	 * @throws InvalidArgumentException if the language code is not valid.
	 *
	 * @param LanguageCode $language The language code of the meta description to return.
	 *
	 * @return string The meta description of the product.
	 */
	public function getMetaDescription(LanguageCode $language)
	{
		return $this->metaDescriptions->getValue($language->asString());
	}


	/**
	 * Get Meta Keywords
	 *
	 * Returns the meta keywords of the product, depending on the provided language code.
	 *
	 * @throws InvalidArgumentException if the language code is not valid.
	 *
	 * @param LanguageCode $language The language code of the meta keywords to return.
	 *
	 * @return string The meta keywords of the product.
	 */
	public function getMetaKeywords(LanguageCode $language)
	{
		return $this->metaKeywords->getValue($language->asString());
	}


	/**
	 * Get Url
	 *
	 * Returns the URL of the product, depending on the provided language code.
	 *
	 * @throws InvalidArgumentException if the language code is not valid.
	 *
	 * @param LanguageCode $language The language code of the URL to return.
	 *
	 * @return string Product URL
	 */
	public function getUrl(LanguageCode $language)
	{
		return $this->url->getValue($language->asString());
	}


	/**
	 * Get URL Keywords
	 *
	 * Returns the URL keywords of the product, depending on the provided language code.
	 *
	 * @throws InvalidArgumentException if the language code is not valid.
	 *
	 * @param LanguageCode $language The language code of the URL keywords to be return.
	 *
	 * @return string The URL keywords of the product.
	 */
	public function getUrlKeywords(LanguageCode $language)
	{
		return $this->urlKeywords->getValue($language->asString());
	}
	
	
	/**
	 * Get URL rewrite
	 *
	 * Returns the URL rewrite of the product, depending on the provided language code.
	 *
	 * @throws InvalidArgumentException if the language code is not valid.
	 *
	 * @param LanguageCode $language The language code of the URL rewrite to be return.
	 *
	 * @return UrlRewrite The URL rewrite of the product.
	 */
	public function getUrlRewrite(LanguageCode $language)
	{
		return $this->urlRewrites->getValue($language->asString());
	}
	
	
	/**
	 * Get URL rewrites
	 *
	 * Returns the URL rewrites of the product.
	 *
	 * @return UrlRewriteCollection The URL rewrites of the product.
	 */
	public function getUrlRewrites()
	{
		return $this->urlRewrites;
	}


	/**
	 * Get Checkout Information
	 *
	 * Returns the checkout information of the product, depending on the provided language code.
	 *
	 * @throws InvalidArgumentException if the language code is not valid.
	 *
	 * @param LanguageCode $language The language code of the checkout information to be return.
	 *
	 * @return string The checkout information of the product.
	 */
	public function getCheckoutInformation(LanguageCode $language)
	{
		return $this->checkoutInformation->getValue($language->asString());
	}


	/**
	 * Get Product Model
	 *
	 * Returns the product model.
	 *
	 * @return string The product model.
	 */
	public function getProductModel()
	{
		return $this->productModel;
	}


	/**
	 * Get EAN
	 *
	 * Returns the EAN of the product.
	 *
	 * @return string The EAN of the product.
	 */
	public function getEan()
	{
		return $this->ean;
	}


	/**
	 * Get Price
	 *
	 * Returns the price of a product.
	 *
	 * @return float The price of the product.
	 */
	public function getPrice()
	{
		return $this->price;
	}


	/**
	 * Get Tax Class ID
	 *
	 * Returns the tax class ID of the product.
	 *
	 * @return int The tax class ID.
	 */
	public function getTaxClassId()
	{
		return $this->taxClassId;
	}


	/**
	 * Get Quantity
	 *
	 * Returns the quantity of the product.
	 *
	 * @return float The quantity of the product.
	 */
	public function getQuantity()
	{
		return $this->quantity;
	}


	/**
	 * Get Weight
	 *
	 * Returns the weight of the product.
	 *
	 * @return float The weight of the product.
	 */
	public function getWeight()
	{
		return $this->weight;
	}


	/**
	 * Get Discount Allowed
	 *
	 * Returns the allowed discount.
	 *
	 * @return float The allowed discount.
	 */
	public function getDiscountAllowed()
	{
		return $this->discountAllowed;
	}


	/**
	 * Get Shipping Costs
	 *
	 * Returns the shipping cost of the product.
	 *
	 * @return float The shipping costs of the product.
	 */
	public function getShippingCosts()
	{
		return $this->shippingCosts;
	}


	/**
	 * Get Shipping Time ID
	 *
	 * Returns the shipping time ID of the product.
	 *
	 * @return int The shipping time ID.
	 */
	public function getShippingTimeId()
	{
		return $this->shippingTimeId;
	}


	/**
	 * Get Product Type ID.
	 *
	 * Returns the product type ID.
	 *
	 * @return int The product type ID.
	 */
	public function getProductTypeId()
	{
		return $this->productTypeId;
	}


	/**
	 * Get Manufacturer ID
	 *
	 * Returns the manufacturer ID.
	 *
	 * @return int The manufacturer ID.
	 */
	public function getManufacturerId()
	{
		return $this->manufacturerId;
	}


	/**
	 * Is FSK 18
	 *
	 * Checks if the product is only available for FSK 18.
	 *
	 * @return bool Is the product FSK18?
	 */
	public function isFsk18()
	{
		return $this->fsk18;
	}


	/**
	 * Is VPE Active
	 *
	 * Checks if VPE is active on the product.
	 *
	 * @return bool Is VPE active on the product?
	 */
	public function isVpeActive()
	{
		return $this->vpeActive;
	}


	/**
	 * Get VPE ID.
	 *
	 * Returns the VPE ID.
	 *
	 * @return int VPE ID.
	 */
	public function getVpeId()
	{
		return $this->vpeId;
	}


	/**
	 * Get VPE Value
	 *
	 * Returns the VPE value.
	 *
	 * @return float The VPE value.
	 */
	public function getVpeValue()
	{
		return $this->vpeValue;
	}


	/**
	 * Get Addon Value
	 *
	 * Returns the addon value of a product, depending on the provided key.
	 *
	 * @throws InvalidArgumentException if the key is not valid.
	 *
	 * @param StringType $key The key of the addon value to return.
	 *
	 * @return string The addon value.
	 */
	public function getAddonValue(StringType $key)
	{
		return $this->addonValues->getValue($key->asString());
	}


	/**
	 * Get Addon Values
	 *
	 * Returns a key value collection of the product.
	 *
	 * @return KeyValueCollection The key value collection.
	 */
	public function getAddonValues()
	{
		return $this->addonValues;
	}


	/**
	 * Set Active
	 *
	 * Activates or deactivates a product status.
	 *
	 * @param BoolType $status The status to activate or deactivate the product.
	 *
	 * @return GXEngineProduct Same instance for chained method calls.
	 */
	public function setActive(BoolType $status)
	{
		$this->active = $status->asBool();

		return $this;
	}


	/**
	 * Set Sort Order
	 *
	 * Sets the sort order of the product.
	 *
	 * @param IntType $sortOrder The sort order.
	 *
	 * @return GXEngineProduct Same instance for chained method calls.
	 */
	public function setSortOrder(IntType $sortOrder)
	{
		$this->sortOrder = $sortOrder->asInt();

		return $this;
	}


	/**
	 * Set Available Date Time
	 *
	 * Sets an available date time.
	 *
	 * @param DateTime $date The date time to add.
	 *
	 * @return GXEngineProduct Same instance for chained method calls.
	 */
	public function setAvailableDateTime(DateTime $date)
	{
		$this->availableDateTime = $date;

		return $this;
	}


	/**
	 * Set Last Modified Date Time
	 *
	 * Sets the last modified date time.
	 *
	 * @param DateTime $date The last modified date time.
	 *
	 * @return GXEngineProduct Same instance for chained method calls.
	 */
	public function setLastModifiedDateTime(DateTime $date)
	{
		$this->lastModifiedDateTime = $date;

		return $this;
	}


	/**
	 * Set Viewed Count
	 *
	 * Sets the viewed count.
	 *
	 * @param IntType      $count    The amount of views.
	 * @param LanguageCode $language The language code for the product name.
	 *
	 * @return GXEngineProduct Same instance for chained method calls.
	 */
	public function setViewedCount(IntType $count, LanguageCode $language)
	{
		$this->viewedCount->setValue($language->asString(), $count->asInt());

		return $this;
	}


	/**
	 * Set Ordered Count
	 *
	 * Sets the ordered count.
	 *
	 * @param IntType $count The ordered count.
	 *
	 * @return GXEngineProduct Same instance for chained method calls.
	 */
	public function setOrderedCount(IntType $count)
	{
		$this->orderedCount = $count->asInt();

		return $this;
	}


	/**
	 * Set Image Container
	 *
	 * Sets the image container of a product.
	 *
	 * @param ProductImageContainerInterface $images
	 *
	 * @return GXEngineProduct Same instance for chained method calls.
	 */
	public function setImageContainer(ProductImageContainerInterface $images)
	{
		$this->imageContainer = $images;

		return $this;
	}


	/**
	 * Sets a product setting object
	 *
	 * @param ProductSettingsInterface $productSettings
	 *
	 * @return GXEngineProduct Same instance for chained method calls.
	 */
	public function setSettings(ProductSettingsInterface $productSettings)
	{
		$this->settings = $productSettings;

		return $this;
	}


	/**
	 * Set Name
	 *
	 * Sets the products name.
	 *
	 * @param StringType   $text     The name of the product.
	 * @param LanguageCode $language The language code for the product name.
	 *
	 * @return GXEngineProduct Same instance for chained method calls.
	 */
	public function setName(StringType $text, LanguageCode $language)
	{
		$this->names->setValue($language->asString(), $text->asString());

		return $this;
	}


	/**
	 * Set Description
	 *
	 * Sets the products description.
	 *
	 * @param StringType   $text     The description.
	 * @param LanguageCode $language The language code for the description.
	 *
	 * @return GXEngineProduct Same instance for chained method calls.
	 */
	public function setDescription(StringType $text, LanguageCode $language)
	{
		$this->descriptions->setValue($language->asString(), $text->asString());

		return $this;
	}


	/**
	 * Set Short Description
	 *
	 * Sets the products description.
	 *
	 * @param StringType   $text     The short description.
	 * @param LanguageCode $language The language code for the short description.
	 *
	 * @return GXEngineProduct Same instance for chained method calls.
	 */
	public function setShortDescription(StringType $text, LanguageCode $language)
	{
		$this->shortDescriptions->setValue($language->asString(), $text->asString());

		return $this;
	}


	/**
	 * Set Keywords
	 *
	 * Sets the products keywords.
	 *
	 * @param StringType   $text     The keywords.
	 * @param LanguageCode $language The language code for the keywords.
	 *
	 * @return GXEngineProduct Same instance for chained method calls.
	 */
	public function setKeywords(StringType $text, LanguageCode $language)
	{
		$this->keywords->setValue($language->asString(), $text->asString());

		return $this;
	}


	/**
	 * Set Meta title.
	 *
	 * Sets the meta title of a product.
	 *
	 * @param StringType   $text
	 * @param LanguageCode $language
	 *
	 * @return GXEngineProduct Same instance for chained method calls.
	 */
	public function setMetaTitle(StringType $text, LanguageCode $language)
	{
		$this->metaTitles->setValue($language->asString(), $text->asString());

		return $this;
	}


	/**
	 * Set Meta description.
	 *
	 * Sets the meta description of a product.
	 *
	 * @param StringType   $text
	 * @param LanguageCode $language
	 *
	 * @return GXEngineProduct Same instance for chained method calls.
	 */
	public function setMetaDescription(StringType $text, LanguageCode $language)
	{
		$this->metaDescriptions->setValue($language->asString(), $text->asString());

		return $this;
	}


	/**
	 * Set Meta Keywords
	 *
	 * Sets the products meta keywords.
	 *
	 * @param StringType   $text     The meta keywords.
	 * @param LanguageCode $language The language code for the meta keywords.
	 *
	 * @return GXEngineProduct Same instance for chained method calls.
	 */
	public function setMetaKeywords(StringType $text, LanguageCode $language)
	{
		$this->metaKeywords->setValue($language->asString(), $text->asString());

		return $this;
	}


	/**
	 * Set URL
	 *
	 * Sets the products URL.
	 *
	 * @param StringType   $url      The URL.
	 * @param LanguageCode $language The language code for the URL keywords.
	 *
	 * @return GXEngineProduct Same instance for chained method calls.
	 */
	public function setUrl(StringType $url, LanguageCode $language)
	{
		$this->url->setValue($language->asString(), $url->asString());

		return $this;
	}


	/**
	 * Set URL Keywords
	 *
	 * Sets the products URL Keywords.
	 *
	 * @param StringType   $text     The URL Keywords.
	 * @param LanguageCode $language The language code for the URL keywords.
	 *
	 * @return GXEngineProduct Same instance for chained method calls.
	 */
	public function setUrlKeywords(StringType $text, LanguageCode $language)
	{
		$this->urlKeywords->setValue($language->asString(), $text->asString());

		return $this;
	}
	
	
	/**
	 * Set URL rewrite
	 *
	 * Sets the products URL rewrite for the provided language code.
	 *
	 * @param UrlRewrite   $urlRewrite The URL rewrite instance.
	 * @param LanguageCode $language   The language code for the URL keywords.
	 *
	 * @return GXEngineProduct Same instance for chained method calls.
	 */
	public function setUrlRewrite(UrlRewrite $urlRewrite, LanguageCode $language)
	{
		$this->urlRewrites->setValue($language->asString(), $urlRewrite);
		
		return $this;
	}
	
	
	/**
	 * Set URL rewrites
	 *
	 * Sets the products URL rewrites.
	 *
	 * @param UrlRewriteCollection $urlRewrites The URL rewrites.
	 *
	 * @return GXEngineProduct Same instance for chained method calls.
	 */
	public function setUrlRewrites(UrlRewriteCollection $urlRewrites)
	{
		$this->urlRewrites = $urlRewrites;
		
		return $this;
	}


	/**
	 * Set Checkout Information
	 *
	 * Sets the checkout information of a product.
	 *
	 * @param StringType   $text     The checkout information.
	 * @param LanguageCode $language The language code for the checkout information.
	 *
	 * @return GXEngineProduct Same instance for chained method calls.
	 */
	public function setCheckoutInformation(StringType $text, LanguageCode $language)
	{
		$this->checkoutInformation->setValue($language->asString(), $text->asString());

		return $this;
	}


	/**
	 * Set Product Model
	 *
	 * Set the product model.
	 *
	 * @param StringType $model The product model.
	 *
	 * @return GXEngineProduct Same instance for chained method calls.
	 */
	public function setProductModel(StringType $model)
	{
		$this->productModel = $model->asString();

		return $this;
	}


	/**
	 * Set EAN
	 *
	 * Sets a EAN for the product.
	 *
	 * @param StringType $ean The EAN to set.
	 *
	 * @return GXEngineProduct Same instance for chained method calls.
	 */
	public function setEan(StringType $ean)
	{
		$this->ean = $ean->asString();

		return $this;
	}


	/**
	 * Set Price
	 *
	 * Sets a price of the product.
	 *
	 * @param DecimalType $price The price to set.
	 *
	 * @return GXEngineProduct Same instance for chained method calls.
	 */
	public function setPrice(DecimalType $price)
	{
		$this->price = $price->asDecimal();

		return $this;
	}


	/**
	 * Set Tax Class ID
	 *
	 * Sets a tax class ID for the product.
	 *
	 * @param IdType $id The tax class ID to set.
	 *
	 * @return GXEngineProduct Same instance for chained method calls.
	 */
	public function setTaxClassId(IdType $id)
	{
		$this->taxClassId = $id->asInt();

		return $this;
	}


	/**
	 * Set Quantity
	 *
	 * Sets a quantity for the product.
	 *
	 * @param DecimalType $quantity The quantity to set.
	 *
	 * @return GXEngineProduct Same instance for chained method calls.
	 */
	public function setQuantity(DecimalType $quantity)
	{
		$this->quantity = $quantity->asDecimal();

		return $this;
	}


	/**
	 * Set Weight
	 *
	 * Sets the weight of a product.
	 *
	 * @param DecimalType $weight The weight to set.
	 *
	 * @return GXEngineProduct Same instance for chained method calls.
	 */
	public function setWeight(DecimalType $weight)
	{
		$this->weight = $weight->asDecimal();

		return $this;
	}


	/**
	 * Set Discount Allowed
	 *
	 * Sets the allowed discount of a product.
	 *
	 * @param DecimalType $discount The discount to set.
	 *
	 * @return GXEngineProduct Same instance for chained method calls.
	 */
	public function setDiscountAllowed(DecimalType $discount)
	{
		$this->discountAllowed = $discount->asDecimal();

		return $this;
	}


	/**
	 * Set Shipping Costs
	 *
	 * Sets the shipping costs of a product.
	 *
	 * @param DecimalType $price The shipping costs to set.
	 *
	 * @return GXEngineProduct Same instance for chained method calls.
	 */
	public function setShippingCosts(DecimalType $price)
	{
		$this->shippingCosts = $price->asDecimal();

		return $this;
	}


	/**
	 * Set Shipping Time ID
	 *
	 * Sets the shipping time ID of a product.
	 *
	 * @param IdType $id The shipping time ID to set.
	 *
	 * @return GXEngineProduct Same instance for chained method calls.
	 */
	public function setShippingTimeId(IdType $id)
	{
		$this->shippingTimeId = $id->asInt();

		return $this;
	}


	/**
	 * Set Product Type ID.
	 *
	 * Sets the product type ID of the product.
	 *
	 * @param IdType $id Product type ID.
	 *
	 * @return GXEngineProduct
	 */
	public function setProductTypeId(IdType $id)
	{
		$this->productTypeId = $id->asInt();

		return $this;
	}


	/**
	 * Set Manufacturer ID
	 *
	 * Sets the manufacturer ID of a product.
	 *
	 * @param IdType $id The manufacturer ID to set.
	 *
	 * @return GXEngineProduct Same instance for chained method calls.
	 */
	public function setManufacturerId(IdType $id)
	{
		$this->manufacturerId = $id->asInt();

		return $this;
	}


	/**
	 * Set FSK 18
	 *
	 * Activates or deactivates FSK18 for a product.
	 *
	 * @param BoolType $status Should FSK be activated?
	 *
	 * @return GXEngineProduct Same instance for chained method calls.
	 */
	public function setFsk18(BoolType $status)
	{
		$this->fsk18 = $status->asBool();

		return $this;
	}


	/**
	 * Set VPE Active
	 *
	 * Activates or deactivates VPE for a product.
	 *
	 * @param BoolType $status Should VPE be activated?
	 *
	 * @return GXEngineProduct Same instance for chained method calls.
	 */
	public function setVpeActive(BoolType $status)
	{
		$this->vpeActive = $status->asBool();

		return $this;
	}


	/**
	 * Set VPE ID
	 *
	 * Sets the VPE ID of a product.
	 *
	 * @param IdType $id The VPE ID to set.
	 *
	 * @return GXEngineProduct Same instance for chained method calls.
	 */
	public function setVpeId(IdType $id)
	{
		$this->vpeId = $id->asInt();

		return $this;
	}


	/**
	 * Set VPE Value
	 *
	 * Sets the VPE value of a product.
	 *
	 * @param DecimalType $vpeValue The VPE value to set.
	 *
	 * @return GXEngineProduct Same instance for chained method calls.
	 */
	public function setVpeValue(DecimalType $vpeValue)
	{
		$this->vpeValue = $vpeValue->asDecimal();

		return $this;
	}


	/**
	 * Set Addon Value
	 *
	 * Sets the addon value of a product.
	 *
	 * @param StringType $key   The key for the addon value.
	 * @param StringType $value The value for the addon.
	 *
	 * @return GXEngineProduct Same instance for chained method calls.
	 */
	public function setAddonValue(StringType $key, StringType $value)
	{
		$this->addonValues->setValue($key->asString(), $value->asString());

		return $this;
	}


	/**
	 * Add Addon Values
	 *
	 * Adds a key value collection to a product.
	 *
	 * @param KeyValueCollection $keyValueCollection The key value collection to add.
	 *
	 * @return GXEngineProduct Same instance for chained method calls.
	 */
	public function addAddonValues(KeyValueCollection $keyValueCollection)
	{
		$this->addonValues->addCollection($keyValueCollection);

		return $this;
	}


	/**
	 * Delete Addon Value
	 *
	 * Deletes an addon value of a product.
	 *
	 * @throws InvalidArgumentException if the key is not valid.
	 *
	 * @param StringType $key The key of the addon value to delete.
	 *
	 * @return GXEngineProduct Same instance for chained method calls.
	 */
	public function deleteAddonValue(StringType $key)
	{
		$this->addonValues->deleteValue($key->asString());

		return $this;
	}


	/**
	 * Sets the added date time.
	 *
	 * @param DateTime $date Added date time.
	 *
	 * @return GXEngineProduct Same instance for chained method calls.
	 */
	public function setAddedDateTime(DateTime $date)
	{
		$this->addedDateTime = $date;

		return $this;
	}


	/**
	 * Returns the image container of the product.
	 *
	 * @return ProductImageContainer Product image container.
	 */
	public function getImageContainer()
	{
		return $this->imageContainer;
	}


	/**
	 * Returns the product's primary image.
	 *
	 * @return ProductImage
	 */
	public function getPrimaryImage()
	{
		return $this->imageContainer->getPrimary();
	}


	/**
	 * Returns a product's additional images.
	 *
	 * @return ProductImageCollection
	 */
	public function getAdditionalImages()
	{
		return $this->imageContainer->getAdditionals();
	}

}