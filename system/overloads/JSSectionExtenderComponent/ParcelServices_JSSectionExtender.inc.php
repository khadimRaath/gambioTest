<?php
/* --------------------------------------------------------------
   ParcelServices_JSSectionExtender.inc.php 2015-07-28 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class ParcelServices_JSSectionExtender extends ParcelServices_JSSectionExtender_parent
{
	protected function parcel_service_edit()
	{
		include_once(get_usermod(DIR_FS_CATALOG . 'admin/html/assets/javascript/legacy/tracking/parcel_service_edit.js'));
	}
}
