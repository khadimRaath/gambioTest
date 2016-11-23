<?php
/* --------------------------------------------------------------
   application_bottom.php 2014-03-18 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------


   based on:
  (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(application_bottom.php,v 1.8 2002/03/15); www.oscommerce.com
   (c) 2003	 nextcommerce (application_bottom.php,v 1.6 2003/08/1); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: application_bottom.php 899 2005-04-29 02:40:57Z hhgag $)

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/


LogControl::get_instance()->get_stop_watch()->stop();
LogControl::get_instance()->write_time_log();

$coo_application_bottom_extender_component = MainFactory::create_object('AdminApplicationBottomExtenderComponent');
$coo_application_bottom_extender_component->set_data('GET', $_GET);
$coo_application_bottom_extender_component->set_data('POST', $_POST);
$coo_application_bottom_extender_component->proceed();

((is_null($___mysqli_res = mysqli_close($GLOBALS["___mysqli_ston"]))) ? false : $___mysqli_res);
