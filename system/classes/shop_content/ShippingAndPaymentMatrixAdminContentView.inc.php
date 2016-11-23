<?php
/* --------------------------------------------------------------
   ShippingAndPaymentMatrixAdminContentView.inc.php 2015-12-03 gambio
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
 */

class ShippingAndPaymentMatrixAdminContentView extends LightboxContentView
{
	public function __construct()
    {
		$this->set_template_dir(DIR_FS_ADMIN . 'html/content/');
		$this->set_content_template('shipping_and_payment_matrix.html');
    }

	//No flat assigns for gambio template
	public function init_smarty()
	{
		parent::init_smarty();
		$this->set_flat_assigns(false);
	}

    public function build_html($p_content_data_array = false, $p_template_file = false)
    {
		$t_country_array = $this->get_country_array();
		$t_language_array = $this->get_language_array();
		$t_shipping_info_array = $this->get_shipping_info_array();
		$t_shipping_time_array = $this->get_shipping_time_array();
		$t_payment_info_array = $this->get_payment_info_array();

		$t_country_array = $this->init_country_array($t_country_array, $t_shipping_info_array, $t_shipping_time_array, $t_payment_info_array);

		$this->set_content_data('country_array', $t_country_array);
		$this->set_content_data('language_array', $t_language_array);
		$this->set_content_data('shipping_info_array', $t_shipping_info_array);
		$this->set_content_data('shipping_time_array', $t_shipping_time_array);
		$this->set_content_data('payment_info_array', $t_payment_info_array);
		$this->set_content_data('actual_language_id', $_SESSION['languages_id']);
		$this->set_content_data('page_token', $_SESSION['coo_page_token']->generate_token());

		$t_html_output = parent::build_html();

		return $t_html_output;
    }

	protected function get_country_array()
	{
		$t_country_array = array();
		$coo_text_manager = MainFactory::create_object('LanguageTextManager', array('countries', $_SESSION['languages_id']));

		$t_sql = '	SELECT
						countries_iso_code_2 AS code,
						status
					FROM
						countries';
		$t_result = xtc_db_query($t_sql);

		while($t_row = xtc_db_fetch_array($t_result))
		{
			$t_country_array[$t_row['code']] = array();
			$t_country_array[$t_row['code']]['name'] = $coo_text_manager->get_text($t_row['code']);
			$t_country_array[$t_row['code']]['status'] = $t_row['status'];
		}

		$t_country_array = $this->resort_country_array($t_country_array);

		return $t_country_array;
	}

	protected function get_language_array()
	{
		$t_language_array = array();

		$t_sql = '	SELECT
						languages_id,
						name,
						image,
						directory
					FROM
						languages
					ORDER BY
						sort_order';
		$t_result = xtc_db_query($t_sql);

		while($t_row = xtc_db_fetch_array($t_result))
		{
			$t_language_array[$t_row['languages_id']]['name'] = $t_row['name'];
			$t_language_array[$t_row['languages_id']]['image'] = DIR_WS_CATALOG . 'lang/' . $t_row['directory'] . '/' . $t_row['image'];
		}

		return $t_language_array;
	}

	protected function get_shipping_info_array()
	{
		$t_shipping_info_array = array();

		$t_sql = '	SELECT
						language_id,
						country_code,
						shipping_info
					FROM
						shipping_and_payment_matrix';
		$t_result = xtc_db_query($t_sql);

		while($t_row = xtc_db_fetch_array($t_result))
		{
			$t_shipping_info_array[$t_row['language_id']][$t_row['country_code']] = htmlspecialchars_wrapper($t_row['shipping_info']);
		}

		return $t_shipping_info_array;
	}

	protected function get_shipping_time_array()
	{
		$t_shipping_time_array = array();

		$t_sql = '	SELECT
						language_id,
						country_code,
						shipping_time
					FROM
						shipping_and_payment_matrix';
		$t_result = xtc_db_query($t_sql);

		while($t_row = xtc_db_fetch_array($t_result))
		{
			$t_shipping_time_array[$t_row['language_id']][$t_row['country_code']] = htmlspecialchars_wrapper($t_row['shipping_time']);
		}

		return $t_shipping_time_array;
	}

	protected function get_payment_info_array()
	{
		$t_payment_info_array = array();

		$t_sql = '	SELECT
						language_id,
						country_code,
						payment_info
					FROM
						shipping_and_payment_matrix';
		$t_result = xtc_db_query($t_sql);

		while($t_row = xtc_db_fetch_array($t_result))
		{
			$t_payment_info_array[$t_row['language_id']][$t_row['country_code']] = htmlspecialchars_wrapper($t_row['payment_info']);
		}

		return $t_payment_info_array;
	}

	protected function init_country_array(array $p_country_array, array $p_shipping_info_array, array $p_shipping_time_array, array $p_payment_info_array)
	{
		$t_selected_country_array = array();
		$t_country_array = $p_country_array;

		if(empty($p_shipping_info_array) == false || empty($p_shipping_time_array) == false || empty($p_payment_info_array) == false)
		{
			$t_selected_country_array = array_merge($t_selected_country_array, $this->get_country_array_from_info_array($p_shipping_info_array));
			$t_selected_country_array = array_merge($t_selected_country_array, $this->get_country_array_from_info_array($p_shipping_time_array));
			$t_selected_country_array = array_merge($t_selected_country_array, $this->get_country_array_from_info_array($p_payment_info_array));
		}

		if(empty($t_selected_country_array) == false)
		{
			$t_country_array = $this->set_country_status_by_country_codes($t_country_array, $t_selected_country_array);
		}

		return $t_country_array;
	}

	protected function get_country_array_from_info_array(array $p_info_array)
	{
		$t_country_array = array();

		foreach($p_info_array as $t_country_info_array)
		{
			$t_country_array = array_merge($t_country_array, array_keys($t_country_info_array));
		}

		return $t_country_array;
	}

	protected function set_country_status_by_country_codes(array $p_country_array, array $p_country_code_array)
	{
		$t_country_array = $p_country_array;

		foreach($t_country_array as $t_country_code => $t_info_array)
		{
			if(in_array($t_country_code, $p_country_code_array))
			{
				$t_country_array[$t_country_code]['status'] = 1;
			}
			else
			{
				$t_country_array[$t_country_code]['status'] = 0;
			}
		}

		return $t_country_array;
	}

	protected function resort_country_array(array $p_country_array)
	{
		uasort($p_country_array, 'uasort_helper_function');

		if((int)STORE_COUNTRY > 0)
		{
			$t_sql = 'SELECT
					countries_iso_code_2 as code
				FROM
					countries
				WHERE
					countries_id = ' . STORE_COUNTRY;
			$t_result = xtc_db_query($t_sql);
			if(xtc_db_num_rows($t_result) == 1)
			{
				$t_row = xtc_db_fetch_array($t_result);
				$t_store_country_array = array();
				$t_store_country_array[$t_row['code']] = $p_country_array[$t_row['code']];
				unset($p_country_array[$t_row['code']]);
				$p_country_array = $t_store_country_array + $p_country_array;
			}
		}

		return $p_country_array;
	}
}

function uasort_helper_function($a, $b)
{
	if($a['name'] > $b['name'])
	{
		return 1;
	}
	else
	{
		return -1;
	}
}