<?php
/* --------------------------------------------------------------
   gm_gprint_admin_column_left.php 2009-11-13 mb
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2009 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/
?><?php 

if (($_SESSION['customers_status']['customers_status_id'] == '0') && ($admin_access['gm_gprint'] == '1')) echo '<li class="leftmenu_body_item"><a class="fav_drag_item" id="BOX_GM_GPRINT" href="' . xtc_href_link(FILENAME_GM_GPRINT, '', 'NONSSL') . '"">GX-Customizer</a></li>';

?>