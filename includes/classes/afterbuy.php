<?php
/* --------------------------------------------------------------
   afterbuy.php 2015-09-02
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------


   based on:
   (c) 2000-2001 The Exchange Project (earlier name of osCommerce)
   (c) 2002-2003 osCommerce (account.php,v 1.59 2003/05/19); www.oscommerce.com
   (c) 2003      nextcommerce (account.php,v 1.12 2003/08/17); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: account.php 1124 2005-07-28 08:50:04Z mz $)

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/
/* -----------------------------------------------------------------------------------------
 $Id: afterbuy.php 1287 2005-10-07 10:41:03Z mz $

 modified by F.T.Store (FTS) 2007-08-156 20:07 FTS
 Version 1.8 (August 2007)

 mickser
 Modifikation:
 2008 	Bei vorhandener Attribut-Artikelnummer diese für die Übertragung verwenden
 2009 	urlencode statt ereg_replace
 		Zahlungsstatus iPayment
		Auswertung Afterbuy-Daten (UID,AID etc.) und eintragen in DB
 2010   getCurrency und getCustomerstatustax ausgelagert (unnötige mehrfach-DB-Anfragen)
 XT-Commerce - community made shopping
 http://www.xt-commerce.com

 Copyright (c) 2003 XT-Commerce
 -----------------------------------------------------------------------------------------
 based on:
 (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
 (c) 2002-2003 osCommerce(Coding Standards); www.oscommerce.com

 Released under the GNU General Public License
 ---------------------------------------------------------------------------------------*/

class xtc_afterbuy_functions_ORIGIN
{
	var $order_id;
	var $payment_id;
	var $payment_name;
	var $paid;


	// constructor
	function xtc_afterbuy_functions_ORIGIN($order_id)
	{
		$this->order_id = $order_id;
	}


	function process_order()
	{
		require_once(DIR_FS_INC . 'xtc_get_attributes_model.inc.php');
		require_once("xmlparserv4.php");
		$this->paid = 0;

		// ############ SETTINGS ################
		//Daten im XT Admin (werden von Afterbuy mitgeteilt)
		$PartnerID    = AFTERBUY_PARTNERID;
		$PartnerPass  = AFTERBUY_PARTNERPASS;
		$UserID       = AFTERBUY_USERID;
		$order_status = AFTERBUY_ORDERSTATUS;

		// ############ THUNK ################

		$oID          = $this->order_id;
		$customer     = array();
		$afterbuy_URL = 'https://api.afterbuy.de/afterbuy/ShopInterfaceUTF8.aspx';

		//***************************************************************************************************************************************//
		//settings:
		$verwende_shop_artikelnummer = 0;
		// 0 = Artikelnummer
		// 1 = interne products_id (DB-ID)
		// 2 = Afterbuy Produkt-ID (wenn vorhanden, in älteren AfterbuyImportSchnittstellenversionen nicht verwenden)

		$paypalexpress = 0;
		$moneybookers  = 0;

		$feedbackdatum = '0';
		//0= Feedbackdatum setzen und KEINE automatische Erstkontaktmail versenden
		//1= KEIN Feedbackdatum setzen, aber automatische Erstkontaktmail versenden (Achtung: Kunde müsste Feedback durchlaufen wenn die Erstkontakt nicht angepasst wird!)
		//2= Feedbackdatum setzen und automatische Erstkontaktmail versenden (Achtung: Erstkontaktmail muss mit Variablen angepasst werden!)

		$versandermittlung_ab = 1;
		// 1 = Versand aus XT
		// 0 = Versandermittlung durch Afterbuy (nur wennStammartikel erkannt wird!)

		$kundenerkennung = '1';
		// 0=Standard EbayName (= gesamte Zeile "Benutzername" in dieser Datei)
		// 1=Email
		// 2=EKNummer (wenn im XT vorhanden!)

		// ############# ARTIKELERKENNUNG SETZEN #############
		// modified FT
		$Artikelerkennung = '1';
		// 0 = Product ID (p_Model XT muss gleich Product ID Afterbuy sein)
		// 1 = Artikelnummer (p_Model XT muss gleich Arrikelnummer Afterbuy sein)
		// 2 = EAN (p_Model XT muss gleich EAN Afterbuy sein)
		// sollen keine Stammartikel erkannt werden, muss die Zeile: $DATAstring .= "Artikelerkennung=" . $Artikelerkennung ."&";  gelöscht werden
		// sollen keine Stammartikel erkannt werden, muss die Zeile: $Artikelerkennung = '1';  gelöscht werden

		//***************************************************************************************************************************************//

		// connect
		$ch = curl_init();

		// This is the URL that you want PHP to fetch. You can also set this option when initializing a session with the curl_init()  function.
		curl_setopt($ch, CURLOPT_URL, "$afterbuy_URL");

		// curl_setopt($ch, CURLOPT_CAFILE, 'D:/curl-ca.crt');
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		//bei einer leeren Transmission Error Mail + cURL Problemen die nächste Zeile auskommentieren
		//curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,0);

		// Set this option to a non-zero value if you want PHP to do a regular HTTP POST. This POST is a normal application/x-www-form-urlencoded  kind, most commonly used by HTML forms.
		curl_setopt($ch, CURLOPT_POST, 1);

		// get order data
		$o_query = xtc_db_query("SELECT * FROM " . TABLE_ORDERS . " WHERE orders_id='" . $oID . "'");
		$oData   = xtc_db_fetch_array($o_query);

		// ############CUSTOMERS ADRESS################
		// modified FT (Neuer Parameter Übergabe der 2.Adresszeile)

		$customer['id']       = $oData['customers_id'];
		$customer['firma']    = urlencode($oData['billing_company']);
		$customer['vorname']  = urlencode($oData['billing_firstname']);
		$customer['nachname'] = urlencode($oData['billing_lastname']);
		$customer['strasse']  = urlencode($oData['billing_street_address']);
		$customer['strasse2'] = urlencode($oData['billing_suburb']);
		$customer['plz']      = $oData['billing_postcode'];
		$customer['ort']      = urlencode($oData['billing_city']);
		$customer['tel']      = $oData['customers_telephone'];
		$customer['fax']      = "";
		$customer['mail']     = $oData['customers_email_address'];
		// get ISO code
		$ctr_query        = xtc_db_query("SELECT countries_iso_code_2 FROM " . TABLE_COUNTRIES .
										 " WHERE  countries_name='" . $oData['customers_country'] . "'");
		$crt_data         = xtc_db_fetch_array($ctr_query);
		$customer['land'] = $crt_data['countries_iso_code_2'];

		// ############ VAT_ID ################

		$ustid_querystrg   =
			"SELECT customers_vat_id, customers_status FROM " . TABLE_CUSTOMERS . " WHERE customers_id ='" .
			$customer['id'] . "'";
		$ustid_query       = xtc_db_query($ustid_querystrg);
		$ustid_data        = xtc_db_fetch_array($ustid_query);
		$customer['ustid'] = $ustid_data['customers_vat_id'];

		// ############ CUSTOMERS ANREDE ################

		$c_query = xtc_db_query("SELECT customers_gender FROM " . TABLE_CUSTOMERS . " WHERE customers_id='" .
								$customer['id'] . "'");
		$c_data  = xtc_db_fetch_array($c_query);
		switch($c_data['customers_gender'])
		{
			case 'm' :
				$customer['gender'] = 'Herr';
				break;
			case 'f' :
				$customer['gender'] = 'Frau';
				break;
			default :
				$customer['gender'] = '';
				break;
		}

		// ############ DELIVERY ADRESS ################
		// modified FT (Neuer Parameter Übergabe der 2.Adresszeile)

		$customer['d_firma']    = urlencode($oData['delivery_company']);
		$customer['d_vorname']  = urlencode($oData['delivery_firstname']);
		$customer['d_nachname'] = urlencode($oData['delivery_lastname']);
		$customer['d_strasse']  = urlencode($oData['delivery_street_address']);
		$customer['d_strasse2'] = urlencode($oData['delivery_suburb']);
		$customer['d_plz']      = $oData['delivery_postcode'];
		$customer['d_ort']      = urlencode($oData['delivery_city']);
		// get ISO code
		$ctr_query          = xtc_db_query("SELECT countries_iso_code_2 FROM " . TABLE_COUNTRIES .
										   " WHERE  countries_name='" . $oData['delivery_country'] . "'");
		$crt_data           = xtc_db_fetch_array($ctr_query);
		$customer['d_land'] = $crt_data['countries_iso_code_2'];

		// ############# KUNDENERKENNUNG SETZEN #############
		// Modifiziert FT

		$DATAstring = "Kundenerkennung=" . $kundenerkennung . "&";

		// ############ GET PRODUCT RELATED TO ORDER / INIT GET STRING ################
		// modified FT (Leerzeichen)

		$p_query = xtc_db_query("SELECT * FROM " . TABLE_ORDERS_PRODUCTS . " WHERE orders_id='" . $oID . "'");
		$p_count = xtc_db_num_rows($p_query);
		$DATAstring .= "Action=new&";
		$DATAstring .= "PartnerID=" . $PartnerID . "&";
		$DATAstring .= "PartnerPass=" . $PartnerPass . "&";
		$DATAstring .= "UserID=" . $UserID . "&";
		$DATAstring .= "Kbenutzername=" . $customer['id'] . "_XTC_" . $oID . "&";
		#oder
		#$DATAstring .= "Kbenutzername=".$customer['mail']."_XTC_".$oID."&";
		$DATAstring .= "Kanrede=" . $customer['gender'] . "&";
		$DATAstring .= "KFirma=" . $customer['firma'] . "&";
		$DATAstring .= "KVorname=" . $customer['vorname'] . "&";
		$DATAstring .= "KNachname=" . $customer['nachname'] . "&";
		$DATAstring .= "KStrasse=" . $customer['strasse'] . "&";
		$DATAstring .= "KStrasse2=" . $customer['strasse2'] . "&";
		$DATAstring .= "KPLZ=" . $customer['plz'] . "&";
		$DATAstring .= "KOrt=" . $customer['ort'] . "&";
		$DATAstring .= "KTelefon=" . $customer['tel'] . "&";
		$DATAstring .= "Kfax=&";
		$DATAstring .= "Kemail=" . $customer['mail'] . "&";
		$DATAstring .= "KLand=" . $customer['land'] . "&";


		// ############# LIEFERANSCHRIFT SETZEN #############
		// Modifiziert FT (Neuer Parameter Übergabe der 2.Adresszeile)
		// hier wird die Rechnungs-und Lieferanschrift verglichen, wenn die Adressen gleich sind, wird kein "L" in der Übersicht gesetzt
		// soll generell ein "L" in der Übersicht gesetzt werden, müssen die $DATAStrings "Lieferanschrift=1&" sein

		if(($customer['firma'] == $customer['d_firma']) && ($customer['vorname'] == $customer['d_vorname']) &&
		   ($customer['nachname'] == $customer['d_nachname']) && ($customer['strasse'] == $customer['d_strasse']) &&
		   ($customer['strasse2'] == $customer['d_strasse2']) && ($customer['plz'] == $customer['d_plz']) &&
		   ($customer['ort'] == $customer['d_ort'])
		)
		{
			$DATAstring .= "Lieferanschrift=0&";
		}
		else
		{
			$DATAstring .= "Lieferanschrift=1&";
			$DATAstring .= "KLFirma=" . $customer['d_firma'] . "&";
			$DATAstring .= "KLVorname=" . $customer['d_vorname'] . "&";
			$DATAstring .= "KLNachname=" . $customer['d_nachname'] . "&";
			$DATAstring .= "KLStrasse=" . $customer['d_strasse'] . "&";
			$DATAstring .= "KLStrasse2=" . $customer['d_strasse2'] . "&";
			$DATAstring .= "KLPLZ=" . $customer['d_plz'] . "&";
			$DATAstring .= "KLOrt=" . $customer['d_ort'] . "&";
			$DATAstring .= "KLLand=" . $customer['d_land'] . "&";
		}

		$DATAstring .= "UsStID=" . $customer['ustid'] . "&";
		$DATAstring .= "VID=" . $oID . "&";

		// ############# HÄNDLERMARKIERUNG AFTERBUY KUNDENDATENSATZ #############
		// Modifiziert FT
		// "H" Kennzeichnung im Kundendatensatz in Afterbuy
		// "Haendler=0&" bedeutet Checkbox deaktiviert
		// "Haendler=1&" bedeutet Checkbox aktiviert
		// "case 'X'" steht für die jeweilige Kundengruppen_ID im XT (-->siehe Admin)

		$customer_status = $ustid_data['customers_status'];
		switch($customer_status)
		{
			case '0': //Admin
				$DATAstring .= "Haendler=0&";
				break;
			case '1': //Gast
				$DATAstring .= "Haendler=0&";
				break;
			case '2': //Kunde
				$DATAstring .= "Haendler=0&";
				break;
			case '3': //im Standard B2B
				$DATAstring .= "Haendler=1&";
				break;
			case '4': //eigene Kundengruppe
				$DATAstring .= "Haendler=0&";
				break;
			case '5': //eigene Kundengruppe
				$DATAstring .= "Haendler=0&";
				break;
			case '6': //eigene Kundengruppe
				$DATAstring .= "Haendler=0&";
				break;
			case '7': //eigene Kundengruppe
				$DATAstring .= "Haendler=0&";
				break;
			default: //wenn alles nicht zutrifft
				$DATAstring .= "Haendler=0&";
		}

		$xt_currency = $this->getCurrency($oData['currency']);

		// ############# PRODUCTS_DATA TEIL1 #############
		// modified FT
		$DATAstring .= "Artikelerkennung=" . $Artikelerkennung . "&";
		$nr     = 0;
		$anzahl = 0;
		while($pDATA = xtc_db_fetch_array($p_query))
		{
			$nr++;

			if($verwende_shop_artikelnummer == 1)
			{
				$artnr = $pDATA['products_id'];
				if($artnr == '')
				{
					$artnr = "99999";
				}
			}
			elseif($verwende_shop_artikelnummer == 2)
			{
				$select_ab_products_id = xtc_db_query("SELECT ab_productsid FROM products WHERE products_id = '" .
													  $pDATA['products_id'] . "'");
				$ab_products_id        = xtc_db_fetch_array($select_ab_products_id);
				$artnr                 = $ab_products_id['ab_productsid'];

			}
			else
			{
				$artnr = $pDATA['products_model'];
			}


			$a_query = xtc_db_query("SELECT * FROM " . TABLE_ORDERS_PRODUCTS_ATTRIBUTES . " WHERE orders_id='" . $oID .
									"' AND orders_products_id='" . $pDATA['orders_products_id'] . "'");
			while($aDATA = xtc_db_fetch_array($a_query))
			{
				if($verwende_shop_artikelnummer == 1)
				{
					$attribute_model = $this->xtc_get_attributes_products_attributes_id($pDATA['products_id'],
																						$aDATA['products_options_values'],
																						$aDATA['products_options']);
					if((int)$attribute_model > 0)
					{
						$artnr = $attribute_model;
					}
				}
				elseif($verwende_shop_artikelnummer == 2)
				{
					$attribute_model = $this->xtc_get_attributes_ab_productsid($pDATA['products_id'],
																			   $aDATA['products_options_values'],
																			   $aDATA['products_options']);
					if((int)$attribute_model > 0)
					{
						$artnr = $attribute_model;
					}
				}
				else
				{
					$attribute_model = xtc_get_attributes_model($pDATA['products_id'],
																$aDATA['products_options_values'],
																$aDATA['products_options']);
					if((int)$attribute_model > 0)
					{
						$artnr = $attribute_model;
					}
				}
			}

			$artnr = preg_replace('/[A-Za-z_\..-]*/', '', $artnr);
			if($artnr == '')
			{
				$artnr = $pDATA['products_id'];
			}

			//$pean_query = xtc_db_query("SELECT * FROM ".TABLE_PRODUCTS." WHERE products_id='".$pDATA['orders_products_id']."' LIMIT 1");
			/*while ($pean = xtc_db_fetch_array($pean_query))
			{
				$attribute_model = xtc_get_attributes_model($pDATA['products_id'], $aDATA['products_options_values'], $aDATA['products_options']);
				if ((int)$attribute_model >0)
				$artnr = $attribute_model;

			}*/
			$DATAstring .= "Artikelnr_" . $nr . "=" . $artnr . "&";
			$DATAstring .= "ArtikelStammID_" . $nr . "=" . $artnr . "&";
			$DATAstring .= "Artikelname_" . $nr . "=" . urlencode($pDATA['products_name']) . "&";

			// ############# PREISÜBERGABE BRUTTO/NETTO NACH KUNDENGRUPPE #############
			// Kundengruppen müssen jeweilige Zuordnung inkl/excl. Anzeige im Admin XT haben

			$price    = $pDATA['products_price'];
			$tax_rate = $pDATA['products_tax'];
			if($pDATA['allow_tax'] == 0)
			{
				$cQuery = xtc_db_query("SELECT customers_status_add_tax_ot FROM " . TABLE_CUSTOMERS_STATUS .
									   " WHERE customers_status_id='" . $oData['customers_status'] . "' LIMIT 0,1");
				$cData  = xtc_db_fetch_array($cQuery);
				if($cData['customers_status_add_tax_ot'] == 0)
				{
					$tax_rate = 0;
				}
				else
				{
					$price += $price / 100 * $tax_rate;
				}
			}
			//Währungsprüfung

			$price = $price * $xt_currency;
			//Währungsprüfung END
			$price = $this->change_dec_separator($price);
			$tax   = $this->change_dec_separator($tax_rate);

			// ############# PRODUCTS_DATA TEIL2 #############

			$DATAstring .= "ArtikelEPreis_" . $nr . "=" . $price . "&";
			$DATAstring .= "ArtikelMwst_" . $nr . "=" . $tax . "&";
			//$DATAstring .= "ArtikelMenge_".$nr."=". ereg_replace("\.", ",", $pDATA['products_quantity'])."&";
			$DATAstring .= "ArtikelMenge_" . $nr . "=" . gm_prepare_number($pDATA['products_quantity']) . "&";
			$url = HTTP_SERVER . DIR_WS_CATALOG . 'product_info.php?products_id=' . $pDATA['products_id'];
			$DATAstring .= "ArtikelLink_" . $nr . "=" . $url . "&";
			//Attributübergabe
			$a_query = xtc_db_query("SELECT * FROM " . TABLE_ORDERS_PRODUCTS_ATTRIBUTES . " WHERE orders_id='" . $oID .
									"' AND orders_products_id='" . $pDATA['orders_products_id'] . "'");
			$options = '';
			while($aDATA = xtc_db_fetch_array($a_query))
			{
				if($options == '')
				{
					$options = $aDATA['products_options'] . ":" . $aDATA['products_options_values'];
				}
				else
				{
					$options .= "|" . $aDATA['products_options'] . ":" . $aDATA['products_options_values'];
				}
			}
			if($options != "")
			{
				$DATAstring .= "Attribute_" . $nr . "=" . $options . "&";
			}
			$anzahl += (int)$pDATA['products_quantity'];

		}
		// ############# ORDER_TOTAL #############

		$order_total_query = xtc_db_query("SELECT
						                      class,
						                      value,
						                      sort_order
						                      FROM " . TABLE_ORDERS_TOTAL . "
						                      WHERE orders_id='" . $oID . "'
						                      ORDER BY sort_order ASC");

		$order_total   = array();
		$zk            = '';
		$cod_fee       = '';
		$cod_flag      = false;
		$discount_flag = false;
		$gv_flag       = false;
		$coupon_flag   = false;
		$gv            = '';

		$customers_status_show_price_tax = $this->getCustomertaxstatus($oData['customers_status']);

		while($order_total_values = xtc_db_fetch_array($order_total_query))
		{

			$order_total[] = array('CLASS' => $order_total_values['class'], 'VALUE' => $order_total_values['value']);

			// ############# NACHNAHME/GUTSCHEINE/KUPONS/RABATTE #############
			if($order_total_values['class'] == 'ot_shipping')
			{
				$shipping = $order_total_values['value'];
			}

			// Nachnamegebuehr
			if($order_total_values['class'] == 'ot_cod_fee')
			{
				$cod_flag = true;
				$cod_fee  = $order_total_values['value'];
			}
			// Rabatt
			if($order_total_values['class'] == 'ot_discount')
			{
				$discount_flag = true;
				$discount      = $order_total_values['value'];
			}
			// Gutschein
			if($order_total_values['class'] == 'ot_gv')
			{
				$gv_flag = true;
				$gv      = $order_total_values['value'];
			}
			// Kupon
			if($order_total_values['class'] == 'ot_coupon')
			{
				$coupon_flag = true;
				$coupon      = $order_total_values['value'];
			}
			// ot_payment
			if($order_total_values['class'] == 'ot_payment')
			{
				$ot_payment_flag = true;
				$ot_payment      = $order_total_values['value'];
			}
			// Bonuspunkte
			if($order_total_values['class'] == 'ot_bonus_fee')
			{
				$bonus_flag = true;
				$bonus_fee  = $order_total_values['value'];
			}
		}

		// ############# ÜBERGABE NACHNAHME/GUTSCHEINE/KUPONS/RABATTE #############

		$xt_currency                     = $this->getCurrency($oData['currency']);
		$customers_status_show_price_tax = $this->getCustomertaxstatus($oData['customers_status']);

// Bonuspunkte Übergabe als Produkt
		if($bonus_flag)
		{
			$nr++;
			$DATAstring .= "Artikelnr_" . $nr . "=99999991&";
			$DATAstring .= "Artikelname_" . $nr . "=Bonuspunkte&";
			$bonus_fee = $this->get_ot_total_fee($customers_status_show_price_tax, $tax_rate, $xt_currency,
												 (-1) * $bonus_fee);

			$DATAstring .= "ArtikelEPreis_" . $nr . "=" . $bonus_fee . "&";
			$DATAstring .= "ArtikelMwst_" . $nr . "=" . $tax . "&";
			$DATAstring .= "ArtikelMenge_" . $nr . "=1&";
			$p_count++;
		}
		// Nachnamegebuehr Übergabe als Produkt
		if($cod_flag)
		{
			$nr++;
			$DATAstring .= "Artikelnr_" . $nr . "=99999999&";
			$DATAstring .= "Artikelname_" . $nr . "=Nachnahme&";

			$cod_fee = $this->get_ot_total_fee($customers_status_show_price_tax, $tax_rate, $xt_currency, $cod_fee);

			$DATAstring .= "ArtikelEPreis_" . $nr . "=" . $cod_fee . "&";
			$DATAstring .= "ArtikelMwst_" . $nr . "=" . $tax . "&";
			$DATAstring .= "ArtikelMenge_" . $nr . "=1&";
			$p_count++;
		}
		// Rabatt Übergabe als Produkt
		if($discount_flag)
		{
			$nr++;
			$DATAstring .= "Artikelnr_" . $nr . "=99999998&";
			$DATAstring .= "Artikelname_" . $nr . "=Rabatt&";

			$value_ot_total = $this->get_ot_total_fee($customers_status_show_price_tax, $tax_rate, $xt_currency,
													  $discount);

			$DATAstring .= "ArtikelEPreis_" . $nr . "=" . $value_ot_total . "&";
			$DATAstring .= "ArtikelMwst_" . $nr . "=" . $tax . "&";
			$DATAstring .= "ArtikelMenge_" . $nr . "=1&";
			$p_count++;
		}
		// Gutschein Übergabe als Produkt
		if($gv_flag)
		{
			$nr++;
			$DATAstring .= "Artikelnr_" . $nr . "=99999997&";
			$DATAstring .= "Artikelname_" . $nr . "=Gutschein&";
			$value_ot_total = $this->get_ot_total_fee($customers_status_show_price_tax, $tax_rate, $xt_currency,
													  (-1) * $gv);

			$DATAstring .= "ArtikelEPreis_" . $nr . "=" . $value_ot_total . "&";
			$DATAstring .= "ArtikelMwst_" . $nr . "=" . $tax . "&";
			$DATAstring .= "ArtikelMenge_" . $nr . "=1&";
			$p_count++;
		}
		// Kupon Übergabe als Produkt
		if($coupon_flag)
		{
			$nr++;
			$DATAstring .= "Artikelnr_" . $nr . "=99999996&";
			$DATAstring .= "Artikelname_" . $nr . "=Kupon&";

			$value_ot_total = $this->get_ot_total_fee($customers_status_show_price_tax, $tax_rate, $xt_currency,
													  (-1) * $coupon);

			$DATAstring .= "ArtikelEPreis_" . $nr . "=" . $value_ot_total . "&";
			$DATAstring .= "ArtikelMwst_" . $nr . "=" . $tax . "&";
			$DATAstring .= "ArtikelMenge_" . $nr . "=1&";
			$p_count++;
		}
		//ot_payment Übergabe als Produkt
		if($ot_payment_flag)
		{
			$nr++;
			$DATAstring .= "Artikelnr_" . $nr . "=99999995&";
			$DATAstring .= "Artikelname_" . $nr . "=Zahlartenrabatt&";
			$value_ot_total = $this->get_ot_total_fee($customers_status_show_price_tax, $tax_rate, $xt_currency,
													  $ot_payment);

			$DATAstring .= "ArtikelEPreis_" . $nr . "=" . $value_ot_total . "&";
			$DATAstring .= "ArtikelMwst_" . $nr . "=" . $tax . "&";
			$DATAstring .= "ArtikelMenge_" . $nr . "=1&";
			$p_count++;
		}

		$DATAstring .= "PosAnz=" . $p_count . "&";

		// ############# ÜBERGABE BRUTTO/NETTO VERSAND #############
		// mofified FT Kundengruppen müssen jeweilige Zuordnung inkl/excl. Anzeige im Admin XT haben
		if($order_total_values['class'] == 'ot_shipping')
		{
			$shipping = $order_total_values['value'];
		}
		if($pDATA['allow_tax'] == 0)
		{
			if($customers_status_show_price_tax == 1)
			{
				$tax_rate = 0;
			}
			else
			{
				$shipping = ((($shipping / 100) * $tax_rate) + $shipping);
			}

		}
		if((int)$xt_currency > 0)
		{
			$shipping = $shipping * $xt_currency;
		}
		//Währungsprüfung END

		$DATAstring .= "Versandkosten=" . $this->change_dec_separator($shipping) . "&";

		$s_method = explode('(', $oData['shipping_method']);
		$s_method = str_replace(' ', '%20', $s_method[0]);
		$DATAstring .= "kommentar=" . urlencode($oData['comments']) . "&";
		$DATAstring .= "Versandart=" . $s_method . "&";
		$DATAstring .= "NoVersandCalc=" . $versandermittlung_ab . "&";
		$DATAstring .= "VID=" . $oID . "&";


		//$DATAstring .= "ZahlartenAufschlag=". ereg_replace("\.", ",", $zahlartenaufschlag). "&";

		$this->getPayment($oData['payment_method']);
		$DATAstring .= "Zahlart=" . $this->payment_name . "&";
		$DATAstring .= "ZFunktionsID=" . $this->payment_id . "&";

		/*if ($oData['payment_method'] == 'paypal_gambio' OR $oData['payment_method'] == 'paypa_ipn') {
			$feedbackdatum = '2';
		}*/

		//Übergabe Bankdaten
		if($oData['payment_method'] == 'banktransfer')
		{

			if($_GET['oID'])
			{
				$b_query = xtc_db_query("SELECT * FROM banktransfer WHERE orders_id='" . (int)$_GET['oID'] . "'");
				$b_data  = xtc_db_fetch_array($b_query);
				$DATAstring .= "Bankname=" . urlencode($b_data['banktransfer_bankname']) . "&";
				$DATAstring .= "BLZ=" . $b_data['banktransfer_blz'] . "&";
				$DATAstring .= "Kontonummer=" . $b_data['banktransfer_number'] . "&";
				$DATAstring .= "Kontoinhaber=" . urlencode($b_data['banktransfer_owner']) . "&";
			}
			else
			{
				$DATAstring .= "Bankname=" . urlencode($_POST['banktransfer_bankname']) . "&";
				$DATAstring .= "BLZ=" . $_POST['banktransfer_blz'] . "&";
				$DATAstring .= "Kontonummer=" . $_POST['banktransfer_number'] . "&";
				$DATAstring .= "Kontoinhaber=" . urlencode($_POST['banktransfer_owner']) . "&";
			}
		}

		if($moneybookers == 1)
		{
			$sql      = "SELECT * FROM `payment_moneybookers` WHERE mb_ORDERID = '" . $oID . "' ORDER BY mb_DATE DESC";
			$mb_query = xtc_db_query($sql);
			if(count($mb_query))
			{
				$mb_data = xtc_db_fetch_array($mb_query);
				if($mb_data['mb_STATUS'] == '2')
				{
					$DATAstring .= "SetPay=1&";
				}
			}
		}
		//
		//$DATAstring .= "MarkierungID=9852&";
		//$DATAstring .= "Bestandart=auktion&"; //shop oder auktion
		$DATAstring .= "Bestandart=shop&";

		if($paypalexpress == 1)
		{
			$paypal_sql   =
				"SELECT * FROM " . TABLE_PAYPAL . " WHERE xtc_order_id ='" . $oID . "' ORDER BY payment_date DESC";
			$paypal_query = xtc_db_query($paypal_sql);
			if(count($paypal_query))
			{
				$paypal_data = xtc_db_fetch_array($paypal_query);
				if($paypal_data['payment_status'] == 'Completed')
				{
					$DATAstring .= "SetPay=1&";
				}
			}
		}

		if($this->paid == 1)
		{
			$DATAstring .= "SetPay=1&";
		}

		$DATAstring .= "NoFeedback=" . $feedbackdatum . "&";
		// #############  CHECK  #############
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $DATAstring);
		$result = curl_exec($ch);
		if(strpos($result, "<success>1</success>") !== false)
		{
			// result ok, mark order
			// extract ID from result
			$cdr = explode('<KundenNr>', $result);
			$cdr = explode('</KundenNr>', $cdr[1]);
			$cdr = $cdr[0];
			xtc_db_query("update " . TABLE_ORDERS . " set afterbuy_success='1',afterbuy_id='" . $cdr .
						 "' where orders_id='" . $oID . "'");
			$p                    = new XMLParser($result);
			$array_complete_parse = $p->getOutput();

			$array_results_parse = $array_complete_parse["result"];
			$ab_aid              = $array_results_parse["data"]["AID"];
			$ab_uid              = $array_results_parse["data"]["UID"];
			$ab_kundennr         = $array_results_parse["data"]["KundenNr"];
			$ab_ekundennr        = $array_results_parse["data"]["EKundenNr"];
			//wenn Kundenkommentar
			if($oData['comments'] != '')
			{
				$mail_content =
					"Name: " . $oData['billing_firstname'] . " " . $oData['billing_lastname'] . "\nEmailadresse: " .
					$oData['customers_email_address'] . "\nKundenkommentar: " . $oData['comments'] .
					"\nBestellnummer: " . $oID . chr(13) . chr(10) . "\n";
				mail(EMAIL_BILLING_ADDRESS, "Kundenkommentar bei Bestellung", $mail_content);
				//mail(EMAIL_BILLING_ADDRESS, "Kundenkommentar bei Bestellung", $mail_content);
			}
			//set new order status
			if($order_status != '')
			{
				xtc_db_query("update " . TABLE_ORDERS . " set orders_status='" . $order_status . "' where orders_id='" .
							 $oID . "'");
			}
		}
		else
		{

			// mail to shopowner
			$mail_content = 'Fehler bei Übertragung der Bestellung: ' . $oID . chr(13) . chr(10) .
							'Folgende Fehlermeldung wurde vom afterbuy.de zurückgegeben:' . chr(13) .
							chr(10) . $result;
			mail(EMAIL_BILLING_ADDRESS, "Afterbuy-Fehlübertragung", $mail_content);
			//mail("info@pimpmyxtc.de", "Afterbuy-Fehl&uuml;bertragung", $mail_content);
		}
		// close session
		curl_close($ch);
	}


	// Funktion zum ueberpruefen ob Bestellung bereits an Afterbuy gesendet.
	function order_send()
	{
		$check_query = xtc_db_query("SELECT afterbuy_success FROM " . TABLE_ORDERS . " WHERE orders_id='" .
									$this->order_id . "'");
		$data        = xtc_db_fetch_array($check_query);

		if($data['afterbuy_success'] == 1)
		{
			return false;
		}

		return true;
	}


	function getCurrency($o_currency)
	{
		//Währungsprüfung
		$curreny_query = xtc_db_query("SELECT * FROM " . TABLE_CURRENCIES . " WHERE code = '" . $o_currency .
									  "' LIMIT 1");
		while($currency_array = xtc_db_fetch_array($curreny_query))
		{
			$xt_currency = $currency_array['value'];
		}

		return $xt_currency;
	}


	function getCustomertaxstatus($customers_status)
	{
		//Steuerprüfung
		$cQuery = xtc_db_query("SELECT customers_status_show_price_tax FROM " . TABLE_CUSTOMERS_STATUS .
							   " WHERE customers_status_id='" . $customers_status . "' LIMIT 1");
		$cData  = xtc_db_fetch_array($cQuery);
		if($cData['customers_status_show_price_tax'] == 1)
		{
			$customers_status_show_price_tax = 1;
		}
		else
		{
			$customers_status_show_price_tax = 2;
		}

		return $customers_status_show_price_tax;
	}


	function getPayment($payment)
	{
		switch($payment)
		{
			case 'banktransfer':
				$this->payment_id   = '7';
				$this->payment_name = "Bankeinzug";
				break;
			case 'cash':
				$this->payment_id   = '2';
				$this->payment_name = "Barzahlung";
				break;
			case 'cod':
				$this->payment_id   = '4';
				$this->payment_name = "Nachnahme";
				break;
			case 'invoice':
				$this->payment_id   = '6';
				$this->payment_name = "Rechnung";
				break;
			case 'moneyorder':
			case 'eustandardtransfer':
				$this->payment_id   = '1';
				$this->payment_name = "Überweisung/Vorkasse";
				break;
			case 'moneybookers':
				$payment_name     = "Moneybookers";
				$this->payment_id = '15';
				break;
			case 'moneybookers_cc':
				$payment_name     = "Moneybookers CC";
				$this->payment_id = '15';
				break;
			case 'moneybookers_cgb':
				$payment_name     = "Moneybookers CGB";
				$this->payment_id = '15';
				break;
			case 'moneybookers_csi':
				$payment_name     = "Moneybookers CSI";
				$this->payment_id = '15';
				break;
			case 'moneybookers_elv':
				$payment_name     = "Moneybookers ELV";
				$this->payment_id = '15';
				break;
			case 'moneybookers_giropay':
				$payment_name     = "Moneybookers GIROPAY";
				$this->payment_id = '15';
				break;
			case 'moneybookers_ideal':
				$payment_name     = "Moneybookers IDEAL";
				$this->payment_id = '15';
				break;
			case 'moneybookers_mae':
				$payment_name     = "Moneybookers MAE";
				$this->payment_id = '15';
				break;
			case 'moneybookers_netpay':
				$payment_name     = "Moneybookers NETPAY";
				$this->payment_id = '15';
				break;
			case 'moneybookers_psp':
				$payment_name     = "Moneybookers PSP";
				$this->payment_id = '15';
				break;
			case 'moneybookers_pwy':
				$payment_name     = "Moneybookers PWY";
				$this->payment_id = '15';
				break;
			case 'moneybookers_sft':
				$payment_name     = "Moneybookers SFT";
				$this->payment_id = '15';
				break;
			case 'moneybookers_wlt':
				$payment_name     = "Moneybookers WLT";
				$this->payment_id = '15';
				break;
			case 'paypal':
			case 'paypalexpress':
			case 'paypal_gambio':
			case 'paypa_ipn':
			case 'paypalng':
			case 'paypal3':
			case 'paypalgambio_alt':
				$this->payment_id   = '5';
				$this->payment_name = "Paypal";
				break;

			case 'sofortueberweisung':
			case 'sofortueberweisungredirect':
			case 'sofortueberweisung_direct':
			case 'sofortueberweisungvorkasse':
			case 'sofort_sofortueberweisung':
				$this->payment_id   = '12';
				$this->payment_name = "Sofortüberweisung";
				break;
			case 'billsafe':
				$this->payment_id   = '18';
				$this->payment_name = "Billsafe";
				break;
			case 'ipayment':
				$this->payment_id   = '99';
				$this->payment_name = "IPayment";
				break;
			case 'cc':
				$this->payment_id   = '99';
				$this->payment_name = "Kreditkarte";
				break;
			case 'amazonadvpay':
				$this->payment_id = '99';
				$this->payment_name = 'Amazon';
				break;
			default:
				$this->payment_id   = '99';
				$this->payment_name = "sonstige Zahlungsweise";
		}
	}


	function xtc_get_attributes_ab_productsid($product_id, $attribute_name, $options_name, $language = '')
	{
		if($language == '')
		{
			$language = $_SESSION['languages_id'];
		}
		$options_value_id_query = xtc_db_query("SELECT
			pa.ab_productsid
			FROM
			" . TABLE_PRODUCTS_ATTRIBUTES . " pa
			INNER JOIN " . TABLE_PRODUCTS_OPTIONS . " po ON po.products_options_id = pa.options_id
			INNER JOIN " . TABLE_PRODUCTS_OPTIONS_VALUES . " pov ON pa.options_values_id = pov.products_options_values_id
			WHERE
			po.language_id = '" . $language . "' AND
			po.products_options_name = '" . $options_name . "' AND
			pov.language_id = '" . $language . "' AND
			pov.products_options_values_name = '" . $attribute_name . "' AND
			pa.products_id='" . $product_id . "'");


		$options_attr_data = xtc_db_fetch_array($options_value_id_query);

		return $options_attr_data['ab_productsid'];
	}


	function xtc_get_attributes_products_attributes_id($product_id, $attribute_name, $options_name, $language = '')
	{
		if($language == '')
		{
			$language = $_SESSION['languages_id'];
		}
		$options_value_id_query = xtc_db_query("SELECT
			pa.products_attributes_id
			FROM
			" . TABLE_PRODUCTS_ATTRIBUTES . " pa
			INNER JOIN " . TABLE_PRODUCTS_OPTIONS . " po ON po.products_options_id = pa.options_id
			INNER JOIN " . TABLE_PRODUCTS_OPTIONS_VALUES . " pov ON pa.options_values_id = pov.products_options_values_id
			WHERE
			po.language_id = '" . $language . "' AND
			po.products_options_name = '" . $options_name . "' AND
			pov.language_id = '" . $language . "' AND
			pov.products_options_values_name = '" . $attribute_name . "' AND
			pa.products_id='" . $product_id . "'");


		$options_attr_data = xtc_db_fetch_array($options_value_id_query);

		return $options_attr_data['products_attributes_id'];
	}


	function get_ot_total_fee($customers_status_show_price_tax, $tax_rate, $xt_currency, $fee)
	{
		//Übergabe Brutto/Netto
		if($pDATA['allow_tax'] == 0)
		{
			if($customers_status_show_price_tax == 1)
			{
				$tax_rate = 0;
			}
			else
			{
				$fee = ((($fee / 100) * $tax_rate) + $fee);
			}

		}

		//Währung berücksichtigen
		if((int)$xt_currency > 0)
		{
			$fee = $fee * $xt_currency;
		}

		return $this->change_dec_separator($fee);
	}


	function change_dec_separator($value)
	{
		return preg_replace("/\./", ",", $value);
	}
}