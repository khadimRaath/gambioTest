<?php
/* --------------------------------------------------------------
  xtc_db_select_db.inc.php 2016-03-07
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2015 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
*/
/* -----------------------------------------------------------------------------------------
   $Id: xtc_db_select_db.inc.php 899 2005-04-29 02:40:57Z hhgag $

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   -----------------------------------------------------------------------------------------
   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(database.php,v 1.2 2002/03/02); www.oscommerce.com
   (c) 2003	 nextcommerce (xtc_db_select_db.inc.php,v 1.3 2003/08/13); www.nextcommerce.org

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

function xtc_db_select_db($database)
{
	return ((bool)mysqli_select_db($GLOBALS["___mysqli_ston"], $database));
}
