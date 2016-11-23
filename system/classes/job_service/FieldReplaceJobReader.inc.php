<?php
/* --------------------------------------------------------------
   FieldReplaceJobReader.inc.php 2014-11-18 gambio
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class FieldReplaceJobReader
 */
class FieldReplaceJobReader
{
	/**
	 * @param int $p_id
	 *
	 * @return FieldReplaceJob
	 */
	public function getById($p_id)
	{
		$sql        = '
			SELECT *
			FROM field_replace_jobs
			WHERE
				field_replace_job_id = "' . (int)$p_id . '"
		';
		$replaceJob = $this->_getBySqlQuery($sql);

		return $replaceJob;
	}


	/**
	 * @param int $p_waitingNumber
	 *
	 * @return FieldReplaceJob
	 */
	public function getByWaitingNumber($p_waitingNumber)
	{
		$sql        = '
			SELECT *
			FROM field_replace_jobs
			WHERE
				waiting_ticket_id = "' . (int)$p_waitingNumber . '"
		';
		$replaceJob = $this->_getBySqlQuery($sql);

		return $replaceJob;
	}


	/**
	 * @param int $p_shippingStatusId
	 *
	 * @return FieldReplaceJob
	 */
	public function getReplaceJobArrayByShippingStatusId($p_shippingStatusId)
	{
		$sql = 'SELECT *
				FROM field_replace_jobs
				WHERE
					table_name = "products" AND
					field_name = "products_shippingtime" AND
					(old_value = "' . (int)$p_shippingStatusId . '" OR 
					new_value = "' . (int)$p_shippingStatusId . '")';

		$replaceJobArray = $this->_getArrayBySqlQuery($sql);

		return $replaceJobArray;
	}


	/**
	 * @param string $p_sql
	 *
	 * @return FieldReplaceJob
	 * @throws Exception
	 */
	protected function _getBySqlQuery($p_sql)
	{
		$objectArray = $this->_getArrayBySqlQuery($p_sql);
		
		if(sizeof($objectArray) > 1)
		{
			throw new Exception('Multiple rows found');
		}
		elseif(sizeof($objectArray) === 0)
		{
			throw new Exception("Query\r\n\r\n" . $p_sql . "\r\n\r\n has no result.");
		}
		
		return $objectArray[0];
	}


	/**
	 * @param bool $p_include_hidden_jobs
	 *
	 * @return array
	 */
	public function getAll($p_include_hidden_jobs = false)
	{
		if($p_include_hidden_jobs)
		{
			$sql = 'SELECT * FROM field_replace_jobs';	
		}
		else
		{
			$sql = 'SELECT * FROM field_replace_jobs WHERE hidden = 0';
		}
		
		$jobsArray = $this->_getArrayBySqlQuery($sql);

		return $jobsArray;
	}


	/**
	 * @param string $p_sql
	 *
	 * @return array
	 * @throws Exception
	 */
	protected function _getArrayBySqlQuery($p_sql)
	{
		$replaceJobArray = array();
		
		$result = xtc_db_query($p_sql);

		while($jobData = xtc_db_fetch_array($result))
		{
			$replaceJob = MainFactory::create_object('FieldReplaceJob');

			$replaceJob->setFieldReplaceJobId($jobData['field_replace_job_id']);
			$replaceJob->setWaitingNumber($jobData['waiting_ticket_id']);
			$replaceJob->setTableName($jobData['table_name']);
			$replaceJob->setFieldName($jobData['field_name']);
			$replaceJob->setOldValue($jobData['old_value']);
			$replaceJob->setNewValue($jobData['new_value']);

			$replaceJobArray[] = $replaceJob;
		}

		return $replaceJobArray;
	}

}