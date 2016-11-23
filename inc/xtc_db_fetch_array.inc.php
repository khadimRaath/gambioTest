<?php
/* --------------------------------------------------------------
   xtc_db_fetch_array.inc.php 2008-07-21 gambio
   Gambio OHG
   http://www.gambio.de
   Copyright (c) 2008 Gambio OHG
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------

xtc_db_fetch_array.inc.php 21.07.2008 pty
   Gambio OHG
   http://www.gambio.de
   Copyright (c) 2007 Gambio OHG
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------


   based on: 
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(database.php,v 1.19 2003/03/22); www.oscommerce.com 
   (c) 2003	 nextcommerce (xtc_db_fetch_array.inc.php,v 1.3 2003/08/13); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: xtc_db_fetch_array.inc.php 864 2005-04-16 12:05:41Z mz $)

   Released under the GNU General Public License 
   ---------------------------------------------------------------------------------------*/
  /*
  function xtc_db_fetch_array($db_query) {
    return mysql_fetch_array($db_query, MYSQL_ASSOC);
  }
  */

function xtc_db_fetch_array(&$db_query,$cq=false) {

	if (DB_CACHE=='true' && $cq) {
		if (!count($db_query)) { 
			return false;
		} else {
			// bof gm
			if(is_array($db_query)) {
				$curr = current($db_query);
				next($db_query);
				return $curr;
			} else {
				return false;
			}
			// eof gm
		}
	} else {
		if (is_array($db_query)) {
			$curr = current($db_query);
			next($db_query);
			return $curr;
		}
		return mysqli_fetch_array($db_query,  MYSQLI_ASSOC);
	}
}

 ?>