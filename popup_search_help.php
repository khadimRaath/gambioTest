<?php
/* --------------------------------------------------------------
   popup_search_help.php 2013-10-02 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2013 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------


   based on: 
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(popup_search_help.php,v 1.3 2003/02/13); www.oscommerce.com
   (c) 2003	 nextcommerce (popup_search_help.php,v 1.6 2003/08/17); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: http://dev1.gambio-shop.de/2008/shop/gambio/icons/persdaten.png 1238 2005-09-24 10:51:19Z mz $) 

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

include ('includes/application_top.php');

require_once (DIR_FS_CATALOG . 'gm/modules/gm_popup_header.php');
$coo_popup_search_help = MainFactory::create_object('PopupSearchHelpContentView');
$t_view_html = $coo_popup_search_help->get_html();

echo $t_view_html;

xtc_db_close();