<?php
/* --------------------------------------------------------------
   JobQueueReader.inc.php 2016-06-01
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class JobQueueReader
 */
class JobQueueReader
{
	protected $deadlineFilter;
	protected $undoneOnlyFilter;
	protected $idFilter;
	protected $dbSelectResult;


	public function __construct()
	{
		$this->deadlineFilter   = null;
		$this->undoneOnlyFilter = false;
		$this->callbackFilter   = null;
		$this->idFilter   = null;

		$this->dbSelectResult = null;
	}


	/**
	 * @param DateTime $deadline
	 */
	public function setDeadlineFilter(DateTime $deadline)
	{
		$this->deadlineFilter = $deadline;
		$this->resetResult();
	}


	/**
	 * @param $p_showUndoneOnly
	 */
	public function setUndoneOnlyFilter($p_showUndoneOnly)
	{
		$this->undoneOnlyFilter = (bool)$p_showUndoneOnly;
		$this->resetResult();
	}


	/**
	 * @param $p_callbackName
	 */
	public function setCallbackFilter($p_callbackName)
	{
		$this->callbackFilter = $p_callbackName;
		$this->resetResult();
	}


	/**
	 * @param $p_waitingTicketId
	 */
	public function setIdFilter($p_waitingTicketId)
	{
		$this->idFilter = (int)$p_waitingTicketId;
		$this->resetResult();
	}


	/**
	 * @return WaitingTicket|null
	 */
	public function getNextWaitingTicket()
	{
		$waitingTicket = null;

		if(isset($this->dbSelectResult) == false)
		{
			$this->buildResult();
		}

		$row = xtc_db_fetch_array($this->dbSelectResult);
		if($row)
		{
			/* @var WaitingTicket $waitingTicket */
			$waitingTicket = MainFactory::create_object('WaitingTicket',
														array(
															$row['waiting_ticket_id'],
															new DateTime($row['due_date']),
															new DateTime($row['done_date']),
															$row['callback'], $row['subject']
														));
		}

		return $waitingTicket;
	}


	/**
	 * @param $p_waitingTicketId
	 *
	 * @return null|WaitingTicket
	 */
	public function getWaitingTicketById($p_waitingTicketId)
	{
		$waitingTicket = null;
		
		$this->setIdFilter($p_waitingTicketId);

		if(isset($this->dbSelectResult) == false)
		{
			$this->buildResult();
		}

		$row = xtc_db_fetch_array($this->dbSelectResult);
		if($row)
		{
			/* @var WaitingTicket $waitingTicket */
			$waitingTicket = MainFactory::create_object('WaitingTicket',
														array(
															$row['waiting_ticket_id'],
															new DateTime($row['due_date']),
															new DateTime($row['done_date']),
															$row['callback'], $row['subject']
														));
		}

		return $waitingTicket;
	}


	protected function resetResult()
	{
		if($this->dbSelectResult != null)
		{
			xtc_db_free_result($this->dbSelectResult);
			$this->dbSelectResult = null;
		}
	}


	protected function buildResult()
	{
		$whereArray = array();
		$wherePart  = '';

		if(isset($this->deadlineFilter))
		{
			$whereArray[] = 'due_date <= "' . $this->deadlineFilter->format('Y-m-d H:i') . '"';
		}

		if($this->undoneOnlyFilter == true)
		{
			$whereArray[] = 'done_date <= "1000-01-01 00:00:00"';
		}

		if(isset($this->callbackFilter))
		{
			$whereArray[] = 'callback = "' . xtc_db_input($this->callbackFilter) . '"';
		}

		if(isset($this->idFilter))
		{
			$whereArray[] = 'waiting_ticket_id = "' . (int)$this->idFilter . '"';
		}

		if(count($whereArray) > 0)
		{
			$wherePart = ' WHERE ';
			$wherePart .= implode(' AND ', $whereArray);
			$wherePart .= ' ';
		}

		$sql                  = '
				SELECT *
				FROM job_waiting_tickets
				' . $wherePart . '
				ORDER BY
					due_date ASC
			';
		$this->dbSelectResult = xtc_db_query($sql);
	}
}