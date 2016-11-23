<?php
/* --------------------------------------------------------------
   function.crypt_link.php 2016-01-14 rn
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */

/**
 * @param $params
 * @param $smarty
 *
 * @return string
 */
function smarty_function_amazon_payment($params, &$smarty)
{
	$readonly = ($params['readonly'] == true) ? 'readOnlyA' : 'a';
	$coo_aap = MainFactory::create_object('AmazonAdvancedPayment');
	$result = '<div id="' . $params['id'] . '" data-gambio-widget="amazon_checkout" ' .
                    'data-amazon_checkout-seller-id="' . $coo_aap->seller_id . '" ' .
                    'data-amazon_checkout-order-reference="' . $_SESSION['amazonadvpay_order_ref_id'] . '" ' .
                    'data-amazon_checkout-country-txt="' . $coo_aap->get_text('country_not_allowed') . '" ' .
                    'data-amazon_checkout-button-txt="' . $coo_aap->get_text("sign_out") . '" ' .
              '></div>';
	return $result;
}