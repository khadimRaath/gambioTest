<?php
/* --------------------------------------------------------------
   download.php 2014-06-19 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------


   based on: 
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(download.php,v 1.9 2003/02/13); www.oscommerce.com 
   (c) 2003	 nextcommerce (download.php,v 1.7 2003/08/17); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: download.php 831 2005-03-13 10:16:09Z mz $)

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

include ('includes/application_top.php');


$coo_download_process = MainFactory::create_object('DownloadProcess');
$coo_download_process->set_('download_id', $_GET['id']);
$coo_download_process->set_('order_id', $_GET['order']);
$coo_download_process->set_('customer_id', $_SESSION['customer_id']);
$coo_download_process->proceed();

xtc_db_close();