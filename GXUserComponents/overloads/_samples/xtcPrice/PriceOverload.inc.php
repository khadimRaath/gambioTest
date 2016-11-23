<?php
/* --------------------------------------------------------------
   PriceOverload.inc.php 2016-06-24
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class PriceOverload
 *
 * This sample demonstrates the overloading of xtcPrice.
 */
class PriceOverload extends PriceOverload_parent
{
	/**
	 * Overloaded "getPrice" method.
	 *
	 * Multiply the product price with 100 to get the cents instead of euro.
	 *
	 * @param $productId
	 *
	 * @return mixed
	 */
	public function getPrice($productId)
	{
		$price = parent::getPrice($productId);
		
		return $price * 100;
	}
	
	
	/**
	 * Overloaded "xtcGetPrice" method.
	 *
	 * Force quantity to be equal to 1, disable include specials and force to consider the properties.
	 *
	 * @param            $productId
	 * @param            $formatPrice
	 * @param            $quantity
	 * @param            $taxClassId
	 * @param            $productPrice
	 * @param int        $returnArray
	 * @param int        $customerId
	 * @param bool|true  $includeSpecial
	 * @param bool|false $considerProperties
	 * @param int        $combisId
	 *
	 * @return mixed
	 */
	public function xtcGetPrice($productId,
	                            $formatPrice,
	                            $quantity,
	                            $taxClassId,
	                            $productPrice,
	                            $returnArray = 0,
	                            $customerId = 0,
	                            $includeSpecial = true,
	                            $considerProperties = false,
	                            $combisId = 0)
	{
		$quantity           = 1;
		$includeSpecial     = false;
		$considerProperties = true;
		
		return parent::xtcGetPrice($productId, $formatPrice, $quantity, $taxClassId, $productPrice, $returnArray,
		                           $customerId, $includeSpecial, $considerProperties, $combisId);
	}
}
