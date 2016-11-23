<?php
/* --------------------------------------------------------------
	CurlRestResponse.inc.php 2014-12-10
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2014 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/

/**
 * refined RestResponse class used by the cURL backend to store additional metadata
 */
class RestCurlResponse extends RestResponse
{
	/**
	 * @var array $curl_info as returnd by curl_getinfo()
	 */
	protected $curl_info;

	/**
	 * sets cURL info
	 * @param array $curlinfo as returned by curl_getinfo()
	 */
	public function setCurlInfo($curlinfo)
	{
		if(is_array($curlinfo))
		{
			$this->curl_info = $curlinfo;
		}
		else
		{
			throw new RestException('cURL info must be an array, not '.gettype($curlinfo));
		}
	}

	/**
	 * return cURL metadata array
	 * @return array cURL metadata
	 */
	public function getCurlInfo()
	{
		return $this->curl_info;
	}
}
