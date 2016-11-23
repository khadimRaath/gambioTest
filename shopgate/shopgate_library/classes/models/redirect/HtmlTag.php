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
 * @method                                            setAttributes(array $value)
 * @method Shopgate_Model_Redirect_HtmlTagAttribute[] getAttributes()
 */
class Shopgate_Model_Redirect_HtmlTag extends Shopgate_Model_Abstract
{
	public function __construct()
	{
		$this->allowedMethods = array(
			'Name',
			'Attributes',
		);
	}
}