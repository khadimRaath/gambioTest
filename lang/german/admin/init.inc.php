<?php
/* --------------------------------------------------------------
   init.inc.php 2015-03-24 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------


   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(german.php,v 1.99 2003/05/28); www.oscommerce.com
   (c) 2003  nextcommerce (german.php,v 1.24 2003/08/24); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: german.php 905 2005-04-29 13:02:06Z novalis $)

   Released under the GNU General Public License
   --------------------------------------------------------------
   Third Party contributions:
   Customers Status v3.x (c) 2002-2003 Copyright Elari elari@free.fr | www.unlockgsm.com/dload-osc/ | CVS : http://cvs.sourceforge.net/cgi-bin/viewcvs.cgi/elari/?sortby=date#dirlist

   Released under the GNU General Public License
   --------------------------------------------------------------*/

@setlocale(LC_TIME, 'de_DE.utf8', 'de_DE.UTF-8', 'de_DE@euro', 'de_DE', 'de-DE', 'de', 'ge', 'German');

$coo_lang_file_master->init_from_lang_file('language_settings');
$coo_lang_file_master->init_from_lang_file('admin_general');
$coo_lang_file_master->init_from_lang_file('gm_general');
$coo_lang_file_master->init_from_lang_file('admin/includes/modules/yoochoose/yoo_lang_german.php');
