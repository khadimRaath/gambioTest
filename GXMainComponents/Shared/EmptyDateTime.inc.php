<?php
/* --------------------------------------------------------------
   EmptyDateTime.inc.php 2016-02-16
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * EmptyDateTime
 *
 * The purpose of this class is to represent a DateTime with support of an empty date 1000-01-01 00:00:00
 *
 * @category System
 * @package  Shared
 * @extends  DateTime
 */
class EmptyDateTime extends DateTime
{
	/**
	 * @var bool
	 */
	protected $isNullDate = false;
	
	
	/**
	 * @param string       $time     A date/time string. NULL will be represented as 1000-01-01 00:00:00 instead of
	 *                               the current time like in DateTime.
	 * @param DateTimeZone $timeZone A DateTimeZone object representing the timezone of $time. If $timezone is omitted,
	 *                               the current timezone will be used.
	 */
	public function __construct($time = '1000-01-01 00:00:00', DateTimeZone $timeZone = null)
	{
		if(strpos($time, '0000') === 0 || empty($time) || $time === '00.00.0000')
		{
			$time = '1000-01-01 00:00:00';
		}
		
		if($time === '1000-01-01 00:00:00')
		{
			$this->isNullDate = true;
		}
		
		parent::__construct($time, $timeZone);
	}
	
	
	/**
	 * Formats a date by a given pattern and ensures, that dates that represent empty data are formatted to
	 * '1000-01-01 00:00:00'
	 *
	 * @param string $format
	 *
	 * @return string
	 */
	public function format($format)
	{
		$formattedDate = parent::format($format);
		
		return $formattedDate;
	}
}