<?php
/*
 * Shopgate GmbH
 * http://www.shopgate.com
 * Copyright © 2012-2014 Shopgate GmbH
 *
 * Released under the GNU General Public License (Version 2)
 * [http://www.gnu.org/licenses/gpl-2.0.html]
*/

/**
 * @method        setName(string $value)
 * @method string getName()
 *
 * @method        setValue(string $value)
 * @method string getValue()
 *
 * @method      setUnset(bool $value)
 * @method bool getUnset()
 *
 * @method      setDisabled(bool $value)
 * @method bool getDisabled()
 *
 * @method                                           setVariables(array $value)
 * @method Shopgate_Model_Redirect_HtmlTagVariable[] getVariables()
 */
class Shopgate_Model_Redirect_DeeplinkSuffixValue extends Shopgate_Model_Abstract
{
	public function __construct()
	{
		$this->allowedMethods = array(
			'Name',
			'Value',
			'Unset',
			'Disabled',
			'Variables',
		);
		
		$this->setVariables(array());
	}
}