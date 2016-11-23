<?php
/* --------------------------------------------------------------
   ParcelServiceReader.inc.php-2014-10-08 gambio
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class ParcelServiceReader
 */
class ParcelServiceReader
{
	/**
	 * @param $p_serviceId
	 *
	 * @return bool|array
	 */
	public function getParcelServiceById($p_serviceId)
	{
		$mysqlResult   = xtc_db_query($this->_getParcelServiceByIdQuery($p_serviceId));
		$parcelService = $this->_createParcelServicesByResultSet($mysqlResult);
		if(empty($parcelService))
		{
			return false;
		}

		return array_shift($parcelService);
	}


	/**
	 * @return array
	 */
	public function getAllParcelServices()
	{
		$mysqlResult    = xtc_db_query($this->_getAllParcelServicesQuery());
		$parcelServices = $this->_createParcelServicesByResultSet($mysqlResult);

		return $parcelServices;
	}


	/**
	 * @param $p_mysqlResult
	 *
	 * @return array
	 */
	protected function _createParcelServicesByResultSet($p_mysqlResult)
	{
		$parcelServiceDataArray = array();
		$parcelServiceArray     = array();

		while($row = xtc_db_fetch_array($p_mysqlResult))
		{
			if(!isset($parcelServiceDataArray[$row['id']]))
			{
				$parcelServiceDataArray[$row['id']] = array();
			}
			$parcelServiceDataArray[$row['id']]['id']                           = $row['id'];
			$parcelServiceDataArray[$row['id']]['name']                         = $row['name'];
			$parcelServiceDataArray[$row['id']]['default']                      = $row['default'];
			$parcelServiceDataArray[$row['id']]['url'][$row['language_id']]     = $row['url'];
			$parcelServiceDataArray[$row['id']]['comment'][$row['language_id']] = $row['comment'];
		}

		foreach($parcelServiceDataArray as $parcelServiceData)
		{
			$parcelServiceArray[] = $this->_createParcelService($parcelServiceData['id'], $parcelServiceData['name'],
															   $parcelServiceData['url'], $parcelServiceData['comment'],
															   $parcelServiceData['default']);
		}

		return $parcelServiceArray;
	}


	/**
	 * @param       $p_id
	 * @param       $p_name
	 * @param array $p_urlArray
	 * @param array $p_commentArray
	 * @param int   $p_default
	 *
	 * @return ParcelService
	 */
	protected function _createParcelService($p_id, $p_name, array $p_urlArray, array $p_commentArray, $p_default = 0)
	{
		return MainFactory::create_object('ParcelService', array(
			$p_id,
			$p_name,
			$p_urlArray,
			$p_commentArray,
			$p_default
		));
	}


	/**
	 * @return string
	 */
	protected function _getAllParcelServicesQuery()
	{
		$query = 'SELECT 
  						`ps`.`parcel_service_id` AS id,
  						`ps`.`name`,
  						`ps`.`default`,
  						`psd`.`url`, 
  						`psd`.`comment`,
  						`psd`.`language_id`
					FROM 
  						`parcel_services` AS `ps`
  					LEFT JOIN
  						`parcel_services_description` AS `psd` USING (`parcel_service_id`)';

		return $query;
	}


	/**
	 * @param $p_serviceId
	 *
	 * @return string
	 */
	protected function _getParcelServiceByIdQuery($p_serviceId)
	{
		$query = 'SELECT 
  						`ps`.`parcel_service_id` AS id, 
  						`ps`.`name`,
  						`ps`.`default`,
  						`psd`.`url`, 
  						`psd`.`comment`,
  						`psd`.`language_id`
					FROM 
  						`parcel_services` AS `ps`
  					LEFT JOIN
  						`parcel_services_description` AS `psd` USING (`parcel_service_id`)
					WHERE 
						`ps`.`parcel_service_id` = "' . (int)$p_serviceId . '"';

		return $query;
	}
}