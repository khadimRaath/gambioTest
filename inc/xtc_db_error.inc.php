<?php
/* --------------------------------------------------------------
   xtc_db_error.inc.php 2014-08-05 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------

   based on: 
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(database.php,v 1.19 2003/03/22); www.oscommerce.com 
   (c) 2003	 nextcommerce (xtc_db_error.inc.php,v 1.4 2003/08/19); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: xtc_db_error.inc.php 899 2005-04-29 02:40:57Z hhgag $) 

   Released under the GNU General Public License 
   ---------------------------------------------------------------------------------------*/
   
function xtc_db_error($p_query, $p_errno, $p_error) 
{
	$coo_logger = LogControl::get_instance();
	$coo_logger->notice($p_error, 'error_handler', 'errors', 'notice', 'SQL ERROR', $p_errno, 'Query:' . "\r\n" . trim($p_query));
	trigger_error('SQL Error', E_USER_ERROR);
}