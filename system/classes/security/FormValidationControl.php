<?php

/* --------------------------------------------------------------
  FormValidationControl.inc.php 2014-11-06 gambio
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2014 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  ---------------------------------------------------------------------------------------
*/

class FormValidationControl extends BaseClass
{
	public function __construct()
	{
	}
	

	public function validate_address(AddressModel $p_address, $p_block_packstation = false)
	{
		$t_error_array = array();

		if(ACCOUNT_GENDER == 'true')
		{
			if(!$this->_accountNamesOptional($p_address) && $p_address->get_('entry_gender') != 'm' && $p_address->get_('entry_gender') != 'f')
			{
				$t_error_array['error_gender'] = ENTRY_GENDER_ERROR;
			}
		}

		if(!$this->_accountNamesOptional($p_address) && strlen_wrapper($p_address->get_('entry_firstname')) < ENTRY_FIRST_NAME_MIN_LENGTH)
		{
			$t_error_array['error_first_name'] = sprintf(ENTRY_FIRST_NAME_ERROR, ENTRY_FIRST_NAME_MIN_LENGTH);
		}

		if(!$this->_accountNamesOptional($p_address) && strlen_wrapper($p_address->get_('entry_lastname')) < ENTRY_LAST_NAME_MIN_LENGTH)
		{
			$t_error_array['error_last_name'] = sprintf(ENTRY_LAST_NAME_ERROR, ENTRY_LAST_NAME_MIN_LENGTH);
		}

		if(ACCOUNT_COMPANY == 'true')
		{
			if(strlen_wrapper($p_address->get_('entry_company')) > 0
			   && strlen_wrapper($p_address->get_('entry_company')) < ENTRY_COMPANY_MIN_LENGTH
			)
			{
				$t_error_array['error_company'] = sprintf(ENTRY_COMPANY_ERROR, ENTRY_COMPANY_MIN_LENGTH);
			}
		}

		if(strlen_wrapper($p_address->get_('entry_street_address')) < ENTRY_STREET_ADDRESS_MIN_LENGTH)
		{
			$t_error_array['error_street'] = sprintf(ENTRY_STREET_ADDRESS_ERROR, ENTRY_STREET_ADDRESS_MIN_LENGTH);
		}

		if($p_block_packstation === true
		   && preg_match('/.*(packstation|postfiliale|filiale).*/i', $p_address->get_('entry_street_address')) == 1
		)
		{
			$t_error_array['error_street'] = ENTRY_STREET_ADDRESS_NOT_STREET;
		}

		if(strlen_wrapper($p_address->get_('entry_postcode')) < ENTRY_POSTCODE_MIN_LENGTH)
		{
			$t_error_array['error_post_code'] = sprintf(ENTRY_POST_CODE_ERROR, ENTRY_POSTCODE_MIN_LENGTH);
		}

		if(strlen_wrapper($p_address->get_('entry_city')) < ENTRY_CITY_MIN_LENGTH)
		{
			$t_error_array['error_city'] = sprintf(ENTRY_CITY_ERROR, ENTRY_CITY_MIN_LENGTH);
		}

		if(is_numeric($p_address->get_('entry_country_id')) == false)
		{
			$t_error_array['error_country'] = ENTRY_COUNTRY_ERROR;
		}

		if(ACCOUNT_STATE == 'true' && $p_address->get_('entry_country_id') > 0)
		{
			// COUNT ZONES
			$t_query     = 'SELECT
							COUNT(*) AS total
						FROM
							' . TABLE_ZONES . '
						WHERE
							zone_country_id =  \'' . (int)$p_address->get_('entry_country_id') . '\'';
			$t_result    = xtc_db_query($t_query);
			$t_check_row = xtc_db_fetch_array($t_result);
			// ZONES EXISTS?
			$t_entry_state_has_zone = ($t_check_row['total'] > 0);
			
			if($t_entry_state_has_zone == false
			   && strlen_wrapper($p_address->get_('entry_state')) < ENTRY_STATE_MIN_LENGTH
			)
			{
				$t_error_array['error_state'] = sprintf(ENTRY_STATE_ERROR, ENTRY_STATE_MIN_LENGTH);
			}

			if($p_address->get_('entry_zone_id') == -1)
			{
				$t_error_array['error_state'] = ENTRY_STATE_ERROR_SELECT;
			}
		}
		
		return $t_error_array;
	}


	protected function _accountNamesOptional(AddressModel $address)
	{
		if(ACCOUNT_NAMES_OPTIONAL === 'true' && $address->get_('entry_company') !== '')
		{
			return true;
		}
		return false;
	}
}