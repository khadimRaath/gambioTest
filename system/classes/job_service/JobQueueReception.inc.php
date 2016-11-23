<?php
/* --------------------------------------------------------------
   JobQueueReception.inc.php 2014-10-16 gambio
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class JobQueueReception
 */
class JobQueueReception
{
	/**
	 * @param DateTime $dueDate
	 * @param string   $p_callback
	 * @param string   $p_subject
	 *
	 * @return WaitingTicket
	 */
	public function createWaitingTicket(DateTime $dueDate, $p_callback, $p_subject)
	{
		$sql = '
			INSERT INTO job_waiting_tickets
			SET
				subject  = "' . xtc_db_input($p_subject) . '",
				callback = "' . xtc_db_input($p_callback) . '",
				due_date = "' . $dueDate->format('Y-m-d H:i') . '"
		';
		xtc_db_query($sql);

		$waitingTicketId = xtc_db_insert_id();
		/* @var WaitingTicket $waitingTicket */
		$waitingTicket   = MainFactory::create_object('WaitingTicket',
													  array($waitingTicketId, $dueDate, new DateTime('1000-01-01 00:00'), $p_callback, $p_subject));

		return $waitingTicket;
	}


	/**
	 * @param int      $p_waitingNumber
	 * @param DateTime $dueDate
	 */
	public function rescheduleWaitingTicket($p_waitingNumber, DateTime $dueDate)
	{
		$sql = '
			UPDATE job_waiting_tickets
			SET
				due_date = "' . $dueDate->format('Y-m-d H:i') . '"
			WHERE
				waiting_ticket_id = "' . (int)$p_waitingNumber . '"
		';
		xtc_db_query($sql);
	}


	/**
	 * @param int    $p_waitingNumber
	 * @param string $p_subject
	 */
	public function changeWaitingTicketSubject($p_waitingNumber, $p_subject)
	{
		$sql = '
			UPDATE job_waiting_tickets
			SET
				subject = "' . xtc_db_input($p_subject) . '"
			WHERE
				waiting_ticket_id = "' . (int)$p_waitingNumber . '"
		';
		xtc_db_query($sql);
	}


	/**
	 * @param int $p_waitingNumber
	 */
	public function setWaitingTicketDone($p_waitingNumber)
	{
		$sql = '
			UPDATE job_waiting_tickets
			SET
				done_date = NOW()
			WHERE
				waiting_ticket_id = "' . (int)$p_waitingNumber . '"
		';
		xtc_db_query($sql);
	}


	/**
	 * @param int $p_waitingNumber
	 */
	public function cancelWaitingTicket($p_waitingNumber)
	{
		$sql = '
			DELETE FROM job_waiting_tickets
			WHERE
				waiting_ticket_id = "' . (int)$p_waitingNumber . '"
		';
		xtc_db_query($sql);
	}
}