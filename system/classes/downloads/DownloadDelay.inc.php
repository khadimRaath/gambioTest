<?php
/* --------------------------------------------------------------
   DownloadDelay.inc.php 2014-07-14 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/


/**
 * Class DownloadDelay
 */
class DownloadDelay
{
	protected $total_delay_seconds;
	protected $delay_days;
	protected $delay_hours;
	protected $delay_minutes;
	protected $delay_seconds;


	/**
	 * @param int $p_days
	 * @param int $p_hours
	 * @param int $p_minutes
	 * @param int $p_seconds
	 */
	public function convert_days_to_seconds($p_days, $p_hours, $p_minutes, $p_seconds)
	{
		$t_delay_total = ((int)$p_days * 60 * 60 * 24) + ((int)$p_hours * 60 * 60) + ((int)$p_minutes * 60) + (int)$p_seconds;

		$this->total_delay_seconds = $t_delay_total;
	}


	/**
	 * @param $p_delay_sec
	 */
	public function convert_seconds_to_days($p_delay_sec)
	{
		$t_total_delay_sec = (int)$p_delay_sec;
		$t_days = floor($t_total_delay_sec / (60 * 60 * 24));

		$t_total_delay = $t_total_delay_sec - ($t_days * 60 * 60 * 24);
		$t_hours = floor($t_total_delay / (60 * 60));

		$t_total_delay = $t_total_delay - ($t_hours * 60 * 60);
		$t_minutes = floor(($t_total_delay) / (60));

		$t_seconds = $t_total_delay - ($t_minutes * 60);

		$this->delay_days = $t_days;
		$this->delay_hours = $t_hours;
		$this->delay_minutes = $t_minutes;
		$this->delay_seconds = $t_seconds;
	}


	/**
	 * @return int
	 */
	public function get_delay_days()
	{
		return $this->delay_days;
	}


	/**
	 * @return int
	 */
	public function get_delay_hours()
	{
		return $this->delay_hours;
	}


	/**
	 * @return int
	 */
	public function get_delay_minutes()
	{
		return $this->delay_minutes;
	}


	/**
	 * @return int
	 */
	public function get_delay_seconds()
	{
		return $this->delay_seconds;
	}


	/**
	 * @return int
	 */
	public function get_total_delay_seconds()
	{
		return $this->total_delay_seconds;
	}



}