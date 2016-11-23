<?php
/* --------------------------------------------------------------
   redirect.php 2013-09-20 gambio
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2013 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------


   based on: 
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(redirect.php,v 1.9 2003/02/13); www.oscommerce.com 
   (c) 2003	 nextcommerce (redirect.php,v 1.7 2003/08/17); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: redirect.php 1060 2005-07-21 18:32:58Z mz $)

   Released under the GNU General Public License 
   ---------------------------------------------------------------------------------------*/

include ('includes/application_top.php');

$coo_redirect_process = MainFactory::create_object('RedirectProcess');
$coo_redirect_process->set_data('GET', $_GET);

$coo_redirect_process->proceed();

$t_redirect_url = $coo_redirect_process->get_redirect_url();
if(empty($t_redirect_url) == false) 
{
	xtc_redirect($t_redirect_url);
} 

xtc_db_close();
