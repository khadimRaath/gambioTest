<?php
/* --------------------------------------------------------------
  AddressModel.inc.php 2016-04-08
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2016 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  ---------------------------------------------------------------------------------------
*/

class AddressModel extends BaseClass
{
	protected $address_book_id;
	protected $customers_id;
	protected $entry_gender;
	protected $entry_firstname;
	protected $entry_lastname;
	protected $entry_company;
	protected $entry_street;
	protected $entry_house_number;
	protected $entry_street_address;
	protected $entry_additional_info;
	protected $entry_suburb;
	protected $entry_postcode;
	protected $entry_city;
	protected $entry_state;
	protected $entry_country_id;
	protected $entry_zone_id;
	protected $address_date_added;
	protected $address_last_modified;
	protected $address_class;
	protected $primary;
	protected $customer_b2b_status;
	
	public function __construct($p_address_book_id = false)
	{	
		// SET VALIDATION RULES
		$this->set_validation_rules();
		
		// LOAD ADDRESS
		if($p_address_book_id !== false)
		{
			$this->address_book_id = (int)$p_address_book_id;
			$this->load();
		}
	}
	
	public function load()
	{
		if($this->address_book_id != 0)
		{
			$t_query = 'SELECT
						*
					FROM
						address_book
					WHERE
						address_book_id = "' . $this->address_book_id . '"';
			$t_result = xtc_db_query($t_query);

			if(xtc_db_num_rows($t_result) == 1)
			{
				$t_address = xtc_db_fetch_array($t_result);
				foreach($t_address as $t_key => $t_value)
				{
					if(property_exists($this, $t_key) && is_null($t_value) == false)
					{
						$this->set_($t_key, $t_value);
					}
				}
			}
		}
	}
	
	public function save()
	{
		$t_sql_data_array = $this->get_sql_data_array();

		if(isset($this->address_book_id) == false || $this->address_book_id == 0)
		{
			$t_uninitialized_array = $this->get_uninitialized_variables(array('customers_id'));
			if(empty($t_uninitialized_array) && $this->customers_id > 0)
			{
				// INSERT
				xtc_db_perform(TABLE_ADDRESS_BOOK, $t_sql_data_array);
				$this->address_book_id = xtc_db_insert_id();
			}
			else
			{
				trigger_error("Variable(s) " . implode(', ', $t_uninitialized_array) . " do(es) not exist in class " . get_class($this) . " or is/are null", E_USER_ERROR);
			}
		}
		else
		{
			// UPDATE
			xtc_db_perform(TABLE_ADDRESS_BOOK, $t_sql_data_array, 'update', 'address_book_id = "' . $this->address_book_id . '"');
		}
		return true;
	}
	
	protected function get_sql_data_array()
	{
		$t_sql_data_array = array();
		if(isset($this->customers_id))
		{
			$t_sql_data_array['customers_id'] = $this->customers_id;
		}
		if(isset($this->entry_gender))
		{
			$t_sql_data_array['entry_gender'] = $this->entry_gender;
		}
		if(isset($this->entry_firstname))
		{
			$t_sql_data_array['entry_firstname'] = $this->entry_firstname;
		}
		if(isset($this->entry_lastname))
		{
			$t_sql_data_array['entry_lastname'] = $this->entry_lastname;
		}
		if(isset($this->entry_company))
		{
			$t_sql_data_array['entry_company'] = $this->entry_company;
		}
		if(isset($this->entry_street_address))
		{
			$t_sql_data_array['entry_street_address'] = $this->entry_street_address;
		}
		if(isset($this->entry_house_number))
		{
			$t_sql_data_array['entry_house_number'] = $this->entry_house_number;
		}
		if(isset($this->entry_additional_info))
		{
			$t_sql_data_array['entry_additional_info'] = $this->entry_additional_info;
		}
		if(isset($this->entry_suburb))
		{
			$t_sql_data_array['entry_suburb'] = $this->entry_suburb;
		}
		if(isset($this->entry_postcode))
		{
			$t_sql_data_array['entry_postcode'] = $this->entry_postcode;
		}
		if(isset($this->entry_city))
		{
			$t_sql_data_array['entry_city'] = $this->entry_city;
		}
		if(isset($this->entry_state))
		{
			$t_sql_data_array['entry_state'] = $this->entry_state;
		}
		if(isset($this->entry_country_id))
		{
			$t_sql_data_array['entry_country_id'] = $this->entry_country_id;
		}
		if(isset($this->entry_zone_id))
		{
			$t_sql_data_array['entry_zone_id'] = $this->entry_zone_id;
		}
		if(isset($this->address_date_added))
		{
			$t_sql_data_array['address_date_added'] = $this->address_date_added;
		}
		if(isset($this->address_class))
		{
			$t_sql_data_array['address_class'] = $this->address_class;
		}
		if(isset($this->customer_b2b_status))
		{
			$t_sql_data_array['customer_b2b_status'] = $this->customer_b2b_status;
		}
		$t_sql_data_array['address_last_modified'] = 'now()';
		
		return $t_sql_data_array;
	}
	
	public function delete()
	{
		$t_uninitialized_array = $this->get_uninitialized_variables(array('address_book_id'));
		if(empty($t_uninitialized_array))
		{
			// DELETE
			xtc_db_perform(TABLE_ADDRESS_BOOK, array(), 'delete', 'address_book_id = "' . $this->address_book_id . '"');
		}
		else
		{
			trigger_error("Variable(s) " . implode(', ', $t_uninitialized_array) . " do(es) not exist in class " . get_class($this) . " or is/are null", E_USER_ERROR);
		}
	}
	
	protected function set_validation_rules()
	{
		$this->validation_rules_array['address_book_id']		= array('type' => 'int');
		$this->validation_rules_array['customers_id']			= array('type' => 'int');
		$this->validation_rules_array['entry_gender']			= array('type' => 'string');
		$this->validation_rules_array['entry_firstname']		= array('type' => 'string');
		$this->validation_rules_array['entry_lastname']			= array('type' => 'string');
		$this->validation_rules_array['entry_company']			= array('type' => 'string');
		$this->validation_rules_array['entry_street_address']	= array('type' => 'string');
		$this->validation_rules_array['entry_house_number']	    = array('type' => 'string');
		$this->validation_rules_array['entry_additional_info']	= array('type' => 'string');
		$this->validation_rules_array['entry_suburb']			= array('type' => 'string');
		$this->validation_rules_array['entry_postcode']			= array('type' => 'string');
		$this->validation_rules_array['entry_city']				= array('type' => 'string');
		$this->validation_rules_array['entry_state']			= array('type' => 'string');
		$this->validation_rules_array['entry_country_id']		= array('type' => 'int');
		$this->validation_rules_array['entry_zone_id']			= array('type' => 'int');
		$this->validation_rules_array['address_date_added']		= array('type' => 'string');
		$this->validation_rules_array['address_last_modified']	= array('type' => 'string');
		$this->validation_rules_array['address_class']			= array('type' => 'string');
		$this->validation_rules_array['primary']				= array('type' => 'bool', 'strict' => 'true');
		$this->validation_rules_array['customer_b2b_status']	= array('type' => 'int');
	}
}