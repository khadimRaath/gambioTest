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
   (c) 2002-2003 osCommerce(german.php,v 1.119 2003/05/19); www.oscommerce.com
   (c) 2003  nextcommerce (german.php,v 1.25 2003/08/25); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: german.php 1308 2005-10-15 14:22:18Z hhgag $)

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

@setlocale(LC_TIME, 'de_DE.utf8', 'de_DE.UTF-8', 'de_DE@euro', 'de_DE', 'de-DE', 'de', 'ge', 'German');

$coo_lang_file_master->init_from_lang_file('language_settings');
$coo_lang_file_master->init_from_lang_file('general');
$coo_lang_file_master->init_from_lang_file('gm_logger');
$coo_lang_file_master->init_from_lang_file('gm_shopping_cart');
$coo_lang_file_master->init_from_lang_file('gm_account_delete');
$coo_lang_file_master->init_from_lang_file('gm_price_offer');
$coo_lang_file_master->init_from_lang_file('gm_tell_a_friend');
$coo_lang_file_master->init_from_lang_file('gm_callback_service');
