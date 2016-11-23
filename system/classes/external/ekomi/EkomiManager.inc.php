<?php
/* --------------------------------------------------------------
   EkomiManager.inc.php 2014-11-19 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/


require_once(DIR_FS_CATALOG . 'gm/classes/lib/nusoap.php');
require_once(DIR_FS_INC . 'xtc_php_mail.inc.php');


class EkomiManager
{
	var $v_wsdl_url = 'http://api.ekomi.de/v2/wsdl';
	var $v_auth = '';
	var $v_version = 'cust-1.0.0';
	var $v_coo_soap_client;
	var $v_coo_soap_proxy;
	var $v_settings_array = array();
	var $v_api_id = 0;
	var $v_api_password = '';

	function EkomiManager($p_api_id, $p_api_password)
	{
		$this->set_api_id($p_api_id);
		$this->set_api_password($p_api_password);
		$this->set_auth();

		$this->v_coo_soap_client = new nusoap_client($this->v_wsdl_url, true);
		$this->v_coo_soap_proxy = $this->v_coo_soap_client->getProxy();

		# check connection
		if($this->v_coo_soap_client->getError() || !is_object($this->v_coo_soap_proxy))
		{
			$this->v_coo_soap_proxy = null;
			
			$coo_ekomi_log = LogControl::get_instance();
			$message = 'Connection to eKomi-API-Server could not be established.';
			$coo_ekomi_log->error($message, 'widgets', 'ekomi_errors', 'error', 'USER ERROR', 0, print_r($this->v_coo_soap_client->getError(), true));
		}
	}


	function set_api_id($p_api_id)
	{
		$this->v_api_id = (int)$p_api_id;

		return true;
	}


	function get_api_id()
	{
		return $this->v_api_id;
	}


	function set_api_password($p_api_password)
	{
		$c_api_password = trim((string)$p_api_password);
		if(strpos($c_api_password, '|') === false)
		{
			$this->v_api_password = $c_api_password;
			return true;
		}

		return false;
	}


	function get_api_password()
	{
		return $this->v_api_password;
	}


	function set_auth()
	{
		if($this->v_api_id > 0 && $this->v_api_password != '')
		{
			$this->v_auth = $this->v_api_id . '|' . $this->v_api_password;
			return true;
		}
		else
		{
			$coo_ekomi_log = LogControl::get_instance();
			$message = 'API-ID or API-Password is missing.';
			$coo_ekomi_log->error($message, 'widgets', 'ekomi_errors');
		}

		return false;
	}
	
	
	function get_order_id($p_coo_order)
	{
		return (int)$p_coo_order['orders_id'];
	}
	
	
	function get_order_timestamp($p_coo_order)
	{
		$order_date = $p_coo_order['coo_order']->info['date_purchased'];
		$order_date_array = explode(' ', $order_date);
		return $order_date_array[0];
	}


	function get_product_ids_array($p_coo_order)
	{
		$product_ids_array = array();

		$products_array = $p_coo_order['coo_order']->products;

		foreach($products_array as $product_information_array)
		{
			$product_ids_array[] = $product_information_array['id'];
		}

		return $product_ids_array;
	}

	
	function put_order($p_coo_order)
	{
		$t_success = false;
		$orders_id = $this->get_order_id($p_coo_order);
		$order_timestamp = $this->get_order_timestamp($p_coo_order);
		$products_ids_array = $this->get_product_ids_array($p_coo_order);

		if($this->v_coo_soap_proxy !== null && $orders_id > 0)
		{
			if($this->load_settings())
			{				
				$t_products_ids = '';
				if(check_data_type($products_ids_array, 'array') && !empty($products_ids_array))
				{
					$t_products_ids = implode(',', $products_ids_array);
				}

				$t_result = $this->v_coo_soap_proxy->putOrder($this->v_auth, $this->v_version, $orders_id, 
															  $t_products_ids, 'utf8', $order_timestamp);
				if(is_string($t_result))
				{
					$t_result_array = unserialize($t_result);

					if(check_data_type($t_result_array, 'array') && $t_result_array['done'] == 1)
					{
						$coo_ekomi_order_information = MainFactory::create_object('GMDataObject', array('ekomi_order_information', array('orders_id' => $orders_id)));
						$coo_ekomi_order_information->delete();

						$coo_ekomi_order_information = MainFactory::create_object('GMDataObject', array('ekomi_order_information'));
						$coo_ekomi_order_information->set_data_value('orders_id', $orders_id);
						$coo_ekomi_order_information->set_data_value('link', $t_result_array['link']);
						$coo_ekomi_order_information->set_data_value('hash', $t_result_array['hash']);
						$coo_ekomi_order_information->save_body_data();

						$t_success = true;
					}
				}
			}
			else
			{
				$coo_ekomi_log = LogControl::get_instance();
				$message = 'API-Interface data is invald. Order (no. ' . $orders_id . ') could not be sent to eKomi.';
				$coo_ekomi_log->error($message, 'widgets', 'ekomi_errors', 'error', 'USER ERROR', 0, print_r($t_result, true));
			}
		}

		return $t_success;
	}

    function isSerialized($str) {
        return ($str == serialize(false) || @unserialize($str) !== false);
    }


	function load_settings()
	{
		$t_success = false;

		if($this->v_coo_soap_proxy !== null)
		{
			$t_settings = $this->v_coo_soap_proxy->getSettings($this->v_auth, $this->v_version, '');
            if(is_string($t_settings))
            {
                if($this->isSerialized($t_settings) === false && $this->isSerialized(utf8_decode($t_settings)) === false)
				{
					$t_settings_array= array();
				}
                else
				{
					if($this->isSerialized($t_settings))
					{
						$t_settings_array = unserialize($t_settings);
					}
					else
					{
						$t_settings_array = unserialize(utf8_decode($t_settings));

						foreach($t_settings_array as $t_key => $t_value)
						{
							$t_settings_array[$t_key] = utf8_encode($t_value);
						}
					}
				}
				
                $t_success = $this->set_settings($t_settings_array);
            }
		}
		
		return $t_success;
	}


	function set_settings($p_settings_array)
	{
		if(check_data_type($p_settings_array, 'array'))
		{
			$this->v_settings_array = $p_settings_array;
			return true;
		}

		return false;
	}


	function get_settings()
	{
		return $this->v_settings_array;
	}


	function send_mails($p_orders_id = false, $p_ignore_delay = false)
	{
		$t_success = $this->load_settings();

		if($t_success)
		{
			$t_success = true;

			$t_orders_id_sql = '';
			if($p_orders_id !== false)
			{
				$t_orders_id_sql = " AND o.orders_id = '" . (int)$p_orders_id . "' ";
			}

			$t_delay_sql = " AND DATE_SUB(NOW(), INTERVAL " . (int)$this->v_settings_array['mail_delay'] . " DAY) > o.date_purchased ";
			if($p_ignore_delay === true)
			{
				$t_delay_sql = '';
			}

			$t_sql = "SELECT
							e.orders_id,
							e.link,
							o.customers_email_address AS email_address,
							o.customers_firstname AS firstname,
							o.customers_lastname AS lastname
						FROM 
							ekomi_order_information e,
							orders o
						WHERE 
							e.date_sent = '0000-00-00 00:00:00' AND
							e.orders_id = o.orders_id "
							. $t_delay_sql
							. $t_orders_id_sql;

			$t_result = xtc_db_query($t_sql);
			while($t_result_array = xtc_db_fetch_array($t_result))
			{
				$t_mail_html = $this->v_settings_array['mail_html'];
				$t_mail_html = str_replace('{vorname}', $t_result_array['firstname'], $t_mail_html);
				$t_mail_html = str_replace('{nachname}', $t_result_array['lastname'], $t_mail_html);
				$t_mail_html = str_replace('{ekomilink}', $t_result_array['link'], $t_mail_html);

				$t_mail_plain = $this->v_settings_array['mail_plain'];
				$t_mail_plain = str_replace('{vorname}', $t_result_array['firstname'], $t_mail_plain);
				$t_mail_plain = str_replace('{nachname}', $t_result_array['lastname'], $t_mail_plain);
				$t_mail_plain = str_replace('{ekomilink}', $t_result_array['link'], $t_mail_plain);

				$t_mail_sent = xtc_php_mail(	$this->v_settings_array['mail_from_email'],
											$this->v_settings_array['mail_from_name'],
											$t_result_array['email_address'],
											$t_result_array['firstname'] . ' ' . $t_result_array['lastname'],
											'',
											$this->v_settings_array['mail_from_email'],
											$this->v_settings_array['mail_from_name'],
											'',
											'',
											$this->v_settings_array['mail_subject'],
											$t_mail_html,
											$t_mail_plain);
			
				if($t_mail_sent)
				{
					$coo_ekomi_order_information = MainFactory::create_object('GMDataObject', array('ekomi_order_information', array('orders_id' => $t_result_array['orders_id'])));
					$coo_ekomi_order_information->set_data_value('date_sent', 'NOW()', true);
					$coo_ekomi_order_information->save_body_data();
				}
				elseif(class_exists('LogControl'))
				{
					$t_success = false;
					$coo_ekomi_log = LogControl::get_instance();
					$message = 'E-mail could not be sent to "' . $t_result_array['email_address'] . '" (' . $t_result_array['firstname'] . ' ' . $t_result_array['lastname'] . '), order number: ' . (int)$t_result_array['orders_id'];
					$coo_ekomi_log->error($message, 'widgets', 'ekomi_errors');
				}
			}
		}

		return $t_success;
	}


	function mail_already_sent($p_orders_id)
	{
		$t_mail_already_sent = false;

		$c_orders_id = (int)$p_orders_id;
		$t_sql = "SELECT orders_id
					FROM ekomi_order_information
					WHERE
						date_sent != '0000-00-00 00:00:00' AND
						orders_id = '" . $c_orders_id . "'";
		$t_result = xtc_db_query($t_sql);
		if(xtc_db_num_rows($t_result) == 1)
		{
			$t_mail_already_sent = true;
		}

		return $t_mail_already_sent;
	}

	
	// not used yet, W.I.P.
	function load_product_feedback($p_products_id = 'all')
	{
		$t_product_feedback = '';

		$coo_load_url = MainFactory::create_object('LoadUrl');
		$t_product_feedback = $coo_load_url->load_url('http://api.ekomi.de/get_productfeedback.php?interface_id=' . rawurlencode($this->get_api_id()) . '&interface_pw=' . rawurlencode($this->get_api_password()) . '&type=csv&product=' . rawurlencode($p_products_id) . '&charset=iso');

		$fp = fopen(DIR_FS_CATALOG . 'cache/ekomi_product_feedback_' . LogControl::get_secure_token() . '.csv', 'w');
		fputs($fp, $t_product_feedback);
		fclose($fp);

		$t_handle = fopen(DIR_FS_CATALOG . 'cache/ekomi_product_feedback_' . LogControl::get_secure_token() . '.csv', "r");
		while( ($t_data_array = fgetcsv($t_handle, 1000, ",", '"')) !== FALSE )
		{
			#print_r($t_data_array); ...
		}
		fclose ($t_handle);
	}
}
