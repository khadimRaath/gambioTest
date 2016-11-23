<?php
/* --------------------------------------------------------------
	ConnectChecker.inc.php 2014-02-27 mabr
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2014 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/

class ConnectCheckerCurlMissingException extends Exception {}
class ConnectCheckerTimeoutException extends Exception {}
class ConnectCheckerConnectionException extends Exception {}

class ConnectChecker 
{
	public function __construct()
	{
		if(function_exists('curl_init') === false)
		{
			throw new ConnectCheckerCurlMissingException();
		}
	}

	public function check_connect($p_url, $p_timeout = 5, $p_post = false, $p_postdata = null, $p_extra_options = null)
	{
		$curl_options = array(
				CURLOPT_URL => $p_url,
				CURLOPT_TIMEOUT => $p_timeout,
				CURLOPT_RETURNTRANSFER => true,
			);
		if(is_array($p_extra_options))
		{
			$curl_options = array_merge($curl_options, $p_extra_options);
		}
		if($p_post == true)
		{
			$curl_options[CURLOPT_POST] = true;
			if($p_postdata !== null)
			{
				$curl_options[CURLOPT_POSTFIELDS] = $p_postdata;
			}
		}

		$t_ch = curl_init();
		curl_setopt_array($t_ch, $curl_options);
		$t_response = curl_exec($t_ch);
		$t_curl_errno = curl_errno($t_ch);
		$t_curl_error = curl_error($t_ch);
		$t_curl_info = curl_getinfo($t_ch);
		curl_close($t_ch);

		if($t_curl_errno > 0)
		{
			throw new ConnectCheckerConnectionException($t_curl_error.' ('.$t_curl_errno.')');
		}

		$t_connectinfo = array(
				'url' => $p_url,
				'errno' => $t_curl_errno,
				'error' => $t_curl_error,
				'info'=> $t_curl_info,
				'response' => $t_response,
			);
		return $t_connectinfo;
	}
}

