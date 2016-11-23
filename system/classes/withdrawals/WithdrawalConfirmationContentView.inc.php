<?php
/* --------------------------------------------------------------
  WithdrawalConfirmationContentView.inc.php 2016-03-11
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2014 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
 */

class WithdrawalConfirmationContentView extends ContentView
{
	protected $customer_gender;
	protected $customer_name;
	protected $customer_street_address;
	protected $customer_postcode;
	protected $customer_city;
	protected $customer_country;
	protected $order_date;
	protected $delivery_date;
	protected $withdrawal_date;
	protected $withdrawal_content;
	protected $outputType;
	protected $languageId;
	protected $language;
	protected $templateFilename;
	protected $templateFolder;
	
	public function __construct($templateFilename = 'withdrawal_confirmation', $templateFolder = '')
	{
		parent::__construct();
		
		$this->set_caching_enabled(false);
		$this->set_flat_assigns(true);
		
		$this->outputType       = 'html';
		$this->templateFilename = $templateFilename;
		$this->templateFolder   = $templateFolder;
	}

	public function prepare_data()
	{
		$this->_setLogo();
		$this->_setCustomerData();
		$this->_setOrderData();
		$this->_setWithdrawalData();
		$this->_setEmailSignature();
		$this->_defineStoreCountryName();
	}

	public function get_html()
	{
		$this->prepare_data();
		$t_output = fetch_email_template($this, $this->templateFilename, $this->outputType, $this->templateFolder,
		                                 $_SESSION['languages_id'], $_SESSION['language']);
		
		return $t_output;
	}
	
	public function fetch($p_filepath)
	{
		// WORKAROUND, da fetch_mail_template fetch-Methode aufruft (nicht existent in ContentView)
		$this->set_template_dir(DIR_FS_CATALOG);
		$this->set_content_template(str_replace(DIR_FS_CATALOG, '', $p_filepath));
		return $this->build_html();
	}
	
	protected function _setLogo()
	{
		$coo_logo_manager = MainFactory::create_object('GMLogoManager', array("gm_logo_mail"));
		if($coo_logo_manager->logo_use == '1')
		{
			$this->set_content_data('LOGO', $coo_logo_manager->get_logo());
		}
	}
	
	protected function _setCustomerData()
	{
		$this->set_content_data('CUSTOMER_GENDER', $this->customer_gender);
		$this->set_content_data('CUSTOMER_NAME', $this->customer_name);
		$this->set_content_data('CUSTOMER_STREET_ADDRESS', $this->customer_street_address);
		$this->set_content_data('CUSTOMER_POSTCODE', $this->customer_postcode);
		$this->set_content_data('CUSTOMER_CITY', $this->customer_city);
		$this->set_content_data('CUSTOMER_COUNTRY', $this->customer_country);
	}
	
	protected function _setOrderData()
	{
		$this->set_content_data('ORDER_DATE', $this->order_date);
		$this->set_content_data('DELIVERY_DATE', $this->delivery_date);
	}

	protected function _setWithdrawalData()
	{
		$this->set_content_data('WITHDRAWAL_DATE', $this->withdrawal_date);
		$this->set_content_data('WITHDRAWAL_CONTENT', $this->withdrawal_content);
	}
	
	protected function _setEmailSignature()
	{
		if(defined('EMAIL_SIGNATURE'))
		{
			$this->set_content_data('EMAIL_SIGNATURE_HTML', nl2br(EMAIL_SIGNATURE));
			$this->set_content_data('EMAIL_SIGNATURE_TEXT', EMAIL_SIGNATURE);
		}
	}
	
	protected function _defineStoreCountryName()
	{
		if((int)STORE_COUNTRY > 0)
		{
			$t_query = 'SELECT countries_iso_code_2 FROM countries WHERE countries_id = "' . xtc_db_input(STORE_COUNTRY) . '"';
			$t_result = xtc_db_query($t_query);
			if(xtc_db_num_rows($t_result) == 1)
			{
				$t_row = xtc_db_fetch_array($t_result);
				$coo_language_text_manager = MainFactory::create_object('LanguageTextManager', array('Countries', $_SESSION['languages_id']));
				define('STORE_COUNTRY_NAME', $coo_language_text_manager->get_text($t_row['countries_iso_code_2']));
			}
		}
	}

	public function get_customer_gender()
	{
		return $this->customer_gender;
	}
	
	public function set_customer_gender($p_customer_gender)
	{
		$this->customer_gender = (string)$p_customer_gender;
	}
	
	public function get_customer_name()
	{
		return $this->customer_name;
	}
	
	public function set_customer_name($p_customer_name)
	{
		$this->customer_name = (string)$p_customer_name;
	}
	
	public function get_customer_street_address()
	{
		return $this->customer_street_address;
	}
	
	public function set_customer_street_address($p_customer_street_address)
	{
		$this->customer_street_address = (string)$p_customer_street_address;
	}

	public function get_customer_postcode()
	{
		return $this->customer_postcode;
	}

	public function set_customer_postcode($p_customer_postcode)
	{
		$this->customer_postcode = (string)$p_customer_postcode;
	}
	
	public function get_customer_city()
	{
		return $this->customer_city;
	}
	
	public function set_customer_city($p_customer_city)
	{
		$this->customer_city = (string)$p_customer_city;
	}
	
	public function get_customer_country()
	{
		return $this->customer_country;
	}
	
	public function set_customer_country($p_customer_country)
	{
		$this->customer_country = (string)$p_customer_country;
	}
	
	public function get_order_date()
	{
		return $this->order_date;
	}
	
	public function set_order_date($p_order_date)
	{
		$this->order_date = (string)$p_order_date;
	}
	
	public function get_delivery_date()
	{
		return $this->delivery_date;
	}
	
	public function set_delivery_date($p_delivery_date)
	{
		$this->delivery_date = (string)$p_delivery_date;
	}
	
	public function get_withdrawal_date()
	{
		return $this->withdrawal_date;
	}
	
	public function set_withdrawal_date($p_withdrawal_date)
	{
		$this->withdrawal_date = (string)$p_withdrawal_date;
	}
	
	public function get_withdrawal_content()
	{
		return $this->withdrawal_content;
	}
	
	public function set_withdrawal_content($p_withdrawal_content)
	{
		$this->withdrawal_content = (string)$p_withdrawal_content;
	}
	
	
	/**
	 * @return string
	 */
	public function getOutputType()
	{
		return $this->outputType;
	}
	
	
	/**
	 * @param string $outputType
	 */
	public function setOutputType($outputType)
	{
		$this->outputType = $outputType;
	}
}
