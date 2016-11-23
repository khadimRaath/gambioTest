<?php

/* --------------------------------------------------------------
   ProductSettings.inc.php 2016-01-28
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class ProductSettings
 *
 * @category   System
 * @package    Product
 * @subpackage Entities
 */
class ProductSettings implements ProductSettingsInterface
{
	/**
	 * Details template.
	 *
	 * @var string
	 */
	protected $detailsTemplate = '';

	/**
	 * Option detail template.
	 *
	 * @var string
	 */
	protected $optionsDetailsTemplate = '';

	/**
	 * Options listing template.
	 *
	 * @var string
	 */
	protected $optionsListingTemplate = '';

	/**
	 * Show on start page?
	 *
	 * @var bool
	 */
	protected $showOnStartpage = false;

	/**
	 * Start page sort order.
	 *
	 * @var int
	 */
	protected $startpageSortOrder = 0;

	/**
	 * Show added time?
	 *
	 * @var bool
	 */
	protected $showAddedDateTime = false;

	/**
	 * Show quantity info?
	 *
	 * @var bool
	 */
	protected $showQuantityInfo = false;

	/**
	 * Show weight?
	 *
	 * @var bool
	 */
	protected $showWeight = false;

	/**
	 * Show price offer?
	 *
	 * @var bool
	 */
	protected $showPriceOffer = false;

	/**
	 * Price status.
	 *
	 * @var int
	 */
	protected $priceStatus = 0;

	/**
	 * Minimal order value.
	 *
	 * @var float
	 */
	protected $minOrder = 0.0;

	/**
	 * Graduated quantity.
	 *
	 * @var float
	 */
	protected $graduatedQuantity = 0.0;

	/**
	 * Is listed as an entry in the sitemap?
	 *
	 * @var bool
	 */
	protected $sitemapEntry = false;

	/**
	 * Sitemap entry priority.
	 *
	 * @var string
	 */
	protected $sitemapPriority = '';

	/**
	 * Sitemap change frequency.
	 *
	 * @var string
	 */
	protected $sitemapChangeFreq = '';

	/**
	 * Show properties price?
	 *
	 * @var bool
	 */
	protected $showPropertiesPrice = false;

	/**
	 * Properties dropdown mode.
	 *
	 * @var string
	 */
	protected $propertiesDropdownMode = '';

	/**
	 * Use properties combination weight?
	 *
	 * @var bool
	 */
	protected $propertiesCombisWeight = false;

	/**
	 * Use properties combination quantity?
	 *
	 * @var int
	 */
	protected $propertiesCombisQuantityCheckMode = 0;

	/**
	 * Use properties combination shipping time?
	 *
	 * @var bool
	 */
	protected $propertiesCombisShippingTime = false;

	/**
	 * Permitted customer status.
	 *
	 * @var array
	 */
	protected $permittedCustomerStatus = array();


	/**
	 * Returns the details template name.
	 *
	 * @return string
	 */
	public function getDetailsTemplate()
	{
		return $this->detailsTemplate;
	}


	/**
	 * Returns the options details template.
	 *
	 * @return string
	 */
	public function getOptionsDetailsTemplate()
	{
		return $this->optionsDetailsTemplate;
	}


	/**
	 * Returns the options listing template.
	 *
	 * @return string
	 */
	public function getOptionsListingTemplate()
	{
		return $this->optionsListingTemplate;
	}


	/**
	 * Returns true when the product is displayed on the start page, false otherwise.
	 *
	 * @return bool
	 */
	public function showOnStartpage()
	{
		return $this->showOnStartpage;
	}


	/**
	 * Returns the sort position of the startpage.
	 *
	 * @return int
	 */
	public function getStartpageSortOrder()
	{
		return $this->startpageSortOrder;
	}


	/**
	 * Returns true when the added date time is to be displayed, false otherwise.
	 *
	 * @return bool
	 */
	public function showAddedDateTime()
	{
		return $this->showAddedDateTime;
	}


	/**
	 * Returns true when the quantity info is to be displayed, false otherwise.
	 *
	 * @return bool
	 */
	public function showQuantityInfo()
	{
		return $this->showQuantityInfo;
	}


	/**
	 * Returns true when the weight is to be displayed, false otherwise.
	 *
	 * @return bool
	 */
	public function showWeight()
	{
		return $this->showWeight;
	}


	/**
	 * Returns true when the price offer is to be displayed, false otherwise.
	 *
	 * @return bool
	 */
	public function showPriceOffer()
	{
		return $this->showPriceOffer;
	}


	/**
	 * Returns the price status.
	 *
	 * @return int
	 */
	public function getPriceStatus()
	{
		return $this->priceStatus;
	}


	/**
	 * Returns the minimum order value.
	 *
	 * @return float
	 */
	public function getMinOrder()
	{
		return $this->minOrder;
	}


	/**
	 * Returns the graduated quantity.
	 *
	 * @return float
	 */
	public function getGraduatedQuantity()
	{
		return $this->graduatedQuantity;
	}


	/**
	 * Returns true when the product is to be displayed in the sitemap, false otherwise.
	 *
	 * @return bool
	 */
	public function isSitemapEntry()
	{
		return $this->sitemapEntry;
	}


	/**
	 * Returns the sitemap priority.
	 *
	 * @return string
	 */
	public function getSitemapPriority()
	{
		return $this->sitemapPriority;
	}


	/**
	 * Returns the sitemap change frequency.
	 *
	 * @return string
	 */
	public function getSitemapChangeFreq()
	{
		return $this->sitemapChangeFreq;
	}


	/**
	 * Returns true when the properties price is to be displayed, false otherwise.
	 *
	 * @return bool
	 */
	public function showPropertiesPrice()
	{
		return $this->showPropertiesPrice;
	}


	/**
	 * Returns the properties dropdown mode.
	 *
	 * @return string
	 */
	public function getPropertiesDropdownMode()
	{
		return $this->propertiesDropdownMode;
	}


	/**
	 * Returns true when the properties combis weight is to be used, false otherwise.
	 *
	 * @return bool
	 */
	public function usePropertiesCombisWeight()
	{
		return $this->propertiesCombisWeight;
	}


	/**
	 * Returns the mode which is used for the quantity check.
	 *
	 * 0 = Default (global stock options)
	 * 1 = Products quantity
	 * 2 = Combis quantity
	 * 3 = No check
	 *
	 * @return int
	 */
	public function getPropertiesCombisQuantityCheckMode()
	{
		return $this->propertiesCombisQuantityCheckMode;
	}


	/**
	 * Returns true when the properties combis shipping time is to be used, false otherwise.
	 *
	 * @return bool
	 */
	public function usePropertiesCombisShippingTime()
	{
		return $this->propertiesCombisShippingTime;
	}


	/**
	 * Sets the details listing template.
	 *
	 * @param StringType $template Name of the template
	 *
	 * @return ProductSettings|$this Same ProductSettings instance for chained method calls.
	 */
	public function setDetailsTemplate(StringType $template)
	{
		$this->detailsTemplate = $template->asString();

		return $this;
	}


	/**
	 * Sets the options details template.
	 *
	 * @param StringType $template Name of the template.
	 *
	 * @return ProductSettings|$this Same ProductSettings instance for chained method calls.
	 */
	public function setOptionsDetailsTemplate(StringType $template)
	{
		$this->optionsDetailsTemplate = $template->asString();

		return $this;
	}


	/**
	 * Sets the options listing template.
	 *
	 * @param StringType $template Name of the template.
	 *
	 * @return ProductSettings|$this Same ProductSettings instance for chained method calls.
	 */
	public function setOptionsListingTemplate(StringType $template)
	{
		$this->optionsListingTemplate = $template->asString();

		return $this;
	}


	/**
	 * Determine whether the product is to be displayed on the startpage or not.
	 *
	 * @param BoolType $status True when it is to be displayed, false otherwise.
	 *
	 * @return ProductSettings|$this Same ProductSettings instance for chained method calls.
	 */
	public function setShowOnStartpage(BoolType $status)
	{
		$this->showOnStartpage = $status->asBool();

		return $this;
	}


	/**
	 * Sets the start page sort order.
	 *
	 * @param IntType $sortOrder Sort position.
	 *
	 * @return ProductSettings|$this Same ProductSettings instance for chained method calls.
	 */
	public function setStartpageSortOrder(IntType $sortOrder)
	{
		$this->startpageSortOrder = $sortOrder->asInt();

		return $this;
	}


	/**
	 * Shows or hides the added date time of a product.
	 *
	 * @param BoolType $status True when it is to be displayed, false otherwise.
	 *
	 * @return ProductSettings|$this Same ProductSettings instance for chained method calls.
	 */
	public function setShowAddedDateTime(BoolType $status)
	{
		$this->showAddedDateTime = $status->asBool();

		return $this;
	}


	/**
	 * Shows or hides the quantity info of a product.
	 *
	 * @param BoolType $status True when it is to be displayed, false otherwise.
	 *
	 * @return ProductSettings|$this Same ProductSettings instance for chained method calls.
	 */
	public function setShowQuantityInfo(BoolType $status)
	{
		$this->showQuantityInfo = $status->asBool();

		return $this;
	}


	/**
	 * Shows or hides the weight of a product.
	 *
	 * @param BoolType $status True when it is to be displayed, false otherwise.
	 *
	 * @return ProductSettings|$this Same ProductSettings instance for chained method calls.
	 */
	public function setShowWeight(BoolType $status)
	{
		$this->showWeight = $status->asBool();

		return $this;
	}


	/**
	 * Shows or hides the price offer of a product.
	 *
	 * @param BoolType $status True when it is to be displayed, false otherwise.
	 *
	 * @return ProductSettings|$this Same ProductSettings instance for chained method calls.
	 */
	public function setShowPriceOffer(BoolType $status)
	{
		$this->showPriceOffer = $status->asBool();

		return $this;
	}


	/**
	 * Sets the price status.
	 *
	 * @param IntType $status New price status.
	 *
	 * @return ProductSettings|$this Same ProductSettings instance for chained method calls.
	 */
	public function setPriceStatus(IntType $status)
	{
		$this->priceStatus = $status->asInt();

		return $this;
	}


	/**
	 * Sets the min order value.
	 *
	 * @param DecimalType $quantity New minimum order.
	 *
	 * @return ProductSettings|$this Same ProductSettings instance for chained method calls.
	 */
	public function setMinOrder(DecimalType $quantity)
	{
		$this->minOrder = $quantity->asDecimal();

		return $this;
	}


	/**
	 * Sets the graduated quantity.
	 *
	 * @param DecimalType $quantity New graduated quantity.
	 *
	 * @return ProductSettings|$this Same ProductSettings instance for chained method calls.
	 */
	public function setGraduatedQuantity(DecimalType $quantity)
	{
		$this->graduatedQuantity = $quantity->asDecimal();

		return $this;
	}


	/**
	 * Determine whether the product is to be displayed in the sitemap or not.
	 *
	 * @param BoolType $status True when it is to be displayed, false otherwise.
	 *
	 * @return ProductSettings|$this Same ProductSettings instance for chained method calls.
	 */
	public function setSitemapEntry(BoolType $status)
	{
		$this->sitemapEntry = $status->asBool();

		return $this;
	}


	/**
	 * Sets the sitemap priority.
	 *
	 * @param StringType $priority New sitemap priority.
	 *
	 * @return ProductSettings|$this Same ProductSettings instance for chained method calls.
	 */
	public function setSitemapPriority(StringType $priority)
	{
		$this->sitemapPriority = $priority->asString();

		return $this;
	}


	/**
	 * Sets the sitemap change frequency.
	 *
	 * @param StringType $freq New sitemap change frequency.
	 *
	 * @return ProductSettings|$this Same ProductSettings instance for chained method calls.
	 */
	public function setSitemapChangeFreq(StringType $freq)
	{
		$this->sitemapChangeFreq = $freq->asString();

		return $this;
	}


	/**
	 * Shows or hides the properties price of a product.
	 *
	 * @param BoolType $status True when it is to be displayed, false otherwise.
	 *
	 * @return ProductSettings|$this Same ProductSettings instance for chained method calls.
	 */
	public function setShowPropertiesPrice(BoolType $status)
	{
		$this->showPropertiesPrice = $status->asBool();

		return $this;
	}


	/**
	 * Sets the properties dropdown mode.
	 *
	 * @param StringType $mode New properties dropdown mode.
	 *
	 * @return ProductSettings|$this Same ProductSettings instance for chained method calls.
	 */
	public function setPropertiesDropdownMode(StringType $mode)
	{
		$this->propertiesDropdownMode = $mode->asString();

		return $this;
	}


	/**
	 * Determine whether the properties combis weight is to be used or not.
	 *
	 * @param BoolType $status True when it is to be used, false otherwise.
	 *
	 * @return ProductSettings|$this Same ProductSettings instance for chained method calls.
	 */
	public function setUsePropertiesCombisWeight(BoolType $status)
	{
		$this->propertiesCombisWeight = $status->asBool();

		return $this;
	}


	/**
	 * Determine which mode for the quantity check should be used.
	 *
	 * 0 = Default (global stock options)
	 * 1 = Products quantity
	 * 2 = Combis quantity
	 * 3 = No check
	 *
	 * @param IntType $status
	 *
	 * @return ProductSettings|$this Same ProductSettings instance for chained method calls.
	 */
	public function setPropertiesCombisQuantityCheckMode(IntType $status)
	{
		$this->propertiesCombisQuantityCheckMode = $status->asInt();

		return $this;
	}


	/**
	 * Determine whether the properties combis shipping time is to be used or not.
	 *
	 * @param BoolType $status True when it is to be used, false otherwise.
	 *
	 * @return ProductSettings|$this Same ProductSettings instance for chained method calls.
	 */
	public function setUsePropertiesCombisShippingTime(BoolType $status)
	{
		$this->propertiesCombisShippingTime = $status->asBool();

		return $this;
	}


	/**
	 * Returns true when the customer status is permitted, false otherwise.
	 *
	 * @param IdType $customerStatusId Id of customer status.
	 *
	 * @return bool
	 */
	public function isPermittedCustomerStatus(IdType $customerStatusId)
	{
		return (array_key_exists($customerStatusId->asInt(),
		                         $this->permittedCustomerStatus)) ? $this->permittedCustomerStatus[$customerStatusId->asInt()] : false;
	}


	/**
	 * Sets customer status permissions.
	 *
	 * @param IdType   $customerStatusId Id of customer status.
	 * @param BoolType $permitted        Is customer permitted or not.
	 *
	 * @return ProductSettings|$this Same ProductSettings instance for chained method calls.
	 */
	public function setPermittedCustomerStatus(IdType $customerStatusId, BoolType $permitted)
	{
		$this->permittedCustomerStatus[$customerStatusId->asInt()] = $permitted->asBool();

		return $this;
	}
}