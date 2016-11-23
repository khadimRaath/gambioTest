<?php
/* --------------------------------------------------------------
   WithdrawalModel.inc.php 2016-07-06
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
 */

class WithdrawalModel
{
	protected $withdrawal_id = 0;
	protected $order_id;
	protected $customer_id;
	protected $customer_gender;
	protected $customer_firstname;
	protected $customer_lastname;
	protected $customer_street_address;
	protected $customer_postcode;
	protected $customer_city;
	protected $customer_country;
	protected $customer_email;
	protected $order_date;
	protected $delivery_date;
	protected $withdrawal_date;
	protected $withdrawal_content;
	protected $date_created;
	protected $created_by_admin;
	
	public function __construct($withdrawal_id = null)
    {
		if(isset($withdrawal_id))
		{
			$this->set_withdrawal_id($withdrawal_id);
			$this->load();
		}
    }
	
	public function load($withdrawal_id = null)
	{
		if(isset($withdrawal_id))
		{
			$this->set_withdrawal_id($withdrawal_id);
		}
		
		if($this->withdrawal_id > 0)
		{
			$query = 'SELECT * FROM withdrawals WHERE withdrawal_id = "' . xtc_db_input($this->withdrawal_id) . '"';
			$result = xtc_db_query($query);
			if(xtc_db_num_rows($result) == 1)
			{
				$row = xtc_db_fetch_array($result);
				$this->set_withdrawal_id($row['withdrawal_id']);
				$this->set_order_id($row['order_id']);
				$this->set_customer_id($row['customer_id']);
				$this->set_customer_gender($row['customer_gender']);
				$this->set_customer_firstname($row['customer_firstname']);
				$this->set_customer_lastname($row['customer_lastname']);
				$this->set_customer_street_address($row['customer_street_address']);
				$this->set_customer_postcode($row['customer_postcode']);
				$this->set_customer_city($row['customer_city']);
				$this->set_customer_country($row['customer_country']);
				$this->set_customer_email($row['customer_email']);
				$this->set_order_date($row['order_date']);
				$this->set_delivery_date($row['delivery_date']);
				$this->set_withdrawal_date($row['withdrawal_date']);
				$this->set_order_id($row['order_id']);
				$this->set_withdrawal_content($row['withdrawal_content']);
				$this->set_date_created($row['date_created']);
				$this->set_created_by_admin((bool)(int)$row['created_by_admin']);
				return true;
			}
			else
			{
				$this->set_withdrawal_id(null);
			}
		}
		return false;
	}
	
	public function save()
	{
		$sql_data_array = array();
		$sql_data_array['order_id'] = $this->get_order_id();
		$sql_data_array['customer_id'] = $this->get_customer_id();
		$sql_data_array['customer_gender'] = xtc_db_prepare_input($this->get_customer_gender());
		$sql_data_array['customer_firstname'] = xtc_db_prepare_input($this->get_customer_firstname());
		$sql_data_array['customer_lastname'] = xtc_db_prepare_input($this->get_customer_lastname());
		$sql_data_array['customer_street_address'] = xtc_db_prepare_input($this->get_customer_street_address());
		$sql_data_array['customer_postcode'] = xtc_db_prepare_input($this->get_customer_postcode());
		$sql_data_array['customer_city'] = xtc_db_prepare_input($this->get_customer_city());
		$sql_data_array['customer_country'] = xtc_db_prepare_input($this->get_customer_country());
		$sql_data_array['customer_email'] = $this->get_customer_email();
		$sql_data_array['order_date'] = $this->get_order_date();
		$sql_data_array['delivery_date'] = $this->get_delivery_date();
		$sql_data_array['withdrawal_content'] = xtc_db_prepare_input($this->get_withdrawal_content());
		$sql_data_array['withdrawal_date'] = $this->get_withdrawal_date();
		$sql_data_array['created_by_admin'] = (int)$this->get_created_by_admin();
		
		if($this->get_withdrawal_id() != 0)
		{
			xtc_db_perform('withdrawals', $sql_data_array, 'update', "withdrawal_id = '". xtc_db_input($this->withdrawal_id) ."'");
			$t_withdrawal_id = $this->get_withdrawal_id();
		}
		else
		{
			$sql_data_array['date_created'] = 'now()';
			xtc_db_perform('withdrawals', $sql_data_array);
			$t_withdrawal_id = xtc_db_insert_id();
		}
		
		$this->set_withdrawal_id($t_withdrawal_id);
		return $this->get_withdrawal_id();
	}
	
	public function delete()
	{
		if($this->withdrawal_id > 0)
		{
			$query = 'DELETE FROM withdrawals WHERE withdrawal_id = "' . xtc_db_input($this->withdrawal_id) . '"';
			return xtc_db_query($query);
		}
		return false;
	}
	
	public function get_withdrawal_id()
	{
		return $this->withdrawal_id;
	}
	
	public function set_withdrawal_id($p_withdrawal_id)
	{
		if((int)$p_withdrawal_id <= 0 && is_null($p_withdrawal_id) == false)
		{
			trigger_error('Withdrawal-ID is not a number!', E_USER_ERROR);
		}
		$this->withdrawal_id = (int)$p_withdrawal_id;
	}
	
	public function get_order_id()
	{
		return $this->order_id;
	}
	
	public function set_order_id($p_order_id)
	{
		if((int)$p_order_id < 0)
		{
			trigger_error('Order-ID is not a number!', E_USER_ERROR);
		}
		$this->order_id = (int)$p_order_id;
	}
	
	public function get_customer_id()
	{
		return $this->customer_id;
	}
	
	public function set_customer_id($p_customer_id)
	{
		if((int)$p_customer_id < 0)
		{
			trigger_error('Customer-ID is not a number!', E_USER_ERROR);
		}
		$this->customer_id = (int)$p_customer_id;
	}
	
	public function get_customer_gender()
	{
		return $this->customer_gender;
	}
	
	public function set_customer_gender($p_customer_gender)
	{
		if(is_string($p_customer_gender) == false)
		{
			trigger_error('Customer gender is not a string!', E_USER_ERROR);
		}
		$this->customer_gender = $p_customer_gender;
	}
	
	public function get_customer_name()
	{
		return $this->customer_firstname . ' ' . $this->customer_lastname;
	}
	
	public function get_customer_firstname()
	{
		return $this->customer_firstname;
	}
	
	public function set_customer_firstname($p_customer_firstname)
	{
		if(is_string($p_customer_firstname) == false)
		{
			trigger_error('Customer firstname is not a string!', E_USER_ERROR);
		}
		$this->customer_firstname = $p_customer_firstname;
	}
	
	public function get_customer_lastname()
	{
		return $this->customer_lastname;
	}
	
	public function set_customer_lastname($p_customer_lastname)
	{
		if(is_string($p_customer_lastname) == false)
		{
			trigger_error('Customer lastname is not a string!', E_USER_ERROR);
		}
		$this->customer_lastname = $p_customer_lastname;
	}
	
	public function get_customer_street_address()
	{
		return $this->customer_street_address;
	}
	
	public function set_customer_street_address($p_customer_street_address)
	{
		if(is_string($p_customer_street_address) == false)
		{
			trigger_error('Customer street address is not a string!', E_USER_ERROR);
		}
		$this->customer_street_address = $p_customer_street_address;
	}
	
	public function get_customer_postcode()
	{
		return $this->customer_postcode;
	}
	
	public function set_customer_postcode($p_customer_postcode)
	{
		if(is_string($p_customer_postcode) == false)
		{
			trigger_error('Customer postcode is not a string!', E_USER_ERROR);
		}
		$this->customer_postcode = $p_customer_postcode;
	}
	
	public function get_customer_city()
	{
		return $this->customer_city;
	}
	
	public function set_customer_city($p_customer_city)
	{
		if(is_string($p_customer_city) == false)
		{
			trigger_error('Customer city is not a string!', E_USER_ERROR);
		}
		$this->customer_city = $p_customer_city;
	}
	
	public function get_customer_country()
	{
		return $this->customer_country;
	}
	
	public function set_customer_country($p_customer_country)
	{
		if(is_string($p_customer_country) == false)
		{
			trigger_error('Customer country is not a string!', E_USER_ERROR);
		}
		$this->customer_country = $p_customer_country;
	}
	
	public function get_customer_email()
	{
		return $this->customer_email;
	}
	
	public function set_customer_email($p_customer_email)
	{
		if(is_string($p_customer_email) == false)
		{
			trigger_error('Customer email is not a string!', E_USER_ERROR);
		}
		$this->customer_email = $p_customer_email;
	}
	
	public function get_order_date()
	{
		return $this->order_date;
	}
	
	public function get_order_date_formatted()
	{
		return date(DATE_FORMAT, strtotime($this->order_date));
	}
	
	public function set_order_date($p_order_date)
	{
		if(is_string($p_order_date) == false)
		{
			trigger_error('Date is not a string!', E_USER_ERROR);
		}

		if($p_order_date == '1000-01-01 00:00:00')
		{
			$this->order_date = date("Y-m-d 00:00:00", 0);
		}
		else
		{
			$this->order_date = date("Y-m-d 00:00:00", strtotime($p_order_date));
		}
	}
	
	public function get_delivery_date()
	{
		return $this->delivery_date;
	}
	
	public function get_delivery_date_formatted()
	{
		return date(DATE_FORMAT, strtotime($this->delivery_date));
	}

	public function set_delivery_date($p_delivery_date)
	{
		if(is_string($p_delivery_date) == false)
		{
			trigger_error('Date is not a string!', E_USER_ERROR);
		}

		if($p_delivery_date == '1000-01-01 00:00:00')
		{
			$this->delivery_date = date("Y-m-d 00:00:00", 0);
		}
		else
		{
			$this->delivery_date = date("Y-m-d 00:00:00", strtotime($p_delivery_date));
		}
	}
	
	public function get_withdrawal_date()
	{
		return $this->withdrawal_date;
	}
	
	public function get_withdrawal_date_formatted()
	{
		return date(DATE_FORMAT, strtotime($this->withdrawal_date));
	}
	
	public function set_withdrawal_date($p_withdrawal_date)
	{
		if(is_string($p_withdrawal_date) == false)
		{
			trigger_error('Date is not a string!', E_USER_ERROR);
		}
		
		if($p_withdrawal_date == '1000-01-01 00:00:00')
		{
			$this->withdrawal_date = date("Y-m-d 00:00:00", 0);
		}
		else
		{
			$this->withdrawal_date = date("Y-m-d 00:00:00", strtotime($p_withdrawal_date));
		}
	}
	
	public function get_withdrawal_content()
	{
		return $this->withdrawal_content;
	}
	
	public function get_withdrawal_content_html()
	{
		return nl2br($this->withdrawal_content);
	}
	
	public function set_withdrawal_content($p_withdrawal_content)
	{
		if(is_string($p_withdrawal_content) == false)
		{
			trigger_error('Withdrawal content is not a string!', E_USER_ERROR);
		}
		$this->withdrawal_content = $p_withdrawal_content;
	}
	
	public function get_date_created()
	{
		return $this->date_created;
	}
	
	public function get_date_created_formatted()
	{
		return date(DATE_FORMAT, strtotime($this->date_created));
	}
	
	public function set_date_created($p_date_created)
	{
		if(is_string($p_date_created) == false)
		{
			trigger_error('Date is not a string!', E_USER_ERROR);
		}

		if($p_date_created == '1000-01-01 00:00:00')
		{
			$this->date_created = date("Y-m-d 00:00:00", 0);
		}
		else
		{
			$this->date_created = date("Y-m-d 00:00:00", strtotime($p_date_created));
		}
	}
	
	public function get_created_by_admin()
	{
		return $this->created_by_admin;
	}
	
	public function set_created_by_admin($p_created_by_admin)
	{
		if(is_bool($p_created_by_admin) == false)
		{
			trigger_error('Created by admin is not a boolean!', E_USER_ERROR);
		}
		$this->created_by_admin = $p_created_by_admin;
	}
}
