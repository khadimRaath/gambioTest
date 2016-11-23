<?php
/* --------------------------------------------------------------
	AmazonIPNAjaxHandler.inc.php 2014-07-21 mabr
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2014 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/

class AmazonIPNAjaxHandler extends AjaxHandler
{
	protected $_coo_aap;
	protected $_lock_file_name;
	protected $_lock_file_handle;
	protected $_request_id;

	public function get_permission_status($p_customers_id = null)
	{
		$this->_coo_aap = MainFactory::create_object('AmazonAdvancedPayment');
		$t_is_enabled = $this->_coo_aap->is_enabled() && $this->_coo_aap->ipn_enabled == true && $this->_coo_aap->erp_mode == false;
		return $t_is_enabled;
	}

	public function proceed()
	{
		$t_success = false;
		$t_output_array = array();
		$this->_lock_file_name = DIR_FS_CATALOG.'cache/amazonipn-'.FileLog::get_secure_token().'.lock';
		$this->_request_id = uniqid();

		//$this->_coo_aap->log("IPN ".$this->_request_id." endpoint called, POST:\n".print_r($this->v_data_array['POST'], true));
		//$this->_coo_aap->log("IPN ".$this->_request_id." endpoint called, GET:\n".print_r($this->v_data_array['GET'], true));
		$t_input = file_get_contents('php://input');
		file_put_contents(DIR_FS_CATALOG.'cache/ipn_input', $t_input);
		$this->_coo_aap->log("IPN ".$this->_request_id." endpoint called, input:\n".$t_input);

		$t_ipn_data = json_decode($t_input);
		$t_message_valid = $this->_check_signature($t_ipn_data);
		if($t_message_valid == true)
		{
			$this->_coo_aap->log('IPN '.$this->_request_id.' received valid message:'.PHP_EOL.print_r($t_ipn_data, true));

			if($t_ipn_data->Type != 'Notification')
			{
				$this->_coo_aap->log("IPN ".$this->_request_id." received unsupported type ".$t_ipn_data->Type);
			}
			else
			{
				if(empty($t_ipn_data->Message) != true)
				{
					$this->_process_message($t_ipn_data->Message);
				}
			}
		}

		return $t_success;
	}

	protected function _process_message($p_message_json)
	{
		try
		{
			$this->_acquire_lock();
			$t_message = json_decode($p_message_json);
			$t_notification_type = $t_message->NotificationType;

			$t_notification_data_xml = $t_message->NotificationData;
			$t_notification_data = simplexml_load_string($t_notification_data_xml);
			$this->_coo_aap->log("IPN ".$this->_request_id." notification data:\n".$t_notification_data->asXML());

			$t_notification_type_method = '_process_'.$t_notification_type;
			if(method_exists($this, $t_notification_type_method))
			{
				$this->$t_notification_type_method($t_notification_data);
			}
			else
			{
				$this->_coo_aap->log('IPN '.$this->_request_id.' Notification type '.$t_notification_type.' unhandled, ignoring message');
			}
			$this->_release_lock();
		}
		catch(Exception $e)
		{
			$this->_coo_aap->log('IPN '.$this->_request_id.' ERROR: '.$e->getMessage());
		}
	}

	protected function _acquire_lock()
	{
		$t_delay_unit = 100; // milliseconds
		$t_delay_exponent = 0;
		$t_time_start = microtime(true);
		$t_max_time = 10; // seconds
		$t_acquired = false;

		while($t_acquired === false && (microtime(true) - $t_time_start < $t_max_time))
		{
			$this->_lock_file_handle = fopen($this->_lock_file_name, 'w');
			if(flock($this->_lock_file_handle, LOCK_EX))
			{
				fwrite($this->_lock_file_handle, $this->_request_id.' '.date('c')."\n");
				fflush($this->_lock_file_handle);
				$t_acquired = true;
			}
			else
			{
				$t_usleep_time = $t_delay_unit * pow(2, $t_delay_exponent) * 1000;
				usleep($t_usleep_time);
				$t_delay_exponent++;
			}
		}

		if($t_acquired === false)
		{
			throw new Exception('Could not acquire lock');
		}
	}

	protected function _release_lock()
	{
		flock($this->_lock_file_handle, LOCK_UN);
		fclose($this->_lock_file_handle);
	}

	protected function _process_PaymentAuthorize($p_auth_details)
	{
		$t_amazon_authorization_id = (string)$p_auth_details->AuthorizationDetails->AmazonAuthorizationId;
		$t_authorization_status = (string)$p_auth_details->AuthorizationDetails->AuthorizationStatus->State;
		if(empty($p_auth_details->AuthorizationDetails->AuthorizationStatus->ReasonCode) != true)
		{
			$t_authorization_status .= ' ('.(string)$p_auth_details->AuthorizationDetails->AuthorizationStatus->ReasonCode.')';
		}
		$this->_coo_aap->log('IPN '.$this->_request_id.' received PaymentAuthorize for '.$t_amazon_authorization_id.', state '.$t_authorization_status);
		try
		{
			$t_orders_id = $this->_coo_aap->get_orders_id_for_authorization_reference_id($t_amazon_authorization_id);
			if($t_orders_id === false)
			{
				throw new Exception('Cannot match authorization '.$t_amazon_authorization_id.' to any order');
			}
			$t_order_reference_id = $this->_coo_aap->get_order_reference_for_orders_id($t_orders_id);
			if($t_order_reference_id === false)
			{
				throw new Exception('Cannot match orders_id '.$t_orders_id.' to OrderReference');
			}
			$this->_coo_aap->log('IPN '.$this->_request_id.' updating OrderReference '.$t_order_reference_id);
			$this->_coo_aap->get_order_reference_details($t_order_reference_id, true);
			$this->_coo_aap->log('IPN '.$this->_request_id.' updating Authorization '.$t_amazon_authorization_id);
			$this->_coo_aap->get_authorization_details($t_amazon_authorization_id, $t_order_reference_id, true);
			$this->_coo_aap->log('IPN '.$this->_request_id.' processed PaymentAuthorize');
		}
		catch(Exception $e)
		{
			$this->_coo_aap->log('IPN '.$this->_request_id.' encountered ERROR: '.$e->getMessage());
		}
	}

	protected function _process_PaymentCapture($p_capture_details)
	{
		$t_capture_id = (string)$p_capture_details->CaptureDetails->AmazonCaptureId;
		$t_amazon_authorization_id = $this->_coo_aap->get_authorization_reference_id_for_capture_reference_id($t_capture_id);
		$this->_coo_aap->log('IPN: '.$this->_request_id.' received PaymentCapture for '.$t_capture_id.', authorization '.$t_amazon_authorization_id);
		try
		{
			$t_orders_id = $this->_coo_aap->get_orders_id_for_authorization_reference_id($t_amazon_authorization_id);
			if($t_orders_id === false)
			{
				throw new Exception('Cannot match authorization '.$t_amazon_authorization_id.' to any order');
			}
			$t_order_reference_id = $this->_coo_aap->get_order_reference_for_orders_id($t_orders_id);
			if($t_order_reference_id === false)
			{
				throw new Exception('Cannot match orders_id '.$t_orders_id.' to OrderReference');
			}
			$this->_coo_aap->log('IPN '.$this->_request_id.' updating OrderReference '.$t_order_reference_id);
			$this->_coo_aap->get_order_reference_details($t_order_reference_id, true);
			$this->_coo_aap->log('IPN '.$this->_request_id.' updating Authorization '.$t_amazon_authorization_id);
			$this->_coo_aap->get_authorization_details($t_amazon_authorization_id, $t_order_reference_id, true);
			$this->_coo_aap->log('IPN '.$this->_request_id.' updating Capture '.$t_capture_id);
			$this->_coo_aap->get_capture_details($t_capture_id, $t_amazon_authorization_id, $t_order_reference_id, true);
			$this->_coo_aap->log('IPN '.$this->_request_id.' processed PaymentCapture');
		}
		catch(Exception $e)
		{
			$this->_coo_aap->log('IPN '.$this->_request_id.' encountered ERROR: '.$e->getMessage());
		}
	}

	protected function _process_PaymentRefund($p_refund_details)
	{
		$t_amazon_refund_id = (string)$p_refund_details->RefundDetails->AmazonRefundId;
		$t_refund_status = (string)$p_refund_details->RefundDetails->RefundStatus->State;
		$this->_coo_aap->log('IPN '.$this->_request_id.' received PaymentRefund for '.$t_amazon_refund_id.', state now '.$t_refund_status);
	}

	protected function _process_OrderReferenceNotification($p_order_reference_details)
	{
		$t_order_reference_id = (string)$p_order_reference_details->OrderReference->AmazonOrderReferenceId;
		$this->_coo_aap->log('IPN '.$this->_request_id.' received OrderReferenceNotification, updating local data');
		try
		{
			$this->_coo_aap->poll_data($t_order_reference_id);
			$this->_coo_aap->log('IPN '.$this->_request_id.' updated order data for '.$t_order_reference_id);
		}
		catch(Exception $e)
		{
			$this->_coo_aap->log('IPN '.$this->_request_id.' encountered ERROR: '.$e->getMessage());
		}
	}


	/**
	Check cryptographic signature on IPN message
	*/
	protected function _check_signature($p_ipn_data)
	{
		if(isset($p_ipn_data->SignatureVersion) == false || $p_ipn_data->SignatureVersion != '1')
		{
			$this->_coo_aap->log('IPN '.$this->_request_id.' message received with unsupported SignatureVersion; aborting');
			die('ERROR: unsupported signature version');
		}
		$t_signature_names = array('Message','MessageId','Subject','Timestamp','TopicArn','Type');
		$t_signed_string = '';
		foreach($t_signature_names as $signature_name)
		{
			if($signature_name == 'Subject' && empty($p_ipn_data->Subject) == true)
			{
				continue;
			}
			$t_signed_string .= $signature_name ."\n". $p_ipn_data->$signature_name ."\n";
		}
		$t_signature = base64_decode($p_ipn_data->Signature);
		$t_certificate = @file_get_contents($p_ipn_data->SigningCertURL);
		if($t_certificate === false)
		{
			$this->_coo_aap->log('IPN '.$this->_request_id.' could not retrieve certificate from '.$p_ipn_data->SigningCertURL.' -> FAILED signature check');
			return false;
		}
		$t_pubkey = openssl_pkey_get_public($t_certificate);
		if($t_pubkey === false)
		{
			$this->_coo_aap->log('IPN '.$this->_request_id.' could not extract public key for signature verification');
			return false;
		}
		else
		{
			$t_signature_verify_result = openssl_verify($t_signed_string, $t_signature, $t_pubkey, OPENSSL_ALGO_SHA1);
			$this->_coo_aap->log('IPN '.$this->_request_id.' signature check result: '.$t_signature_verify_result);
			if($t_signature_verify_result == 1)
			{
				$this->_coo_aap->log('IPN '.$this->_request_id.' signature valid');
				return true;
			}
		}
		return false;
	}


}