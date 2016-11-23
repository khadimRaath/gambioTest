<?php
/* --------------------------------------------------------------
   gm_opensearch.php 2014-07-17 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/
require ('includes/application_top.php');
	
$coo_opensearch = MainFactory::create_object('OpensearchBoxContentView');
$t_view_html = $coo_opensearch->get_html();

echo $t_view_html;

xtc_db_close();