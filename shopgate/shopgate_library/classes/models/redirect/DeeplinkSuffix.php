<?php
/*
 * Shopgate GmbH
 * http://www.shopgate.com
 * Copyright © 2012-2014 Shopgate GmbH
 *
 * Released under the GNU General Public License (Version 2)
 * [http://www.gnu.org/licenses/gpl-2.0.html]
*/

class Shopgate_Model_Redirect_DeeplinkSuffix extends Shopgate_Model_Abstract
{
	/** @var Shopgate_Model_Redirect_DeeplinkSuffixValue [string, Shopgate_Model_Redirect_DeeplinkSuffixValue] */
	protected $valuesByType;
	
	/**
	 * @param string                                      $type
	 * @param Shopgate_Model_Redirect_DeeplinkSuffixValue $value
	 */
	public function addValue($type, Shopgate_Model_Redirect_DeeplinkSuffixValue $value)
	{
		$this->valuesByType[$type] = $value;
	}
	
	/**
	 * @param string $type
	 *
	 * @return Shopgate_Model_Redirect_DeeplinkSuffixValue
	 */
	public function getValue($type)
	{
		if (!isset($this->valuesByType[$type]) || ($this->valuesByType[$type] === null)) {
			return new Shopgate_Model_Redirect_DeeplinkSuffixValueUnset();
		}
		
		if ($this->valuesByType[$type] === false) {
			return new Shopgate_Model_Redirect_DeeplinkSuffixValueDisabled();
		}
		
		return $this->valuesByType[$type];
	}
}