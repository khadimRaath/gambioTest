<?php
/* --------------------------------------------------------------
	print_intraship_label.php 2016-07-19
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2015 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]

IMPORTANT! THIS FILE IS DEPRECATED AND WILL BE REPLACED IN THE FUTURE.
MODIFY IT ONLY FOR FIXES. DO NOT APPEND IT WITH NEW FEATURES, USE THE
NEW GX-ENGINE LIBRARIES INSTEAD.

	--------------------------------------------------------------
*/

/* -----------------------------------------------------------------------------------------
$Id: print_intraship_label.php v1.00 2016-01-19

Autor: Nico Bauer (c) 2010 Amber Holding GmbH for DHL Vertriebs GmbH & Co. OHG

Released under the GNU General Public License (Version 2)
[http://www.gnu.org/licenses/gpl-2.0.html]

based on:
(c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
(c) 2002-2003 osCommerce(ot_cod_fee.php,v 1.02 2003/02/24); www.oscommerce.com
(C) 2001 - 2003 TheMedia, Dipl.-Ing Thomas Plänkers ; http://www.themedia.at & http://www.oscommerce.at
(c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: ot_cod_fee.php 1003 2005-07-10 18:58:52Z mz $)

Released under the GNU General Public License
-----------------------------------------------------------------------------------------*/

function prettyXML($xml) {
	if(empty($xml)) {
		return '-- NO CONTENT --';
	}
	$doc = new DOMDocument();
	$doc->preserveWhiteSpace = false;
	$doc->formatOutput = true;
	$doc->loadXML($xml);
	return $doc->saveXML();
}

function is_pageTokenClassAvailable() {
	if(class_exists('PageToken')
		&& isset($_SESSION['coo_page_token'])
		&& is_object($_SESSION['coo_page_token'])
		&& get_class($_SESSION['coo_page_token']) == 'PageToken')
	{
		return true;
	}
	return false;
}

//benötigte Dateien einlesen
require_once 'includes/application_top.php';
require_once DIR_FS_INC . 'xtc_get_attributes_model.inc.php';

if(is_pageTokenClassAvailable() == true) {
	$page_token = $_SESSION['coo_page_token']->generate_token();
}

$gmintraship = new GMIntraship();

//Order-ID ermitteln
$oID = (int)$_GET['oID'];
if($oID == 0 && (!isset($_GET['error']))) {
	$errormsg = urlencode($gmintraship->get_text('OERDERERROR'));
	xtc_redirect(xtc_href_link('print_intraship_label.php', 'oID='.$oID.'&error='.$errormsg));
};

//Intrship Webservice URL
$dhlwsdlurl = $gmintraship->getWSDLLocation();
$dhlintrashipurl = $gmintraship->getIntrashipPortalURL();

//letzte Sendung aus Datenbank löschen
if($_POST['action'] == 'deleteshipment') {
	xtc_db_query("update " . TABLE_ORDERS . " set intraship_shipmentnumber = '' where orders_id = '".$oID."'");
	xtc_redirect(xtc_href_link('print_intraship_label.php', 'oID='.$oID));
}

//Bestelldaten ermitteln
$order_data_query = xtc_db_query("select * from ".TABLE_ORDERS." where orders_id = '".xtc_db_input($oID)."'");
$order_data = xtc_db_fetch_array($order_data_query);

//DHL ProductCode und PartnerID herausssuchen
$productcode = $gmintraship->getProductCode($order_data['delivery_country_iso_code_2']);
$partnerid = $gmintraship->getPartnerID($order_data['delivery_country_iso_code_2']);

// Packstation-Handling
if(preg_match('/(.*)\/(\d+)/', $order_data['delivery_lastname'], $matches) == 1) {
	$postnumber = $matches[2];
	$order_data['delivery_lastname'] = preg_replace('/(.*)\/\d+/', '$1', $order_data['delivery_lastname']);
	$order_data['delivery_name'] = preg_replace('/(.*)\/\d+/', '$1', $order_data['delivery_name']);
	//$order_data['delivery_suburb'] = $postnumber;
}

//Voreinstellungen für den Debug-Modus

if($gmintraship->debug == true) {
	$ekp = '5000000000';
	$user = 'geschaeftskunden_api';
	$password = 'Dhl_ep_test1';
	$partnerid = '01';
	$cig_user = GMIntraship::DEVID;
	$cig_pwd = GMIntraship::DEVPWD;
}
else {
	$ekp = $gmintraship->ekp;
	$user = $gmintraship->user;
	$password = $gmintraship->password;
	$cig_user = GMIntraship::APPID;
	$cig_pwd = GMIntraship::APPToken;
}


//SSO für Intraship Web-Oberfläche
if($_GET['action'] == 'intraship') {
	$options = array(
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_HEADER         => true,
		CURLOPT_FOLLOWLOCATION => (ini_get('open_basedir') == '' && (ini_get('safe_mode') == 'Off' || ini_get('safe_mode') == false)),
		CURLOPT_ENCODING       => "",
		CURLOPT_USERAGENT      => "Mozilla/4.0",
		CURLOPT_AUTOREFERER    => true,
		CURLOPT_CONNECTTIMEOUT => 20,
		CURLOPT_TIMEOUT        => 20,
		CURLOPT_MAXREDIRS      => 1,
	);

	$url_options = array(
		'login' => $user,
		'pwd' => $password,
		'LANGUAGE' => 'DE',
	);

	$request = curl_init($dhlintrashipurl.'?' . http_build_query($url_options));
	curl_setopt_array( $request, $options );
	$content = curl_exec( $request );
	$err     = curl_errno( $request );
	$errmsg  = curl_error( $request );
	$header  = curl_getinfo( $request );
	curl_close( $request );

	xtc_redirect($header['url']);
}

//Zahlweise COD und in Deutschland?
if(($order_data['payment_method'] == 'cod') && ($order_data['delivery_country_iso_code_2'] == 'DE')) {
	$cod = true;
	//Nachnahmebetrag ermitteln und formatieren
	if(isset($_POST['CODAmount'])) {
		$cod_amount = preg_replace('/[^0-9.,]/','',$_POST['CODAmount']);
		$cod_amount = str_replace(',','.',$cod_amount);
		$cod_amount = number_format($cod_amount, '2', '.', '');
	}
	else {
		$cod_amount_query = xtc_db_query("select value from ".TABLE_ORDERS_TOTAL." where orders_id='".$oID."' order by sort_order desc LIMIT 1");
		$cod_amount_array = xtc_db_fetch_array($cod_amount_query);
		$cod_amount = number_format($cod_amount_array['value'],'2','.','');
	}
};

//Versanddatum = Heute
$shipmentdate=date('Y-m-d');

//Gewichtsberechnung

//Gewicht per Formular eingegeben
if(isset($_POST['WeightInKG'])) {
	$dhl_weight = preg_replace('/[^0-9.,]/','',$_POST['WeightInKG']);
	$dhl_weight = str_replace(',','.',$dhl_weight);
}
else {
	//Versandkartongewicht ermitteln
	$shipping_box_query = xtc_db_query("select configuration_value from configuration where configuration_key='SHIPPING_BOX_WEIGHT'");
	$shipping_box_weight=xtc_db_fetch_array($shipping_box_query);
	//Startgewicht = Versandkartongewicht
	$dhl_weight = $shipping_box_weight['configuration_value'];
	$weight_query = xtc_db_query("select * from " . TABLE_ORDERS_PRODUCTS . " where orders_id = '".$oID."'");
	while ($weight_product = xtc_db_fetch_array($weight_query)) {
		//Basisgewicht ermitteln
		$basic_weight_query = xtc_db_query("select products_weight from " . TABLE_PRODUCTS . " where products_id = '".$weight_product['products_id']."'");
		$basic_weight = xtc_db_fetch_array($basic_weight_query);
		//language-id ermitteln
		$weight_language_query = xtc_db_query("select languages_id from ". TABLE_LANGUAGES ." where directory = '".$order_data['language']."'");
		$weight_language_id = xtc_db_fetch_array($weight_language_query);
		$weight_language_id = $weight_language_id['languages_id'];
		//auf attribute überprüfen
		$weight_attributes_query = xtc_db_query("select * from " . TABLE_ORDERS_PRODUCTS_ATTRIBUTES . " where orders_id = '".$oID."' and orders_products_id = '".$weight_product['orders_products_id']."'");
		$attributes_weight = 0;
		while($weight_attribute = xtc_db_fetch_array($weight_attributes_query)) {
			//Attribute aus Bestellung ermitteln
			$weight_model = xtc_get_attributes_model($weight_product['products_id'], $weight_attribute['products_options_values'], $weight_attribute['products_options'], $weight_language_id);
			if ($weight_model != '') {
				$weight_attribute_weight_query = xtc_db_query("select options_values_weight, weight_prefix from ".TABLE_PRODUCTS_ATTRIBUTES." where attributes_model = '".$weight_model."'");
				$weight_attribute_weight = xtc_db_fetch_array($weight_attribute_weight_query);
				if ($weight_attribute_weight['weight_prefix'] == '-') {
					$attributes_weight -= $weight_attribute_weight['options_values_weight'];
				}
				else {
					$attributes_weight += $weight_attribute_weight['options_values_weight'];
				}
			}
		}
		$dhl_weight += ($basic_weight['products_weight'] + $attributes_weight) * $weight_product['products_quantity'];
	}
}

//Formatierung des Gewichtes für Intraship
$dhl_weight = number_format($dhl_weight, 2,'.','');

//Straßenname und Hausnummer trennen
if(empty($order_data['delivery_house_number']))
{
	if(preg_match('/(.*)\s+(\d+.*)$/i', trim($order_data['delivery_street_address']), $matches) == 1)
	{
		$receiver_streetname   = $matches[1];
		$receiver_streetnumber = $matches[2];
	}
	else
	{
		$receiver_streetname   = trim($order_data['delivery_street_address']);
		$receiver_streetnumber = '';
	}
}
else
{
	$receiver_streetnumber = $order_data['delivery_house_number'];
	$receiver_streetname   = $order_data['delivery_street_address'];
}

//Array für Intraship-XML Bilden
$intraship = array(
	'Version' => array (
		'majorRelease' => '1',
		'minorRelease' => '0',
	),
	'ShipmentOrder' => array(
		'SequenceNumber' => '1',
		'Shipment' => array(
			'ShipmentDetails' => array(
				'ProductCode' => $productcode,
				'ShipmentDate' => $shipmentdate,
				'EKP' => $ekp,
				'Attendance' => array(
					'partnerID' => $partnerid
				),
				'CustomerReference' => $oID,
				'ShipmentItem' => array(
					'WeightInKG' => $dhl_weight,
					'PackageType' => 'PK'
				)
			),
		)
	)
);

//Nachnahme-Array bilden
if($cod==true) {
	$intraship['ShipmentOrder']['Shipment']['ShipmentDetails']['Service'] = array(
		'ServiceGroupOther' => array(
			'COD' => array(
				'CODAmount' => $cod_amount,
				'CODCurrency' => 'EUR'
			)
		)
	);

	//Bankdaten für Nachnahme hinzufügen
	$intraship['ShipmentOrder']['Shipment']['ShipmentDetails']['BankData'] = array(
		'accountOwner' => $gmintraship->cod_account_holder,
		'accountNumber' => $gmintraship->cod_account_number,
		'bankCode' => $gmintraship->cod_bank_number,
		'bankName' => $gmintraship->cod_bank_name,
		'iban' => $gmintraship->cod_iban,
		'bic' => $gmintraship->cod_bic,
		'note' => (string)$oID,
	);
};

//Regiopaket AT Zusatzdaten
if($productcode == 'RPN') {
	$intraship['ShipmentOrder']['Shipment']['ShipmentDetails']['Service']['ServiceGroupDHLPaket']['RegioPacket'] = 'AT';
};

//Versenderinformationen
$intraship['ShipmentOrder']['Shipment']['Shipper'] = array(
	'Company' => array(
		'Company' => array(
			'name1' => $gmintraship->shipper_name,
		)
	),
	'Address' => array(
		'streetName' => $gmintraship->shipper_street,
		'streetNumber' => $gmintraship->shipper_house,
		'Zip' => array(
			'germany' => $gmintraship->shipper_postcode
		),
		'city' => $gmintraship->shipper_city,
		'Origin' => array(
			'countryISOCode' => 'DE'
		)
	),
	'Communication' => array(
		'phone' => $gmintraship->shipper_phone,
		'contactPerson' => $gmintraship->shipper_contact,
		'email' => $gmintraship->shipper_email,
	)
);

//Empfängerinformationen
$intraship['ShipmentOrder']['Shipment']['Receiver'] = array(
	'Company' => array(
		'Company' => array(
			'name1' => $order_data['delivery_company'],
			'name2' => $order_data['delivery_suburb'],
		)
	),
	'Communication' => array(
		//'phone' => $order_data['customers_telephone'], // optional
		//'email' => $order_data['customers_email_address'],
		'contactPerson' => $order_data['delivery_firstname'].' '.$order_data['delivery_lastname']
	)
);

$is_packstation = false;
$is_postfiliale = false;
if(isset($postnumber)) {
	if(preg_match('/.*packstation.*/i', $receiver_streetname) == 1) {
		// Packstation-Adresse
		$is_packstation = true;
		$intraship['ShipmentOrder']['Shipment']['Receiver']['Packstation'] = array(
			'PackstationNumber' => $receiver_streetnumber,
			'PostNumber' => $postnumber,
			'Zip' => $order_data['delivery_postcode'],
			'City' => $order_data['delivery_city'],
		);
	}
	if(preg_match('/.*postfiliale.*/i', $receiver_streetname) == 1) {
		$is_postfiliale = true;
		$intraship['ShipmentOrder']['Shipment']['Receiver']['Address'] = array(
			'streetName' => 'Postfiliale',
			'streetNumber' => $receiver_streetnumber,
			'city' => $order_data['delivery_city'],
			'Origin' => array(
				'countryISOCode' => $order_data['delivery_country_iso_code_2']
			)
		);
		$intraship['ShipmentOrder']['Shipment']['Receiver']['Company']['Company']['name2'] = $postnumber;
		$intraship['ShipmentOrder']['Shipment']['Receiver']['Address']['Zip']['germany'] = $order_data['delivery_postcode'];
	}
}
else {
	// klassische Adresse
	$intraship['ShipmentOrder']['Shipment']['Receiver']['Address'] = array(
		'streetName' => $receiver_streetname,
		'streetNumber' => $receiver_streetnumber,
		'city' => $order_data['delivery_city'],
		'Origin' => array(
			'countryISOCode' => $order_data['delivery_country_iso_code_2']
		)
	);
	//PLZ länderspezifisch zuordnen
	if($order_data['delivery_country_iso_code_2']=='DE') {
		$intraship['ShipmentOrder']['Shipment']['Receiver']['Address']['Zip']['germany'] = $order_data['delivery_postcode'];
	}
	elseif ($order_data['delivery_country_iso_code_2']=='GB') {
		$intraship['ShipmentOrder']['Shipment']['Receiver']['Address']['Zip']['england'] = $order_data['delivery_postcode'];
	}
	else {
		$intraship['ShipmentOrder']['Shipment']['Receiver']['Address']['Zip']['other'] = $order_data['delivery_postcode'];
	}
}


if(!empty($order_data['customers_telephone'])) {
	$intraship['ShipmentOrder']['Shipment']['Receiver']['Communication']['phone'] = $order_data['customers_telephone'];
}
else {
	$intraship['ShipmentOrder']['Shipment']['Receiver']['Communication']['phone'] = '+00-00000-000000';
}

if($gmintraship->send_announcement == true) {
	$intraship['ShipmentOrder']['Shipment']['Receiver']['Communication']['email'] = $order_data['customers_email_address'];
}

//Wenn keine Firma dann Firmenname durch Vor- und Nachnamen ersetzen
if(!$order_data['delivery_company']) {
	$intraship['ShipmentOrder']['Shipment']['Receiver']['Company']['Company']['name1'] = $order_data['delivery_firstname'].' '.$order_data['delivery_lastname'];
	//Beim nationalen Versand kann dann die Kontaktperson entfallen
	if($productcode == 'EPN') {
		$intraship['ShipmentOrder']['Shipment']['Receiver']['Communication']['contactPerson'] = '';
	}
}


//Daten für erhöhte Versicherung
if(isset($_POST['insurance'])){
	$intraship['ShipmentOrder']['Shipment']['ShipmentDetails']['Service']['ServiceGroupOther']['HigherInsurance'] = array(
		'InsuranceAmount' => '2500',
		'InsuranceCurrency' => 'EUR'
	);
}

//Daten für Sperrgut
if(isset($_POST['bulkfreight'])) {
	$intraship['ShipmentOrder']['Shipment']['ShipmentDetails']['Service']['ServiceGroupOther']['Bulkfreight'] = '';
}

//E-Mail-Benachrichtigung
if(!empty($order_data['customers_email_address']) && strlen($order_data['customers_email_address']) <= 20) {
	$intraship['ShipmentOrder']['Shipment']['ShipmentDetails']['Notification']['RecipientName'] = substr($order_data['customers_name'], 0, 45);
	$intraship['ShipmentOrder']['Shipment']['ShipmentDetails']['Notification']['RecipientEmailAddress'] = substr($order_data['customers_email_address'], 0, 20);
}

//internationaler Versand mit dummy Zolldaten
if($productcode == 'BPI') {
	$amount_query = xtc_db_query("select value from ".TABLE_ORDERS_TOTAL." where orders_id='".$oID."' order by sort_order desc LIMIT 1");
	$amount_array = xtc_db_fetch_array($amount_query);
	$amount = number_format($amount_array['value'],'2','.','');
	$intraship['ShipmentOrder']['Shipment']['ExportDocument'] = array(
		'InvoiceDate' => $shipmentdate,
		'ExportType' => '0',
		'ExportTypeDescription' => 'Merchandise',
		'CommodityCode' => '00000000',
		'TermsOfTrade' => 'DDU',
		'Amount' => '1',
		'Description' => 'Customsform Order '.$oID,
		'CountryCodeOrigin' => 'DE',
		'CustomsValue' => $amount,
		'CustomsCurrency' => 'EUR',
		'ExportDocPosition' => array(
			'Description' => 'Item',
			'CountryCodeOrigin' => 'DE',
			'CommodityCode' => '00000000',
			'Amount' => '1',
			'NetWeightInKG' => $dhl_weight,
			'GrossWeightInKG' => $dhl_weight,
			'CustomsValue' => $amount,
			'CustomsCurrency' => 'EUR'
		)
	);
	//Service Premium für internationalen Versand
	if($gmintraship->bpi_use_premium) {
		$intraship['ShipmentOrder']['Shipment']['ShipmentDetails']['Service']['ServiceGroupBusinessPackInternational']['Premium'] = 'Premium';
	}
	else {
		$intraship['ShipmentOrder']['Shipment']['ShipmentDetails']['Service']['ServiceGroupBusinessPackInternational']['Economy'] = 'Economy';
	}
}

//Rückgabe als URL zum Versandaufkleber
$intraship['ShipmentOrder']['LabelResponseType'] = 'URL';

// Umgang mit Problemen mit Leitcodierung
$intraship['ShipmentOrder']['PRINTONLYIFCODEABLE'] = isset($_POST['onlyifcodeable']) ? '1' : '0';

$cigcredentials = $gmintraship->getWebserviceCredentials();

$labelInfo = '<!-- no info -->';

//Label anfordern oder löschen
if(((isset($_POST['getlabel'])) || (isset($_POST['stornolabel']))) && ($_GET['error'] == '')) {
	//Optionsarray für die SOAP-Anfrage
	$options = array(
		'location' => $gmintraship->getWebserviceEndpoint(),
		'authentication' => SOAP_AUTHENTICATION_BASIC,
		'login' => $cigcredentials->user,
        'password' =>  $cigcredentials->password,
        'HTTP_PASS' => $cigcredentials->password,
		'encoding' => 'UTF-8',
		'trace' => 1,
		'cache_wsdl' => WSDL_CACHE_NONE,
	);
	$soapClient = new SoapClient($dhlwsdlurl, $options);

	//Zugangsdaten in SOAP-Header
	$headers = array();
	$sh_param = array(
		'user' => $user,
		'signature' => $password,
		'type' => '0'
	);
	$headers[] = new SoapHeader('http://dhl.de/webservice/cisbase','Authentification', $sh_param);

	//SOAPClient
	$soapClient->__setSoapHeaders($headers);

	//SOAP Aktion festlegen
	if(isset($_POST['getlabel'])) {
		$function = 'CreateShipmentDD';
	}

	if(isset($_POST['stornolabel'])) {
		//letzte Versandnummer ermktteln
		$orderdata_raw = explode(',', $order_data['intraship_shipmentnumber']);
		foreach ($orderdata_raw as $number => $tracking_raw) {
			$tracking_array = explode(' ', $tracking_raw);
			$orderdata_array[$tracking_array[0]] = $tracking_array[1];
		}
		foreach($orderdata_array as $shippingnumber => $shippingdate) {
			$lastshipment = $shippingnumber;
		}
		//Array für Storno-Anfrage bilden
		$intraship = array(
			'Version' => array (
				'majorRelease' => '1',
				'minorRelease' => '0'
			),
			'ShipmentNumber' => array(
				'shipmentNumber' => $lastshipment
			)
		);
		$function = 'DeleteShipmentDD';
	}

	//SOAP Anfrage ausführen
	try {
		//header('Content-Type: text/plain'); die(print_r($intraship, true));
		$result = $soapClient->__soapCall($function, array($intraship));
		if($gmintraship->debug == true)
		{
			$debuglog = new FileLog('intraship-debug', true);
			$debuglog->write("\n### ".date('c')." ###\n\n");
			$debuglog->write("Request:\n".prettyXML($soapClient->__getLastRequest()) . "\n");
			$debuglog->write("Response:\n".prettyXML($soapClient->__getLastResponse()) . "\n");
		}
		//Auf Fehlermeldung von Intraship prüfen
		if(property_exists($result,'status')) {
			$statuscheck = $result->status;
		}
		else {
			$statuscheck = $result->Status;
		}
		//Fehlerausgabe zusammenstellen
		if($statuscheck->StatusCode != '0') {
			if($result->CreationState->StatusCode == '1102')
			{
				$codeable = false;
			}
			if($function == 'CreateShipmentDD')
			{
				if(is_array($result->CreationState->StatusMessage)) {
					foreach($result->CreationState->StatusMessage as $statusmessage) {
						if(strpos($statusmessage, '[NON_CODABLE]') !== false) {
							$codeable = false;
						}
					}
				}
				else {
					if(strpos((string)$result->CreationState->StatusMessage, '[NON_CODABLE]') !== false) {
						$codeable = false;
					}
				}
			}

			if(isset($codeable) && $codeable == false) {
				$statusMessages = is_array($result->CreationState->StatusMessage) ? $result->CreationState->StatusMessage : [ (string)$result->CreationState->StatusMessage ];
				$statusMessage = '';
				foreach($statusMessages as $sMessage)
				{
					$statusMessage .= (string)$sMessage . '<br>';
				}
				$_SESSION['intraship_warning_not_codeable'] = true;
				throw new Exception('Adresse ist nicht leitcodierbar! <br> ' . $statusMessage);
			}
			else
			{
				$errormsg = print_r($result, true);
				$_SESSION['intraship_error'] = $errormsg;
				$errorlog = new FileLog('intraship-errors', true);
				$errorlog->write("### ".date('c')." ###\nERROR: ".$errormsg."\n\n");
				$errorlog->write("WSDL: ".$dhlwsdlurl."\n");
				$errorlog->write("Endpoint: ".$gmintraship->getWebserviceEndpoint()."\n");
				$errorlog->write("Request:\n".prettyXML($soapClient->__getLastRequest()) . "\n");
				$errorlog->write("Response:\n".prettyXML($soapClient->__getLastResponse()) . "\n");
				throw new Exception('Es ist ein Fehler aufgetreten! (Details im Logfile intraship-errors)');
			}
		}
		else {
			if($gmintraship->debug == true) {
				$debuglog = new FileLog('intraship-debug', true);
				$debuglog->write("\n### ".date('c')." ERROR Status == 0 ###\n\n");
				$debuglog->write("Request:\n".prettyXML($soapClient->__getLastRequest()) . "\n");
				$debuglog->write("Response:\n".prettyXML($soapClient->__getLastResponse()) . "\n");
			}

			// Label erhalten
			if(isset($_POST['getlabel'])) {
				//Versandinformationen in Datenbank schreibem
				$gmintraship->storeTrackingNumber($oID, (string)$result->CreationState->ShipmentNumber->shipmentNumber);
				if($order_data['intraship_shipmentnumber']) {
					$fieldtext = ',';
				}
				$fieldtext .= xtc_db_input((string)$result->CreationState->ShipmentNumber->shipmentNumber.' '.$shipmentdate);
				xtc_db_query("update " . TABLE_ORDERS . " set intraship_shipmentnumber = concat(intraship_shipmentnumber, '".$fieldtext."') where orders_id = '".$oID."'");
				xtc_db_query("replace into orders_intraship_labels (orders_id, label_url) values('".$oID."', '".xtc_db_input((string)$result->CreationState->Labelurl)."')");

				$ordersStatusComment = $gmintraship->get_text('EMAILTEXT_1').$result->CreationState->ShipmentNumber->shipmentNumber.$gmintraship->get_text('EMAILTEXT_2');
				$gmintraship->setOrderStatus($oID, $gmintraship->status_id_sent, $ordersStatusComment, (bool)$gmintraship->send_email);

				$labelUrl = $result->CreationState->Labelurl;
				$labelInfo = 'Label erzeugt: <a href="' . $labelUrl . '">jetzt abrufen</a>';
			}

			//Storno in Datenbank ausführen
			if(isset($_POST['stornolabel'])) {
				//letzte Tracking-Information löschen
				$length = strrpos($order_data['intraship_shipmentnumber'], ',');
				if(!$length) {
					$fieldtext = '';
				}
				else {
					$fieldtext = substr($order_data['intraship_shipmentnumber'], 0, $length);
				}
				xtc_db_query("update " . TABLE_ORDERS . " set intraship_shipmentnumber = '".$fieldtext."' where orders_id = '".$oID."'");
				xtc_db_query("DELETE FROM orders_intraship_labels WHERE orders_id = '".$oID."'");

				$gmintraship->setOrderStatus($oID, $gmintraship->status_id_storno, $gmintraship->get_text('CANCELTEXT'), (bool)$gmintraship->send_email);
				$labelInfo = 'Sendung wurde storniert';
			}
		}
	}
	catch(SoapFault $fault) {
		//Fehlermeldung schon bei der SOAP-Anfrage
		$labelInfo = $fault->getMessage();
		$errorlog = new FileLog('intraship-errors', true);
		$errorlog->write("### ".date('c')." ###\nERROR/SF: ".$errormsg."\n\n");
		$errorlog->write('ERROR: ' . $fault->getMessage() . "\n");
		$errorlog->write("WSDL: ".$dhlwsdlurl."\n");
		$errorlog->write("Endpoint: ".$gmintraship->getWebserviceEndpoint()."\n");
		$errorlog->write("Request:\n".prettyXML($soapClient->__getLastRequest()) . "\n");
		$errorlog->write("Response:\n".prettyXML($soapClient->__getLastResponse()) . "\n");
	}
	catch(Exception $e)
	{
		$labelInfo = 'FEHLER: ' . $e->getMessage();
	}
	unset($soapClient);
}

if($_SERVER['REQUEST_METHOD'] == 'POST')
{
	$_SESSION['intraship_labelinfo'] = $labelInfo;
	xtc_redirect(xtc_href_link(basename(__FILE__), 'oID='.$oID));
}
else
{
	$labelInfo = isset($_SESSION['intraship_labelinfo']) ? $_SESSION['intraship_labelinfo'] : '';
	unset($_SESSION['intraship_labelinfo']);
}

$label_url = $gmintraship->getLabelURL($oID);

if($intraship['ShipmentOrder']['Shipment']['ShipmentDetails']['ShipmentItem']['WeightInKG'] < 0.1) {
	$intraship['ShipmentOrder']['Shipment']['ShipmentDetails']['ShipmentItem']['WeightInKG'] = '0.10';
}

?>
<!DOCTYPE html>
<html <?php echo HTML_PARAMS; ?>>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $_SESSION['language_charset']; ?>">
	<title><?php echo TITLE; ?></title>
	<link rel="stylesheet" type="text/css" href="html/assets/styles/legacy/stylesheet.css">
	<style>
		#intraship_form {
			display: block;
			margin-left: 50px;
			margin-top: 30px;
		}
		div.labelinfo {
			background-color: #ffcc00;
			margin-top: 1em;
			padding: 1ex 1em;
			color: #000;
		}
		table.intraship td {
			padding: 3px 4px;
		}
	</style>
</head>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF">
	<!-- header //-->
	<?php
		require (DIR_WS_INCLUDES.'header.php');
	?>
	<!-- header_eof //-->

	<!-- body //-->
	<table border="0" width="100%" cellspacing="2" cellpadding="2">
		<tr>
			<td class="columnLeft2" width="<?php echo BOX_WIDTH; ?>" valign="top">
				<table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="1" cellpadding="1" class="columnLeft">

					<!-- left_navigation //-->
					<?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
					<!-- left_navigation_eof //-->

				</table>
			</td>
			<!-- body_text //-->
			<td  class="boxCenter" width="100%" valign="top">
				<table border="0" width="100%" cellspacing="0" cellpadding="0">
					<tr>
						<td class="pageHeading">DHL Intraship</td>
					</tr>
				</table>

				<?php
					// Fehlermeldungen anzeigen
					if($_GET['error'] != '' && !empty($_SESSION['intraship_error']))
					{
						$errormsg = $_SESSION['intraship_error'];
						unset($_SESSION['intraship_error']);
						echo '<table width="100%"><tr><td style="font-family: sans-serif; font-size: 12px; font-weight: bold; background-color: #ffb3b5; border:1px solid">';
						echo 'ERROR:<br><br><pre>'.$errormsg.'</pre>';
						echo '</td></tr></table>';
						if($_GET['function'] == 'DeleteShipmentDD') {
							echo '<a class="button" href="'.xtc_href_link('print_intraship_label.php', 'oID='.$oID.'&action=deleteshipment').'">'.$gmintraship->get_text('DELETESHIPMENT').'</a>';
						}
					}
				?>

				<form action="print_intraship_label.php?oID=<?= $oID ?>" id="intraship_form" method="POST">
					<table class="intraship" valign="top" style="background: #FFCC00">
						<?php
							//evtl. vorhandene Sendungsnummern anzeigen
							if($order_data['intraship_shipmentnumber']) {
								echo '<tr class="main"><td valign="top"><b>Sendungsnummer</b></td><td colspan="2">';
								$orderdata_raw = explode(',',$order_data['intraship_shipmentnumber']);
								foreach($orderdata_raw as $number => $tracking_raw) {
									$tracking_array = explode(' ', $tracking_raw);
									$orderdata_array[$tracking_array[0]] = $tracking_array[1];
								}
								//Links fürs Tracking erstellen
								$labelcount = 0;
								foreach($orderdata_array as $shippingnumber => $shippingdate) {
									echo '<a href="http://nolp.dhl.de/nextt-online-public/set_identcodes.do?lang=de&idc='.$shippingnumber.'">'.$shippingnumber.' '.$shippingdate.'</a><br>';
									$lastshipmentdate = $shippingdate;
									$labelcount++;
								}
								echo '</td></tr>';
							}
						?>
						<tr class="main">
							<td valign="top" ><b>Absender</b></td>
							<td valign="top">
								<?php echo $intraship['ShipmentOrder']['Shipment']['Shipper']['Company']['Company']['name1']?><br>
								<?php echo $intraship['ShipmentOrder']['Shipment']['Shipper']['Communication']['contactPerson']?><br>
								<?php echo $intraship['ShipmentOrder']['Shipment']['Shipper']['Address']['streetName']?>&nbsp;<?php echo $intraship['ShipmentOrder']['Shipment']['Shipper']['Address']['streetNumber']?><br>
								<?php echo ' '.$intraship['ShipmentOrder']['Shipment']['Shipper']['Address']['Zip']['germany']?> <?php echo $intraship['ShipmentOrder']['Shipment']['Shipper']['Address']['city']?>
							</td>
							<td valign="top"><b>EKP</b><br><b>Partner-ID</b></td>
							<td valign="top">
								<?php echo $intraship['ShipmentOrder']['Shipment']['ShipmentDetails']['EKP']?><br>
								<?php echo $intraship['ShipmentOrder']['Shipment']['ShipmentDetails']['Attendance']['partnerID'] ?>
							</td>
						</tr>
						<tr>
							<td>
							</td>
						</tr>
						<tr class="main">
							<td><b>Versanddatum</b></td>
							<td><?php echo $intraship['ShipmentOrder']['Shipment']['ShipmentDetails']['ShipmentDate'] ?></td>
							<td><b>Gewicht</b></td>
							<td>
								<input type="hidden" name="oID" value="<?php echo $oID?>">
								<input type="text" name="WeightInKG" value="<?php echo $intraship['ShipmentOrder']['Shipment']['ShipmentDetails']['ShipmentItem']['WeightInKG']?>"/> kg
							</td>
						</tr>
						<tr>
							<td>
							</td>
						</tr>
						<tr class="main">
							<td valign="top"><b>Empf&auml;nger</b></td>
							<td valign="top">
								<?php echo $intraship['ShipmentOrder']['Shipment']['Receiver']['Company']['Company']['name1']?><br>
								<?php echo $intraship['ShipmentOrder']['Shipment']['Receiver']['Company']['Company']['name2']?><br>
								<?php echo $intraship['ShipmentOrder']['Shipment']['Receiver']['Communication']['contactPerson']?><br>
								<?php if(isset($postnumber) && $is_packstation): ?>
									Postnummer <?php echo $postnumber ?><br>
									Packstation <?php echo $receiver_streetnumber ?><br>
									<?php echo $order_data['delivery_postcode'] ?> <?php echo $order_data['delivery_city'] ?><br>
								<?php else: ?>
									<?php echo $intraship['ShipmentOrder']['Shipment']['Receiver']['Address']['streetName']?> <?php echo $intraship['ShipmentOrder']['Shipment']['Receiver']['Address']['streetNumber']?><br>
									<?php
										if($intraship['ShipmentOrder']['Shipment']['Receiver']['Address']['Origin']['countryISOCode']=='DE') {
											echo $intraship['ShipmentOrder']['Shipment']['Receiver']['Address']['Zip']['germany'];
										}
										elseif($intraship['ShipmentOrder']['Shipment']['Receiver']['Address']['Origin']['countryISOCode']=='GB') {
											echo  $intraship['ShipmentOrder']['Shipment']['Receiver']['Address']['Zip']['england'];
										}
										else {
											echo $intraship['ShipmentOrder']['Shipment']['Receiver']['Address']['Zip']['other'];
										}
										echo ' '.$intraship['ShipmentOrder']['Shipment']['Receiver']['Address']['city']
									?>
								<?php endif ?>
							</td>
							<td colspan="2" valign="top">
								<table class="main">
									<tr>
										<td><b>E-Mail</b></td>
										<td><?php echo $intraship['ShipmentOrder']['Shipment']['Receiver']['Communication']['email']?></td>
									</tr>
									<tr>
										<td><b>Telefon</b></td>
										<td><?php echo $intraship['ShipmentOrder']['Shipment']['Receiver']['Communication']['phone']?></td>
									</tr>
								</table>
							</td>
						</tr>
						<tr>
							<td>
							</td>
						</tr>

						<?php
							//Nachnahmeinformationen wenn vorhanden anzeigen
							if($cod == 'true') {
								echo '<tr class="main"><td>';
								echo '<INPUT NAME="cod" TYPE=CHECKBOX DISABLED CHECKED><b> Nachnahme</b>';
								echo '</td><td>';
								echo '<input type="text" name="CODAmount" value="'.$intraship['ShipmentOrder']['Shipment']['ShipmentDetails']['Service']['ServiceGroupOther']['COD']['CODAmount'].'"/> EUR';
								echo '</td></tr>';
							}
						?>
						<tr class="main">
							<td>
								<?php
									//zusätzliche Services nur national auswählbar
									if(($productcode == 'EPN')/* && (!$order_data['intraship_shipmentnumber'])*/) {
										echo xtc_draw_checkbox_field('insurance', '', false). '<b> Zusatzversicherung</b>';
										echo '</td><td>';
										echo '2.500 EUR';
										echo '</td></tr>';
										echo '<tr class="main"><td>';
										echo xtc_draw_checkbox_field('bulkfreight', '', false). '<b> Sperrgut</b>';
									}
								?>
							</td>
						</tr>
						<tr class="main">
							<td colspan="3">
								<input type="checkbox" name="onlyifcodeable" value="1" id="onlyifcodeable" checked>
								<label for="onlyifcodeable"><strong>Label nur erzeugen, wenn Adresse leitcodierbar</strong></label>
							</td>
						</tr>
						<tr>
							<td></td>
						</tr>
						<tr class="main">
							<td colspan="4" align="right">
							</td>
						</tr>
						<tr class="main">
							<td colspan="2" align="left">
								<?php
									// Link zurück zur orders.php
									echo '<a class="button" href="'.xtc_href_link(FILENAME_ORDERS, 'oID='.$oID.'&action=edit').'">'.BUTTON_BACK.'</a>&nbsp;';
								?>
							</td>
							<td colspan="2" align="right">
								<?php
									//Storno nur wählbar wenn letzte Sendungsnummer von heute
									if(($order_data['intraship_shipmentnumber']) && ($lastshipmentdate == $shipmentdate)) {
										echo '<input class="button" type="submit" name="stornolabel" value="'.$gmintraship->get_text('BUTTON_STORNO').'"/>&nbsp';
									}
									//Label erstellen nur wählbar wenn heute noch keines erstellt und weniger als 5 vorhanden
									if(($lastshipmentdate!=$shipmentdate) && ($labelcount < 5)) {
										echo '<input class="button" type="submit" name="getlabel" value="'.$gmintraship->get_text('BUTTON_GETLABEL').'"/>&nbsp';
									}
								?>
								<?php if(!empty($label_url)): ?>
									<a class="button" href="<?php echo $label_url ?>">Label abrufen</a>&nbsp;
								<?php endif ?>
							</td>
						</tr>
					</table>
				</form>

				<?php if($labelInfo): ?>
					<div class="labelinfo breakpoint-small gx-container"><?= $labelInfo ?></div>
				<?php endif ?>
			</td>
		</tr>
	</table>
</body>
</html>
