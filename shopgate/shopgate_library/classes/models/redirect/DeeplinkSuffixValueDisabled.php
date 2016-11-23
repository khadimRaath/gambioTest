<?php
/*
 * Shopgate GmbH
 * http://www.shopgate.com
 * Copyright © 2012-2014 Shopgate GmbH
 *
 * Released under the GNU General Public License (Version 2)
 * [http://www.gnu.org/licenses/gpl-2.0.html]
*/

class Shopgate_Model_Redirect_DeeplinkSuffixValueDisabled extends Shopgate_Model_Redirect_DeeplinkSuffixValue
{
	public function __construct()
	{
		parent::__construct();
		
		$this->setDisabled(true);
	}
}