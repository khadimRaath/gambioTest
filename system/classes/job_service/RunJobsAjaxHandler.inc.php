<?php
/* --------------------------------------------------------------
   RunJobsAjaxHandler.inc.php 2015-04-13 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class RunJobsAjaxHandler extends AjaxHandler
{
	public function get_permission_status($p_customersId = null)
	{
		$token = LogControl::get_secure_token();
		$token = md5($token);

		if($token != $this->v_data_array['GET']['token'])
		{
			return false;
		}
		
		return true;	
	}
	
	public function proceed()
	{
		$jobQueueReader    = MainFactory::create_object('JobQueueReader');
		$jobQueueReception = MainFactory::create_object('JobQueueReception');
		$jobReaderFactory  = MainFactory::create_object('JobReaderFactory');

		$jobQueueExecuter = MainFactory::create_object('JobQueueExecuter', array($jobQueueReader, $jobQueueReception, $jobReaderFactory) );

		$deadline = new DateTime('now');
		$jobQueueExecuter->execute($deadline);

		return true;
	}
}