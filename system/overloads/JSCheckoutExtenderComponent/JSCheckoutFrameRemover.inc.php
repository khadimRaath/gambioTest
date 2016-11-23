<?php
/* --------------------------------------------------------------
	JSCheckoutFrameRemover.inc.php 2014-07-17 mabr
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2014 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/

class JSCheckoutFrameRemover extends JSCheckoutFrameRemover_parent
{
	public function proceed()
	{
		parent::proceed();

		if(isset($_SESSION['payment']) && strpos($_SESSION['payment'], 'wcp_') !== 0)
		{
			echo 'if(top != self) { top.location = self.location; }'.PHP_EOL;
		}
	}
}
