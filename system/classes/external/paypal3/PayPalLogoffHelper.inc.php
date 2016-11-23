<?php
/* --------------------------------------------------------------
	PayPalLogoffHelper.inc.php 2015-04-16
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2015 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/

/**
 * Helper class used to log out guest customers with accounts created from ECS data
 */
class PayPalLogoffHelper
{
	public function logoffGuest($customer_id)
	{
		$this->delete_guest_account($customer_id);
		$this->reset_session();
	}

	/**
	 * Resets the session data from the customer when on log off.
	 *
	 */
	protected function reset_session()
	{
		if($_SESSION['style_edit_mode'] !== 'edit')
		{
			xtc_session_destroy();
		}

		unset($_SESSION['customer_id']);
		unset($_SESSION['customer_default_address_id']);
		unset($_SESSION['customer_first_name']);
		unset($_SESSION['customer_country_id']);
		unset($_SESSION['customer_zone_id']);
		unset($_SESSION['comments']);
		unset($_SESSION['user_info']);
		unset($_SESSION['customers_status']);
		unset($_SESSION['selected_box']);
		unset($_SESSION['shipping']);
		unset($_SESSION['payment']);
		unset($_SESSION['ccard']);

		// GV Code Start
		unset($_SESSION['gv_id']);
		unset($_SESSION['cc_id']);
		// GV Code End

		$_SESSION['cart']->reset();

		// write customers status guest in session again
		require(DIR_WS_INCLUDES . 'write_customers_status.php');
	}

	/**
	 * @param int  $p_customerId
	 * @param bool $p_checkAccountType
	 *
	 * @return bool|int
	 */
	protected function delete_guest_account($p_customerId, $p_checkAccountType = true)
	{
		$deletedCustomerId = false;

		if(DELETE_GUEST_ACCOUNT === 'true')
		{
			$c_customerId = (int)$p_customerId;

			if($p_checkAccountType)
			{
				/* @var GMDataObject $customer */
				$customer = MainFactory::create_object('GMDataObject',
				                                       array(TABLE_CUSTOMERS, array('customers_id' => $c_customerId)));
				$isGuest  = $customer->get_result_count() == 1 && $customer->get_data_value('account_type') == '1';
			}
			else
			{
				$isGuest = true;
			}

			if($isGuest)
			{
				$this->_deleteGuestAccountFromDatabase($c_customerId);

				$deletedCustomerId = $c_customerId;
			}
		}

		return $deletedCustomerId;
	}

	/**
	 * @param int $p_customerId
	 */
	protected function _deleteGuestAccountFromDatabase($p_customerId)
	{
		$c_customerId = (int)$p_customerId;

		xtc_db_query("DELETE FROM " . TABLE_CUSTOMERS . " WHERE customers_id = '" . $c_customerId . "'");
		xtc_db_query("DELETE FROM " . TABLE_ADDRESS_BOOK . " WHERE customers_id = '" . $c_customerId . "'");
		xtc_db_query("DELETE FROM " . TABLE_CUSTOMERS_INFO . " WHERE customers_info_id = '" . $c_customerId . "'");
		xtc_db_query("DELETE FROM " . TABLE_CUSTOMERS_BASKET . " WHERE customers_id = '" . $c_customerId . "'");
		xtc_db_query("DELETE FROM " . TABLE_CUSTOMERS_BASKET_ATTRIBUTES . " WHERE customers_id = '" . $c_customerId
		             . "'");
		xtc_db_query("DELETE FROM " . TABLE_CUSTOMERS_IP . " WHERE customers_id = '" . $c_customerId . "'");
		xtc_db_query("DELETE FROM " . TABLE_CUSTOMERS_WISHLIST . " WHERE customers_id = '" . $c_customerId . "'");
		xtc_db_query("DELETE FROM " . TABLE_CUSTOMERS_WISHLIST_ATTRIBUTES . " WHERE customers_id = '" . $c_customerId
		             . "'");
		xtc_db_query("DELETE FROM " . TABLE_CUSTOMERS_STATUS_HISTORY . " WHERE customers_id = '" . $c_customerId . "'");
		xtc_db_query("DELETE FROM " . TABLE_COUPON_GV_CUSTOMER . " WHERE customer_id = '" . $c_customerId . "'");
		xtc_db_query("DELETE FROM " . TABLE_COUPON_GV_QUEUE . " WHERE customer_id = '" . $c_customerId . "'");
		xtc_db_query("DELETE FROM " . TABLE_WHOS_ONLINE . " WHERE customer_id = '" . $c_customerId . "'");
		xtc_db_query("DELETE FROM " . TABLE_GM_GPRINT_CART_ELEMENTS . " WHERE customers_id = '" . $c_customerId . "'");
		xtc_db_query("DELETE FROM " . TABLE_GM_GPRINT_WISHLIST_ELEMENTS . " WHERE customers_id = '" . $c_customerId
		             . "'");
		xtc_db_query("UPDATE " . TABLE_ORDERS . " SET customers_id = 0 WHERE customers_id = '" . $c_customerId . "'");
		xtc_db_query("UPDATE " . TABLE_GM_GPRINT_UPLOADS . " SET customers_id = 0 WHERE customers_id = '"
		             . $c_customerId . "'");
		xtc_db_query("UPDATE " . TABLE_NEWSLETTER_RECIPIENTS . " SET customers_id = 0 WHERE customers_id = '"
		             . $c_customerId . "'");
		xtc_db_query("UPDATE " . TABLE_COUPON_REDEEM_TRACK . "  SET customer_id = 0 WHERE customer_id = '"
		             . $c_customerId . "'");
		xtc_db_query("UPDATE withdrawals SET customer_id = 0 WHERE customer_id = '" . $c_customerId . "'");
	}

}