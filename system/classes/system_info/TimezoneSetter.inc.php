<?php
/* --------------------------------------------------------------
  TimezoneSetter.inc.php 2014-08-28 gambio
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2014 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
 */

class TimezoneSetter
{
	public function set_date_default_timezone($p_timezone = false)
	{
		if($p_timezone !== false)
		{
			@date_default_timezone_set($p_timezone);
		}
		elseif(defined('DATE_TIMEZONE'))
		{
			@date_default_timezone_set(DATE_TIMEZONE);
		}
		else
		{
			@date_default_timezone_set('Europe/Berlin');
		}
	}
}