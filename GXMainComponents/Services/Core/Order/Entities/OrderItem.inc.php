<?php
/* --------------------------------------------------------------
   OrderItem.php 2016-03-15
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('OrderItemInterface');

/**
 * Class OrderItem
 *
 * @category   System
 * @package    Order
 * @subpackage Entities
 */
class OrderItem implements OrderItemInterface
{
	/**
	 * Product model.
	 *
	 * @var string
	 */
	protected $productModel = '';
	
	/**
	 * Name.
	 *
	 * @var string
	 */
	protected $name = '';
	
	/**
	 * Price.
	 *
	 * @var float
	 */
	protected $price = 0.0;
	
	/**
	 * Quantity.
	 *
	 * @var float
	 */
	protected $quantity = 0.0;
	
	/**
	 * Tax amount.
	 *
	 * @var float
	 */
	protected $tax = 0.0;
	
	/**
	 * Tax allowed on this order item.
	 *
	 * @var bool
	 */
	protected $taxAllowed = false;
	
	/**
	 * Amount of discount made on this order item..
	 *
	 * @var float
	 */
	protected $discountMade = 0.0;
	
	/**
	 * Order item shipping time info text.
	 *
	 * @var string
	 */
	protected $shippingTimeInfo = '';
	
	/**
	 * Attributes of the order item.
	 *
	 * @var OrderItemAttributeCollection
	 */
	protected $attributes;
	
	/**
	 * Order item quantity unit name.
	 *
	 * @var string
	 */
	protected $quantityUnitName = '';
	
	/**
	 * Checkout information.
	 *
	 * @var string
	 */
	protected $checkoutInformation = '';
	
	/**
	 * Download information.
	 *
	 * @var OrderItemDownloadInformationCollection
	 */
	protected $downloadInformation;
	
	/**
	 * Order item addon collection.
	 *
	 * @var EditableKeyValueCollection
	 */
	protected $addonValues;
	
	
	/**
	 * OrderItem constructor.
	 *
	 * @param StringType $name Order item name.
	 */
	public function __construct(StringType $name)
	{
		$this->setName($name);
		
		// Set empty download information
		$this->downloadInformation = MainFactory::create('OrderItemDownloadInformationCollection');
		
		// Set addon values collection.
		// Note, that there is no setter method for assign the addonValues collection.
		$addonValues       = MainFactory::create('EditableKeyValueCollection', array());
		$this->addonValues = $addonValues;
	}
	
	
	/**
	 * Returns the product model of the order item.
	 *
	 * @return string Product model of the order item.
	 */
	public function getProductModel()
	{
		return $this->productModel;
	}
	
	
	/**
	 * Returns the name of the order item.
	 *
	 * @return string Name of the order item.
	 */
	public function getName()
	{
		return $this->name;
	}
	
	
	/**
	 * Returns the price of the order item.
	 *
	 * @return double Price of the order item.
	 */
	public function getPrice()
	{
		return $this->price;
	}
	
	
	/**
	 * Returns the quantity of the order item.
	 *
	 * @return double Quantity of the order item.
	 */
	public function getQuantity()
	{
		return $this->quantity;
	}
	
	
	/**
	 * Returns the final price of the order item.
	 *
	 * @return float Final price of the order item.
	 */
	public function getFinalPrice()
	{
		return $this->price * $this->quantity;
	}
	
	
	/**
	 * Returns the tax of the order item.
	 *
	 * @return float Tax of the order item.
	 */
	public function getTax()
	{
		return $this->tax;
	}
	
	
	/**
	 * Is tax of the order item allowed?
	 *
	 * @return bool Is tax of the order item allowed?
	 */
	public function isTaxAllowed()
	{
		return $this->taxAllowed;
	}
	
	
	/**
	 * Returns the amount of discount of the order item.
	 *
	 * @return float Amount of discount of the order item.
	 */
	public function getDiscountMade()
	{
		return $this->discountMade;
	}
	
	
	/**
	 * Returns the shipping time of the order item.
	 *
	 * @return string Shipping time of the order item.
	 */
	public function getShippingTimeInfo()
	{
		return $this->shippingTimeInfo;
	}
	
	
	/**
	 * Returns the attributes of the order item.
	 *
	 * @return OrderItemAttributeCollection Attributes of the order item.
	 */
	public function getAttributes()
	{
		// If no collection is set, create a new empty one.
		if(null === $this->attributes)
		{
			$this->attributes = MainFactory::create('OrderItemAttributeCollection', array());
		}
		
		return $this->attributes;
	}
	
	
	/**
	 * Returns the name of quantity unit of the order item.
	 *
	 * @return string Name of quantity unit of the order item.
	 */
	public function getQuantityUnitName()
	{
		return $this->quantityUnitName;
	}
	
	
	/**
	 * Returns the checkout information of the order item.
	 *
	 * @return string Checkout information of the order item.
	 */
	public function getCheckoutInformation()
	{
		return $this->checkoutInformation;
	}
	
	
	/**
	 * Returns the download information collection of the order item.
	 *
	 * @return OrderItemDownloadInformationCollection Download information collection of the order item.
	 */
	public function getDownloadInformation()
	{
		return $this->downloadInformation;
	}
	
	
	/**
	 * Returns the addon collection of an order item.
	 *
	 * @return EditableKeyValueCollection Addon collection.
	 */
	public function getAddonValues()
	{
		return $this->addonValues->getClone();
	}
	
	
	/**
	 * Returns the order addon key value from collection.
	 *
	 * @param StringType $key Addon key.
	 *
	 * @return string Addon value.
	 */
	public function getAddonValue(StringType $key)
	{
		return $this->addonValues->getValue($key->asString());
	}
	
	
	/**
	 * Sets product model of the OrderItem.
	 *
	 * @param StringType $model Model of the OrderItem.
	 *
	 * @return OrderItem Same instance for method chaining.
	 */
	public function setProductModel(StringType $model)
	{
		$this->productModel = $model->asString();
		
		return $this;
	}
	
	
	/**
	 * Sets name of the OrderItem.
	 *
	 * @param StringType $name Name of the OrderItem.
	 *
	 * @return OrderItem Same instance for method chaining.
	 */
	public function setName(StringType $name)
	{
		$this->name = $name->asString();
		
		return $this;
	}
	
	
	/**
	 * Sets price of the OrderItem.
	 *
	 * @param DecimalType $price Price of the OrderItem.
	 *
	 * @return OrderItem Same instance for method chaining.
	 */
	public function setPrice(DecimalType $price)
	{
		$this->price = $price->asDecimal();
		
		return $this;
	}
	
	
	/**
	 * Sets quantity of the OrderItem.
	 *
	 * @param DecimalType $quantity Quantity of the OrderItem.
	 *
	 * @return OrderItem Same instance for method chaining.
	 */
	public function setQuantity(DecimalType $quantity)
	{
		$this->quantity = $quantity->asDecimal();
		
		return $this;
	}
	
	
	/**
	 * Sets tax of the OrderItem.
	 *
	 * @param DecimalType $tax Tax of the OrderItem.
	 *
	 * @return OrderItem Same instance for method chaining.
	 */
	public function setTax(DecimalType $tax)
	{
		$this->tax = $tax->asDecimal();
		
		return $this;
	}
	
	
	/**
	 * Sets whether tax of the OrderItem is allowed or not.
	 *
	 * @param BoolType $allow Tax allowed or not?
	 *
	 * @return OrderItem Same instance for method chaining.
	 */
	public function setTaxAllowed(BoolType $allow)
	{
		$this->taxAllowed = $allow->asBool();
		
		return $this;
	}
	
	
	/**
	 * Sets discount of the OrderItem.
	 *
	 * @param DecimalType $discount Discount of the OrderItem.
	 *
	 * @return OrderItem Same instance for method chaining.
	 */
	public function setDiscountMade(DecimalType $discount)
	{
		$this->discountMade = $discount->asDecimal();
		
		return $this;
	}
	
	
	/**
	 * Sets shipping time of the OrderItem.
	 *
	 * @param StringType $time Shipping time of the OrderItem.
	 *
	 * @return OrderItem Same instance for method chaining.
	 */
	public function setShippingTimeInfo(StringType $time)
	{
		$this->shippingTimeInfo = $time->asString();
		
		return $this;
	}
	
	
	/**
	 * Sets attributes of the OrderItem.
	 *
	 * @param OrderItemAttributeCollection $attributeCollection Attributes of the OrderItem.
	 *
	 * @return OrderItem Same instance for method chaining.
	 */
	public function setAttributes(OrderItemAttributeCollection $attributeCollection)
	{
		$this->attributes = $attributeCollection;
		
		return $this;
	}
	
	
	/**
	 * Sets name of quantity unit of the OrderItem.
	 *
	 * @param StringType $name Name of quantity unit of the OrderItem.
	 *
	 * @return OrderItem Same instance for method chaining.
	 */
	public function setQuantityUnitName(StringType $name)
	{
		$this->quantityUnitName = $name->asString();
		
		return $this;
	}
	
	
	/**
	 * Sets the checkout information.
	 *
	 * @param StringType $checkoutInformation Contains the checkout info of the order item.
	 *
	 * @return OrderItem Same instance for method chaining.
	 */
	public function setCheckoutInformation(StringType $checkoutInformation)
	{
		$this->checkoutInformation = $checkoutInformation->asString();
		
		return $this;
	}
	
	
	/**
	 * Sets the download information of the OrderItem.
	 *
	 * @param OrderItemDownloadInformationCollection $downloads Download information collection.
	 *
	 * @return OrderItem Same instance for method chaining.
	 */
	public function setDownloadInformation(OrderItemDownloadInformationCollection $downloads)
	{
		$this->downloadInformation = $downloads;
		
		return $this;
	}
	
	
	/**
	 * Adds/updates a key value in the addon value collection.
	 *
	 * @param StringType $key   Addon key.
	 * @param StringType $value Addon value.
	 *
	 * @return OrderItem Same instance for method chaining.
	 */
	public function setAddonValue(StringType $key, StringType $value)
	{
		$this->addonValues->setValue($key->asString(), $value->asString());
		
		return $this;
	}
	
	
	/**
	 * Merges the existing addon values with new ones.
	 *
	 * @param KeyValueCollection $addonValues Contains the new addon values to be merged with the existing ones.
	 *
	 * @return OrderItem Same instance for method chaining.
	 */
	public function addAddonValues(KeyValueCollection $addonValues)
	{
		$this->addonValues->addCollection($addonValues);
		
		return $this;
	}
	
	
	/**
	 * Deletes a specific addon value entry by key.
	 *
	 * @param StringType $key Addon key.
	 *
	 * @return OrderItem Same instance for method chaining.
	 */
	public function deleteAddonValue(StringType $key)
	{
		$this->addonValues->deleteValue($key->asString());
		
		return $this;
	}
}