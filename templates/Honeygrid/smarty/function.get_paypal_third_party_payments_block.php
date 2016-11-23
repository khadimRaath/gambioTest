<?php
/* --------------------------------------------------------------
   function.get_paypal_third_party_payments_block.php 2016-01-25 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   -------------------------------------------------------------- 
*/

function smarty_function_get_paypal_third_party_payments_block($params, &$smarty) 
{
	$thirdPartyPaymentsHelper = MainFactory::create('PayPalThirdPartyPaymentsHelper');
	
	return $thirdPartyPaymentsHelper->getThirdPartyPaymentsBlock();
}