<?php
/* --------------------------------------------------------------
   CreateVVCodeAjaxHandler.inc.php 2013-11-13 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2013 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

require_once(DIR_FS_INC.'xtc_render_vvcode.inc.php');

class CreateVVCodeAjaxHandler extends AjaxHandler
{
	function get_permission_status($p_customers_id=NULL)
	{
		return true;
	}

	function proceed()
	{
		vvcode_render_code($_SESSION['vvcode']);
		return true;
	}
}