<?php
/* --------------------------------------------------------------
  ParcelTrackingCodeReader.inc.php 2014-10-08 gm
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2014 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
*/


/**
 * Class ParcelTrackingNumbers
 */
class ParcelTrackingCodeReader
{
	/**
	 * @param ParcelTrackingCode $parcelTrackingCodeItem
	 * @param $p_orderId
	 * 
*@return array
	 */
	public function getTackingCodeItemsByOrderId(ParcelTrackingCode $parcelTrackingCodeItem, $p_orderId)
	{

		$query = 'SELECT
						orders_parcel_tracking_code_id,
						tracking_code,
						creation_date,
						url,
						comment,
						parcel_service_name
					FROM
						orders_parcel_tracking_codes
					WHERE
						order_id = ' . (int)$p_orderId . '
					ORDER BY
						creation_date ASC
					';

		$result = xtc_db_query($query, 'db_link', false);

		$parcelTrackingCodeItem_array = array();

		while($row = xtc_db_fetch_array($result))
		{
			$parcelTrackingCodeItemTemp = clone $parcelTrackingCodeItem;

			$parcelTrackingCodeItemTemp->setTrackingCodeId($row['orders_parcel_tracking_code_id']);
			$parcelTrackingCodeItemTemp->setTrackingCode($row['tracking_code']);
			$parcelTrackingCodeItemTemp->setCreationDate($row['creation_date']);
			$parcelTrackingCodeItemTemp->setServiceUrl($row['url']);
			$parcelTrackingCodeItemTemp->setServiceComment($row['comment']);
			$parcelTrackingCodeItemTemp->setServiceName($row['parcel_service_name']);
			$parcelTrackingCodeItemTemp->setOrderId($p_orderId);

			$parcelTrackingCodeItem_array[] = $parcelTrackingCodeItemTemp;
		}

		return $parcelTrackingCodeItem_array;
	}
}