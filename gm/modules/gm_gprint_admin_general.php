<?php
/* --------------------------------------------------------------
   general.php 2009-11-13 mb
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2009 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/
?><?php 

require_once('../gm/modules/gm_gprint_tables.php'); 
require_once('../gm/classes/GMJSON.php');
require_once('../gm/classes/GMGPrintOrderElements.php');
require_once('../gm/classes/GMGPrintOrderSurfaces.php');
require_once('../gm/classes/GMGPrintOrderSurfacesManager.php');
require_once('../gm/classes/GMGPrintOrderManager.php');

$coo_gm_gprint_order_manager = new GMGPrintOrderManager();
$coo_gm_gprint_order_manager->delete_order($order_id);

?>