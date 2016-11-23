<?php
/**
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the GNU General Public License (GPL 2)
 * that is bundled with this package in the file LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Payone to newer
 * versions in the future. If you wish to customize Payone for your
 * needs please refer to http://www.payone.de for more information.
 *
 * @category        Payone
 * @package         Payone_Api
 * @subpackage      Adapter
 * @copyright       Copyright (c) 2012 <info@noovias.com> - www.noovias.com
 * @author          Matthias Walter <info@noovias.com>
 * @license         <http://www.gnu.org/licenses/> GNU General Public License (GPL 2)
 * @link            http://www.noovias.com
 */

/**
 *
 * @category        Payone
 * @package         Payone_Api
 * @subpackage      Adapter
 * @copyright       Copyright (c) 2012 <info@noovias.com> - www.noovias.com
 * @license         <http://www.gnu.org/licenses/> GNU General Public License (GPL 2)
 * @link            http://www.noovias.com
 */

/* --------------------------------------------------------------
    Curl.php 2014-06-20 mabr
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2014 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------
*/

class Payone_Api_Adapter_Http_Curl extends Payone_Api_Adapter_Http_Abstract
{
    /**
     * @return array
     * @throws Payone_Api_Exception_InvalidResponse
     */
    protected function doRequest()
    {
        $response = array();
        $urlArray = $this->generateUrlArray();

        $urlHost = $urlArray['host'];
        $urlPath = isset($urlArray['path']) ? $urlArray['path'] : '';
        $urlScheme = $urlArray['scheme'];
        $urlQuery = $urlArray['query'];

        $this->gmLog("Request:\n" . print_r($urlArray, true));

        $curl = curl_init($urlScheme . "://" . $urlHost . $urlPath);

        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $urlQuery);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($curl, CURLOPT_TIMEOUT, self::DEFAULT_TIMEOUT);

        $result = curl_exec($curl);
        $this->setRawResponse($result);
        $curlInfo = curl_getinfo($curl);
        $curlErrno = curl_errno($curl);
        $curlError = curl_error($curl);
        curl_close($curl);

        if ($curlInfo['http_code'] != 200)
        {
            $this->gmLog(sprintf("ERROR response - %s (%d)\n%s", $curlError, $curlErrno, print_r($curlInfo, true)));
            throw new Payone_Api_Exception_InvalidResponse();
        }
        elseif ($curlError > 0) {
            // $response[] = "errormessage=" . curl_errno($curl) . ": " . curl_error($curl);
            $response[] = sprintf("errormessage=%d:%s", $curlErrno, $curlError);
            $this->gmLog(sprintf("ERROR response - %s (%d)\n%s", $curlError, $curlErrno, print_r($curlInfo, true)));
        }
        else {
            $response = explode("\n", $result);
        }
        $this->gmLog(sprintf("Response:\n%s\n", $result));

        return $response;
    }


    protected function gmLog($message)
    {
        $t_curl_log = new FileLog('payment-payone-curllog', true);
        $t_curl_log->write(str_repeat('=', 100).PHP_EOL);
        $t_curl_log->write(date('c').PHP_EOL);
        $t_curl_log->write($message);
    }
}
