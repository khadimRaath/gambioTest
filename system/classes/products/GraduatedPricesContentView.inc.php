<?php
/* --------------------------------------------------------------
   GraduatedPricesContentView.inc.php 2015-05-29 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------


   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommercebased on original files from OSCommerce CVS 2.2 2002/08/28 02:14:35 www.oscommerce.com
   (c) 2003	 nextcommerce (graduated_prices.php,v 1.11 2003/08/17); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: graduated_prices.php 1243 2005-09-25 09:33:02Z mz $)

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

/**
 * Class GraduatedPricesContentView
 */
class GraduatedPricesContentView extends ContentView
{
	protected $customers_status_graduated_prices = 0;
	protected $graduated_prices_array = array();
	protected $coo_product;
	
	function __construct()
	{
		parent::__construct();
		
		$this->set_content_template('module/graduated_price.html');
		$this->set_flat_assigns(true);
	}

	
	function prepare_data()
	{
		$uninitializedArray = $this->get_uninitialized_variables(array('coo_product'));

		if(empty($uninitializedArray))
		{
			$this->get_data();
			$this->add_data();
		}
		else
		{
			trigger_error("Variable(s) " . implode(', ', $uninitializedArray) . " do(es) not exist in class " . get_class($this) . " or is/are null", E_USER_ERROR);
		}
	}
	
	
	protected function get_data()
	{
		$this->build_html = false;
		$this->graduated_prices_array = array();

		$this->graduated_prices_array = $this->coo_product->getGraduated();
		
		if(count($this->graduated_prices_array) > 1 && $this->customers_status_graduated_prices == 1)
		{
			$this->build_html = true;
		}
	}
	
	
	protected function add_data()
	{
		if($this->build_html == false)
		{
			return;
		}

		$this->content_array['module_content'] = $this->graduated_prices_array;
	}


	/**
	 * @param product $product
	 */
	public function set_coo_product(product $product)
	{
		$this->coo_product = $product;
	}


	/**
	 * @return product
	 */
	public function get_coo_product()
	{
		return $this->coo_product;
	}


	/**
	 * @param int $p_customers_status_graduated_prices
	 */
	public function set_customers_status_graduated_prices($p_customers_status_graduated_prices)
	{
		$this->customers_status_graduated_prices = (int)$p_customers_status_graduated_prices;
	}


	/**
	 * @return int
	 */
	public function get_customers_status_graduated_prices()
	{
		return $this->customers_status_graduated_prices;
	}


	/**
	 * @param array $p_graduated_prices_array
	 */
	public function set_graduated_prices_array(array $p_graduated_prices_array)
	{
		$this->graduated_prices_array = $p_graduated_prices_array;
	}


	/**
	 * @return array
	 */
	public function get_graduated_prices_array()
	{
		return $this->graduated_prices_array;
	}
}