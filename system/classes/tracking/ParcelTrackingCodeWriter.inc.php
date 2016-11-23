<?php
/* --------------------------------------------------------------
   ParcelTrackingCodeWriter.inc.php 2016-09-20
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class ParcelTrackingCodeWriter
 */
class ParcelTrackingCodeWriter
{
	/**
	 * @param $p_url
	 * @param $p_trackingCode
	 *
	 * @return string
	 */
	public function buildTrackingUrl($p_url, $p_trackingCode)
	{
		return str_replace('{TRACKING_NUMBER}', rawurlencode((string)$p_trackingCode), (string)$p_url);
	}
	
	
	/**
	 * @param $p_string
	 * @param $p_trackingCode
	 *
	 * @return string
	 */
	public function resolveTrackingCodePlaceholder($p_string, $p_trackingCode)
	{
		return str_replace('{TRACKING_NUMBER}', (string)$p_trackingCode, (string)$p_string);
	}
	
	
	/**
	 * @param $p_trackingId
	 *
	 * @return bool
	 */
	public function deleteTrackingCode($p_trackingId)
	{
		$query = 'DELETE FROM orders_parcel_tracking_codes WHERE orders_parcel_tracking_code_id = '
		         . (int)$p_trackingId;
		
		xtc_db_query($query);
		
		return true;
	}
	
	
	/**
	 * @param                     $p_orderId
	 * @param                     $p_trackingCode
	 * @param                     $p_parcelServiceId
	 * @param ParcelServiceReader $parcelServiceReadService
	 *
	 * @return int
	 * @throws UnexpectedValueException
	 * @throws Exception
	 */
	public function insertTrackingCode($p_orderId,
	                                   $p_trackingCode,
	                                   $p_parcelServiceId,
	                                   ParcelServiceReader $parcelServiceReadService)
	{
		if(empty($p_orderId))
		{
			throw new UnexpectedValueException('cannot insert tracking code, because orderId empty');
		}
		elseif(empty($p_trackingCode))
		{
			return 0;
		}
		
		/** @var ParcelService $parcelService */
		$parcelService = $parcelServiceReadService->getParcelServiceById($p_parcelServiceId);
		
		if($parcelService === false)
		{
			throw new Exception('parcelService could not be found');
		}
		
		$p_trackingCode = preg_replace('/\s/', '', $p_trackingCode);
		
		$languageId        = $this->_getLanguageIdByOrderId($p_orderId);
		$parcelServiceName = $parcelService->getName();
		$url               = $this->buildTrackingUrl($parcelService->getUrlByLanguageId($languageId), $p_trackingCode);
		$comment           = $this->resolveTrackingCodePlaceholder($parcelService->getCommentByLanguageId($languageId),
		                                                           $p_trackingCode);
		
		$query = 'INSERT INTO orders_parcel_tracking_codes
					SET
						order_id = ' . (int)$p_orderId . ',
						tracking_code = "' . xtc_db_input($p_trackingCode) . '",
						parcel_service_id = ' . (int)$p_parcelServiceId . ',
						parcel_service_name = "' . xtc_db_input($parcelServiceName) . '",
						language_id = ' . (int)$languageId . ',
						url = "' . xtc_db_input($url) . '",
						comment = "' . xtc_db_input($comment) . '"';
		
		xtc_db_query($query);
		
		return xtc_db_insert_id();
	}
	
	
	/**
	 * @param                     $p_orderId
	 * @param                     $p_trackingUrl
	 * @param                     $p_parcelServiceId
	 * @param ParcelServiceReader $parcelServiceReadService
	 * @param                     $p_trackingCode
	 *
	 * @return int
	 * @throws UnexpectedValueException
	 * @throws Exception
	 */
	public function insertTrackingUrl($p_orderId,
	                                  $p_trackingUrl,
	                                  $p_parcelServiceId,
	                                  ParcelServiceReader $parcelServiceReadService,
	                                  $p_trackingCode = '')
	{
		if(empty($p_orderId))
		{
			throw new UnexpectedValueException('cannot insert tracking code, because orderId empty');
		}
		elseif(empty($p_trackingUrl))
		{
			return 0;
		}
		
		/** @var ParcelService $parcelService */
		$parcelService = $parcelServiceReadService->getParcelServiceById($p_parcelServiceId);
		
		if($parcelService === false)
		{
			throw new Exception('parcelService could not be found');
		}
		
		$languageId        = $this->_getLanguageIdByOrderId($p_orderId);
		$parcelServiceName = $parcelService->getName();
		$url               = $p_trackingUrl;
		$comment           = $this->resolveTrackingCodePlaceholder($parcelService->getCommentByLanguageId($languageId),
		                                                           '');
		$trackingCode      = empty($p_trackingCode) ? $url : $p_trackingCode;
		
		$query = 'INSERT INTO orders_parcel_tracking_codes
					SET
						order_id = ' . (int)$p_orderId . ',
						tracking_code = "' . xtc_db_input($trackingCode) . '",
						parcel_service_id = ' . (int)$p_parcelServiceId . ',
						parcel_service_name = "' . xtc_db_input($parcelServiceName) . '",
						language_id = ' . (int)$languageId . ',
						url = "' . xtc_db_input($url) . '",
						comment = "' . xtc_db_input($comment) . '"';
		
		xtc_db_query($query);
		
		return xtc_db_insert_id();
	}
	
	
	/**
	 * @param int $p_orderId
	 *
	 * @return int
	 * @throws Exception
	 */
	protected function _getLanguageIdByOrderId($p_orderId)
	{
		$query  = 'SELECT l.languages_id
					FROM
						orders o ,
						languages l
					WHERE
						o.orders_id = ' . (int)$p_orderId . ' AND
						o.language = l.directory
					ORDER BY l.status DESC
					LIMIT 1';
		$result = xtc_db_query($query);
		
		if(xtc_db_num_rows($result) == 0)
		{
			throw new Exception('language_id of order ' . (int)$p_orderId . ' could not be determined');
		}
		
		$row        = xtc_db_fetch_array($result);
		$languageId = (int)$row['languages_id'];
		
		return $languageId;
	}
}