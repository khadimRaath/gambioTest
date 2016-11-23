<?php
/* --------------------------------------------------------------
  GMAttributesCalculator.php 2015-05-20 gm
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2015 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
 */

class GMAttributesCalculator_ORIGIN
{
	public $products_id = 0;
	public $attributes_array = array();
	public $tax_class_id = 0;
	public $combis_id = 0;

	public function __construct($p_products_id, $p_attributes_array, $p_tax_class_id, $p_combis_id = 0)
	{
		$this->products_id = $p_products_id;
		$this->attributes_array = $p_attributes_array;
		$this->tax_class_id = $p_tax_class_id;
		$this->combis_id = $p_combis_id;
	}

	public function attributes_price()
	{
		global $xtPrice;
		
		$t_attributes_price = 0;
		$t_customers_status_discount_attributes_copy = $xtPrice->cStatus['customers_status_discount_attributes'];
		$xtPrice->cStatus['customers_status_discount_attributes'] = 0; // kein Rabatt berechnen, da Gesamtpreis später rabattiert wird

		for($i = 0; $i < count($this->attributes_array); $i++)
		{
			$t_values_array = $xtPrice->xtcGetOptionPrice($this->products_id, $this->attributes_array[$i]['option'], $this->attributes_array[$i]['value']);
			$t_attributes_price += $t_values_array['price'];
		}
		
		$xtPrice->cStatus['customers_status_discount_attributes'] = $t_customers_status_discount_attributes_copy; // alten Zustand wiederherstellen

		return $t_attributes_price;
	}

	public function calculate($p_quantity = 1, $p_format = false, $p_currency = false, $p_return_array = 0)
	{
		global $xtPrice;
		
		$t_price = 0;

		if($xtPrice->cStatus['customers_status_show_price_tax'] != '0')
		{
			$t_tax_rate = $xtPrice->TAX[$this->tax_class_id];
		}
		else
		{
			$t_tax_rate = 0;
		}

		$t_price = $xtPrice->getPprice($this->products_id);

		if($this->combis_id > 0)
		{
			$t_combi_price = $xtPrice->get_properties_combi_price($this->combis_id, 0, false);
			$t_price += $t_combi_price;
		}

		// graduated sPrice
		$quantity = $p_quantity + xtc_get_qty($this->products_id);
		$graduated_sPrice = $xtPrice->xtcGetGraduatedPrice($this->products_id, $quantity, $this->combis_id);

		// check specialprice
		$t_new_price = $xtPrice->xtcCheckSpecial($this->products_id, $this->combis_id);
		if(empty($t_new_price) === false)
		{
			$t_price = $xtPrice->xtcFormatSpecial($this->products_id, $xtPrice->xtcAddTax($t_new_price, $t_tax_rate) + $this->attributes_price(), $xtPrice->xtcAddTax($t_price, $t_tax_rate) + $this->attributes_price(), $p_format, $p_return_array);
		}
		// check graduated
		elseif($xtPrice->cStatus['customers_status_graduated_prices'] == '1' && !empty($graduated_sPrice) && $graduated_sPrice != $t_price)
		{
			if($graduated_sPrice < $t_price)
			{
				$t_price = $xtPrice->xtcFormatSpecialGraduated($this->products_id, $xtPrice->xtcAddTax($graduated_sPrice, $t_tax_rate) + $this->attributes_price(), $xtPrice->xtcAddTax($t_price, $t_tax_rate) + $this->attributes_price(), $p_format, $p_return_array, $this->products_id);
			}
			else
			{
				$t_price = $xtPrice->xtcFormat($xtPrice->xtcAddTax($graduated_sPrice, $t_tax_rate) + $this->attributes_price(), $p_format, 0, $p_currency, $p_return_array, $this->products_id);
			}
		}
		// check Group Price
		elseif($xtPrice->xtcGetGroupPrice($this->products_id, $p_quantity, $this->combis_id))
		{
			$t_new_price = $xtPrice->xtcGetGroupPrice($this->products_id, $p_quantity, $this->combis_id);
			
			if($t_new_price < $t_price)
			{
				$t_price = $xtPrice->xtcFormatSpecialGraduated($this->products_id, $xtPrice->xtcAddTax($t_new_price, $t_tax_rate) + $this->attributes_price(), $xtPrice->xtcAddTax($t_price, $t_tax_rate) + $this->attributes_price(), $p_format, $p_return_array, $this->products_id);
			}
			elseif($xtPrice->xtcCheckDiscount($this->products_id))
			{
				$t_discount = $xtPrice->xtcCheckDiscount($this->products_id);
				
				if($xtPrice->cStatus['customers_status_discount_attributes'] == '1')
				{
					$t_price = $xtPrice->xtcFormatSpecialDiscount($this->products_id, $t_discount, $xtPrice->xtcAddTax($t_price, $t_tax_rate) + $this->attributes_price(), $p_format, $p_return_array);
				}
				else
				{
					$t_price = $xtPrice->xtcFormatSpecialDiscount($this->products_id, $t_discount, $xtPrice->xtcAddTax($t_price, $t_tax_rate), $p_format, $p_return_array, $this->attributes_price());
				}
			}
			else
			{
				$t_price = $xtPrice->xtcFormat($xtPrice->xtcAddTax($t_new_price, $t_tax_rate) + $this->attributes_price(), $p_format, 0, $p_currency, $p_return_array, $this->products_id);
			}
		}
		// check Product Discount
		elseif($xtPrice->xtcCheckDiscount($this->products_id))
		{
			$t_discount = $xtPrice->xtcCheckDiscount($this->products_id);
			
			if($this->combis_id != 0)
			{
				$t_price -= $t_combi_price;
			}
			
			$t_combi_price = $xtPrice->get_properties_combi_price($this->combis_id, $this->tax_class_id);
			if($xtPrice->cStatus['customers_status_discount_attributes'] == '1' && $xtPrice->xtcAddTax($t_price, $t_tax_rate) + $this->attributes_price() != 0)
			{
				$t_price = $xtPrice->xtcFormatSpecialDiscount($this->products_id, $t_discount, $xtPrice->xtcAddTax($t_price, $t_tax_rate) + $this->attributes_price(), $p_format, $p_return_array, 0, $t_combi_price);
			}
			else
			{
				$t_price = $xtPrice->xtcFormatSpecialDiscount($this->products_id, $t_discount, $xtPrice->xtcAddTax($t_price, $t_tax_rate), $p_format, $p_return_array, $this->attributes_price(), $t_combi_price);
			}
		}
		// normal price
		else
		{
			$t_price = $xtPrice->xtcFormat($xtPrice->xtcAddTax($t_price, $t_tax_rate) + $this->attributes_price(), $p_format, 0, $p_currency, $p_return_array, $this->products_id);
		}

		return $t_price;
	}
}
MainFactory::load_origin_class('GMAttributesCalculator');