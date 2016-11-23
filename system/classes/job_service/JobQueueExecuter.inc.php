<?php

/* --------------------------------------------------------------
   JobQueueExecuter.inc.php 2014-11-18 gambio
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class JobQueueExecuter
{
	protected $jobQueueReader;
	protected $jobQueueReception;
	protected $jobReaderFactory;

	public function __construct(JobQueueReader $jobQueueReader, JobQueueReception $jobQueueReception, JobReaderFactory $jobReaderFactory)
	{
		$this->jobQueueReader    = $jobQueueReader;
		$this->jobQueueReception = $jobQueueReception;
		$this->jobReaderFactory  = $jobReaderFactory;
	}


	public function execute(DateTime $deadline)
	{
		$this->jobQueueReader->setDeadlineFilter($deadline);
		$this->jobQueueReader->setUndoneOnlyFilter(true);
		//$this->jobQueueReader->setCallbackFilter('FieldReplace');

		while(($waitingTicket = $this->jobQueueReader->getNextWaitingTicket()) !== null)
		{
			$waitingNumber = $waitingTicket->getWaitingNumber();
			$callback      = $waitingTicket->getCallback();

			$jobReader = $this->jobReaderFactory->getJobReader($callback);

			try
			{
				$job = $jobReader->getByWaitingNumber($waitingNumber);

				$jobFinished = $job->execute();

				if($jobFinished === true)
				{
					$this->jobQueueReception->setWaitingTicketDone($waitingNumber);
				}
			}
			catch(Exception $e)
			{
				$coo_logger = LogControl::get_instance();
				$coo_logger->notice('Job with waiting number ' . $waitingNumber . ' could not be created', 'error_handler', 'errors', 'notice', 'USER NOTICE', 0, $e->getMessage());
			}
		}
	}
}