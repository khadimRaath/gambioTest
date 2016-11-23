<?php
/* --------------------------------------------------------------
	AmazonMWSRequest.inc.php 2014-07-30_1220 mabr
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2014 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/

class AmazonMWSRequest
{
	protected $_endpoint_url;
	protected $_action;
	protected $_domain_name;
	protected $_uri_path;
	protected $_aws_access_key_id;
	protected $_secret_key;
	protected $_http_action;
	protected $_parameters;
	protected $_marketplace_ids;
	protected $_seller_id;
	protected $_timestamp;
	protected $_version;
	protected $_signature_method;
	protected $_signature_version;
	protected $_signature;
	protected $_query_string;
	protected $_user_agent;
	protected $_request_timeout;

	protected $_logger;

	public function __construct($p_endpoint_url = null, $p_aws_access_key = null, $p_secret_key = null, $p_seller_id = null, $p_parameters = null, $p_http_action = 'POST')
	{
		$this->_logger = new FileLog('payment-amzmwsrequests', true);
		$this->set_endpoint_url($p_endpoint_url);
		$this->set_aws_access_key_id($p_aws_access_key);
		$this->set_secret_key($p_secret_key);
		$this->set_http_action($p_http_action);
		$this->set_seller_id($p_seller_id);
		$this->set_marketplace_ids(array());
		$this->_timestamp = time();
		$this->_signature_version = '2';
		$this->_signature_method = 'HmacSHA256';
		$this->set_parameters($p_parameters);
		$this->_user_agent = 'Gambio/GX2 (Language=PHP/'.PHP_VERSION.')';
		$this->_request_timeout = 10;
	}

	public function log($message)
	{
		list($t_millis, $t_secs) = explode(' ', microtime());
		$t_timestamp = sprintf('%s.%03d', date('Y-m-d H:i:s', $t_secs), round($t_millis * 1000));
		$t_message = $t_timestamp.' | '.$message.PHP_EOL;
		$this->_logger->write($t_message);
	}

	public function set_endpoint_url($p_endpoint_url)
	{
		$t_endpoint_url = trim((string)$p_endpoint_url);
		if(empty($t_endpoint_url))
		{
			throw new AmazonMWSRequestException('Endpoint URL must not be empty');
		}
		$this->_endpoint_url = $t_endpoint_url;
		$t_parsed_ep_url = parse_url($t_endpoint_url);
		if($t_parsed_ep_url === false)
		{
			throw new AmazonMWSRequestException('Endpoint URL is malformed');
		}
		$this->_domain_name = strtolower($t_parsed_ep_url['host']);
		$this->_uri_path = $t_parsed_ep_url['path'];
		$t_api_version_re = '/.*(\d{4}-\d{2}-\d{2}).*/';
		if(preg_match($t_api_version_re, $t_endpoint_url, $t_matches) == 1)
		{
			$this->_version = $t_matches[1];
		}
	}

	public function set_aws_access_key_id($p_aws_access_key)
	{
		$this->_aws_access_key_id = $p_aws_access_key;
	}

	public function set_secret_key($p_secret_key)
	{
		$this->_secret_key = $p_secret_key;
	}

	public function set_http_action($p_http_action = 'GET')
	{
		$t_http_action = strtoupper((string)$p_http_action);
		$t_allowed_http_actions = array('GET', 'POST');
		if(in_array($t_http_action, $t_allowed_http_actions) === false)
		{
			throw new AmazonMWSRequestException('Action not allowed');
		}
		$this->_http_action = $t_http_action;
	}

	public function set_parameters($p_parameters = null)
	{
		$t_parameters = array();
		if(is_array($p_parameters))
		{
			$t_parameters = $p_parameters;
		}
		$t_parameters['AWSAccessKeyId'] = $this->_aws_access_key_id;
		$t_parameters['Action'] = $this->_action;
		$t_parameters['SellerId'] = $this->_seller_id;
		$t_parameters['SignatureMethod'] = $this->_signature_method;
		$t_parameters['SignatureVersion'] = $this->_signature_version;
		$t_parameters['Timestamp'] = date('c', $this->_timestamp);
		$t_parameters['Version'] = $this->_version;
		uksort($t_parameters, 'strnatcmp');
		$this->_parameters = $t_parameters;
	}

	public function set_seller_id($p_seller_id)
	{
		$t_seller_id = trim((string)$p_seller_id);
		$this->_seller_id = $t_seller_id;
	}

	public function set_marketplace_ids(array $p_marketplace_ids)
	{
		$this->_marketplace_ids = $p_marketplace_ids;
	}

	public function set_version($p_version)
	{
		$this->_version = $p_version;
	}

	public function set_action($p_action)
	{
		$this->_action = $p_action;
	}

	/* ------------------------------------------------------------------------------------------------------ */

	public function proceed($p_action, $p_parameters = null)
	{
		$this->set_action($p_action);
		if(is_array($p_parameters))
		{
			$t_parameters = $p_parameters;
		}
		else
		{
			$t_parameters = array();
		}
		$this->set_parameters($t_parameters);
		$this->_query_string = $this->_make_query_string();
		$this->_signature = $this->_make_signature_hmac256();
		$this->_parameters['Signature'] = $this->_signature;

		$curl_options = array(
				CURLOPT_TIMEOUT => $this->_request_timeout,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_USERAGENT => $this->_user_agent,
			);
		$curl_options[CURLOPT_URL] = $this->_endpoint_url;
		if($this->_http_action == 'GET')
		{
			$curl_options[CURLOPT_URL] .= '?'.$this->_query_string.'&Signature='.rawurlencode($this->_signature);
			$this->log('GET URL: '.$curl_options[CURLOPT_URL]);
		}
		elseif($this->_http_action == 'POST')
		{
			$curl_options[CURLOPT_POST] = true;
			$curl_options[CURLOPT_POSTFIELDS] = $this->_query_string.'&Signature='.rawurlencode($this->_signature);
			$this->log('POST data: '.$curl_options[CURLOPT_POSTFIELDS]);
		}
		$t_ch = curl_init();
		curl_setopt_array($t_ch, $curl_options);
		$t_response = curl_exec($t_ch);
		$t_errno = curl_errno($t_ch);
		$t_error = curl_error($t_ch);
		$t_info = curl_getinfo($t_ch);
		curl_close($t_ch);
		if($t_errno > 0)
		{
			throw new AmazonMWSRequestException('ERROR: '.$t_errno.' '.$t_error);
		}
		return $t_response;
	}

	protected function _make_query_string()
	{
		$t_encoded_parameters = array();
		foreach($this->_parameters as $param_key => $param_value)
		{
			$t_encoded_key = rawurlencode($param_key);
			$t_encoded_value = rawurlencode($param_value);
			$t_encoded_parameters[] = $t_encoded_key.'='.$t_encoded_value;
		}
		$t_encoded_parameters_string = implode('&', $t_encoded_parameters);
		return $t_encoded_parameters_string;
	}

	protected function _make_signature_hmac256()
	{
		$t_string_to_sign = $this->_http_action ."\n". $this->_domain_name ."\n". $this->_uri_path ."\n". $this->_query_string;
		$t_hash = hash_hmac('sha256', $t_string_to_sign, $this->_secret_key, true);
		$t_hash_b64 = base64_encode($t_hash);
		return $t_hash_b64;
	}
}

class AmazonMWSRequestException extends Exception {}

