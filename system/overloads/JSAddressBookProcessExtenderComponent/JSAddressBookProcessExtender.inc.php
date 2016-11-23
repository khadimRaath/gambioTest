<?php
/* --------------------------------------------------------------
   JSAddressBookProcessExtender.inc.php 2015-06-05 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class JSAddressBookProcessExtender extends JSAddressBookProcessExtender_parent
{
	function proceed()
	{
		parent::proceed();

		if(ACCOUNT_B2B_STATUS === 'true' && gm_get_env_info('TEMPLATE_VERSION') >= FIRST_GX2_TEMPLATE_VERSION)
		{
			include_once(get_usermod(DIR_FS_CATALOG . 'templates/'.CURRENT_TEMPLATE.'/javascript/B2BStatusDependencyHandler.js'));
		}
	}
}