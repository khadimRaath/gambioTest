<?php

/* --------------------------------------------------------------
   AbstractJob.inc.php 2014-10-01 gambio
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

abstract class AbstractJob
{
	protected $waitingNumber;

	public function setWaitingNumber($p_waitingNumber)
	{
		$this->waitingNumber = (int)$p_waitingNumber;
	}

	public function getWaitingNumber()
	{
		return $this->waitingNumber;
	}

	public abstract function execute();
}
