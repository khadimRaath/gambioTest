<?php
/* --------------------------------------------------------------
	IntrashipAdminOrderActionExtender.inc.php 2015-12-15
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2015 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/

class IntrashipAdminOrderActionExtender extends IntrashipAdminOrderActionExtender_parent {
	public function proceed()
	{
		parent::proceed();
		if(isset($_SESSION['intraship_warning_not_codeable']) && $_SESSION['intraship_warning_not_codeable'] == true)
		{
			$intraship = new GMIntraship();
			$GLOBALS['messageStack']->add_session($intraship->get_text('label_not_codeable'));
			unset($_SESSION['intraship_warning_not_codeable']);
		}
	}
}
