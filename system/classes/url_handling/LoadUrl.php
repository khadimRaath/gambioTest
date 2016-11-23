<?php
/* --------------------------------------------------------------
   LoadUrl.php 2015-10-02
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class LoadUrl
 */
class LoadUrl
{
	protected $url;
	protected $allowedUrls = array('http://news.gambio.de/',
									'https://news.gambio.de/',
									'http://news.gambio-support.de/',
									'https://news.gambio-support.de/',
									'https://www.gambio-support.de/',
									'http://api.ekomi.de/');


	/**
	 * load the url
	 * 
	 * @param string $p_url
	 * @param array  $headerArray
	 * @param string $p_iframeParams
	 * @param bool|null   $p_sslVerifypeer
	 * @param bool|null   $p_sslVerifyhost
	 *
	 * @return bool|string
	 */
	public function load_url($p_url, array $headerArray = array(), $p_iframeParams = '', $p_sslVerifypeer = null, $p_sslVerifyhost = null)
	{
		// link to news server
		$this->url = (string)$p_url;
		
		if(strpos($this->url, 'http') !== 0)
		{
			$url = base64_decode($this->url);

			if(strpos($url, 'http') === 0)
			{
				$this->url = $url;
			}
		}
		
		$urlAllowed = false;
		foreach($this->allowedUrls as $url)
		{
			if(strpos($this->url, $url) === 0)
			{
				$urlAllowed = true;
			}
		}
		
		if(!$urlAllowed)
		{
			return false;
		}

		// check what to use - curl, stream or, last chance, iframe
		if(function_exists('curl_init') && function_exists('curl_exec'))
		{
			$data = $this->_useCurl($headerArray, $p_sslVerifypeer, $p_sslVerifyhost);
		}
		else
		{
			$data = $this->_useStream($headerArray);

			if($data === false)
			{
				$data = $this->use_iframe($p_iframeParams);
			}
		}

		return $data;
	}


	/**
	 * get url by curl
	 * 
	 * @param array $headerArray
	 * @param bool|null  $p_sslVerifypeer
	 * @param bool|null  $p_sslVerifyhost
	 *
	 * @return bool|string
	 */
	protected function _useCurl(array $headerArray = array(), $p_sslVerifypeer = null, $p_sslVerifyhost = null)
	{
		// init curl
		$ch = curl_init();
		
		// set curl options
		curl_setopt($ch, CURLOPT_URL, $this->url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FAILONERROR, true);
		curl_setopt($ch, CURLOPT_TIMEOUT, 5);

		if(substr((string)$this->url, 0, 5) == 'https')
		{
			if($p_sslVerifypeer !== null)
			{
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, (bool)$p_sslVerifypeer);
			}

			if($p_sslVerifyhost !== null)
			{
				curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, (bool)$p_sslVerifyhost);
			}
		}

		if(!empty($headerArray) && is_array($headerArray))
		{
			foreach($headerArray as $headerData)
			{
				curl_setopt($ch, CURLOPT_HTTPHEADER, array($headerData));
			}
		}
		
		// execute curl
		$data = curl_exec($ch);
		
		// if curl error, return false
		if(curl_errno($ch))
		{
			$data = $this->use_iframe('');
		}
		
		// close curl
		curl_close($ch);

		return $data;
	}


	/**
	 * get url by stream
	 * 
	 * @param array $headerArray
	 *
	 * @return bool|string
	 */
	protected function _useStream(array $headerArray = array())
	{
		// set options
		$options = array('http' => array('method' => 'POST',
										 'header' => "Content-type: application/x-www-form-urlencoded\r\n"));

		// get content
		$request  = stream_context_create($options);
		$response = @file_get_contents($this->url, false, $request);

		if($response)
		{
			$data = $response;
		}
		else
		{
			$data = $this->use_iframe('');
		}

		return $data;
	}


	/**
	 *  get url in iframe
	 * 
	 * @param $p_iframeParams
	 *
	 * @return string
	 */
	protected function use_iframe($p_iframeParams)
	{
		$c_iframeParams = 'width="100%" height="1000px" scrolling="yes" frameborder="0"';
		if(!empty($p_iframeParams))
		{
			$c_iframeParams = (string)$p_iframeParams;
		}

		$data = '<iframe src="' . $this->url . '" ' . $c_iframeParams . '></iframe>';

		return $data;
	}
}