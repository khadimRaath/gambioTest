<?php
/* --------------------------------------------------------------
	OutputBufferingApplicationTopExtender.inc.php 2.5_2014-08-11_1722 mabr
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2014 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/

class OutputBufferingApplicationTopExtender extends OutputBufferingApplicationTopExtender_parent
{
	public function proceed()
	{
		ob_start();
		parent::proceed();
	}
}
