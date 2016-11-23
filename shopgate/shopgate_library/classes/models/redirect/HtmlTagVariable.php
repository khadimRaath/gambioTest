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
 * @method        setFunctionName(string $value)
 * @method string getFunctionName()
 */
class Shopgate_Model_Redirect_HtmlTagVariable extends Shopgate_Model_Abstract
{
	public function __construct()
	{
		$this->allowedMethods = array(
			'Name',
			'FunctionName',
		);
	}
}