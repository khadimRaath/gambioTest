<?php

/* --------------------------------------------------------------
   FieldReplaceJobReader.inc.php 2014-10-01 gambio
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('FieldReplaceJobReader');

class ProductsFieldReplaceJobReader extends FieldReplaceJobReader
{
	protected function _getBySqlQuery($p_sql)
	{
		$result = xtc_db_query($p_sql);

		if(xtc_db_num_rows($result) == 0)
		{
			throw new Exception('Id not found');
		}
		elseif(xtc_db_num_rows($result) > 1)
		{
			throw new Exception('Multiple rows found');
		}

		$jobData = xtc_db_fetch_array($result);

		$replaceJob = MainFactory::create_object('ProductsFieldReplaceJob');

		$replaceJob->setFieldReplaceJobId($jobData['field_replace_job_id']);
		$replaceJob->setWaitingNumber($jobData['waiting_ticket_id']);
		$replaceJob->setFieldName($jobData['field_name']);
		$replaceJob->setOldValue($jobData['old_value']);
		$replaceJob->setNewValue($jobData['new_value']);

		return $replaceJob;
	}

}