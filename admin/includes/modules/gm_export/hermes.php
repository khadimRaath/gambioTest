<?php
/* --------------------------------------------------------------
  hermes.php 2014-01-08 gm
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

class hermes
{
	var $code, $title, $description, $enabled;

	function hermes()
	{
		global $order;

		$this->code = 'hermes';
		$this->title = MODULE_HERMES_TEXT_TITLE;
		$this->description = MODULE_HERMES_TEXT_DESCRIPTION;
		$this->sort_order = MODULE_HERMES_SORT_ORDER;
		$this->enabled = ((MODULE_HERMES_STATUS == 'True') ? true : false);
		$this->CAT = array();
		$this->PARENT = array();
	}

	function process($file)
	{
		@ xtc_set_time_limit(0);
		$orders_query_where = "WHERE c.countries_iso_code_2=o.delivery_country_iso_code_2 ";
		
		if($_POST['oders_status'] != null)
		{
			$orders_query_where .= "AND o.orders_status='" . (int)$_POST['oders_status'] . "'";
		}
		
		$schema = '';
		//$schema = 'Vorname(20);Nachname/Firmenname(25);Adresszusatz(20);Straße(27);Hausnummer(5);PLZ(5);Ort(25);Bezirk(25)*;Land(3);Tel. Vorwahl(6);Telefonnummer(8);E-Mail Adresse(80);Kundenreferenznummer(20); Paketklasse(3); Nachnahmebetrag(7); ' . "\n";
		
		$orders_query = "SELECT o.orders_id,
								o.customers_id,
								o.customers_telephone,
								o.customers_email_address,
								o.delivery_name,
								o.delivery_firstname,
								o.delivery_lastname,
								o.delivery_company,
								o.delivery_street_address,
								o.delivery_suburb,
								o.delivery_city,
								o.delivery_postcode,
								o.delivery_state,
								o.delivery_country,
								o.delivery_country_iso_code_2,
								c.countries_iso_code_3,
								o.payment_method,
								o.comments,
								o.date_purchased,
								o.orders_status,
								o.currency,
								o.shipping_class
							FROM 
								orders o, 
								countries c "
								. $orders_query_where;

		$customers_query = xtc_db_query($orders_query);
		
		while($customers = xtc_db_fetch_array($customers_query))
		{
			$paket = trim($_POST['gm_paket']);
			$order_value = " ";
			
			if($customers[payment_method] == 'cod')
			{
				$cod_query = "SELECT text from orders_total where orders_id='" . (int)$customers['orders_id'] . "' and class='ot_cod_fee'";
				$cod_query = xtc_db_query($cod_query);
				$cod_array = xtc_db_fetch_array($cod_query);
				$order_value_query = "SELECT value FROM orders_total WHERE orders_id='" . (int)$customers['orders_id'] . "' AND class='ot_total'";
				$order_value_query = xtc_db_query($order_value_query);
				$order_value_array = xtc_db_fetch_array($order_value_query);
				$order_value = number_format((double)$order_value_array['value'], 2);
				$order_value = str_replace('.', ',', $order_value);
			}
			
			$comment = preg_replace("/\s+/", " ", $customers['comments']);
			$cod_value = str_replace(" " . $customers['currency'], '', $cod_array['text']);

			$delivery_firstname = $customers['delivery_firstname'];
			if(empty($delivery_firstname))
			{
				$delivery_firstname = ' ';
			}

			$delivery_lastname = $customers['delivery_lastname'];
			if(empty($delivery_lastname))
			{
				$delivery_lastname = ' ';
			}

			$delivery_street_address = $customers['delivery_street_address'];
			if(empty($delivery_street_address))
			{
				$delivery_street_address = ' ';
			}

			$delivery_postcode = $customers['delivery_postcode'];
			if(empty($delivery_postcode))
			{
				$delivery_postcode = ' ';
			}

			$delivery_city = $customers['delivery_city'];
			if(empty($delivery_city))
			{
				$delivery_city = ' ';
			}

			$delivery_suburb = $customers['delivery_suburb'];
			if(empty($delivery_suburb))
			{
				$delivery_suburb = ' ';
			}

			$countries_iso_code_3 = $customers['countries_iso_code_3'];
			if(empty($countries_iso_code_3))
			{
				$countries_iso_code_3 = ' ';
			}

			$customers_email_address = $customers['customers_email_address'];
			if(empty($customers_email_address))
			{
				$customers_email_address = ' ';
			}

			$customers_id = $customers['customers_id'];
			if(empty($customers_id))
			{
				$customers_id = ' ';
			}

			if(empty($paket))
			{
				$paket = ' ';
			}

			if(empty($order_value))
			{
				$order_value = ' ';
			}

			$schema_entry = $delivery_firstname . ";" .
							$delivery_lastname . ";" .
							$delivery_suburb . ";" .
							$delivery_street_address . ";" .
							' ' . ";" .
							$delivery_postcode . ";" .
							$delivery_city . ";" .
							' ' . ";" .
							$countries_iso_code_3 . ";" .
							' ' . ";" .
							' ' . ";" .
							$customers_email_address . ";" .
							$customers_id . ";" .
							$paket . ";" .
							$order_value . ";" .
							' ' . ";" . "\n";

			$schema .= $schema_entry;
		}
		
		if(empty($schema))
		{
			$schema = ' ';
		}
		
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
					$query = "UPDATE orders SET orders_status ='" . (int)$_POST['oders_status_new'] . "' WHERE orders_status = '" . (int)$_POST['oders_status'] . "'";
					xtc_db_query($query);
				}
				exit;

				break;
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
								'<span class="options-title">' . GM_PAKET . '</span><br><input type="text" name="gm_paket" value="S" size="4" /><br>' .
								'<span class="options-title">' . EXPORT_TYPE . '</span><br>' .
								EXPORT . '<br>' .
		                        xtc_draw_pull_down_menu('export', $export_values_array, 'yes'));
	}

	function check()
	{
		if(!isset($this->_check))
		{
			$check_query = xtc_db_query("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_HERMES_STATUS'");
			$this->_check = xtc_db_num_rows($check_query);
		}
		
		return $this->_check;
	}

	function install()
	{
		xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value,  configuration_group_id, sort_order, set_function, date_added) values ('MODULE_HERMES_FILE', 'hermes.txt',  '6', '1', '', now())");
		xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value,  configuration_group_id, sort_order, set_function, date_added) values ('MODULE_HERMES_STATUS', 'True',  '6', '1', 'gm_cfg_select_option(array(\'True\', \'False\'), ', now())");
	}

	function remove()
	{
		xtc_db_query("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "')");
	}

	function keys()
	{
		return array('MODULE_HERMES_STATUS',
						'MODULE_HERMES_FILE');
	}
}