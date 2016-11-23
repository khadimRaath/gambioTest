<?php

/* --------------------------------------------------------------
  $Id: Import.php 0.1 2010-07-16 $

  brickfox Multichannel eCommerce
  http://www.brickfox.de

  Copyright (c) 2010 brickfox by NETFORMIC GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  -------------------------------------------------------------- */

class Brickfox_Lib_Import
{
	var $_total;
	var $_processed;
	var $_failed = array();
	var $_customerId;

	/**
	 * import orders
	 *
	 * @param string $importFile
	 * @return void
	 */
	function importOrders($importFile)
	{
		$xmlReader = new XMLReader ();
		$count = 0;
		$countCheck = 0;
		$xmlReader->open($importFile);

		while ($xmlReader->read())
		{
			if ($xmlReader->nodeType == XMLReader::ELEMENT) {
				switch ($xmlReader->localName)
				{
					case 'Orders':
						$countCheck = $xmlReader->getAttribute('count');
						break;
					case 'Order':
						$dom = new DomDocument ();
						$node = $dom->importNode($xmlReader->expand(), true);
						$dom->appendChild($node);
						$xml = $dom->saveXML();

						unset ($dom);

						$simpleXml = simplexml_load_string($xml);

						$success = $this->_importXml($simpleXml);

						if ($success === true) {
							$count++;
						}
						else
						{
							$this->addFailed($success);
						}

						break;
				}
			}
		}

		$this->setTotal($countCheck);
		$this->setProcessed($count);
	}

	/**
	 * export orders
	 *
	 * @return SimpleXMLElement
	 */
	function exportOrders()
	{
		$obj_dom = new DomDocument ();
		$rootNode = $obj_dom->createElement('Orders');
		$obj_node = $obj_dom->createElement('Totals', $this->getTotal());
		$rootNode->appendChild($obj_node);
		$obj_node = $obj_dom->createElement('Process', $this->getProcessed());
		$rootNode->appendChild($obj_node);
		foreach ($this->getFailed() as $fail)
		{
			$OrderNode = $obj_dom->createElement('Order');
			$obj_node = $obj_dom->createElement('OrderId', $fail);
			$OrderNode->appendChild($obj_node);
			$rootNode->appendChild($OrderNode);
		}
		$obj_dom->appendChild($rootNode);
		$str_xml = $obj_dom->saveXML();
		unset ($obj_dom);

		$simpleXml = simplexml_load_string($str_xml);

		return $str_xml;
	}

	/**
	 * prepare data
	 *
	 * @param string $data
	 * @return string
	 */
	function getPrepareData($data)
	{
		return ((isset($GLOBALS["___mysqli_ston"]) && is_object($GLOBALS["___mysqli_ston"])) ? mysqli_real_escape_string($GLOBALS["___mysqli_ston"], utf8_decode($data)) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""));
	}

	/**
	 * _import the simple xml element
	 *
	 * @param SimpleXMLElement  $simpleXml
	 * @return bool|resource
	 */
	function _importXml($simpleXml)
	{
		$checkOrderQuery = xtc_db_query(
			'SELECT count(brickfox_orders_id) as count
			FROM brickfox_orders
			WHERE brickfox_orders_id = "' . $simpleXml->OrderId . '"'
		);

		$count = xtc_db_fetch_array($checkOrderQuery);

		if ($count['count'] > 0) {
			return $simpleXml->OrderId;
		}

		$success = true;

		$this->createCustomer($simpleXml);

		$query =
				'INSERT INTO ' . TABLE_ORDERS . '
			(
				customers_id,
				customers_name,
				customers_company,
				customers_vat_id,
				customers_street_address,
				customers_city,
				customers_postcode,
				customers_state,
				customers_country,
				customers_telephone,
				customers_email_address,
				customers_address_format_id,
				delivery_name,
				delivery_company,
				delivery_street_address,
				delivery_city,
				delivery_postcode,
				delivery_state,
				delivery_country,
				delivery_country_iso_code_2,
				delivery_address_format_id,
				billing_name,
				billing_company,
				billing_street_address,
				billing_city,
				billing_postcode,
				billing_state,
				billing_country,
				billing_country_iso_code_2,
				billing_address_format_id,
				orders_status,
				last_modified,
				date_purchased,
				currency,
				currency_value,
				customers_status_name,
				comments,
				payment_method,
				payment_class,
				shipping_method,
				shipping_class
			)
			VALUES
			(
				"' . intval($this->getCustomerId()) . '",
				"' . $this->getPrepareData($simpleXml->BillingParty->Name) . '",
				"' . $this->getPrepareData($simpleXml->BillingParty->Company) . '",
				"' . $this->getPrepareData($simpleXml->BillingParty->VatId) . '",
				"' . $this->getPrepareData($simpleXml->BillingParty->Address) . '\n'
				. $this->getPrepareData($simpleXml->BillingParty->AddressAdd) . '",
				"' . $this->getPrepareData($simpleXml->BillingParty->City) . '",
				"' . $this->getPrepareData($simpleXml->BillingParty->PostalCode) . '",
				"' . $this->getPrepareData($simpleXml->BillingParty->State) . '",
				"' . $this->getPrepareData($simpleXml->BillingParty->Country) . '",
				"' . $this->getPrepareData($simpleXml->BillingParty->PhonePrivat) . '",
				"' . $this->getPrepareData($simpleXml->BillingParty->EmailAddress) . '",
				"5",
				"' . $this->getPrepareData($simpleXml->DeliveryParty->Name) . '",
				"' . $this->getPrepareData($simpleXml->DeliveryParty->Company) . '",
				"' . $this->getPrepareData($simpleXml->DeliveryParty->Address) . '\n'
				. $this->getPrepareData($simpleXml->DeliveryParty->AddressAdd) . '",
				"' . $this->getPrepareData($simpleXml->DeliveryParty->City) . '",
				"' . $this->getPrepareData($simpleXml->DeliveryParty->PostalCode) . '",
				"' . $this->getPrepareData($simpleXml->DeliveryParty->State) . '",
				"' . $this->getPrepareData($simpleXml->DeliveryParty->Country) . '",
				"' . $this->getPrepareData($simpleXml->DeliveryParty->Country) . '",
				"5",
				"' . $this->getPrepareData($simpleXml->BillingParty->Name) . '",
				"' . $this->getPrepareData($simpleXml->BillingParty->Company) . '",
				"' . $this->getPrepareData($simpleXml->BillingParty->Address) . '\n'
				. $this->getPrepareData($simpleXml->BillingParty->AddressAdd) . '",
				"' . $this->getPrepareData($simpleXml->BillingParty->City) . '",
				"' . $this->getPrepareData($simpleXml->BillingParty->PostalCode) . '",
				"' . $this->getPrepareData($simpleXml->BillingParty->State) . '",
				"' . $this->getPrepareData($simpleXml->BillingParty->Country) . '",
				"' . $this->getPrepareData($simpleXml->BillingParty->Country) . '",
				"5",
				"1",
				NOW(),
				"' . $this->getPrepareData($simpleXml->OrderDate) . '",
				"EUR",
				"1",
				"brickfox",
				"' . $this->getPrepareData($simpleXml->Comment) . '",
				"' . $this->getPrepareData($simpleXml->PaymentMethod) . '",
				"' . $this->getPrepareData($simpleXml->PaymentMethod) . '",
				"' . $this->getPrepareData($simpleXml->ShippingMethod) . '",
				"' . $this->getPrepareData($simpleXml->ShippingMethod) . '"
			)';

		$success = xtc_db_query($query);

		if (!$success) {
			return $simpleXml->OrderId;
		}

		$orderId = xtc_db_insert_id();

		$success = xtc_db_query(
			'INSERT INTO brickfox_orders
			(
				brickfox_orders_id,
				extern_orders_id,
				intern_orders_id
			)
			VALUES
			(
				"' . $simpleXml->OrderId . '",
				"' . $this->getPrepareData($simpleXml->ExternOrderId) . '",
				"' . $orderId . '"
			)'
		);

		if (!$success) {
			return $simpleXml->OrderId;
		}

		$success = xtc_db_query(
			'INSERT INTO ' . TABLE_ORDERS_TOTAL . '
			(
				orders_id,
				title,
				text,
				value,
				class,
				sort_order
			)
			VALUES
			(
				"' . $orderId . '",
				"Zwischensumme:",
				"' . $simpleXml->TotalAmountProducts . ' EUR",
				"' . $simpleXml->TotalAmountProducts . '",
				"ot_subtotal",
				"10"
			)'
		);

		if (!$success) {
			return $simpleXml->OrderId;
		}

		$success = xtc_db_query(
			'INSERT INTO ' . TABLE_ORDERS_TOTAL . '
			(
				orders_id,
				title,
				text,
				value,
				class,
				sort_order
			)
			VALUES
			(
				"' . $orderId . '",
				"Versandkosten:",
				"' . $simpleXml->ShippingCost . ' EUR",
				"' . $simpleXml->ShippingCost . '",
				"ot_shipping",
				"30"
			)'
		);

		if (!$success) {
			return $simpleXml->OrderId;
		}

		$success = xtc_db_query(
			'INSERT INTO ' . TABLE_ORDERS_TOTAL . '
			(
				orders_id,
				title,
				text,
				value,
				class,
				sort_order
			)
			VALUES
			(
				"' . $orderId . '",
				"inkl. MwSt.:",
				"' . $simpleXml->TotalAmountVat . ' EUR",
				"' . $simpleXml->TotalAmountVat . '",
				"ot_tax",
				"50"
			)'
		);

		if (!$success) {
			return $simpleXml->OrderId;
		}

		$success = xtc_db_query(
			'INSERT INTO ' . TABLE_ORDERS_TOTAL . '
			(
				orders_id,
				title,
				text,
				value,
				class,
				sort_order
			)
			VALUES
			(
				"' . $orderId . '",
				"<b>Summe</b>:",
				"' . $simpleXml->TotalAmount . ' EUR",
				"' . $simpleXml->TotalAmount . '",
				"ot_total",
				"99"
			)'
		);

		if (!$success) {
			return $simpleXml->OrderId;
		}

		foreach ($simpleXml->OrderLines->OrderLine as $orderLine)
		{
			$success = xtc_db_query(
				'INSERT INTO ' . TABLE_ORDERS_PRODUCTS . '
				(
					orders_id,
					products_id,
					products_name,
					products_price,
					final_price,
					products_tax,
					products_quantity
				)
				VALUES
				(
					"' . $orderId . '",
					"' . $this->getPrepareData($orderLine->ExternProductId) . '",
					"' . $this->getPrepareData($orderLine->ProductName) . '",
					"' . $orderLine->ProductsPrice . '",
					"' . $orderLine->ProductsPriceTotal . '",
					"' . $orderLine->TaxRate . '",
					"' . $orderLine->QuantityOrdered . '"
				)'
			);

			if (!$success) {
				return $simpleXml->OrderId;
			}

			$orderProductId = xtc_db_insert_id();

			$success = xtc_db_query(
				'INSERT INTO brickfox_orders_lines
				(
					brickfox_orders_lines_id,
					orders_products_id
				)
				VALUES
				(
					"' . $orderLine->OrderLineId . '",
					"' . $orderProductId . '"
				)'
			);

			if (!$success) {
				return $simpleXml->OrderId;
			}

			foreach ($orderLine->OrderLineOptions->OrderLineOption as $option)
			{
				xtc_db_query(
					'INSERT INTO ' . TABLE_ORDERS_PRODUCTS_ATTRIBUTES . '
					(
						orders_id,
						orders_products_id,
						products_options,
						products_options_values,
						options_values_price,
						price_prefix
					)
					VALUES
					(
						"' . $orderId . '",
						"' . $orderProductId . '",
						"' . $option->Option . '",
						"' . $option->OptionValue . '",
						"0",
						"+"
					)'
				);
			}
		}

		foreach ($simpleXml->OrderFiles->OrderFile as $orderFile)
		{
			$success = xtc_db_query(
				'INSERT INTO ' . TABLE_ORDERS_PRODUCTS_DOWNLOAD . '
				(
					orders_id,
					orders_products_filename
				)
				VALUES
				(
					"' . $orderId . '",
					"' . $this->getPrepareData($orderFile->Path) . '"
				)'
			);

			if (!$success) {
				return $simpleXml->OrderId;
			}
		}

		return $success;
	}

	/**
	 * get total count
	 *
	 * @return int
	 */
	function getTotal()
	{
		return $this->_total;
	}

	/**
	 * set total count
	 *
	 * @param int $total
	 * @return void
	 */
	function setTotal($total)
	{
		$this->_total = $total;
	}

	/**
	 * get processed count
	 *
	 * @return int
	 */
	function getProcessed()
	{
		return $this->_processed;
	}

	/**
	 * set processed count
	 *
	 * @param int $processed
	 * @return void
	 */
	function setProcessed($processed)
	{
		$this->_processed = $processed;
	}

	/**
	 * get failed
	 *
	 * @return array
	 */
	function getFailed()
	{
		return $this->_failed;
	}

	/**
	 * set failed
	 *
	 * @param array $failed
	 * @return void
	 */
	function setFailed($failed)
	{
		$this->_failed = $failed;
	}

	/**
	 * add failed array item
	 *
	 * @param string $failed
	 * @return void
	 */
	function addFailed($failed)
	{
		$this->_failed[] = $failed;
	}

	/**
	 * get customer id
	 *
	 * @return int
	 */
	function getCustomerId()
	{
		return $this->_customerId;
	}

	/**
	 * set customer id
	 *
	 * @param int $customerId
	 * @return void
	 */
	function setCustomerId($customerId)
	{
		$this->_customerId = $customerId;
	}

	function getCustomersGuestStatusId()
	{
		return DEFAULT_CUSTOMERS_STATUS_ID_GUEST;
	}

	/**
	 * create new customer in group guest
	 *
	 * @param SimpleXMLElement $simpleXml
	 * @return void
	 */
	function createCustomer($simpleXml)
	{
		$sqlArray = array(
			'customers_status' => $this->getCustomersGuestStatusId(),
			'customers_cid' => '',
			'customers_vat_id' => $this->getPrepareData($simpleXml->BillingParty->VatId),
			'customers_email_address' => $this->getPrepareData($simpleXml->BillingParty->EmailAddress),
			'customers_telephone' => $this->getPrepareData($simpleXml->BillingParty->PhonePrivat),
			'customers_date_added' => 'now()',
			'customers_last_modified' => 'now()'
		);

		$firstName = $this->getPrepareData($simpleXml->BillingParty->FirstName);
		if ($firstName != '') {
			$sqlArray['customers_firstname'] = $firstName;
		}

		$lastName = $this->getPrepareData($simpleXml->BillingParty->LastName);
		if ($lastName != '') {
			$lastName = $lastName;
		} else {
			$lastName = $this->getPrepareData($simpleXml->BillingParty->Name);
		}

		$sqlArray['customers_lastname'] = $lastName;

		$customersGender = '';
		if (ACCOUNT_GENDER == 'true') {
			$title = $this->getPrepareData($simpleXml->BillingParty->Title);
			if ($title == 'Herr') {
				$customersGender = 'm';
			}
			elseif ($title == 'Frau') {
				$customersGender = 'f';
			}
			$sqlArray['customers_gender'] = $customersGender;
		}

		if (ACCOUNT_DOB == 'true') {
			$sqlArray['customers_dob'] = $this->getPrepareData($simpleXml->BillingParty->DateOfBirth);
		}

		xtc_db_perform(TABLE_CUSTOMERS, $sqlArray);

		$this->setCustomerId(xtc_db_insert_id());

		$queryCountry = "SELECT countries_id FROM " . TABLE_COUNTRIES . " ";
		$queryCountry .= "WHERE countries_name = '" . xtc_db_input($this->getPrepareData($simpleXml->BillingParty->Country)) . "' ";
		$queryCountry .= "OR countries_iso_code_2 = '" . xtc_db_input($this->getPrepareData($simpleXml->BillingParty->Country)) . "' ";
		$queryCountry .= "OR countries_iso_code_3 = '" . xtc_db_input($this->getPrepareData($simpleXml->BillingParty->Country)) . "' ";

		$resultCountry = xtc_db_query($queryCountry);

		$countriesId = 0;
		if (xtc_db_num_rows($resultCountry) == 1) {
			$countryValues = xtc_db_fetch_array($resultCountry);
			$countriesId = $countryValues['countries_id'];
		}

		$zoneId = 0;

		$state = $this->getPrepareData($simpleXml->BillingParty->State);

		if (ACCOUNT_STATE == 'true' && $state != '') {
			if ($countriesId > 0) {
				$queryCheck = xtc_db_query("SELECT COUNT(*) AS total FROM " . TABLE_ZONES . " WHERE zone_country_id = '" . xtc_db_input($countriesId) . "'");
				$checkTotal = xtc_db_fetch_array($queryCheck);
				$entryStateHasZones = ($checkTotal['total'] > 0);
				if ($entryStateHasZones == true) {
					$query = "SELECT zone_id FROM " . TABLE_ZONES . " WHERE zone_country_id = '" . xtc_db_input($countriesId) . "' AND zone_name = '" . xtc_db_input($state) . "'";
					$queryZone = xtc_db_query($query);
					if (xtc_db_num_rows($queryZone) == 1) {
						$zoneValues = xtc_db_fetch_array($queryZone);
						$zoneId = $zoneValues['zone_id'];
					} else {
						$queryZone = xtc_db_query("select zone_id from " . TABLE_ZONES . " where zone_country_id = '" . xtc_db_input($countriesId) . "' and zone_code = '" . xtc_db_input($state) . "'");
						if (xtc_db_num_rows($queryZone) >= 1) {
							$zoneValues = xtc_db_fetch_array($queryZone);
							$zoneId = $zoneValues['zone_id'];
						}
					}
				}
			}
		}

		$sqlArray = array(
			'customers_id' => $this->getCustomerId(),
			'entry_street_address' => $this->getPrepareData($simpleXml->BillingParty->Address),
			'entry_postcode' => $this->getPrepareData($simpleXml->BillingParty->PostalCode),
			'entry_city' => $this->getPrepareData($simpleXml->BillingParty->City),
			'address_date_added' => 'now()',
			'address_last_modified' => 'now()'
		);

		if ($countriesId > 0) {
			$sqlArray['entry_country_id'] = $countriesId;
		}

		if ($zoneId > 0) {
			$sqlArray['entry_zone_id'] = $zoneId;
			$sqlArray['entry_state'] = '';
		} else {
			$sqlArray['entry_zone_id'] = '0';
			if ($state != '') {
				$sqlArray['entry_state'] = $state;
			} else {
				$sqlArray['entry_state'] = '';
			}
		}

		if ($firstName != '') {
			$sqlArray['entry_firstname'] = $firstName;
		}

		$sqlArray['entry_lastname'] = $lastName;

		if (ACCOUNT_GENDER == 'true') {
			$sqlArray['entry_gender'] = $customersGender;
		}

		if (ACCOUNT_COMPANY == 'true') {
			$company = $this->getPrepareData($simpleXml->BillingParty->Company);
			if ($company != '') {
				$sqlArray['entry_company'] = $company;
			}
		}

		if (ACCOUNT_SUBURB == 'true') {
			$suburb = $this->getPrepareData($simpleXml->BillingParty->Suburb);
			if ($suburb != '') {
				$sqlArray['entry_suburb'] = $suburb;
			}
		}

		xtc_db_perform(TABLE_ADDRESS_BOOK, $sqlArray);

		$addressId = xtc_db_insert_id();

		xtc_db_query("UPDATE " . TABLE_CUSTOMERS . " SET customers_default_address_id = '" . $addressId . "' WHERE customers_id = '" . $this->getCustomerId() . "'");
		xtc_db_query("INSERT INTO " . TABLE_CUSTOMERS_INFO . " (customers_info_id, customers_info_number_of_logons, customers_info_date_account_created) values ('" . $this->getCustomerId() . "', '0', now())");
	}
}

?>