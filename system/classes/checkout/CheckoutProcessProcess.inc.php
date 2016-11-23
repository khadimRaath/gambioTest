<?php
/* --------------------------------------------------------------
  CheckoutProcessProcess.inc.php 2016-07-11
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2016 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------


  based on:
  (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
  (c) 2002-2003 osCommerce(checkout_process.php,v 1.128 2003/05/28); www.oscommerce.com
  (c) 2003	 nextcommerce (checkout_process.php,v 1.30 2003/08/24); www.nextcommerce.org
  (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: checkout_process.php 1277 2005-10-01 17:02:59Z mz $)

  Released under the GNU General Public License
  ----------------------------------------------------------------------------------------
  Third Party contribution:

  Customers Status v3.x  (c) 2002-2003 Copyright Elari elari@free.fr | www.unlockgsm.com/dload-osc/ | CVS : http://cvs.sourceforge.net/cgi-bin/viewcvs.cgi/elari/?sortby=date#dirlist

  Credit Class/Gift Vouchers/Discount Coupons (Version 5.10)
  http://www.oscommerce.com/community/contributions,282
  Copyright (c) Strider | Strider@oscworks.com
  Copyright (c  Nick Stanko of UkiDev.com, nick@ukidev.com
  Copyright (c) Andre ambidex@gmx.net
  Copyright (c) 2001,2002 Ian C Wilson http://www.phesis.org

  Released under the GNU General Public License
  --------------------------------------------------------------------------------------- */

require_once(DIR_WS_CLASSES . 'payment.php');
require_once(DIR_WS_CLASSES . 'order_total.php');
require_once(DIR_FS_CATALOG . 'gm/inc/set_shipping_status.php');

MainFactory::load_class('DataProcessing');

/**
 * Class CheckoutProcessProcess
 *
 * This class handles the final checkout process. The main parts of this process are:
 * - Validating the order
 * - Processing payment actions
 * - Storing the order
 * - Stock adjustments
 * - Sending order email confirmation
 */
class CheckoutProcessProcess extends CheckoutControl
{
	/**
	 * @var order $coo_order
	 */
	protected $coo_order;
	
	/**
	 * @var order_total $coo_order_total
	 */
	protected $coo_order_total;
	
	/**
	 * @var payment $coo_payment
	 */
	protected $coo_payment;
	
	/**
	 * @var PropertiesControl $coo_properties
	 */
	protected $coo_properties;
	
	/**
	 * @var shipping $coo_shipping
	 */
	protected $coo_shipping;
	
	protected $tmp_order = false;
	protected $tmp_status;
	protected $order_id;
	protected $order_totals_array;
	
	/**
	 * @var OrderWriteService $orderWriteService ;
	 */
	protected $orderWriteService;


	/**
	 * CheckoutProcessProcess constructor.
	 *
	 * Checks if the checkout process was already started and sets a tmp_order-flag accordingly.
	 */
	public function __construct()
	{
		parent::__construct();
		
		if(isset($GLOBALS['tmp']))
		{
			$this->tmp_order = $GLOBALS['tmp'];
		}
	}


	/**
	 * Defines validation rules for data that will be set via public set_()-method
	 */
	protected function set_validation_rules()
	{
		$this->validation_rules_array['coo_order']          = array('type' => 'object', 'object_type' => 'order');
		$this->validation_rules_array['coo_order_total']    = array('type' => 'object', 'object_type' => 'order_total');
		$this->validation_rules_array['coo_payment']        = array('type' => 'object', 'object_type' => 'payment');
		$this->validation_rules_array['coo_properties']     = array(
			'type'        => 'object',
			'object_type' => 'PropertiesControl'
		);
		$this->validation_rules_array['coo_shipping']       = array('type' => 'object', 'object_type' => 'shipping');
		$this->validation_rules_array['tmp_order']          = array('type' => 'bool');
		$this->validation_rules_array['tmp_status']         = array('type' => 'int');
		$this->validation_rules_array['order_id']           = array('type' => 'int');
		$this->validation_rules_array['order_totals_array'] = array('type' => 'array');
	}


	/**
	 * The proceed method is the main method of the class and performs the complete checkout process.
	 *
	 * @return bool
	 */
	public function proceed()
	{
		if($this->check_redirect())
		{
			return true;
		}
		
		$this->_initOrderData();
		
		// check if tmp order id exists
		if(!isset($_SESSION['tmp_oID']) || !is_int($_SESSION['tmp_oID']))
		{
			$this->save_order();
			
			$this->save_module_data();
			$this->process_products();
			$this->save_tracking_data();
			
			// redirect to payment service
			if($this->tmp_order)
			{
				$this->coo_payment->payment_action();
			}
		}
		
		if($this->tmp_order == false)
		{
			// NEW EMAIL configuration !
			$this->coo_order_total->apply_credit();
			
			$this->send_order_mail();
			
			// load the after_process function from the payment modules
			$this->coo_payment->after_process();
			
			$this->reset();
			
			$this->set_redirect_url(xtc_href_link(FILENAME_CHECKOUT_SUCCESS, '', 'SSL'));
			
			return true;
		}
		
		return true;
	}


	/**
	 * The check_redirect method validates the process. If it's not valid, a redirect url will be set. If everything is
	 * fine, false will be returned, otherwise true.
	 *
	 * The following rules must be met:
	 * - cart items have to be in stock
	 * - the customer must be logged in
	 * - the customer is allowed to see prices
	 * - order customer address data is set
	 * - payment method is defined
	 * - cart was not manipulated during checkout process
	 *
	 * @return bool
	 */
	public function check_redirect()
	{
		// check if cart items are still in stock
		if($this->check_stock() === false)
		{
			$this->set_redirect_url(xtc_href_link(FILENAME_SHOPPING_CART));
			
			return true;
		}
		
		// if the customer is not logged on, redirect them to the login page
		if(!isset($_SESSION['customer_id']))
		{
			$this->set_redirect_url(xtc_href_link(FILENAME_LOGIN, '', 'SSL'));
			
			return true;
		}
		
		if($_SESSION['customers_status']['customers_status_show_price'] != '1')
		{
			$this->set_redirect_url(xtc_href_link(FILENAME_DEFAULT, '', 'NONSSL'));
			
			return true;
		}
		
		if(!isset($_SESSION['sendto']))
		{
			$this->set_redirect_url(xtc_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL'));
			
			return true;
		}
		
		if((xtc_not_null(MODULE_PAYMENT_INSTALLED)) && (!isset($_SESSION['payment']))
		   && (!isset($_SESSION['credit_covers']))
		)
		{
			$this->set_redirect_url(xtc_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL'));
			
			return true;
		}
		
		// avoid hack attempts during the checkout procedure by checking the internal cartID
		if(isset($_SESSION['cart']->cartID) && isset($_SESSION['cartID'])
		   && $_SESSION['cart']->cartID != $_SESSION['cartID']
		)
		{
			$this->set_redirect_url(xtc_href_link(FILENAME_CHECKOUT_SHIPPING, '', 'SSL'));
			
			return true;
		}
		
		return false;
	}


	/**
	 * The _initOrderData method will be executed at the beginning of the proceed method. It sets shipping, payment and
	 * order data that will be needed for the checkout process.
	 */
	protected function _initOrderData()
	{
		if(isset($_SESSION['credit_covers']))
		{
			$_SESSION['payment'] = ''; //ICW added for CREDIT CLASS
		}
		
		$this->coo_payment = new payment($_SESSION['payment']);
		
		// load the selected shipping module
		$this->coo_shipping = new shipping($_SESSION['shipping']);
		
		$GLOBALS['order'] = new order();
		$this->coo_order  = $GLOBALS['order'];
		
		// load the before_process function from the payment modules
		$this->coo_payment->before_process();
		
		$GLOBALS['order_total_modules'] = new order_total();
		$this->coo_order_total          = $GLOBALS['order_total_modules'];
		$this->order_totals_array       = $this->coo_order_total->process();
		$GLOBALS['order_totals']        =& $this->order_totals_array;
		
		# PropertiesControl Object
		$this->coo_properties = MainFactory::create_object('PropertiesControl');
		
		// check if tmp order id exists
		if(isset($_SESSION['tmp_oID']) && is_int($_SESSION['tmp_oID']))
		{
			$GLOBALS['tmp']       = false;
			$this->tmp_order      =& $GLOBALS['tmp'];
			$GLOBALS['insert_id'] = $_SESSION['tmp_oID'];
			$this->order_id       = $GLOBALS['insert_id'];
		}
		else
		{
			// check if tmp order need to be created
			//if (isset ($GLOBALS[$_SESSION['payment']]->form_action_url) && $GLOBALS[$_SESSION['payment']]->tmpOrders) {
			if($GLOBALS[$_SESSION['payment']]->tmpOrders == true)
			{
				$GLOBALS['tmp']   = true;
				$this->tmp_order  =& $GLOBALS['tmp'];
				$this->tmp_status = $GLOBALS[$_SESSION['payment']]->tmpStatus;
			}
			else
			{
				$GLOBALS['tmp']   = false;
				$this->tmp_order  =& $GLOBALS['tmp'];
				$this->tmp_status = $this->coo_order->info['order_status'];
			}
			
			$this->_setCCNumber();
			$this->_setComments();
		}
		
		/** @var OrderWriteService */
		$this->orderWriteService = StaticGXCoreLoader::getService('OrderWrite');
	}


	/**
	 * The _setCCNumber method encrypts the credit card number and writes it into the order object coo_order
	 */
	protected function _setCCNumber()
	{
		if(strtolower(CC_ENC) == 'true')
		{
			$ccNumber                           = $this->coo_order->info['cc_number'];
			$this->coo_order->info['cc_number'] = changedatain($ccNumber, CC_KEYCHAIN);
		}
	}


	/**
	 * The _setComments method writes comment data into the order object coo_order
	 */
	protected function _setComments()
	{
		if(isset($this->v_data_array['POST']['comments']) && empty($this->coo_order->info['comments']))
		{
			$this->coo_order->info['comments'] = gm_prepare_string($this->v_data_array['POST']['comments'], true);
		}
		elseif(isset($_SESSION['comments']) && empty($this->coo_order->info['comments']))
		{
			$this->coo_order->info['comments'] = $_SESSION['comments'];
		}
	}


	/**
	 * The save_order method stores the order and sets the orderId
	 */
	public function save_order()
	{
		$orderId = $this->orderWriteService->createNewCustomerOrder($this->_getCustomerId(),
		                                                            $this->_getCustomerStatusInformation(),
		                                                            $this->_getCustomerNumber(),
		                                                            $this->_getCustomerEmail(),
		                                                            $this->_getCustomerTelephone(),
		                                                            $this->_getCustomerVatId(),
		                                                            $this->_getCustomerDefaultAddress(),
		                                                            $this->_getBillingAddress(),
		                                                            $this->_getDeliveryAddress(),
		                                                            $this->_getOrderItemCollection(),
		                                                            $this->_getOrderTotalCollection(),
		                                                            $this->_getOrderShippingType(),
		                                                            $this->_getOrderPaymentType(),
		                                                            $this->_getCurrencyCode(),
		                                                            $this->_getLanguageCode(),
		                                                            $this->_getOrderTotalWeight(),
		                                                            $this->_getComment(),
		                                                            $this->_getOrderStatusId(),
		                                                            $this->_getOrderAddonValuesCollection());
		
		$this->_setOrderId($orderId);
	}


	/**
	 * The save_module_data method handles the order process of third party plug-ins like magnalister
	 */
	public function save_module_data()
	{
		/* magnalister v1.0.1 */
		if(function_exists('magnaExecute'))
		{
			magnaExecute('magnaInsertOrderDetails', array('oID' => (int)$this->order_id), array('order_details.php'));
		}
		
		if(function_exists('magnaExecute'))
		{
			magnaExecute('magnaInventoryUpdate', array('action' => 'inventoryUpdateOrder'),
			             array('inventoryUpdate.php'));
		}
		/* END magnalister */
	}
	
	
	/******************************************************************************************************************
	 * BEGIN METHODS BELONGING TO ORDER SERVICE PROCESS
	 ******************************************************************************************************************/
	
	/**
	 * The _getShippingTime method determines the shipping time text for a single product and returns it as a string.
	 *
	 * @param array $product
	 *
	 * @return string
	 */
	protected function _getShippingTime(array $product)
	{
		$coo_properties = MainFactory::create_object('PropertiesControl');
		$combisId       = $coo_properties->extract_combis_id($product['id']);
		$shippingTime   = (string)$product['shipping_time'];
		
		if(!empty($combisId))
		{
			$query  = 'SELECT use_properties_combis_shipping_time FROM products WHERE products_id = '
			          . (int)$product['id'];
			$result = xtc_db_query($query);
			$row    = xtc_db_fetch_array($result);
			if($row['use_properties_combis_shipping_time'] === '1')
			{
				require_once DIR_FS_CATALOG . 'includes/classes/main.php';
				$main         = new main();
				$query        = 'SELECT combi_shipping_status_id FROM products_properties_combis WHERE products_properties_combis_id = '
				                . (int)$combisId;
				$result       = xtc_db_query($query);
				$row          = xtc_db_fetch_array($result);
				$shippingTime = (string)$main->getShippingStatusName($row['combi_shipping_status_id']);
			}
		}
		
		return $shippingTime;
	}
	
	
	/**
	 * The _addProperties method adds OrderItemProperty objects to the given $orderItemAttributesArray.
	 * A OrderItemProperty objects contains property data like name, value and price.
	 *
	 * @param array $product
	 * @param array &$orderItemAttributesArray
	 */
	protected function _addProperties(array $product, array &$orderItemAttributesArray)
	{
		$combisId = $this->coo_properties->extract_combis_id($product['id']);
		if(!empty($combisId))
		{
			$propertiesArray = $this->coo_properties->get_properties_combis_details($combisId,
			                                                                        $_SESSION['languages_id']);
			
			/** @var OrderObjectService $orderObjectService */
			$orderObjectService = StaticGXCoreLoader::getService('OrderObject');
			
			foreach($propertiesArray as $property)
			{
				/** @var OrderItemProperty $orderItemProperty */
				$orderItemProperty = $orderObjectService->createOrderItemPropertyObject(new StringType($property['properties_name']),
				                                                                        new StringType($property['values_name']));
				$orderItemProperty->setCombisId(new IdType($combisId));
				$orderItemProperty->setPrice(new DecimalType($property['value_price']));
				
				if(isset($property['value_price_type']))
				{
					$orderItemProperty->setPriceType(new StringType($property['value_price_type']));
				}
				
				$orderItemAttributesArray[] = $orderItemProperty;
			}
		}
	}
	
	
	/**
	 * The _addProperties method adds OrderItemAttribute objects to the given $orderItemAttributesArray.
	 * A OrderItemProperty objects contains attribute data like name, value and price.
	 *
	 * @param array $product
	 * @param array &$orderItemAttributesArray
	 */
	protected function _addAttributes(array $product, array &$orderItemAttributesArray)
	{
		if(isset($product['attributes']))
		{
			foreach($product['attributes'] as $attribute)
			{
				$query = "SELECT
								popt.products_options_name,
								poval.products_options_values_name,
								pa.options_values_price,
								pa.price_prefix,
								pa.options_id,
								pa.options_values_id
							FROM
								" . TABLE_PRODUCTS_OPTIONS . " popt,
								" . TABLE_PRODUCTS_OPTIONS_VALUES . " poval,
								" . TABLE_PRODUCTS_ATTRIBUTES . " pa
							WHERE
								pa.products_id = '" . (int)$product['id'] . "' AND
								pa.options_id = '" . (int)$attribute['option_id'] . "' AND
								pa.options_id = popt.products_options_id AND
								pa.options_values_id = '" . (int)$attribute['value_id'] . "' AND
								pa.options_values_id = poval.products_options_values_id AND
								popt.language_id = '" . (int)$_SESSION['languages_id'] . "' AND
								poval.language_id = '" . (int)$_SESSION['languages_id'] . "'";
				
				$result = xtc_db_query($query);
				
				/** @var OrderObjectService $orderObjectService */
				$orderObjectService = StaticGXCoreLoader::getService('OrderObject');
				
				if(xtc_db_num_rows($result))
				{
					$row = xtc_db_fetch_array($result);
					
					$orderItemAttribute = $orderObjectService->createOrderItemAttributeObject(new StringType($row['products_options_name']),
					                                                                          new StringType($row['products_options_values_name']));
					$orderItemAttribute->setPrice(new DecimalType($row['options_values_price']));
					$orderItemAttribute->setPriceType(new StringType($row['price_prefix']));
					
					$orderItemAttributesArray[] = $orderItemAttribute;
				}
				else // GX-Customizer set data will be connected to empty attribute
				{
					$orderItemAttribute = $orderObjectService->createOrderItemAttributeObject(new StringType(''),
					                                                                          new StringType(''));
					
					$orderItemAttributesArray[] = $orderItemAttribute;
				}
			}
		}
	}
	
	
	/**
	 * The _getOrderTotalCollection method returns a collection of OrderTotal objects. It contains data like subtotal,
	 * shipping method / costs, VAT and total.
	 *
	 * @return OrderTotalCollection
	 */
	protected function _getOrderTotalCollection()
	{
		$orderTotalObjects = array();
		
		/** @var OrderObjectService $orderObjectService */
		$orderObjectService = StaticGXCoreLoader::getService('OrderObject');
		
		foreach($this->order_totals_array as $orderTotal)
		{
			$orderTotalObjects[] = $orderObjectService->createOrderTotalObject(new StringType($orderTotal['title']),
			                                                                   new DecimalType((float)$orderTotal['value']),
			                                                                   new StringType($orderTotal['text']),
			                                                                   new StringType($orderTotal['code']),
			                                                                   MainFactory::create('IntType',
			                                                                                       $orderTotal['sort_order']));
		}
		
		$orderTotals = MainFactory::create('OrderTotalCollection', $orderTotalObjects);
		
		return $orderTotals;
	}
	
	
	/**
	 * The _getOrderItemCollection method returns a collection of OrderItem objects. An OrderItem object represents a
	 * product with all its information.
	 *
	 * @return OrderItemCollection
	 */
	protected function _getOrderItemCollection()
	{
		$items = array();
		
		/** @var OrderObjectService $orderObjectService */
		$orderObjectService = StaticGXCoreLoader::getService('OrderObject');
		
		foreach($this->coo_order->products as $product)
		{
			$this->_updateProductsModel($product);
			
			$item = $orderObjectService->createOrderItemObject(new StringType($product['name']));
			$item->setProductModel(new StringType((string)$product['model']));
			$item->setPrice(new DecimalType($product['price']));
			$item->setQuantity(new DecimalType($product['qty']));
			$item->setTax(new DecimalType($product['tax']));
			$item->setTaxAllowed(new BoolType((bool)(int)$_SESSION['customers_status']['customers_status_show_price_tax']));
			
			if(isset($product['discount_allowed']))
			{
				$item->setDiscountMade(new DecimalType($product['discount_allowed']));
			}
			
			$item->setShippingTimeInfo(new StringType($this->_getShippingTime($product)));
			$item->setCheckoutInformation(new StringType($product['checkout_information'] ?: ''));
			$this->_setOrderItemAttributeCollection($product, $item);
			$this->_setDownloadInformation($product, $item);
			
			if(!empty($product['quantity_unit_id']))
			{
				$item->setQuantityUnitName(new StringType($product['unit_name']));
			}
			
			$this->_setOrderItemAddonValues($item, $product);
			
			$items[] = $item;
		}
		
		$orderItems = MainFactory::create('OrderItemCollection', $items);
		
		return $orderItems;
	}
	
	
	/**
	 * The _getDiscount method returns the customer's order discount as a DecimalType.
	 *
	 * @return DecimalType
	 */
	protected function _getDiscount()
	{
		$discount = new DecimalType(0.0);
		
		if($_SESSION['customers_status']['customers_status_ot_discount_flag'] == 1)
		{
			$discount = new DecimalType($_SESSION['customers_status']['customers_status_ot_discount']);
		}
		
		return $discount;
	}
	
	
	/**
	 * The _getBillingAddress method returns a CustomerAddress object representing the billing address if found.
	 * Otherwise null will be returned.
	 *
	 * @return CustomerAddress|null
	 */
	protected function _getBillingAddress()
	{
		/** @var AddressBookService $addressBookService */
		$addressBookService = StaticGXCoreLoader::getService('AddressBook');
		
		$addressBookId  = new IdType($_SESSION['billto']);
		$billingAddress = $addressBookService->findAddressById($addressBookId);
		
		return $billingAddress;
	}
	
	
	/**
	 * The _getDeliveryAddress method returns a CustomerAddress object representing the delivery address if found.
	 * Otherwise null will be returned.
	 *
	 * @return CustomerAddress|null
	 */
	protected function _getDeliveryAddress()
	{
		/** @var AddressBookService $addressBookService */
		$addressBookService = StaticGXCoreLoader::getService('AddressBook');
		
		$addressBookId   = new IdType($_SESSION['sendto']);
		$deliveryAddress = $addressBookService->findAddressById($addressBookId);
		
		return $deliveryAddress;
	}
	
	
	/**
	 * The _getCustomerDefaultAddress method returns a CustomerAddress object representing the customer's main address.
	 *
	 * @return CustomerAddress
	 */
	protected function _getCustomerDefaultAddress()
	{
		/** @var CustomerReadService $customerReadService */
		$customerReadService = StaticGXCoreLoader::getService('CustomerRead');
		
		/** @var Customer $customer */
		$customer = $customerReadService->getCustomerById($this->_getCustomerId());
		
		$customerAddress = $customer->getDefaultAddress();
		
		return $customerAddress;
	}
	
	
	/**
	 * The _getCustomerEmail method returns the customer's email address as an EmailStringType object.
	 *
	 * @return EmailStringType
	 */
	protected function _getCustomerEmail()
	{
		/** @var CustomerReadService $customerReadService */
		$customerReadService = StaticGXCoreLoader::getService('CustomerRead');
		
		/** @var Customer $customer */
		$customer = $customerReadService->getCustomerById($this->_getCustomerId());
		
		$customerEmail = new EmailStringType((string)$customer->getEmail());
		
		return $customerEmail;
	}
	
	
	/**
	 * The _getCustomerTelephone method returns the customer's telephone number a StringType object.
	 *
	 * return StringType
	 */
	protected function _getCustomerTelephone()
	{
		/** @var CustomerReadService $customerReadService */
		$customerReadService = StaticGXCoreLoader::getService('CustomerRead');
		
		/** @var Customer $customer */
		$customer = $customerReadService->getCustomerById($this->_getCustomerId());
		
		$customerTelephone = new StringType((string)$customer->getTelephoneNumber());
		
		return $customerTelephone;
	}
	
	
	/**
	 *  The _getLanguageCode method returns the language code of the customers session as a LanguageCode object.
	 *
	 * @return LanguageCode
	 */
	protected function _getLanguageCode()
	{
		$languageCode = new LanguageCode(new NonEmptyStringType($_SESSION['language_code']));
		
		return $languageCode;
	}
	

	/**
	 * Returns the total weight of the order.
	 * To get the total weight, the method uses the shopping cart instance in the session.
	 *
	 * @return \DecimalType
	 */
	protected function _getOrderTotalWeight()
	{
		return new DecimalType($_SESSION['cart']->show_weight());
	}
	
	
	/**
	 * The _getCustomerStatusInformation method returns a CustomerStatusInformation object representing the
	 * customer's status containing customer group information.
	 *
	 * @return CustomerStatusInformation
	 */
	protected function _getCustomerStatusInformation()
	{
		$isGuest = new BoolType($_SESSION['customers_status']['customers_status_id']
		                        == DEFAULT_CUSTOMERS_STATUS_ID_GUEST);
		
		$customerStatusInfo = MainFactory::create('CustomerStatusInformation',
		                                          new IdType($_SESSION['customers_status']['customers_status_id']),
		                                          new StringType($_SESSION['customers_status']['customers_status_name']),
		                                          new StringType($_SESSION['customers_status']['customers_status_image']),
		                                          $this->_getDiscount(), $isGuest);
		
		return $customerStatusInfo;
	}
	
	
	/**
	 * The _getCustomerIP method returns the customer's IP if allowed. Otherwise an empty string will be returned.
	 *
	 * @return string
	 */
	protected function _getCustomerIP()
	{
		$customerIP = '';
		
		if(($this->v_data_array['POST']['gm_log_ip'] == 'save' && gm_get_conf("GM_LOG_IP") == '1')
		   || (gm_get_conf("GM_SHOW_IP") == '1' && gm_get_conf("GM_LOG_IP") == '1')
		)
		{
			$customerIP = $_SERVER["REMOTE_ADDR"];
			
			if($_SERVER["HTTP_X_FORWARDED_FOR"])
			{
				$customerIP = $_SERVER["HTTP_X_FORWARDED_FOR"];
			}
		}
		
		return $customerIP;
	}
	
	
	/**
	 * The _getOrderShippingType method returns a OrderShippingType object representing the shipping method.
	 *
	 * @return OrderShippingType
	 */
	protected function _getOrderShippingType()
	{
		return MainFactory::create('OrderShippingType',
		                           new StringType((string)$this->coo_order->info['shipping_method']),
		                           new StringType((string)$this->coo_order->info['shipping_class']));
	}
	
	
	/**
	 * The _getOrderPaymentType method returns a OrderPaymentType object representing the payment method.
	 *
	 * @return OrderPaymentType
	 */
	protected function _getOrderPaymentType()
	{
		return MainFactory::create('OrderPaymentType', new StringType((string)$this->coo_order->info['payment_method']),
		                           new StringType((string)$this->coo_order->info['payment_class']));
	}
	
	
	/**
	 * The _getCurrencyCode method returns a CurrencyCode object representing the currency of the order.
	 *
	 * @return CurrencyCode
	 */
	protected function _getCurrencyCode()
	{
		return MainFactory::create('CurrencyCode', new NonEmptyStringType($this->coo_order->info['currency']));
	}
	
	
	/**
	 * The _getCustomerNumber method returns the customer number as a StringType object.
	 *
	 * @return StringType
	 */
	protected function _getCustomerNumber()
	{
		return new StringType((string)$this->coo_order->customer['csID']);
	}
	
	
	/**
	 * The _getCustomerVatId method returns the VAT-ID of the customer as a StringType object.
	 *
	 * @return StringType
	 */
	protected function _getCustomerVatId()
	{
		return new StringType((string)$_SESSION['customer_vat_id']);
	}
	
	
	/**
	 * The _getCustomerId method returns the ID of the customer as a IdType object.
	 *
	 * @return IdType
	 */
	protected function _getCustomerId()
	{
		return new IdType($_SESSION['customer_id']);
	}
	
	
	/**
	 * The _getComment method returns the order's comment as a StringType object.
	 *
	 * @return StringType
	 */
	protected function _getComment()
	{
		return new StringType($this->coo_order->info['comments']);
	}
	
	/**
	 * The _getOrderStatusId method returns the order status ID as an IdType object if it's different to the default
	 * order status ID. Otherwise null will be returned.
	 *
	 * @return IdType|null
	 */
	protected function _getOrderStatusId()
	{
		if(isset($this->tmp_status) && $this->tmp_status !== (int)DEFAULT_ORDERS_STATUS_ID)
		{
			return new IntType($this->tmp_status);
		}
		else
		{
			// Return null for the optional parameter so OrderWriteService will determine the default status id.
			return null;
		}
	}
	
	
	/**
	 * The _setOrderId method sets the order ID to this instance and the tmp_oID variable stored in the session.
	 *
	 * @param int $orderId
	 */
	protected function _setOrderId($orderId)
	{
		$GLOBALS['insert_id'] = $orderId;
		$this->order_id       = $GLOBALS['insert_id'];
		$_SESSION['tmp_oID']  = $this->order_id;
	}
	
	
	/**
	 * The _setOrderItemAddonValues method sets additional order information data as an AddonValue.
	 *
	 * @param OrderItemInterface $item
	 * @param array              $product
	 */
	protected function _setOrderItemAddonValues(OrderItemInterface $item, array $product)
	{
		// identifier (e.g. 1{12}3{14}5x2) is needed for saving GX-Customizer data
		$item->setAddonValue(new StringType('identifier'), new StringType((string)$product['id']));
		$item->setAddonValue(new StringType('productId'), new StringType((string)(int)$product['id']));
		$item->setAddonValue(new StringType('quantityUnitId'), new StringType((string)(int)$product['quantity_unit_id']));
	}
	
	
	/**
	 * The _getOrderAddonValuesCollection returns a collection of addon values represented as an
	 * EditableKeyValueCollection.
	 *
	 * Use this method for overloading adding more addon values to the order.
	 *
	 * @return EditableKeyValueCollection
	 */
	protected function _getOrderAddonValuesCollection()
	{
		$downloadAbandonmentStatus = '0';
		if(isset($_SESSION['abandonment_download']) && $_SESSION['abandonment_download'] === 'true')
		{
			$downloadAbandonmentStatus = '1';
		}
		
		$serviceAbandonmentStatus = '0';
		if(isset($_SESSION['abandonment_service']) && $_SESSION['abandonment_service'] === 'true')
		{
			$serviceAbandonmentStatus = '1';
		}
		
		$addonValues = MainFactory::create('EditableKeyValueCollection', array(
			'customerIp'                => $this->_getCustomerIP(),
			'downloadAbandonmentStatus' => $downloadAbandonmentStatus,
			'serviceAbandonmentStatus'  => $serviceAbandonmentStatus,
			'ccType'                    => $this->coo_order->info['cc_type'],
			'ccOwner'                   => $this->coo_order->info['cc_owner'],
			'ccNumber'                  => $this->coo_order->info['cc_number'],
			'ccExpires'                 => $this->coo_order->info['cc_expires'],
			'ccStart'                   => $this->coo_order->info['cc_start'],
			'ccIssue'                   => $this->coo_order->info['cc_cvv'],
			'ccCvv'                     => '123'
		));
		
		return $addonValues;
	}
	
	
	/**
	 * The _setOrderItemAttributeCollection method sets order attributes. Attributes are OrderItemAttributeCollection
	 * objects containing property and attribute information.
	 *
	 * @param array              $product
	 * @param OrderItemInterface $item
	 */
	protected function _setOrderItemAttributeCollection(array $product, OrderItemInterface $item)
	{
		/**
		 * Array of OrderItemProperty and OrderItemAttribute objects
		 */
		$orderItemAttributesArray = array();
		$this->_addProperties($product, $orderItemAttributesArray);
		$this->_addAttributes($product, $orderItemAttributesArray);
		
		if(count($orderItemAttributesArray))
		{
			$orderItemAttributeCollection = MainFactory::create('OrderItemAttributeCollection',
			                                                    $orderItemAttributesArray);
			
			$item->setAttributes($orderItemAttributeCollection);
		}
	}
	
	
	/**
	 * The _setDownloadInformation method sets download data as an OrderItemDownloadInformationCollection object.
	 *
	 * @param array              $product
	 * @param OrderItemInterface $item
	 * 
	 * @throws InvalidArgumentException
	 */
	protected function _setDownloadInformation(array $product, OrderItemInterface $item)
	{
		if(DOWNLOAD_ENABLED == 'true' && isset($product['attributes']))
		{
			$orderItemDownloadInformationArray = array(); 
			
			foreach($product['attributes'] as $attribute)
			{
				$query  = 'SELECT
								pad.`products_attributes_maxdays` AS `max_days_allowed`,
								pad.`products_attributes_maxcount` AS `count_available`,
								pad.`products_attributes_filename` AS `filename`
							FROM
								' . TABLE_PRODUCTS_ATTRIBUTES . ' pa,
								' . TABLE_PRODUCTS_ATTRIBUTES_DOWNLOAD . ' pad 
							WHERE
								pa.`products_id` = ' . (int)$product['id'] . ' AND
								pa.`options_id` = ' . (int)$attribute['option_id'] . ' AND
								pa.`options_values_id` = ' . (int)$attribute['value_id'] . ' AND
								pa.`products_attributes_id` = pad.`products_attributes_id`';
				$result = xtc_db_query($query);
				
				if(xtc_db_num_rows($result))
				{
					$row = xtc_db_fetch_array($result);
					
					$orderItemDownloadInformation = MainFactory::create('OrderItemDownloadInformation',
					                                                    new FilenameStringType($row['filename']),
					                                                    new IntType($row['max_days_allowed']),
					                                                    new IntType($row['count_available']));
					
					$orderItemDownloadInformationArray[] = $orderItemDownloadInformation; 
				}
			}
			
			$orderItemDownloadInformationCollection = MainFactory::create('OrderItemDownloadInformationCollection', 
			                                                              $orderItemDownloadInformationArray);
			$item->setDownloadInformation($orderItemDownloadInformationCollection); 
		}
	}
	
	/******************************************************************************************************************
	 * END METHODS BELONING TO ORDER SERVICE PROCESS
	 ******************************************************************************************************************/
	
	/******************************************************************************************************************
	 * BEGIN DEPRECATED METHODS
	 ******************************************************************************************************************/
	
	/**
	 * @deprecated The method will be replaced by a checkout service soon.
	 */
	public function process_products()
	{
		// initialized for the email confirmation
		$GLOBALS['products_ordered']      = '';
		$GLOBALS['products_ordered_html'] = '';
		$GLOBALS['subtotal']              = 0;
		$GLOBALS['total_tax']             = 0;
		
		for($i = 0, $n = sizeof($this->coo_order->products); $i < $n; $i++)
		{
			// check if combis exists
			$t_combis_id = $this->coo_properties->extract_combis_id($this->coo_order->products[$i]['id']);
			
			$this->update_stock($this->coo_order->products[$i], $t_combis_id);
			
			$t_order_products_id = 0; // not used anymore
			
			# save selected properties_combi in product
			$this->save_property_data($this->coo_order->products[$i], $t_order_products_id, $t_combis_id);
			
			$this->update_special($this->coo_order->products[$i]);
			
			$this->coo_order_total->update_credit_account($i); // GV Code ICW ADDED FOR CREDIT CLASS SYSTEM
			
			$this->process_attributes($t_order_products_id, $this->coo_order->products[$i]);
			
			$GLOBALS['total_weight'] += ($this->coo_order->products[$i]['qty']
			                             * $this->coo_order->products[$i]['weight']);
		}
	}
	
	
	/**
	 * @deprecated The method will be replaced by a checkout service soon.
	 *
	 * @param array $p_product_array
	 * @param int   $p_combis_id
	 */
	public function update_stock($p_product_array, $p_combis_id)
	{
		$updateProductShippingStatus = false;
		
		$t_products_sql_data_array = array();
		
		// Stock Update - Joao Correia
		if(STOCK_LIMITED == 'true')
		{
			if(DOWNLOAD_ENABLED == 'true')
			{
				$t_stock_query = "SELECT
												p.products_quantity,
												pad.products_attributes_filename
											FROM
												" . TABLE_PRODUCTS . " p
												LEFT JOIN " . TABLE_PRODUCTS_ATTRIBUTES . " pa ON p.products_id=pa.products_id
												LEFT JOIN " . TABLE_PRODUCTS_ATTRIBUTES_DOWNLOAD . " pad ON pa.products_attributes_id=pad.products_attributes_id
											WHERE p.products_id = '" . xtc_get_prid($p_product_array['id']) . "'";
				
				// Will work with only one option for downloadable products
				// otherwise, we have to build the query dynamically with a loop
				$t_product_attributes_array = $p_product_array['attributes'];
				
				if(is_array($t_product_attributes_array))
				{
					$t_stock_query = "SELECT
													p.products_quantity,
													pad.products_attributes_filename
												FROM
													" . TABLE_PRODUCTS . " p
													LEFT JOIN " . TABLE_PRODUCTS_ATTRIBUTES
					                 . " pa ON (p.products_id=pa.products_id AND pa.options_id = '"
					                 . (int)$t_product_attributes_array[0]['option_id']
					                 . "' AND pa.options_values_id = '"
					                 . (int)$t_product_attributes_array[0]['value_id'] . "')
													LEFT JOIN " . TABLE_PRODUCTS_ATTRIBUTES_DOWNLOAD . " pad ON pa.products_attributes_id=pad.products_attributes_id
												WHERE p.products_id = '" . xtc_get_prid($p_product_array['id']) . "'";
				}
				$t_stock_result = xtc_db_query($t_stock_query, "db_link", false);
			}
			else
			{
				$t_stock_result = xtc_db_query("SELECT products_quantity
														FROM " . TABLE_PRODUCTS . "
														WHERE products_id = '" . xtc_get_prid($p_product_array['id'])
				                               . "'", "db_link", false);
			}
			
			if(xtc_db_num_rows($t_stock_result) > 0)
			{
				if(empty($p_combis_id) == false)
				{
					$coo_combis_admin_control = MainFactory::create_object("PropertiesCombisAdminControl");
					$t_use_combis_quantity    = $coo_combis_admin_control->get_use_properties_combis_quantity(xtc_get_prid($p_product_array['id']));
				}
				else
				{
					$t_use_combis_quantity = 0;
				}
				
				$t_stock_values_array = xtc_db_fetch_array($t_stock_result);
				
				if(!$t_stock_values_array['products_attributes_filename']
				   && ((empty($p_combis_id) && STOCK_CHECK == 'true')
				       || (!empty($p_combis_id)
				           && (($t_use_combis_quantity == 0 && STOCK_CHECK == 'true')
				               || $t_use_combis_quantity == 1)))
				)
				{
					$t_stock_left = $t_stock_values_array['products_quantity'] - $p_product_array['qty'];
					
					$t_products_sql_data_array['products_quantity'] = $t_stock_left;
					
					$updateProductShippingStatus = true;
				}
				// do not decrement quantities if products_attributes_filename exists
				else
				{
					$t_stock_left = $t_stock_values_array['products_quantity'];
				}
				
				if(($t_stock_left <= 0) && (STOCK_ALLOW_CHECKOUT == 'false') && GM_SET_OUT_OF_STOCK_PRODUCTS == 'true')
				{
					if(!empty($p_combis_id)
					   && (($t_use_combis_quantity == 0 && STOCK_CHECK == 'true'
					        && ATTRIBUTE_STOCK_CHECK == 'true')
					       || $t_use_combis_quantity == 2)
					)
					{
						$t_available_combi_exists = $this->coo_properties->available_combi_exists((int)xtc_get_prid($p_product_array['id']),
						                                                                          $p_combis_id);
						
						if($t_available_combi_exists == false)
						{
							$t_products_sql_data_array['products_status'] = '0';
						}
					}
					else
					{
						if(empty($p_combis_id) || $t_use_combis_quantity == 4)
						{
							$t_products_sql_data_array['products_status'] = '0';
						}
					}
				}
				
				$t_only_combi_check      = !empty($p_combis_id)
				                           && (($t_use_combis_quantity == 0 && STOCK_CHECK == 'true'
				                                && ATTRIBUTE_STOCK_CHECK == 'true')
				                               || $t_use_combis_quantity == 2);
				$t_restock_level_reached = $t_stock_left <= STOCK_REORDER_LEVEL;
				
				// stock_notifier
				if(SEND_EMAILS == 'true' && STOCK_CHECK == 'true' && $t_restock_level_reached
				   && (!$t_only_combi_check
				       || empty($p_combis_id))
				)
				{
					$t_products_name_query = xtc_db_query("SELECT products_name
																	FROM products_description
																	WHERE
																		products_id = '"
					                                      . xtc_get_prid($p_product_array['id']) . "' AND
																		language_id = '" . $_SESSION['languages_id']
					                                      . "'");
					$t_product_result      = mysqli_fetch_array($t_products_name_query);
					
					$t_subject = GM_OUT_OF_STOCK_NOTIFY_TEXT . ' ' . $t_product_result['products_name'];
					$t_body    = $t_product_result['products_name'] . "\n" . $p_product_array['model'] . "\n"
					             . GM_OUT_OF_STOCK_NOTIFY_TEXT . ': ' . (double)$t_stock_left . "\n" . HTTP_SERVER
					             . DIR_WS_CATALOG . 'product_info.php?info=p' . xtc_get_prid($p_product_array['id'])
					             . "\n" . HTTP_SERVER . DIR_WS_CATALOG . 'admin/categories.php?pID='
					             . xtc_get_prid($p_product_array['id']) . '&action=new_product';
					
					// send mail
					$this->send_mail($t_subject, $t_body);
				}
				// stock_notifier
			}
		}
		
		// Update products_ordered (for bestsellers list)
		$t_products_sql_data_array['products_ordered'] = 'products_ordered + ' . (double)$p_product_array['qty'];
		
		$this->update_product($t_products_sql_data_array, xtc_get_prid($p_product_array['id']));
		
		if($updateProductShippingStatus)
		{
			// set products_shippingtime:
			set_shipping_status($p_product_array['id']);
		}
	}
	
	
	/**
	 * @deprecated The method will be replaced by a checkout service soon.
	 *
	 * @param array $p_products_sql_data_array
	 * @param int   $p_products_id
	 */
	public function update_product($p_products_sql_data_array, $p_products_id)
	{
		$this->add_product_data($p_products_sql_data_array, $p_products_id);
		
		$this->wrapped_db_perform(__FUNCTION__, TABLE_PRODUCTS, $p_products_sql_data_array, 'update',
		                          'products_id = "' . (int)$p_products_id . '"', 'db_link', false);
	}
	
	
	/**
	 * @deprecated The method will be replaced by a checkout service soon.
	 *
	 * @param string $p_subject
	 * @param string $p_body
	 *
	 * @return bool
	 */
	public function send_mail($p_subject, $p_body)
	{
		return xtc_php_mail(STORE_OWNER_EMAIL_ADDRESS, STORE_NAME, STORE_OWNER_EMAIL_ADDRESS, STORE_NAME, '',
		                    STORE_OWNER_EMAIL_ADDRESS, STORE_NAME, '', '', $p_subject,
		                    nl2br(htmlentities_wrapper($p_body)), $p_body);
	}
	
	
	/**
	 * The method does not save property data of an order item anymore. It updates the combis quantity and sends
	 * stock warning mails. The method will be replaced by a checkout service in the near future.
	 *
	 * @deprecated The method will be replaced by a checkout service soon.
	 *
	 * @param array $p_product_array
	 * @param int   $p_order_products_id
	 * @param int   $p_combis_id
	 */
	public function save_property_data($p_product_array, $p_order_products_id, $p_combis_id)
	{
		if(is_object($GLOBALS['coo_debugger']))
		{
			$GLOBALS['coo_debugger']->log('checkout_process: $GLOBALS[\'order\']->products[$i][id] '
			                              . $p_product_array['id'], 'Properties');
		}
		
		if(is_object($GLOBALS['coo_debugger']))
		{
			$GLOBALS['coo_debugger']->log('checkout_process: extract_combis_id ' . $p_combis_id, 'Properties');
		}
		
		if(empty($p_combis_id) == false)
		{
			if(empty($p_combis_id) == false)
			{
				$coo_combis_admin_control = MainFactory::create_object("PropertiesCombisAdminControl");
				$t_use_combis_quantity    = $coo_combis_admin_control->get_use_properties_combis_quantity(xtc_get_prid($p_product_array['id']));
			}
			else
			{
				$t_use_combis_quantity = 0;
			}
			
			# update properties_combi quantity
			if(STOCK_LIMITED == 'true'
			   && (($t_use_combis_quantity == 0 && STOCK_CHECK == 'true'
			        && ATTRIBUTE_STOCK_CHECK == 'true')
			       || $t_use_combis_quantity == 2)
			)
			{
				$t_quantity_change = $p_product_array['qty'] * -1;
				$t_value           = $this->coo_properties->change_combis_quantity($p_combis_id, $t_quantity_change);
				
				set_shipping_status($p_product_array['id'], $p_combis_id);
				
				if(SEND_EMAILS == 'true' && $t_value <= STOCK_REORDER_LEVEL)
				{
					$t_products_name_query = xtc_db_query("SELECT products_name
																	FROM products_description
																	WHERE
																		products_id = '"
					                                      . xtc_get_prid($p_product_array['id']) . "' AND
																		language_id = '" . $_SESSION['languages_id']
					                                      . "'");
					$t_product_result      = mysqli_fetch_array($t_products_name_query);
					
					$t_combis_details          = $this->coo_properties->get_properties_combis_details($p_combis_id,
					                                                                                  $_SESSION['languages_id']);
					$t_selection_strings_array = array();
					
					foreach($t_combis_details as $t_property)
					{
						$t_selection_strings_array[] = $t_property['properties_name'] . ': '
						                               . $t_property['values_name'];
					}
					
					$t_subject = GM_OUT_OF_STOCK_NOTIFY_TEXT . ' ' . $t_product_result['products_name'] . ' ('
					             . implode(', ', $t_selection_strings_array) . ')';
					$t_body    = $t_product_result['products_name'] . "\n" . implode("\n", $t_selection_strings_array)
					             . "\n" . $p_product_array['model'] . "\n" . GM_OUT_OF_STOCK_NOTIFY_TEXT . ': '
					             . (double)$t_value . "\n" . HTTP_SERVER . DIR_WS_CATALOG . 'product_info.php?info=p'
					             . xtc_get_prid($p_product_array['id']) . "\n" . HTTP_SERVER . DIR_WS_CATALOG
					             . 'admin/categories.php?pID=' . xtc_get_prid($p_product_array['id'])
					             . '&action=new_product';
					
					// send mail
					$this->send_mail($t_subject, $t_body);
				}
			}
		}
	}
	
	
	/**
	 * @deprecated The method will be replaced by a checkout service soon.
	 *
	 * @param array $p_product_array
	 */
	public function update_special($p_product_array)
	{
		$t_specials_query = xtc_db_query("SELECT
													products_id,
													specials_quantity
												FROM " . TABLE_SPECIALS . "
												WHERE products_id = '" . xtc_get_prid($p_product_array['id']) . "'");
		
		if(xtc_db_num_rows($t_specials_query))
		{
			$t_special_array = xtc_db_fetch_array($t_specials_query);
			$t_new_quantity  = ($t_special_array['specials_quantity'] - $p_product_array['qty']);
			
			if($t_new_quantity >= 1)
			{
				$t_sql_data_array                      = array();
				$t_sql_data_array['specials_quantity'] = $t_new_quantity;
				
				$this->add_special_data($t_sql_data_array, $t_special_array);
				
				$this->wrapped_db_perform(__FUNCTION__, TABLE_SPECIALS, $t_sql_data_array, 'update',
				                          'products_id = "' . xtc_get_prid($p_product_array['id']) . '"');
			}
			elseif(STOCK_CHECK == 'true')
			{
				$t_sql_data_array                      = array();
				$t_sql_data_array['status']            = '0';
				$t_sql_data_array['specials_quantity'] = $t_new_quantity;
				
				$this->add_special_data($t_sql_data_array, $t_special_array);
				
				$this->wrapped_db_perform(__FUNCTION__, TABLE_SPECIALS, $t_sql_data_array, 'update',
				                          'products_id = "' . xtc_get_prid($p_product_array['id']) . '"');
			}
		}
	}
	
	
	/**
	 * @deprecated The method will be replaced by a checkout service soon.
	 *
	 * @param int   $p_order_products_id
	 * @param array $p_product_array
	 */
	public function process_attributes($p_order_products_id, $p_product_array)
	{
		if(isset($p_product_array['attributes']))
		{
			for($j = 0, $n2 = sizeof($p_product_array['attributes']); $j < $n2; $j++)
			{
				// update attribute stock
				if(STOCK_LIMITED == 'true')
				{
					$this->update_product_attribute($p_product_array, $p_product_array['attributes'][$j]);
				}
				
				// attributes stock_notifier
				$this->update_attribute_stock($p_product_array, $p_product_array['attributes'][$j]);
			}
		}
	}
	
	
	/**
	 * @deprecated The method will be replaced by a checkout service soon.
	 *
	 * @param array $p_product_array
	 * @param array $p_attribute_array
	 */
	public function update_product_attribute($p_product_array, $p_attribute_array)
	{
		$t_sql_data_array                     = array();
		$t_sql_data_array['attributes_stock'] = 'attributes_stock - ' . $p_product_array['qty'];
		$t_where_part                         = 'products_id = "' . (int)$p_product_array['id'] . '" AND
											options_values_id = "' . (int)$p_attribute_array['value_id'] . '" AND
											options_id = "' . (int)$p_attribute_array['option_id'] . '"';
		
		$this->add_product_attribute_data($t_sql_data_array, $p_attribute_array);
		
		$this->wrapped_db_perform(__FUNCTION__, TABLE_PRODUCTS_ATTRIBUTES, $t_sql_data_array, 'update', $t_where_part,
		                          'db_link', false);
	}
	
	
	/**
	 * @deprecated The method will be replaced by a checkout service soon.
	 *
	 * @param array $p_product_array
	 * @param array $p_attribute_array
	 */
	public function update_attribute_stock($p_product_array, $p_attribute_array)
	{
		// Avenger
		if(SEND_EMAILS == 'true' && ATTRIBUTE_STOCK_CHECK == 'true')
		{
			$t_attributes_result = xtc_db_query("SELECT
															pd.products_name,
															pa.attributes_stock,
															po.products_options_name,
															pov.products_options_values_name
														FROM
															products_description pd,
															products_attributes pa,
															products_options po,
															products_options_values pov
														WHERE
															pa.products_id = '" . xtc_get_prid($p_product_array['id']) . "' AND
															pa.options_values_id = '" . $p_attribute_array['value_id'] . "' AND
															pa.options_id = '" . $p_attribute_array['option_id'] . "' AND
															po.products_options_id = '"
			                                    . $p_attribute_array['option_id'] . "' AND
															po.language_id = '" . $_SESSION['languages_id'] . "' AND
															pov.products_options_values_id = '"
			                                    . $p_attribute_array['value_id'] . "' AND
															pov.language_id = '" . $_SESSION['languages_id'] . "' AND
															pd.products_id = '" . xtc_get_prid($p_product_array['id']) . "' AND
															pd.language_id = '" . $_SESSION['languages_id'] . "'");
			if(xtc_db_num_rows($t_attributes_result) == 1)
			{
				$t_attributes_array = xtc_db_fetch_array($t_attributes_result);
				
				if($t_attributes_array['attributes_stock'] <= STOCK_REORDER_LEVEL)
				{
					$t_subject = GM_OUT_OF_STOCK_NOTIFY_TEXT . ' ' . $t_attributes_array['products_name'] . ' - '
					             . $t_attributes_array['products_options_name'] . ': '
					             . $t_attributes_array['products_options_values_name'];
					$t_body    = $t_attributes_array['products_name'] . ' - '
					             . $t_attributes_array['products_options_name'] . ': '
					             . $t_attributes_array['products_options_values_name'] . "\n"
					             . $p_product_array['model'] . "\n" . GM_OUT_OF_STOCK_NOTIFY_TEXT . ': '
					             . (double)$t_attributes_array['attributes_stock'] . "\n" . HTTP_SERVER . DIR_WS_CATALOG
					             . 'product_info.php?info=p' . xtc_get_prid($p_product_array['id']) . "\n" . HTTP_SERVER
					             . DIR_WS_CATALOG . 'admin/categories.php?pID=' . xtc_get_prid($p_product_array['id'])
					             . '&action=new_product';
					
					$this->send_mail($t_subject, $t_body);
				}
			}
		}
		// Avenger
	}
	
	
	/**
	 * @deprecated The method will be replaced by a checkout service soon.
	 */
	public function save_tracking_data()
	{
		$t_sql_data_array = array();
		
		if(isset($_SESSION['tracking']['refID']))
		{
			$t_sql_data_array['refferers_id'] = $_SESSION['tracking']['refID'];
			
			// check if late or direct sale
			$t_customers_logon_result = "SELECT customers_info_number_of_logons FROM " . TABLE_CUSTOMERS_INFO
			                            . " WHERE customers_info_id  = '" . $_SESSION['customer_id'] . "'";
			$t_customers_logon_result = xtc_db_query($t_customers_logon_result);
			$t_customers_logon_array  = xtc_db_fetch_array($t_customers_logon_result);
			
			if($t_customers_logon_array['customers_info_number_of_logons'] == 0)
			{
				// direct sale
				$t_sql_data_array['conversion_type'] = '1';
			}
			else
			{
				// late sale
				$t_sql_data_array['conversion_type'] = '2';
			}
		}
		else
		{
			$t_customers_result     = xtc_db_query("SELECT refferers_id as ref FROM " . TABLE_CUSTOMERS
			                                       . " WHERE customers_id = '" . $_SESSION['customer_id'] . "'");
			$t_customers_data_array = xtc_db_fetch_array($t_customers_result);
			
			if(xtc_db_num_rows($t_customers_result))
			{
				$t_sql_data_array['refferers_id'] = $t_customers_data_array['ref'];
				
				// check if late or direct sale
				$t_customers_logon_result = "SELECT customers_info_number_of_logons FROM " . TABLE_CUSTOMERS_INFO
				                            . " WHERE customers_info_id  = '" . $_SESSION['customer_id'] . "'";
				$t_customers_logon_result = xtc_db_query($t_customers_logon_result);
				$t_customers_logon_array  = xtc_db_fetch_array($t_customers_logon_result);
				
				if($t_customers_logon_array['customers_info_number_of_logons'] == 0)
				{
					// direct sale
					$t_sql_data_array['conversion_type'] = '1';
				}
				else
				{
					// late sale
					$t_sql_data_array['conversion_type'] = '2';
				}
			}
		}
		
		$this->add_tracking_data($t_sql_data_array, $t_customers_logon_array);
		
		$this->wrapped_db_perform(__FUNCTION__, TABLE_ORDERS, $t_sql_data_array, 'update',
		                          'orders_id = "' . (int)$this->order_id . '"');
	}
	
	
	/**
	 * @deprecated The method will be replaced by a checkout service soon.
	 */
	public function reset()
	{
		$_SESSION['cart']->reset(true);
		
		// unregister session variables used during checkout
		unset($_SESSION['sendto']);
		unset($_SESSION['billto']);
		unset($_SESSION['shipping']);
		unset($_SESSION['payment']);
		unset($_SESSION['comments']);
		unset($_SESSION['last_order']);
		unset($_SESSION['tmp_oID']);
		unset($_SESSION['cc']);
		unset($_SESSION['nvpReqArray']);
		unset($_SESSION['reshash']);
		$GLOBALS['last_order'] = $this->order_id;
		
		//GV Code Start
		if(isset($_SESSION['credit_covers']))
		{
			unset($_SESSION['credit_covers']);
		}
		$this->coo_order_total->clear_posts(); //ICW ADDED FOR CREDIT CLASS SYSTEM
		// GV Code End
		
		// GX-Customizer:
		if(is_object($_SESSION['coo_gprint_cart']))
		{
			$_SESSION['coo_gprint_cart']->empty_cart();
		}
	}
	
	
	/**
	 * @deprecated The method will be replaced by a checkout service soon.
	 *
	 * @return bool
	 */
	public function send_order_mail()
	{
		// no mail for Heidelpay orders
		if(strpos($this->coo_payment->selected_module, 'hp') === 0)
		{
			return false;
		}
		
		$coo_send_order_process = MainFactory::create_object('SendOrderProcess');
		$coo_send_order_process->set_('order_id', $this->order_id);
		$t_success = $coo_send_order_process->proceed();
		
		return $t_success;
	}
	
	
	/**
	 * @deprecated The method will be replaced by a checkout service soon.
	 *
	 * @param $p_sql_data_array
	 * @param $p_products_id
	 */
	public function add_product_data(&$p_sql_data_array, $p_products_id)
	{
		// use for overloading
	}
	
	
	/**
	 * @deprecated The method will be replaced by a checkout service soon.
	 *
	 * @param array $p_sql_data_array
	 * @param array $p_special_array
	 */
	public function add_special_data(&$p_sql_data_array, $p_special_array)
	{
		// use for overloading
	}
	
	
	/**
	 * @deprecated The method will be replaced by a checkout service soon.
	 *
	 * @param array $p_sql_data_array
	 * @param array $p_product_attributes_array
	 */
	public function add_product_attribute_data(&$p_sql_data_array, $p_product_attributes_array)
	{
		// use for overloading
	}
	
	
	/**
	 * @deprecated The method will be replaced by a checkout service soon.
	 *
	 * @param array $p_sql_data_array
	 * @param array $p_customer_array
	 */
	public function add_tracking_data(&$p_sql_data_array, $p_customer_array)
	{
		// use for overloading
	}
	
	
	/**
	 * Update the products model according to the properties data.
	 *
	 * @param array $product
	 *
	 * @deprecated The method will be replaced by a checkout service soon.
	 */
	protected function _updateProductsModel(array &$product)
	{
		// check if combis exists
		$combiId = $this->coo_properties->extract_combis_id($product['id']);
		if(empty($combiId) === false)
		{
			$combiModelResult = xtc_db_query('SELECT combi_model FROM products_properties_combis WHERE products_properties_combis_id = '
			                                 . (int)$combiId);
			if(xtc_db_num_rows($combiModelResult))
			{
				$combiModelResult = xtc_db_fetch_array($combiModelResult);
				if($combiModelResult['combi_model'] != '')
				{
					$product['model'] = (APPEND_PROPERTIES_MODEL == "true"
					                     && $product['model'] != '') ? $product['model'] . '-'
					                                                   . $combiModelResult['combi_model'] : $combiModelResult['combi_model'];
				}
			}
		}
	}
	
	/******************************************************************************************************************
	 * END DEPRECATED METHODS
	 ******************************************************************************************************************/
}