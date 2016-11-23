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
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: english.php 1260 2005-09-29 17:48:04Z gwinger $)

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

@setlocale(LC_TIME, 'en_US.utf8', 'en_US.UTF-8', 'en_EN@euro', 'en_US', 'en-US', 'en', 'English');

$coo_lang_file_master->init_from_lang_file('language_settings');
$coo_lang_file_master->init_from_lang_file('general');
$coo_lang_file_master->init_from_lang_file('gm_logger');
$coo_lang_file_master->init_from_lang_file('gm_shopping_cart');
$coo_lang_file_master->init_from_lang_file('gm_account_delete');
$coo_lang_file_master->init_from_lang_file('gm_price_offer');
$coo_lang_file_master->init_from_lang_file('gm_tell_a_friend');
$coo_lang_file_master->init_from_lang_file('gm_callback_service');
