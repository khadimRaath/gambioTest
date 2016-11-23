<?php
/* --------------------------------------------------------------
   WaitingTicket.inc.php 2014-10-13 gambio
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class WaitingTicket
 */
class WaitingTicket
{
	protected $waitingTicketId;
	protected $dueDate;
	protected $doneDate;
	protected $callback;
	protected $subject;


	/**
	 * @param int      $p_waitingTicketId
	 * @param DateTime $dueDate
	 * @param DateTime $doneDate
	 * @param string   $p_callback
	 * @param string   $p_subject
	 */
	public function __construct($p_waitingTicketId, DateTime $dueDate, DateTime $doneDate, $p_callback, $p_subject)
	{
		$this->waitingTicketId = $p_waitingTicketId;
		$this->dueDate         = $dueDate;
		$this->doneDate        = $doneDate;
		$this->callback        = $p_callback;
		$this->subject         = $p_subject;
	}


	/**
	 * @return int
	 */
	public function getWaitingNumber()
	{
		return $this->waitingTicketId;
	}


	/**
	 * @return DateTime
	 */
	public function getDueDate()
	{
		return $this->dueDate;
	}


	/**
	 * @return DateTime
	 */
	public function getDoneDate()
	{
		return $this->doneDate;
	}


	/**
	 * @return string
	 */
	public function getCallback()
	{
		return $this->callback;
	}


	/**
	 * @return string
	 */
	public function getSubject()
	{
		return $this->subject;
	}
}