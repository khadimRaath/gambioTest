<?php
/* --------------------------------------------------------------
   FieldReplaceJobWriter.inc.php 2014-10-08 gambio
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class FieldReplaceJobWriter
 */
class FieldReplaceJobWriter
{
	/**
	 * @param FieldReplaceJob $replaceJob
	 */
	public function write(FieldReplaceJob $replaceJob)
	{
		if($replaceJob->getFieldReplaceJobId() == null)
		{
			$this->_insert($replaceJob);
		}
		else
		{
			$this->_update($replaceJob);
		}
	}


	/**
	 * @param FieldReplaceJob $replaceJob
	 *
	 * @return FieldReplaceJob
	 */
	protected function _insert(FieldReplaceJob $replaceJob)
	{
		$sql = '
			INSERT INTO field_replace_jobs
			SET
				waiting_ticket_id  = "' . xtc_db_input($replaceJob->getWaitingNumber()) . '",
				table_name = "' . xtc_db_input($replaceJob->getTableName()) . '",
				field_name = "' . xtc_db_input($replaceJob->getFieldName()) . '",
				old_value = "' . xtc_db_input($replaceJob->getOldValue()) . '",
				new_value = "' . xtc_db_input($replaceJob->getNewValue()) . '",
				hidden = "' . (int)$replaceJob->getHidden() . '"
		';
		xtc_db_query($sql);

		$fieldReplaceJobId = ((is_null($___mysqli_res = mysqli_insert_id($GLOBALS["___mysqli_ston"]))) ? false : $___mysqli_res);
		$replaceJob->setFieldReplaceJobId($fieldReplaceJobId);

		return $replaceJob;
	}


	/**
	 * @param FieldReplaceJob $replaceJob
	 *
	 * @return bool
	 */
	protected function _update(FieldReplaceJob $replaceJob)
	{
		$sql = '
			UPDATE field_replace_jobs
			SET
				waiting_ticket_id  = "' . xtc_db_input($replaceJob->getWaitingNumber()) . '",
				table_name = "' . xtc_db_input($replaceJob->getTableName()) . '",
				field_name = "' . xtc_db_input($replaceJob->getFieldName()) . '",
				old_value = "' . xtc_db_input($replaceJob->getOldValue()) . '",
				new_value = "' . xtc_db_input($replaceJob->getNewValue()) . '",
				hidden = "' . (int)$replaceJob->getHidden() . '"
			WHERE
				field_replace_job_id = "' . xtc_db_input($replaceJob->getFieldReplaceJobId()) . '"
		';
		xtc_db_query($sql);

		return true;
	}


	/**
	 * @param FieldReplaceJob $replaceJob
	 *
	 * @return bool
	 */
	public function delete(FieldReplaceJob $replaceJob)
	{
		$sql = '
			DELETE FROM field_replace_jobs
			WHERE field_replace_job_id = "' . (int)$replaceJob->getFieldReplaceJobId() . '"
		';
		xtc_db_query($sql);

		return true;
	}

}
