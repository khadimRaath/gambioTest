<?php
/* --------------------------------------------------------------
   modifier.detect_page.php 2015-10-27 tw@gambio
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

function smarty_modifier_detect_page($string)
{
	$coo_application_bottom_extender_component = MainFactory::create_object('ApplicationBottomExtenderComponent');
	$coo_application_bottom_extender_component->set_data('GET', $_GET);
	$coo_application_bottom_extender_component->init_page();
	$t_page = $coo_application_bottom_extender_component->get_page();
	
	return $t_page;
}