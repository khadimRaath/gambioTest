<?php
/* --------------------------------------------------------------
	AmazonAdvPayOrderExtender.inc.php 2014-07-09_1604 mabr
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2014 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/

class AmazonAdvPayOrderExtender extends AmazonAdvPayOrderExtender_parent
{
	public function proceed()
	{
		ob_end_clean();
		$t_order_reference_id = $this->_get_order_reference_id($this->v_data_array['GET']['oID']);
		if($t_order_reference_id === false)
		{
			ob_start();
			parent::proceed();
			return;
		}

		$t_page_url = HTTP_SERVER.DIR_WS_ADMIN.'orders.php?'.http_build_query($this->v_data_array['GET']);
		$messages_ns = 'messages_'.basename(__FILE__);
		if(!isset($_SESSION[$messages_ns])) {
			$_SESSION[$messages_ns] = array();
		}

		$coo_aap = MainFactory::create_object('AmazonAdvancedPayment');

		if($_SERVER['REQUEST_METHOD'] == 'POST') {
			if(isset($_POST['amazonadvpay']))
			{
				$t_poll_required = false;

				if(isset($_POST['amazonadvpay']['close_order']))
				{
					try
					{
						$auth_details = $coo_aap->close_order_reference($t_order_reference_id);
						$_SESSION[$messages_ns][] = '##order_closed';
						$t_poll_required = true;
					}
					catch(Exception $e)
					{
						$_SESSION[$messages_ns][] = '##close_order_error: '.$e->getMessage();
					}
				}

				if(isset($_POST['amazonadvpay']['auth']))
				{
					try
					{
						$auth_details = $coo_aap->authorize_payment($t_order_reference_id, $_POST['amazonadvpay']['auth']['amount'], $_POST['amazonadvpay']['auth']['currency']);
						$_SESSION[$messages_ns][] = '##payment_authorized';
						$t_poll_required = true;
					}
					catch(Exception $e)
					{
						$_SESSION[$messages_ns][] = '##auth_error: '.$e->getMessage();
					}
				}

				if(isset($_POST['amazonadvpay']['closeauth']))
				{
					try
					{
						$close_auth_details = $coo_aap->close_authorization($_POST['amazonadvpay']['closeauth']['auth_ref_id']);
						$_SESSION[$messages_ns][] = '##authorization_closed';
						$t_poll_required = true;
					}
					catch(Exception $e)
					{
						$_SESSION[$messages_ns][] = '##close_auth_error: '.$e->getMessage();
					}
				}

				if(isset($_POST['amazonadvpay']['capture']))
				{
					try
					{
						$t_amazon_auth_id = $_POST['amazonadvpay']['capture']['auth_ref_id'];
						$t_capture_amount = (double)$_POST['amazonadvpay']['capture']['amount'];
						$t_capture_currency = $_POST['amazonadvpay']['capture']['currency'];
						$capture_details = $coo_aap->capture_payment($t_amazon_auth_id, $t_capture_amount, $t_capture_currency);
						$_SESSION[$messages_ns][] = '##payment_captured';
						$t_poll_required = true;
					}
					catch(Exception $e)
					{
						$_SESSION[$messages_ns][] = '##capture_error: '.$e->getMessage();
					}
				}

				if(isset($_POST['amazonadvpay']['refund']))
				{
					try
					{
						$t_amazon_capture_id = $_POST['amazonadvpay']['refund']['capture_id'];
						$t_refund_amount = (double)$_POST['amazonadvpay']['refund']['amount'];
						$t_refund_currency = $_POST['amazonadvpay']['refund']['currency'];
						$refund_details = $coo_aap->refund_payment($t_amazon_capture_id, $t_refund_amount, $t_refund_currency);
						$_SESSION[$messages_ns][] = '##payment_refunded';
						$t_poll_required = true;
					}
					catch(Exception $e)
					{
						$_SESSION[$messages_ns][] = '##refund_error: '.$e->getMessage();
					}
				}

				if(isset($_POST['amazonadvpay']['billing_address']))
				{
					try
					{
						$coo_aap->update_billing_address((int)$this->v_data_array['GET']['oID'], $_POST['amazonadvpay']['billing_address']);
						$_SESSION[$messages_ns][] = '##billing_address_updated';
					}
					catch(Exception $e)
					{
						$_SESSION[$messages_ns][] = '##error_updating_billing_address: '.$e->getMessage();
					}
				}

				if(isset($_POST['amazonadvpay']['update_data']) || $t_poll_required = true)
				{
					try
					{
						$coo_aap->poll_data($t_order_reference_id);
					}
					catch(Exception $e)
					{
						$_SESSION[$messages_ns][] = '##error_updating_order_data: '.$e->getMessage();
					}

				}
			}

			xtc_redirect($t_page_url);
		}


		try
		{
			$t_order_reference_details = $coo_aap->get_order_reference_details($t_order_reference_id);
			// $t_debug = htmlspecialchars($t_order_reference_details->asXML());
			$t_order_reference_status = (string)$t_order_reference_details->GetOrderReferenceDetailsResult->OrderReferenceDetails->OrderReferenceStatus->State;
			$t_order_reference_status_reason = '';
			if(empty($t_order_reference_details->GetOrderReferenceDetailsResult->OrderReferenceDetails->OrderReferenceStatus->ReasonCode) != true)
			{
				$t_order_reference_status_reason = ' ('.(string)$t_order_reference_details->GetOrderReferenceDetailsResult->OrderReferenceDetails->OrderReferenceStatus->ReasonCode.')';
			}
			$t_order_amount = (string)$t_order_reference_details->GetOrderReferenceDetailsResult->OrderReferenceDetails->OrderTotal->Amount;
			$t_order_currency = (string)$t_order_reference_details->GetOrderReferenceDetailsResult->OrderReferenceDetails->OrderTotal->CurrencyCode;
			$t_order_creation_timestamp = (string)$t_order_reference_details->GetOrderReferenceDetailsResult->OrderReferenceDetails->CreationTimestamp;
			$t_order_creation_timestamp_localtime = date('c', strtotime($t_order_creation_timestamp));

			$t_authorization_details = array();
			$t_capture_details = array();
			$t_refund_details = array();
			$t_billing_addresses = array();
			if(isset($t_order_reference_details->GetOrderReferenceDetailsResult->OrderReferenceDetails->IdList))
			{
				$t_authorizations = array();
				foreach($t_order_reference_details->GetOrderReferenceDetailsResult->OrderReferenceDetails->IdList->member as $member)
				{
					$t_authorizations[] = (string)$member;
				}
				foreach($t_authorizations as $t_amz_auth_id)
				{
					$t_authorization_details[$t_amz_auth_id] = $coo_aap->get_authorization_details($t_amz_auth_id, $t_order_reference_id);
					if(isset($t_authorization_details[$t_amz_auth_id]->GetAuthorizationDetailsResult->AuthorizationDetails->AuthorizationBillingAddress))
					{
						$t_billing_address = $t_authorization_details[$t_amz_auth_id]->GetAuthorizationDetailsResult->AuthorizationDetails->AuthorizationBillingAddress;
						$t_billing_addresses[$t_amz_auth_id] = array(
							'name' => (string)$t_billing_address->Name,
							'street' => (string)$t_billing_address->AddressLine2,
							'city' => (string)$t_billing_address->City,
							'postcode' => (string)$t_billing_address->PostalCode,
							'country_iso2' => (string)$t_billing_address->CountryCode,
						);
					}
					if(isset($t_authorization_details[$t_amz_auth_id]->GetAuthorizationDetailsResult->AuthorizationDetails->IdList))
					{
						$t_capture_details[$t_amz_auth_id] = array();
						foreach($t_authorization_details[$t_amz_auth_id]->GetAuthorizationDetailsResult->AuthorizationDetails->IdList->member as $capture_member)
						{
							$t_capture_ref_id = (string)$capture_member;
							$t_capture_details[$t_amz_auth_id][$t_capture_ref_id] = $coo_aap->get_capture_details($t_capture_ref_id, $t_amz_auth_id, $t_order_reference_id);
							$t_refund_details[$t_capture_ref_id] = array();
							if(isset($t_capture_details[$t_amz_auth_id][$t_capture_ref_id]->GetCaptureDetailsResult->CaptureDetails->IdList))
							{
								foreach($t_capture_details[$t_amz_auth_id][$t_capture_ref_id]->GetCaptureDetailsResult->CaptureDetails->IdList->member as $refund_member)
								{
									$t_refund_id = (string)$refund_member;
									$t_refund_details[$t_capture_ref_id][$t_refund_id] = $coo_aap->get_refund_details($t_refund_id, $t_capture_ref_id, $t_amz_auth_id, $t_order_reference_id);
								}
							}
						}
					}
				}
			}
			//$t_debug = print_r($t_refund_details, true);

			$t_messages = $_SESSION[$messages_ns];
			$_SESSION[$messages_ns] = array();

			ob_start();
			include DIR_FS_CATALOG.'admin/html/content/orders_amazonadvpay.php';
			echo $coo_aap->replaceLanguagePlaceholders(ob_get_clean());
		}
		catch(Exception $e)
		{
			$t_debug = $e->getMessage();
			echo '<p class="amazonadvpay_error">'.$t_debug.'</p>';
		}
		ob_start();
		$this->addContent();
		parent::proceed();
	}

	protected function _get_order_reference_id($p_orders_id)
	{
		$t_query =
			'SELECT
				`order_reference_id`
			FROM
				`amzadvpay_orders`
			WHERE
				`orders_id` = \':orders_id\'';
		$t_query = strtr($t_query, array(':orders_id' => (int)$p_orders_id));
		$t_order_reference_id = false;
		$t_result = xtc_db_query($t_query);
		while($t_row = xtc_db_fetch_array($t_result))
		{
			$t_order_reference_id = $t_row['order_reference_id'];
		}
		return $t_order_reference_id;
	}
}