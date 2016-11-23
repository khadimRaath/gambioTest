<?php
/* --------------------------------------------------------------
   Customer.inc.php 2015-06-27
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('CustomerInterface');

/**
 * Class Customer
 *
 * This class is used for managing customer data
 *
 * @category   System
 * @package    Customer
 * @implements CustomerInterface
 */
class Customer implements CustomerInterface
{
	/**
	 * Customer ID.
	 * @var int
	 */
	protected $id;

	/**
	 * Customer number.
	 * @var CustomerNumberInterface
	 */
	protected $customerNumber;

	/**
	 * Customer gender.
	 * @var CustomerGenderInterface
	 */
	protected $gender;

	/**
	 * Customer first name.
	 * @var CustomerFirstnameInterface
	 */
	protected $firstname;

	/**
	 * Customer last name.
	 * @var CustomerLastnameInterface
	 */
	protected $lastname;

	/**
	 * Customer date of birth.
	 * @var DateTime
	 */
	protected $dateOfBirth;

	/**
	 * Customer VAT number.
	 * @var CustomerVatNumberInterface
	 */
	protected $vatNumber;

	/**
	 * Customer VAT number status.
	 * @var int
	 */
	protected $vatNumberStatus = 0;

	/**
	 * Customer call number.
	 * @var CustomerCallNumberInterface
	 */
	protected $telephoneNumber;

	/**
	 * Customer fax number.
	 * @var CustomerCallNumberInterface
	 */
	protected $faxNumber;

	/**
	 * Customer E-Mail address.
	 * @var CustomerEmailInterface
	 */
	protected $email;

	/**
	 * Customer password.
	 * @var CustomerPasswordInterface
	 */
	protected $password;

	/**
	 * Customer default address.
	 * @var CustomerAddressInterface
	 */
	protected $defaultAddress;

	/**
	 * Customer status ID.
	 * @var int
	 */
	protected $customerStatusId = 0;

	/**
	 * Customer guest status.
	 * @var bool
	 */
	protected $isGuest = false;
	
	/**
	 * Addons collection.
	 *
	 * @var EditableKeyValueCollection
	 */
	protected $addonValues;


	/**
	 * Constructor of the class Customer
	 */
	public function __construct()
	{
		$this->customerNumber  = MainFactory::create('CustomerNumber', '');
		$this->gender          = MainFactory::create('CustomerGender', '');
		$this->firstname       = MainFactory::create('CustomerFirstname', '');
		$this->lastname        = MainFactory::create('CustomerLastname', '');
		$this->dateOfBirth     = MainFactory::create('CustomerDateOfBirth', '1000-01-01 00:00:00');
		$this->vatNumber       = MainFactory::create('CustomerVatNumber', '');
		$this->telephoneNumber = MainFactory::create('CustomerCallNumber', '');
		$this->faxNumber       = MainFactory::create('CustomerCallNumber', '');
		$this->email           = MainFactory::create('CustomerEmail', 'temp@example.org');
		$this->password        = MainFactory::create('CustomerPassword', md5(time() . rand(1, 999000)));
		$this->addonValues     = MainFactory::create('EditableKeyValueCollection', array());
	}


	/**
	 * Returns the customer's ID.
	 *
	 * @return int Customer's ID.
	 */
	public function getId()
	{
		return $this->id;
	}
	
	/**
	 * Returns the customer ID.
	 *
	 * @return int
	 */
	public function getAddonValueContainerId()
	{
		return $this->getId();
	}


	/**
	 * Returns the customer's number.
	 *
	 * @return string customerNumber Customer's number.
	 */
	public function getCustomerNumber()
	{
		return $this->customerNumber;
	}


	/**
	 * Sets the customer's number.
	 *
	 * @param CustomerNumberInterface $customerNumber Customer's number.
	 */
	public function setCustomerNumber(CustomerNumberInterface $customerNumber)
	{
		$this->customerNumber = $customerNumber;
	}


	/**
	 * Returns the customer's status ID.
	 *
	 * @return int customerStatusId Customer's status ID.
	 */
	public function getStatusId()
	{
		return $this->customerStatusId;
	}


	/**
	 * Sets the customer's status ID.
	 *
	 * @param int $p_statusId Customer's status ID.
	 *
	 * @throws InvalidArgumentException
	 */
	public function setStatusId($p_statusId)
	{
		if(!is_numeric($p_statusId) || ((int)$p_statusId != (double)$p_statusId))
		{
			throw new InvalidArgumentException('$p_statusId int expected.');
		}
		
		$this->customerStatusId = (int)$p_statusId;
	}


	/**
	 * Checks if customer is a guest.
	 *
	 * @return bool Is customer a guest?
	 */
	public function isGuest()
	{
		return $this->isGuest;
	}


	/**
	 * Returns the customer's gender.
	 *
	 * @return CustomerGenderInterface Customer's gender.
	 */
	public function getGender()
	{
		return $this->gender;
	}


	/**
	 * Returns the customer's first name.
	 *
	 * @return CustomerFirstnameInterface Customer's first name.
	 */
	public function getFirstname()
	{
		return $this->firstname;
	}

	
	/**
	 * Returns the customer's last name.
	 *
	 * @return CustomerLastnameInterface Customer's last name.
	 */
	public function getLastname()
	{
		return $this->lastname;
	}


	/**
	 * Returns the customer's date of birth.
	 *
	 * @return DateTime date of birth Customer's date of birth.
	 */
	public function getDateOfBirth()
	{
		return $this->dateOfBirth;
	}


	/**
	 * Returns the customer's VAT number.
	 *
	 * @return CustomerVatNumberInterface Customer's VAT number.
	 */
	public function getVatNumber()
	{
		return $this->vatNumber;
	}


	/**
	 * Returns the customer's VAT number status.
	 *
	 * @return int Customer's VAT number status.
	 */
	public function getVatNumberStatus()
	{
		return $this->vatNumberStatus;
	}


	/**
	 * Returns the customer's telephone number.
	 *
	 * @return CustomerCallNumberInterface Customer's telephone number.
	 */
	public function getTelephoneNumber()
	{
		return $this->telephoneNumber;
	}


	/**
	 * Returns the customer's fax number.
	 *
	 * @return CustomerCallNumberInterface Customer's fax number.
	 */
	public function getFaxNumber()
	{
		return $this->faxNumber;
	}


	/**
	 * Returns the customer's email.
	 *
	 * @return CustomerEmailInterface Customer's email.
	 */
	public function getEmail()
	{
		return $this->email;
	}


	/**
	 * Returns the customer's password.
	 *
	 * @return CustomerPasswordInterface Customer's password.
	 */
	public function getPassword()
	{
		return $this->password;
	}


	/**
	 * Returns the customer's default address.
	 *
	 * @return CustomerAddressInterface Customer's default address.
	 */
	public function getDefaultAddress()
	{
		return $this->defaultAddress;
	}


	/**
	 * Sets the customer's guest status.
	 *
	 * @param boolean $p_isPGuest Customer's guest status.
	 *
	 * @throws InvalidArgumentException if $p_isGuest is not a boolean value
	 */
	public function setGuest($p_isPGuest)
	{
		if(!is_bool($p_isPGuest))
		{
			throw new InvalidArgumentException('$p_isGuest bool expected.');
		}
		$this->isGuest = (boolean)$p_isPGuest;
	}


	/**
	 * Sets the customer's ID.
	 *
	 * @param IdType $id customerId Customer ID.
	 *
	 * @throws InvalidArgumentException If $p_id is not an integer or if $p_id is lower than 1.
	 */
	public function setId(IdType $id)
	{
		$this->id = (int)(string)$id;
	}


	/**
	 * Sets the customer's gender.
	 *
	 * @param CustomerGenderInterface $gender Customer's gender.
	 */
	public function setGender(CustomerGenderInterface $gender)
	{
		$this->gender = $gender;
	}


	/**
	 * Sets the customer's first name.
	 *
	 * @param CustomerFirstnameInterface $firstname Customer's first name.
	 */
	public function setFirstname(CustomerFirstnameInterface $firstname)
	{
		$this->firstname = $firstname;
	}


	/**
	 * Sets the customer's last name.
	 *
	 * @param CustomerLastnameInterface $lastname Customer's last name.
	 */
	public function setLastname(CustomerLastnameInterface $lastname)
	{
		$this->lastname = $lastname;
	}


	/**
	 * Sets the customer's date of birth.
	 *
	 * @param DateTime $dateOfBirth date of birth Customer's date of birth.
	 */
	public function setDateOfBirth(DateTime $dateOfBirth)
	{
		$this->dateOfBirth = $dateOfBirth;
	}


	/**
	 * Sets the customer's VAT number.
	 *
	 * @param CustomerVatNumberInterface $vatNumber Customer's VAT number.
	 */
	public function setVatNumber(CustomerVatNumberInterface $vatNumber)
	{
		$this->vatNumber = $vatNumber;
	}


	/**
	 * Sets the customer's VAT number status.
	 *
	 * @param int $p_vatNumberStatus Customer's VAT number status.
	 */
	public function setVatNumberStatus($p_vatNumberStatus)
	{
		$this->vatNumberStatus = (int)$p_vatNumberStatus;
	}


	/**
	 * Sets the customer's telephone number.
	 *
	 * @param CustomerCallNumberInterface $telephoneNumber Customer's telephone number.
	 */
	public function setTelephoneNumber(CustomerCallNumberInterface $telephoneNumber)
	{
		$this->telephoneNumber = $telephoneNumber;
	}


	/**
	 * Sets the customer's fax number.
	 *
	 * @param CustomerCallNumberInterface $faxNumber Customer's fax number.
	 */
	public function setFaxNumber(CustomerCallNumberInterface $faxNumber)
	{
		$this->faxNumber = $faxNumber;
	}


	/**
	 * Sets the customer's email.
	 *
	 * @param CustomerEmailInterface $email Customer's email.
	 */
	public function setEmail(CustomerEmailInterface $email)
	{
		$this->email = $email;
	}


	/**
	 * Sets the customer's password.
	 *
	 * @param CustomerPasswordInterface $password Customer's password.
	 */
	public function setPassword(CustomerPasswordInterface $password)
	{
		$this->password = $password;
	}


	/**
	 * Sets the customer's default address.
	 *
	 * @param CustomerAddressInterface $address Customer's default address.
	 */
	public function setDefaultAddress(CustomerAddressInterface $address)
	{
		$this->defaultAddress = $address;
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
}