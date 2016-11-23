<?php
/* --------------------------------------------------------------
   AbstractCustomerServiceFactory.inc.php 2015-02-18 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class AbstractCustomerServiceFactory
 *
 * @category System
 * @package  Customer
 */
abstract class AbstractCustomerServiceFactory
{
	/**
	 * Returns the country service.
	 *
	 * @return CountryService Country service.
	 */
	abstract public function getCountryService();


	/**
	 * Returns the customer service.
	 *
	 * @return CustomerService Customer service.
	 */
	abstract public function getCustomerService();


	/**
	 * Returns the customer registration input validator service.
	 *
	 * @return CustomerRegistrationInputValidatorService Customer registration input validator service.
	 */
	abstract public function getCustomerRegistrationInputValidatorService();


	/**
	 * Returns the customer account input validator.
	 *
	 * @return CustomerAccountInputValidator Customer account input validator.
	 */
	abstract public function getCustomerAccountInputValidator();


	/**
	 * Returns the customer input validator.
	 *
	 * @return CustomerAddressInputValidator Customer input validator.
	 */
	abstract public function getCustomerAddressInputValidatorService();


	/**
	 * Returns the database query builder.
	 *
	 * @return CI_DB_query_builder Query builder.
	 */
	abstract public function getDatabaseQueryBuilder();


	/**
	 * Returns the address book service.
	 *
	 * @return AddressBookService Address book service.
	 */
	abstract public function getAddressBookService();


	/**
	 * Creates a customer read service object.
	 *
	 * @return CustomerReadService Customer read service.
	 */
	abstract public function createCustomerReadService();


	/**
	 * Creates a customer service object.
	 *
	 * @return CustomerService Customer service.
	 */
	abstract public function createCustomerWriteService();
}