<?php
/* --------------------------------------------------------------
   CrossSellingContentView.inc.php 2015-05-29 gambio
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------

   (c) 2005 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: cross_selling.php 1243 2005-09-25 09:33:02Z mz $) 

   Released under the GNU General Public License
   -----------------------------------------------------------------------------------------
   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(also_purchased_products.php,v 1.21 2003/02/12); www.oscommerce.com
   (c) 2003	 nextcommerce (also_purchased_products.php,v 1.9 2003/08/17); www.nextcommerce.org
   ---------------------------------------------------------------------------------------*/

/**
 * Class CrossSellingContentView
 */
class CrossSellingContentView extends ContentView
{
	protected $coo_product;
	protected $type = 'cross_selling';
	
	public function __construct()
	{
		parent::__construct();
		
		$this->set_flat_assigns(true);
	}
	
	
	public function prepare_data()
	{
		$this->build_html = false;
		$t_uninitialized_array = $this->get_uninitialized_variables(array('coo_product',
																		  'type')
		);

		if(empty($t_uninitialized_array))
		{
			$t_data_array = $this->get_data();
			$this->add_data($t_data_array);
		}
		else
		{
			trigger_error("Variable(s) " . implode(', ', $t_uninitialized_array) . " do(es) not exist in class " . get_class($this) . " or is/are null", E_USER_ERROR);
		}

		$showRating = false;
		if(gm_get_conf('ENABLE_RATING') === 'true' && gm_get_conf('SHOW_RATING_IN_GRID_AND_LISTING') === 'true')
		{
			$showRating = true;
		}
		$this->content_array['showRating'] = $showRating;
	}


	/**
	 * @return array
	 */
	protected function get_data()
	{
		switch($this->type)
		{
			case 'cross_selling':
				$this->set_content_template('module/cross_selling.html');
				return $this->coo_product->getCrossSells();

			case 'reverse_cross_selling':
				$this->set_content_template('module/reverse_cross_selling.html');
				return $this->coo_product->getReverseCrossSells();
			
			default:
				return array();
		}
	}
	
	
	protected function add_data($p_data_array)
	{
		if(count($p_data_array) > 0)
		{
			$this->build_html = true;
			$this->set_content_data('TRUNCATE_PRODUCTS_NAME', gm_get_conf('TRUNCATE_PRODUCTS_NAME'));
			$this->set_content_data('module_content', $p_data_array);
		}
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
	 * @param string $p_type
	 */
	public function set_type($p_type)
	{
		$this->type = (string)$p_type;
	}


	/**
	 * @return string
	 */
	public function get_type()
	{
		return $this->type;
	}
}