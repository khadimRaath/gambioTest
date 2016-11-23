<?php
/* --------------------------------------------------------------
   ParcelServiceWriter.inc.php 2015-06-09 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class ParcelServiceWriter
 */
class ParcelServiceWriter
{
	/**
	 * @param ParcelService $parcelService
	 *
	 * @return bool
	 */
	public function insertParcelService(ParcelService $parcelService)
	{
		if($parcelService->getDefault() === 1)
		{
			$this->_clearDefaultForAllParcelServices();
		}
		$languageIdArray = $this->_getLanguageIdArrayByParcelService($parcelService);

		$this->_insertIntoParcelServices($parcelService);
		$success = 1;
		foreach($languageIdArray as $languageId)
		{
			$success &= $this->_insertIntoParcelServicesDescription($parcelService, $languageId);
		}

		return (boolean)$success;
	}


	/**
	 * @param ParcelService $parcelService
	 *
	 * @return bool
	 */
	public function updateParcelService(ParcelService $parcelService)
	{
		if($parcelService->getDefault() === 1)
		{
			$this->_clearDefaultForAllParcelServices();
		}
		$languageIdArray = $this->_getLanguageIdArrayByParcelService($parcelService);

		$this->_updateParcelServices($parcelService);
		$success = 1;
		foreach($languageIdArray as $languageId)
		{
			$success &= $this->_updateParcelServicesDescription($parcelService, $languageId);
		}

		return (boolean)$success;
	}


	/**
	 * @param $p_serviceId
	 */
	public function deleteParcelService($p_serviceId)
	{
		$this->_deleteParcelServices($p_serviceId);
		$this->_deleteParcelServicesDescription($p_serviceId);
	}


	/**
	 * @param ParcelService $parcelService
	 *
	 * @return bool
	 */
	protected function _insertIntoParcelServices(ParcelService $parcelService)
	{
		xtc_db_query($this->_insertIntoParcelServicesQuery($parcelService));

		$parcelService->setId((int)((is_null($___mysqli_res = mysqli_insert_id($GLOBALS["___mysqli_ston"]))) ? false : $___mysqli_res));

		return $parcelService->getId() !== 0;
	}


	/**
	 * @param ParcelService $parcelService
	 * @param               $p_languageId
	 *
	 * @return bool
	 */
	protected function _insertIntoParcelServicesDescription(ParcelService $parcelService, $p_languageId)
	{
		if($parcelService->getId() === 0)
		{
			return false;
		}
		$result = xtc_db_query($this->_insertIntoParcelServicesDescriptionQuery($parcelService, $p_languageId));

		return $result !== false;
	}


	/**
	 * @param ParcelService $parcelService
	 *
	 * @return bool|int
	 */
	protected function _updateParcelServices(ParcelService $parcelService)
	{
		if($parcelService->getId() === 0)
		{
			return false;
		}
		xtc_db_query($this->_updateParcelServicesQuery($parcelService));

		return mysqli_affected_rows($GLOBALS["___mysqli_ston"]);
	}


	/**
	 * @param ParcelService $parcelService
	 * @param               $p_languageId
	 *
	 * @return bool|int
	 */
	protected function _updateParcelServicesDescription(ParcelService $parcelService, $p_languageId)
	{
		if($parcelService->getId() === 0)
		{
			return false;
		}
		xtc_db_query($this->_updateParcelServicesDescriptionQuery($parcelService, $p_languageId));

		return mysqli_affected_rows($GLOBALS["___mysqli_ston"]);
	}


	/**
	 * @param $p_serviceId
	 *
	 * @return int
	 */
	protected function _deleteParcelServices($p_serviceId)
	{
		xtc_db_query($this->_deleteParcelServicesQuery($p_serviceId));

		return mysqli_affected_rows($GLOBALS["___mysqli_ston"]);
	}


	/**
	 * @param $p_serviceId
	 *
	 * @return int
	 */
	protected function _deleteParcelServicesDescription($p_serviceId)
	{
		xtc_db_query($this->_deleteParcelServicesDescriptionQuery($p_serviceId));

		return mysqli_affected_rows($GLOBALS["___mysqli_ston"]);
	}


	/**
	 * @param ParcelService $parcelService
	 *
	 * @return array
	 */
	protected function _getLanguageIdArrayByParcelService(ParcelService $parcelService)
	{
		$languageIdArray = array_unique(array_merge(array_keys($parcelService->getUrlArray()),
													array_keys($parcelService->getCommentArray())));

		return $languageIdArray;
	}


	/**
	 * @return int
	 */
	protected function _clearDefaultForAllParcelServices()
	{
		xtc_db_query($this->_clearDefaultForAllParcelServicesQuery());

		return mysqli_affected_rows($GLOBALS["___mysqli_ston"]);
	}


	/**
	 * @param ParcelService $parcelService
	 *
	 * @return string
	 */
	protected function _insertIntoParcelServicesQuery(ParcelService $parcelService)
	{
		$query = 'INSERT INTO `parcel_services` SET `name` = "' . xtc_db_input(xtc_db_prepare_input($parcelService->getName())) . '", `default` = "' .
				 (int)$parcelService->getDefault() . '"';

		return $query;
	}


	/**
	 * @param ParcelService $parcelService
	 * @param               $p_languageId
	 *
	 * @return string
	 */
	protected function _insertIntoParcelServicesDescriptionQuery(ParcelService $parcelService, $p_languageId)
	{
		$urls     = $parcelService->getUrlArray();
		$comments = $parcelService->getCommentArray();
		
		$query =
			'INSERT INTO `parcel_services_description` SET `url` = "' . xtc_db_input(xtc_db_prepare_input($urls[$p_languageId])) .
			'", `comment` = "' . xtc_db_input(xtc_db_prepare_input($comments[$p_languageId])) . '", `parcel_service_id` = ' .
			(int)$parcelService->getId() . ', `language_id` = ' . (int)$p_languageId;

		return $query;
	}


	/**
	 * @param ParcelService $parcelService
	 *
	 * @return string
	 */
	protected function _updateParcelServicesQuery(ParcelService $parcelService)
	{
		$query = 'UPDATE `parcel_services` SET `name` = "' . xtc_db_input(xtc_db_prepare_input($parcelService->getName())) . '", `default` = "' .
				 (int)$parcelService->getDefault() . '" WHERE `parcel_service_id` = ' . (int)$parcelService->getId();

		return $query;
	}


	/**
	 * @param ParcelService $parcelService
	 * @param               $p_languageId
	 *
	 * @return string
	 */
	protected function _updateParcelServicesDescriptionQuery(ParcelService $parcelService, $p_languageId)
	{
		$urlArray = $parcelService->getUrlArray();
		$commentArray = $parcelService->getCommentArray();
		$query = 'UPDATE `parcel_services_description` SET `url` = "' . xtc_db_input(xtc_db_prepare_input($urlArray[$p_languageId])) .
				 '", `comment` = "' . xtc_db_input(xtc_db_prepare_input($commentArray[$p_languageId])) .
				 '" WHERE `parcel_service_id` = ' . (int)$parcelService->getId() . ' AND `language_id` = ' . (int)$p_languageId;

		return $query;
	}


	/**
	 * @param $p_serviceId
	 *
	 * @return string
	 */
	protected function _deleteParcelServicesQuery($p_serviceId)
	{
		$query = 'DELETE FROM `parcel_services` WHERE `parcel_service_id` = ' . (int)$p_serviceId;

		return $query;
	}


	/**
	 * @param $p_serviceId
	 *
	 * @return string
	 */
	protected function _deleteParcelServicesDescriptionQuery($p_serviceId)
	{
		$query = 'DELETE FROM `parcel_services_description` WHERE `parcel_service_id` = ' . (int)$p_serviceId;

		return $query;
	}


	/**
	 * @return string
	 */
	protected function _clearDefaultForAllParcelServicesQuery()
	{
		$query = 'UPDATE `parcel_services` SET `default` = 0';

		return $query;
	}
}