<?php
/* --------------------------------------------------------------
   LogoffContentControl 2016-03-16
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------


   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(logoff.php,v 1.12 2003/02/13); www.oscommerce.com
   (c) 2003	 nextcommerce (logoff.php,v 1.16 2003/08/17); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: logoff.php 1071 2005-07-22 16:36:53Z mz $)

   Released under the GNU General Public License
   -----------------------------------------------------------------------------------------
   Third Party contribution:

   Credit Class/Gift Vouchers/Discount Coupons (Version 5.10)
   http://www.oscommerce.com/community/contributions,282
   Copyright (c) Strider | Strider@oscworks.com
   Copyright (c  Nick Stanko of UkiDev.com, nick@ukidev.com
   Copyright (c) Andre ambidex@gmx.net
   Copyright (c) 2001,2002 Ian C Wilson http://www.phesis.org


   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

MainFactory::load_class('DataProcessing');

/**
 * Class LogoffContentControl
 */
class LogoffContentControl extends DataProcessing
{
	/** @var CustomerWriteService $customerWriteService */
	protected $customerWriteService;
	
	/**
	 * @return bool
	 */
	public function proceed()
	{
		//delete guests from database
		if(!isset($this->v_data_array['GET']['logoff']))
		{
			if(is_numeric($_SESSION['customer_id']) && (int)$_SESSION['customer_id'] == (double)$_SESSION['customer_id'] && $_SESSION['customer_id'] > 0)
			{
				$this->delete_guest_account($_SESSION['customer_id']);
				$this->reset_session();
			}

			$redirectUrl = $this->_buildRedirectUrl();

			if(isset($_SESSION['language_code'])) // Keep language selection after logout.
			{
				$redirectUrl .= '&language=' . $_SESSION['language_code'];
			}
			
			$this->set_redirect_url($redirectUrl);
		}
		else
		{
			$this->_resetNotificationsStatus();
		}

		/* @var LogoffContentView $logoffContentView */
		$logoffContentView = MainFactory::create_object('LogoffContentView');
		$this->v_output_buffer = $logoffContentView->get_html();
		
		return true;
	}
	
	protected function reset_session()
	{
		if($_SESSION['style_edit_mode'] !== 'edit' && $_SESSION['STYLE_EDIT_AUTH'] !== true)
		{
			xtc_session_destroy();
		}

		unset($_SESSION['customer_id']);
		unset($_SESSION['customer_default_address_id']);
		unset($_SESSION['customer_first_name']);
		unset($_SESSION['customer_country_id']);
		unset($_SESSION['customer_zone_id']);
		unset($_SESSION['customer_b2b_status']);
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
				$customer = MainFactory::create_object('GMDataObject', array(TABLE_CUSTOMERS, array('customers_id' => $c_customerId)));
				$isGuest = $customer->get_result_count() == 1 && $customer->get_data_value('account_type') == '1';
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
	 * @return array
	 */
	public function delete_unused_guest_accounts()
	{
		$deletedCustomersArray = array();
		
		if(DELETE_GUEST_ACCOUNT == 'true')
		{
			$query = "SELECT 
							c.customers_id,
							w.customer_id
						FROM
							" . TABLE_CUSTOMERS . " c
						LEFT JOIN " . TABLE_WHOS_ONLINE . " AS w ON (c.customers_id = w.customer_id)
						WHERE
							c.customers_status = " . (int)DEFAULT_CUSTOMERS_STATUS_ID_GUEST . " AND
							c.account_type = 1 AND
							w.customer_id IS NULL";
			$result = xtc_db_query($query, 'db_link', false);
			while($resultArray = xtc_db_fetch_array($result))
			{
				$deletedCustomersArray[] = $this->delete_guest_account($resultArray['customers_id'], false);			
			}
		}
		
		return $deletedCustomersArray;
	}


	/**
	 * @return string
	 */
	protected function _buildRedirectUrl()
	{
		$redirectUrl = 'logoff.php?logoff=1';

		if(isset($_SESSION['hide_topbar']) && $_SESSION['hide_topbar'])
		{
			$redirectUrl .= '&hide_topbar=1';
		}

		if(isset($_SESSION['hide_popup_notification']) && $_SESSION['hide_popup_notification'])
		{
			$redirectUrl .= '&hide_popup_notification=1';
		}

		return $redirectUrl;
	}


	protected function _resetNotificationsStatus()
	{
		if(isset($this->v_data_array['GET']['hide_topbar']) && $this->v_data_array['GET']['hide_topbar'] === '1')
		{
			$_SESSION['hide_topbar'] = true;
		}

		if(isset($this->v_data_array['GET']['hide_popup_notification']) &&
		   $this->v_data_array['GET']['hide_popup_notification'] === '1'
		)
		{
			$_SESSION['hide_popup_notification'] = true;
		}
	}


	/**
	 * @param int $p_customerId
	 */
	protected function _deleteGuestAccountFromDatabase($p_customerId)
	{
		if($this->customerWriteService === null)
		{
			/** @var CustomerWriteService */
			$this->customerWriteService = StaticGXCoreLoader::getService('CustomerWrite');
		}

		$this->customerWriteService->deleteCustomerById(new IdType((int)$p_customerId));
	}

}