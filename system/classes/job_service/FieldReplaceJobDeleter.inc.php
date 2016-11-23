<?php
/* --------------------------------------------------------------
   FieldReplaceJobDeleter.inc.php 2014-10-01 gambio
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/



class FieldReplaceJobDeleter
{
	public function delete(FieldReplaceJob $replaceJob)
	{
		$id = $replaceJob->getFieldReplaceJobId();
		$this->deleteById($id);
	}


	public function deleteById($p_replaceJobId )
	{
		$c_replaceJobId = (int)$p_replaceJobId;
		if($c_replaceJobId == 0)
		{
			throw new Exception('No Id to delete');
		}

		$this->delete_from_field_replace_jobs($c_replaceJobId);
	}


	protected function delete_from_field_replace_jobs($p_replaceJobId)
	{
		$sql = '
			DELETE FROM field_replace_jobs
			WHERE
				field_replace_job_id = "'.(int)$p_replaceJobId.'"
		';
		xtc_db_query($sql);
	}

}