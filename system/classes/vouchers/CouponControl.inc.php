<?php
/* --------------------------------------------------------------
  CouponControl.inc.php 2014-09-21 gm
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2014 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
 */

class CouponControl extends BaseClass
{
	protected $id = 0;
	protected $shipping_free = false;
	protected $percentaged = false;
	protected $amount = 0;
	protected $restricted_categories_array = array();
	protected $restricted_products_array = array();
	protected $products_array = array();
	protected $order_id = 0;
	protected $currency_exchange_rate = 1;

	public function __construct($p_coupon_code, $p_order_id, $p_currency_exchange_rate)
	{
		$this->currency_exchange_rate = (double)$p_currency_exchange_rate;
		
		$this->load_coupon($p_coupon_code);
		$this->load_products($p_order_id);
	}
	
	public function load_coupon($p_coupon_code)
	{
		$t_sql = 'SELECT 
						coupon_id,
						coupon_type,
						coupon_amount,
						restrict_to_products,
						restrict_to_categories
					FROM ' . TABLE_COUPONS . '
					WHERE
						coupon_code = "' . xtc_db_input($p_coupon_code) . '" AND
						coupon_active = "Y" AND
						coupon_type != "G"';
		$t_result = xtc_db_query($t_sql);
		if(xtc_db_num_rows($t_result) == 1)
		{
			$t_result_array = xtc_db_fetch_array($t_result);
			
			$this->id = (int)$t_result_array['coupon_id'];
			
			$this->amount = (double)$t_result_array['coupon_amount'];
			
			if($t_result_array['coupon_type'] == 'S')
			{
				$this->shipping_free = true;
			}
			elseif($t_result_array['coupon_type'] == 'P')
			{
				$this->percentaged = true;
				if($this->amount > 100)
				{
					$this->amount = 100;
				}
			}
			
			if($this->amount < 0)
			{
				$this->amount = 0;
			}
			
			if($this->percentaged === false)
			{
				 $this->amount *= $this->currency_exchange_rate;
			}
			
			if(!empty($t_result_array['restrict_to_categories']))
			{
				$this->restricted_categories_array = explode(',', $t_result_array['restrict_to_categories']);
			}
			
			if(!empty($t_result_array['restrict_to_products']))
			{
				$this->restricted_products_array = explode(',', $t_result_array['restrict_to_products']);
			}
		}
	}
	
	public function load_products($p_order_id)
	{
		$this->order_id = (int)$p_order_id;
		
		// reset
		$this->products_array = array();
		
		$t_sql = 'SELECT
						orders_products_id,
						products_id,
						SUM(final_price) AS amount,
						products_tax,
						allow_tax
					FROM ' . TABLE_ORDERS_PRODUCTS . '
					WHERE orders_id = "' . (int)$p_order_id . '"
					GROUP BY orders_id, products_id, products_tax, allow_tax'; 
		$t_result = xtc_db_query($t_sql);
		while($t_result_array = xtc_db_fetch_array($t_result))
		{
			$this->products_array[] = array('orders_products_id' => $t_result_array['orders_products_id'],
											'products_id' => $t_result_array['products_id'],
											'amount' => (double)$t_result_array['amount'],
											'tax_rate' => (double)$t_result_array['products_tax'],
											'allow_tax' => $t_result_array['allow_tax']);
		}
		
		$this->filter_products();
	}
	
	public function filter_products()
	{
		if(!empty($this->restricted_products_array))
		{
			foreach($this->products_array as $t_key => $t_product_array)
			{
				if(in_array($t_product_array['products_id'], $this->restricted_products_array) == false)
				{
					unset($this->products_array[$t_key]);
				}
			}
		}
		
		if(!empty($this->restricted_categories_array))
		{
			foreach($this->products_array as $t_key => $t_product_array)
			{
				$t_sql = 'SELECT categories_index FROM categories_index WHERE products_id = "' . (int)$this->products_array[$t_key] . '"';
				$t_result = xtc_db_query($t_sql);
				
				if(xtc_db_num_rows($t_result) == 1)
				{
					$t_result_array = xtc_db_fetch_array($t_result);
					preg_match_all('-(\d+)-', $t_result_array['categories_index'], $t_matches_array);
					
					foreach($t_matches_array[1] as $t_categories_id)
					{
						if(in_array($t_categories_id, $this->restricted_categories_array) == false)
						{
							unset($this->products_array[$t_key]);
						}
					}
				}
			}
		}
	}
	
	public function calculate_discount()
	{
		$t_total_amount = 0;
		
		foreach($this->products_array as $t_product_array)
		{
			$t_total_amount += $t_product_array['amount'];			
		}
		
		$t_reduced_amount = $t_total_amount;
		
		if($this->percentaged)
		{
			$t_reduced_amount = ((100 - $this->amount) / 100) * $t_reduced_amount;
		}
		else
		{
			$t_reduced_amount -= $this->amount;
		}
		
		if($t_reduced_amount < 0)
		{
			$t_reduced_amount = 0;
		}
		
		$t_discount = $t_total_amount - $t_reduced_amount;
		
		return $t_discount;
	}
	
	public function redeem($p_customer_id)
	{
		$c_customer_id = (int)$p_customer_id;
		
		if($c_customer_id > 0 && $this->id > 0 && $this->order_id > 0)
		{
			$t_sql = 'INSERT INTO ' . TABLE_COUPON_REDEEM_TRACK . ' 
						SET 
							coupon_id = "' . (int)$this->id . '",
							redeem_date = NOW(),
							redeem_ip = "' . xtc_db_input(xtc_get_ip_address()) . '",
							customer_id = "' . $c_customer_id . '",
							order_id = "' . (int)$this->order_id . '"';
			xtc_db_query($t_sql);
			
			return true;
		}
		
		return false;
	}
	
	public function calculate_taxes_discount($t_discount_amount)
	{
		$t_amount_array = array();
		$t_taxes_discount_array = array();
		$t_total_amount = 0;
		$t_net = false;
		
		foreach($this->products_array as $t_product_array)
		{
			if(isset($t_amount_array[$t_product_array['tax_rate']]) === false)
			{
				$t_amount_array[$t_product_array['tax_rate']] = 0;
			}
			
			$t_amount_array[$t_product_array['tax_rate']] += $t_product_array['amount'];
			$t_total_amount += $t_product_array['amount'];
			
			if($t_product_array['allow_tax'] == '0')
			{
				$t_net = true;
			}
		}
		
		foreach($t_amount_array as $t_tax_rate => $t_amount)
		{
			if(isset($t_taxes_discount_array[$t_tax_rate]) === false)
			{
				$t_taxes_discount_array[$t_tax_rate] = 0;
			}
			
			if($this->percentaged)
			{
				$t_reduced_amount = ((100 - $this->amount) / 100) * $t_amount_array[$t_tax_rate];
			}
			else
			{
				$t_ratio = 1 / $t_total_amount * $t_amount;
				$t_reduced_amount = $t_amount_array[$t_tax_rate] - $t_ratio * $t_discount_amount;
			}
			
			if($t_net)
			{
				$t_tax = $t_amount * ($t_tax_rate / 100);
				$t_reduced_tax = $t_reduced_amount * ($t_tax_rate / 100);
			}
			else
			{
				$t_tax = ($t_amount / (1 + ($t_tax_rate / 100)) - $t_amount) * -1;
				$t_reduced_tax = ($t_reduced_amount / (1 + ($t_tax_rate / 100)) - $t_reduced_amount) * -1;
			}
			
			$t_taxes_discount_array[$t_tax_rate] = $t_tax - $t_reduced_tax;
						
			if($t_taxes_discount_array[$t_tax_rate] < 0)
			{
				$t_taxes_discount_array[$t_tax_rate] = 0;
			}
		}
		
		return $t_taxes_discount_array;
	}
}