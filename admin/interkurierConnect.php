<?php
/*----------------------------------
Web-work24.de
Jahnstraße 18 
94249 Bodenmais
Telefon: 09924903768
email:info@web-work24.de
-------------------------------------*/
/* Interkurier Connect*/
$interkurier = xtDBquery("select * from orders_status where orders_status_id = '" . $status . "'");
$iName = mysqli_fetch_object($interkurier);
if (strcmp($iName->orders_status_name,'Expressversand freigegeben') == 0){
	
	$fp = fsockopen("www.interkurier.de", 80, $errno, $errstr, 30); 
	if (!$fp) {
   	 echo "$errstr ($errno)<br />\n";
	} else {
		$configuration_query = xtc_db_query('select configuration_key as cfgKey, configuration_value as cfgValue from '.TABLE_CONFIGURATION. ' where configuration_key like "%INTERKURIER%" order by cfgKey') ;
		$source = '';
		while ($configuration = xtc_db_fetch_array($configuration_query)) {
			$configuration['cfgKey']  == 'MODULE_SHIPPING_INTERKURIER_FETCH_CITY' ? $source .= '?fetch_city=' . str_replace(' ','__',$configuration['cfgValue']) : '';
			 $configuration['cfgKey']  == 'MODULE_SHIPPING_INTERKURIER_FETCH_COUNTRY' ? $source .= '&fetch_country=' . str_replace(' ','__',$configuration['cfgValue']) : '';
			 $configuration['cfgKey']  == 'MODULE_SHIPPING_INTERKURIER_FETCH_POSTCODE' ? $source .= '&fetch_postcode=' . str_replace(' ','__',$configuration['cfgValue']) : '';
			 
			 $configuration['cfgKey']  == 'MODULE_SHIPPING_INTERKURIER_FETCH_STREET' ? $source .= '&fetch_street=' . str_replace(' ','__',$configuration['cfgValue']) : '';
			 $configuration['cfgKey']  == 'MODULE_SHIPPING_INTERKURIER_FETCH_FIRM' ? $source .= '&fetch_firm=' . str_replace(' ','__',$configuration['cfgValue']) : '';
			 $configuration['cfgKey']  == 'MODULE_SHIPPING_INTERKURIER_FETCH_NAME' ? $source .= '&fetch_name=' . str_replace(' ','__',$configuration['cfgValue']) : '';
			 $configuration['cfgKey']  == 'MODULE_SHIPPING_INTERKURIER_FETCH_EMAIL' ? $source .= '&fetch_email=' . str_replace(' ','__',$configuration['cfgValue']) : '';
			 $configuration['cfgKey']  == 'MODULE_SHIPPING_INTERKURIER_FETCH_PHONE' ? $source .= '&fetch_phone=' . str_replace(' ','__',$configuration['cfgValue']) : '';
			 $configuration['cfgKey']  == 'MODULE_SHIPPING_INTERKURIER_NOTIFICATION' ? $source .= '&notification=' . $configuration['cfgValue'] : '';
			 $configuration['cfgKey']  == 'MODULE_SHIPPING_INTERKURIER_INVOICE_CITY' ? $source .= '&invoice_city=' . str_replace(' ','__',$configuration['cfgValue']) : '';
			 $configuration['cfgKey']  == 'MODULE_SHIPPING_INTERKURIER_INVOICE_COUNTRY' ? $source .= '&invoice_country=' . str_replace(' ','__',$configuration['cfgValue']) : '';
			 $configuration['cfgKey']  == 'MODULE_SHIPPING_INTERKURIER_INVOICE_POSTCODE' ? $source .= '&invoice_postcode=' . str_replace(' ','__',$configuration['cfgValue']) : '';
			 
			 $configuration['cfgKey']  == 'MODULE_SHIPPING_INTERKURIER_INVOICE_STREET' ? $source .= '&invoice_street=' . str_replace(' ','__',$configuration['cfgValue']) : '';
			 $configuration['cfgKey']  == 'MODULE_SHIPPING_INTERKURIER_INVOICE_FIRM' ? $source .= '&invoice_firm=' . str_replace(' ','__',$configuration['cfgValue']) : '';
			 $configuration['cfgKey']  == 'MODULE_SHIPPING_INTERKURIER_INVOICE_NAME' ? $source .= '&invoice_name=' . str_replace(' ','__',$configuration['cfgValue']) : '';
			 $configuration['cfgKey']  == 'MODULE_SHIPPING_INTERKURIER_INVOICE_EMAIL' ? $source .= '&invoice_email=' . str_replace(' ','__',$configuration['cfgValue']) : '';
			 $configuration['cfgKey']  == 'MODULE_SHIPPING_INTERKURIER_INVOICE_PHONE' ? $source .= '&invoice_phone=' . str_replace(' ','__',$configuration['cfgValue']) : '';
			 $configuration['cfgKey']  == 'MODULE_SHIPPING_INTERKURIER_INVOICE_NOTIFICATION' ? $source .= '&invoice_notification=' . $configuration['cfgValue'] : '';
		}

		$destination = '';
		$order_query = xtDBquery("select * from orders where orders_id = '" . $oID . "'");
		$o = mysqli_fetch_object($order_query);
		$order_products = xtDBquery("select * from orders_products where orders_id = '" . $oID . "'");
		$products = '';
		
		
		while($op = mysqli_fetch_object($order_products)){
			$weight_query = xtDBquery("select products_weight from products where products_id = '" . $op->products_id . "'");
			$weight = mysqli_fetch_object($weight_query);
			$products .= $op->products_quantity	. ':-:' . str_replace(' ','__',$op->products_name) . ':-:Karton:-:' .$op->products_price. ':-:' . $op->final_price .':-:' .  $weight->products_weight  . ';';
		}
		$order_total = xtDBquery("select * from orders_total where orders_id = '" . $oID . "' and class = 'ot_total'");
		$ot = mysqli_fetch_object($order_total);
		
		$destination .= '&shop=' .str_replace(' ','__',STORE_NAME) . '&owner=' . str_replace(' ','__',STORE_OWNER) . '&orders_id=' . $oID . '&delivery_firstname=' .  str_replace(' ','__',$o->delivery_firstname) . '&delivery_lastname=' . 
		str_replace(' ','__',$o->delivery_lastname).
						'&delivery_company=' .  str_replace(' ','__',$o->delivery_company) . '&delivery_street_address=' .	 str_replace(' ','__',$o->delivery_street_address) . '&delivery_suburb=' . 
						 str_replace(' ','__',$o->delivery_suburb) . '&delivery_city=' .str_replace(' ','__',$o->delivery_city).
						'&delivery_postcode=' .  str_replace(' ','__',$o->delivery_postcode) . '&delivery_country=' .  str_replace(' ','__',$o->delivery_country) . '&customers_telephone='.
						 str_replace(' ','__',$o->customers_telephone) . '&customers_email_address='. str_replace(' ','__',$o->customers_email_address).
						'&orders_total=' . $ot->value . '&orders_products=' . $products;
		
		$complete_transfer = $source . $destination;
		
		fputs($fp, "GET /verwaltung/insert.php". $complete_transfer ." GET / HTTP/1.1\r\nHost: www.interkurier.de\r\n\r\n"); 
		
		//while(!feof($fp))
			//echo fgets($fp, 4096); // Antwort lesen 
		fclose($fp);
	}
	//break;
}