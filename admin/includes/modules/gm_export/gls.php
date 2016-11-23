<?php
/* --------------------------------------------------------------
  gls.php 2014-01-08 gm
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2014 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------


  based on:
  (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
  (c) 2002-2003 osCommerce(cod.php,v 1.28 2003/02/14); www.oscommerce.com
  (c) 2003	 nextcommerce (invoice.php,v 1.6 2003/08/24); www.nextcommerce.org
  (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: froogle.php 1188 2005-08-28 14:24:34Z matthias $)

  Released under the GNU General Public License
  --------------------------------------------------------------------------------------- */
defined('_VALID_XTC') or die('Direct Access to this location is not allowed.');

class gls
{
	var $code, $title, $description, $enabled;

	function gls()
	{
		global $order;

		$this->code = 'gls';
		$this->title = MODULE_GLS_TEXT_TITLE;
		$this->description = MODULE_GLS_TEXT_DESCRIPTION;
		$this->sort_order = MODULE_GLS_SORT_ORDER;
		$this->enabled = ((MODULE_GLS_STATUS == 'True') ? true : false);
		$this->CAT = array();
		$this->PARENT = array();
	}

	function process($file)
	{
		@ xtc_set_time_limit(0);
		
		if($_POST['oders_status'] != null)
		{
			$orders_query_where = " WHERE orders_status='" . $_POST['oders_status'] . "'";
		}
		
		$schema = ' ';
		//$schema = 'Bestellnummer;Name;Versandart;?;Zahlungsart;Firma Rechnung;Anrede;Name;Strasse;Länderkürzel;PLZ;Ort;Telefon;?;Email;Kundennummer;?;Firma Lieferanschrift;Name;Strasse;PLZ;Ort;Länderkürzel;Bestellwert;?;Versandart;?;?;?;?;Land;Land;?;Paketgröße;?;?;?;?' . "\n";
		
		$orders_query = "SELECT orders_id,
								customers_id, 
								customers_telephone,
								customers_email_address,
								delivery_name,  		
								delivery_firstname, 
								delivery_lastname, 
								delivery_company, 
								delivery_street_address, 
								delivery_suburb, 
								delivery_city, 
								delivery_postcode, 
								delivery_state, 
								delivery_country, 
								delivery_country_iso_code_2, 
								payment_method, 
								shipping_method,
								shipping_class,		
								comments, 
								date_purchased, 
								orders_status, 
								currency, 
								shipping_class 
							FROM orders "
							. $orders_query_where;

		$customers_query = xtc_db_query($orders_query);
		while($customers = xtc_db_fetch_array($customers_query))
		{
			$paket = trim($_POST['gm_paket']);
			$order_value = ' ';
			
			if($customers[payment_method] == 'cod')
			{
				$cod_query = "SELECT text from orders_total where orders_id='" . $customers['orders_id'] . "' and class='ot_cod_fee'";
				$cod_query = xtc_db_query($cod_query);
				$cod_array = xtc_db_fetch_array($cod_query);
				$paket .= ', NN';
			}
			$order_value_query = "SELECT value FROM orders_total WHERE orders_id='" . $customers['orders_id'] . "' AND class='ot_total'";
			$order_value_query = xtc_db_query($order_value_query);
			$order_value_array = xtc_db_fetch_array($order_value_query);
			$order_value = round($order_value_array['value'], 2);
			$order_value = str_replace('.', ',', $order_value);

			$comment = preg_replace("/\s+/", " ", $customers['comments']);
			$cod_value = str_replace(" " . $customers['currency'], '', $cod_array['text']);
			$country_short = 'D';
			$country = 'DE';
			$schema_entry = $customers['orders_id'] . ";" .
							" " . ";" .
							$customers['shipping_class'] . ";" .
							" " . ";" .
							$customers['payment_method'] . ";" .
							" " . ";" .
							" " . ";" .
							$customers['delivery_name'] . ";" .
							$customers['delivery_street_address'] . " " . $customers['delivery_suburb'] . ";" .
							$country_short . ";" .
							$customers['delivery_postcode'] . ";" .
							$customers['delivery_city'] . ";" .
							$customers['customers_telephone'] . ";" .
							" " . ";" .
							$customers['customers_email_address'] . ";" .
							" " . ";" .
							" " . ";" .
							" " . ";" .
							$customers['delivery_name'] . ";" .
							$customers['delivery_street_address'] . " " . $customers['delivery_suburb'] . ";" .
							$customers['delivery_postcode'] . ";" .
							$customers['delivery_city'] . ";" .
							$country_short . ";" .
							$order_value . ";" .
							" " . ";" .
							$customers['shipping_class'] . ";" .
							" " . ";" .
							$customers['customers_id'] . ";" .
							" " . ";" .
							" " . ";" .
							$country . ";" .
							$country . ";" .
							" " . ";" .
							$paket . ";" .
							" " . ";" .
							" " . ";" .
							" " . ";" .
							" " . "\n";
			$schema .= $schema_entry;
		}

		if(empty($schema))
		{
			$schema = ' ';
		}

		if($file != null)
		{
			// create File
			$fp = fopen(DIR_FS_DOCUMENT_ROOT . 'export/' . $file, "w+");
			fputs($fp, $schema);
			fclose($fp);

			switch($_POST['export'])
			{
				case 'yes' :
					// send File to Browser
					$extension = substr($file, -3);
					$fp = fopen(DIR_FS_DOCUMENT_ROOT . 'export/' . $file, "rb");
					$buffer = fread($fp, filesize(DIR_FS_DOCUMENT_ROOT . 'export/' . $file));
					fclose($fp);
					header('Content-type: application/x-octet-stream');
					header('Content-disposition: attachment; filename=' . $file);
					echo $buffer;
					
					if($_POST['oders_status_new'] != null && $_POST['oders_status'] != null)
					{
						$query = "UPDATE orders SET orders_status ='" . $_POST['oders_status_new'] . "' WHERE orders_status = '" . $_POST['oders_status'] . "'";
						xtc_db_query($query);
					}
					exit;

					break;
			}
		}
		
		if($_POST['oders_status_new'] != null && $_POST['oders_status'] != null)
		{
			$query = "UPDATE orders SET orders_status = '" . (int)$_POST['oders_status_new'] . "' WHERE orders_status = '" . (int)$_POST['oders_status'] . "'";
			xtc_db_query($query);
		}
	}

	function display()
	{
		$customers_statuses_array = xtc_get_customers_statuses();

		// build Currency Select

		$orders_status_array = array(
			array(
				'id' => '',
				'text' => ALL_ORDER_STATUS
			)
		);
		
		$orders_status_query = xtc_db_query("SELECT 
													orders_status_name, 
													orders_status_id 
												FROM " . TABLE_ORDERS_STATUS . "
												WHERE language_id = '" . (int)$_SESSION['languages_id'] . "'
												ORDER BY orders_status_id");
		while($orders_status = xtc_db_fetch_array($orders_status_query))
		{
			$orders_status_array[] = array(
				'id' => $orders_status['orders_status_id'],
				'text' => $orders_status['orders_status_name'],
			);
		}
		
		$orders_status_new_array = $orders_status_array;
		$orders_status_new_array[0]['text'] = ORDER_STATUS_NO_CHANGE;

		$export_values_array = array(
			array(
				'id' => 'yes',
				'text' => EXPORT_YES
			),
			array(
				'id' => 'no',
				'text' => EXPORT_NO
			)
		);

		return array('text' => '<span class="options-title">' . ORDERS_STATUS . '</span><br>' .
		                       ORDERS_STATUS_DESC . '<br>' .
		                       xtc_draw_pull_down_menu('oders_status', $orders_status_array
		                       ) . '<br><br>' .
		                       ORDERS_STATUS_NEW_DESC . '<br>' .
		                       xtc_draw_pull_down_menu('oders_status_new', $orders_status_new_array) . '<br>' .
		                       '<span class="options-title">' . GM_PAKET . '</span><br><input type="text" name="gm_paket" value="NP" size="4" /><br>' .
		                       '<span class="options-title">' . EXPORT_TYPE . '</span><br>' .
		                       EXPORT . '<br>' .
		                       xtc_draw_pull_down_menu('export', $export_values_array, 'yes'));
	}

	function check()
	{
		if(!isset($this->_check))
		{
			$check_query = xtc_db_query("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_GLS_STATUS'");
			$this->_check = xtc_db_num_rows($check_query);
		}
		
		return $this->_check;
	}

	function install()
	{
		xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value,  configuration_group_id, sort_order, set_function, date_added) values ('MODULE_GLS_FILE', 'gls.txt',  '6', '1', '', now())");
		xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value,  configuration_group_id, sort_order, set_function, date_added) values ('MODULE_GLS_STATUS', 'True',  '6', '1', 'gm_cfg_select_option(array(\'True\', \'False\'), ', now())");
	}

	function remove()
	{
		xtc_db_query("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "')");
	}

	function keys()
	{
		return array(
			'MODULE_GLS_STATUS',
			'MODULE_GLS_FILE'
		);
	}
}
