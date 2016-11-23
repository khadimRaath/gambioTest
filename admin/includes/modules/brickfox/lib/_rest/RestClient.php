<?php

/* --------------------------------------------------------------
  $Id: RestClient.php 0.1 2010-07-16 $

  brickfox Multichannel eCommerce
  http://www.brickfox.de

  Copyright (c) 2010 brickfox by NETFORMIC GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  -------------------------------------------------------------- */

class brickfox_RestClient
{
	var $_bfVersion = 'GX2v1.1';
	var $_dataFile;
	var $_xmlFile;
	var $_brickfoxConfiguration;
	var $_host;
	var $_path = '/BFpublic/XmlInterface/rest/server';
	var $_resultFile;
	var $_resultMessage;

	/**
	 * Constructor
	 *
	 * @param Brickfox_Lib_BrickfoxConfiguration $brickfoxConfiguration
	 */
	function brickfox_RestClient(Brickfox_Lib_BrickfoxConfiguration $brickfoxConfiguration)
	{
		$this->setBrickfoxConfiguration($brickfoxConfiguration);
		$url = parse_url($brickfoxConfiguration->getUrl());
		$this->setHost($url['host']);
	}

	/**
	 * get Brickfox version
	 *
	 * @return string
	 */
	function getBfVersion()
	{
		return $this->_bfVersion;
	}

	/**
	 * get Brickfox Configuration
	 *
	 * @return Brickfox_Lib_BrickfoxConfiguration
	 */
	function getBrickfoxConfiguration()
	{
		return $this->_brickfoxConfiguration;
	}

	/**
	 * set Brickfox Configuration
	 *
	 * @param Brickfox_Lib_BrickfoxConfiguration $brickfoxConfiguration
	 */
	function setBrickfoxConfiguration($brickfoxConfiguration)
	{
		$this->_brickfoxConfiguration = $brickfoxConfiguration;
	}

	/**
	 * get result file
	 * @return string
	 */
	function getResultFile()
	{
		return $this->_resultFile;
	}

	/**
	 * set resultFile
	 *
	 * @param string $resultFile
	 */
	function setResultFile($resultFile)
	{
		$this->_resultFile = $resultFile;
	}

	/**
	 * get result message
	 *
	 * @return string
	 */
	function getResultMessage()
	{
		return $this->_resultMessage;
	}

	/**
	 * set result message
	 *
	 * @param string $resultMessage
	 */
	function setResultMessage($resultMessage)
	{
		$this->_resultMessage = $resultMessage;
	}

	/**
	 * get host
	 *
	 * @return string
	 */
	function getHost()
	{
		return $this->_host;
	}

	/**
	 * set host
	 *
	 * @param string $host
	 */
	function setHost($host)
	{
		$this->_host = $host;
	}

	/**
	 * get data filename
	 *
	 * @return string
	 */
	function getDataFile()
	{
		return $this->_dataFile;
	}

	/**
	 * set data filename
	 *
	 * @param string $dataFile
	 */
	function setDataFile($dataFile)
	{
		$this->_dataFile = $dataFile;
	}

	/**
	 * get XML filename
	 * @return string
	 */
	function getXmlFile()
	{
		return $this->_xmlFile;
	}

	/**
	 * set XML filename
	 *
	 * @param string $xmlFile
	 */
	function setXmlFile($xmlFile)
	{
		$this->_xmlFile = $xmlFile;
	}

	/**
	 * get path
	 *
	 * @return string
	 */
	function getPath()
	{
		return $this->_path;
	}

	/**
	 * __call()
	 *
	 * Magic Method to be called every time the Class gets called.
	 * Checks for method, HTTP-method and arguments to pass to the appropriate
	 * _*Get-Method
	 *
	 * @param string $method The name of the called method
	 * @param array $args The arguments passed to the method.
	 * The first one is the HTTP-Method as string.
	 * The second one is an array (or string if only one required) of arguments
	 * to pass to the rest method. Either associative or named arrays are
	 * accepted.
	 * @return string Parsed REST-answer
	 */
	function __call($method, $args)
	{
		if (isset($args[0])) {
			switch (strtolower($args[0])) {
				case 'get':
					$restMethod = '_restGet';
					break;
				case 'post':
					$restMethod = '_restPost';
					break;
				default:
					throw new Exception('Unknown HTTP-Method');
			}

			$arguments = array();
			if (isset($args[1])) {
				if (is_array($args[1])) {
					$arguments = $args[1];
				} else {
					$arguments = array('arg2' => (string)$args[1]);
				}
			}
			unset($args);

			$this->$restMethod($method, $arguments);
			unset($arguments);
		} else {
			throw new Exception('No HTTP-Method given');
		}
	}

	/**
	 * transfer XML file to stream
	 *
	 * @param string $exportXmlFile
	 * @param string $exportRestFile
	 */
	function transferToStream($exportXmlFile, $exportRestFile)
	{
		$this->setXmlFile($exportXmlFile);
		$this->setDataFile($exportRestFile);
		$fp = fopen($exportXmlFile, 'r');

		stream_filter_register('urlencode.*', 'Brickfox_Lib_URLEncodeFilter');
		stream_filter_append($fp, 'convert.base64-encode');
		stream_filter_append($fp, 'urlencode.foo');

		$dataStream = fopen($exportRestFile, 'w');
		fwrite($dataStream, "&data=");
		stream_copy_to_stream($fp, $dataStream);
		fclose($fp);

		unlink($exportXmlFile);
		fclose($dataStream);
	}

	/**
	 * _restGet()
	 *
	 * Build a HTTP-GET-request and pass it to _restRequest()
	 *
	 * @param string $method
	 * @param array $arguments
	 * @return string Parsed REST-answer
	 */
	function _restGet($method, $arguments)
	{
		$path = $this->getPath() . '/username/' . $this->getBrickfoxConfiguration()->getUsername();
		$path .= '/password/' . md5($this->getBrickfoxConfiguration()->getPassword());
		$path .= '?method=' . $method;

		$path .= $this->getDataStringByArgments($arguments);

		$request = "GET " . $path . " HTTP/1.1\r\n";
		$request .= "Host: " . $this->getHost() . "\r\n";
		$request .= "Connection: Close\r\n\r\n";

		$this->_restRequest($request);
	}

	/**
	 * _restPost()
	 *
	 * Build a HTTP-POST-request and pass it to _restRequest()
	 *
	 * @param string $method
	 * @param array $arguments
	 * @return string Parsed REST-answer
	 */
	function _restPost($method, $arguments)
	{
		$dataString = 'username=' . $this->getBrickfoxConfiguration()->getUsername();
		$dataString .= '&password=' . md5($this->getBrickfoxConfiguration()->getPassword());
		$dataString .= "&method=" . $method;
		$dataString .= $this->getDataStringByArgments($arguments);

		unset($arguments);

		$contentLength = strlen($dataString);

		if ($this->getDataFile()) {
			$contentLength += filesize($this->getDataFile());
		}

		$request = "POST " . $this->getPath() . " HTTP/1.1\r\n";
		$request .= "Host: " . $this->getHost() . "\r\n";
		$request .= "Content-type: application/x-www-form-urlencoded\r\n";
		$request .= "Content-length: " . $contentLength . "\r\n";
		$request .= "Connection: Close\r\n\r\n";
		$request .= $dataString;

		$this->_restRequest($request);
	}

	/**
	 * parse arguments array to string
	 *
	 * @param array $arguments
	 * @return string
	 */
	function getDataStringByArgments($arguments)
	{
		$dataString = '';
		foreach ($arguments as $key => $value) {
			$dataString .= '&' . $key . '=' . $value;
		}
		return $dataString;
	}

	/**
	 * _restRequest()
	 *
	 * Send a request to the REST-Server
	 *
	 * @param string $request
	 * @return string Parsed REST-answer
	 */
	function _restRequest($request)
	{
		$sslhost = $this->getHost();
		if ($this->getBrickfoxConfiguration()->getPort() == 443) {
			$sslhost = "ssl://" . $this->getHost();
		}

		$fp = fsockopen($sslhost, $this->getBrickfoxConfiguration()->getPort());

		if ($fp) {
			fwrite($fp, $request);

			if ($this->getDataFile()) {

				$dataStream = fopen($this->getDataFile(), 'r');
				stream_copy_to_stream($dataStream, $fp);
				fclose($dataStream);
				$this->setDataFile('');
			}

			$data = '';

			while (!feof($fp)) {
				$data .= fgets($fp, 128);
			}

			fclose($fp);

			$result = explode("\r\n\r\n", $data, 2);

			$content = isset($result[1]) ? $result[1] : $data;

			$answer = @simplexml_load_string($content);

			if (false === $answer) {
				throw new Exception($data);
			}

			$status = $answer->xpath('//status');
			$response = $answer->xpath('//response');

			if ($status[0] == 'success') {
				$resultFile = (string)$response[0];
				$this->setResultFile($resultFile);
			} else {
				throw new Exception($data);
			}
		}
	}

	/**
	 * confirmation transfer
	 *
	 * @param string $type
	 * @return string
	 */
	function confirmationTransfer($type)
	{
		if (!$this->getResultFile() || $this->getResultFile() == '') {
			$result = 'File cannot be exported';
		} else {
			$result = $this->import('get', array('file' => $this->getResultFile(), 'type' => $type));
		}

		return $result;
	}

	/**
	 * shop brickfox version
	 *
	 * @param string $brickfoxShopModule
	 * @return DomDocument
	 */
	function showVersion($brickfoxShopModule)
	{
		$domDocument = new DomDocument ();
		$rootNode = $domDocument->createElement('Brickfox');
		$node = $domDocument->createElement('Product', 'Brickfox Multichannel');
		$rootNode->appendChild($node);
		$node = $domDocument->createElement('Shop', $brickfoxShopModule);
		$rootNode->appendChild($node);
		$node = $domDocument->createElement('Version', $this->getBfVersion());
		$rootNode->appendChild($node);
		$domDocument->appendChild($rootNode);

		return $domDocument;
	}

}

?>