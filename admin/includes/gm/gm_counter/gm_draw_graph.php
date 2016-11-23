<?php
/* --------------------------------------------------------------
   gm_draw_graph.php 2008-04-24 gambio
   Gambio OHG
   http://www.gambio.de
   Copyright (c) 2008 Gambio OHG
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]

   IMPORTANT! THIS FILE IS DEPRECATED AND WILL BE REPLACED IN THE FUTURE. 
   MODIFY IT ONLY FOR FIXES. DO NOT APPEND IT WITH NEW FEATURES, USE THE
   NEW GX-ENGINE LIBRARIES INSTEAD.
   --------------------------------------------------------------
*/
?><?php
	defined('_VALID_XTC') or die('Direct Access to this location is not allowed.');

	require(DIR_FS_ADMIN . '/gm/classes/GMStat.php');

	$stat = new GMStat();

	$stat->setGraph();

?>