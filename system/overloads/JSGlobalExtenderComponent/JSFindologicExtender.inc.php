<?php
/* --------------------------------------------------------------
	JSFindologicExtender.inc.php 2014-07-04 mabr
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2014 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/

class JSFindologicExtender extends JSFindologicExtender_parent
{
	public function proceed()
	{
		parent::proceed();

		if(gm_get_conf('FL_USE_SEARCH') == 1)
		{
			$t_jsfile = get_usermod(DIR_FS_CATALOG.'templates/'.CURRENT_TEMPLATE.'/javascript/Findologic.js');
			if(file_exists($t_jsfile))
			{
				include $t_jsfile;
			}
		}
	}
}
