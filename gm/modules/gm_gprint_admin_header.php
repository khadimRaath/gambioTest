<?php
/* --------------------------------------------------------------
   gm_gprint_admin_header.php 2016-07-14 
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

if(strstr($_SERVER['PHP_SELF'], 'gm_gprint.php') == 'gm_gprint.php')
{ 
	echo '<script type="text/javascript" src="' . DIR_WS_CATALOG . 'gm/javascript/jquery/plugins/ajaxfileupload/ajaxfileupload.js"></script>';
} 
