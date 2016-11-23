<?php
/* --------------------------------------------------------------
  gm_logo.php 2014-02-11 gm
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2014 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------


  based on:
  (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
  (c) 2002-2003 osCommerce(languages.php,v 1.14 2003/02/12); www.oscommerce.com
  (c) 2003	 nextcommerce (languages.php,v 1.8 2003/08/17); www.nextcommerce.org
  (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: languages.php 1262 2005-09-30 10:00:32Z mz $)

  Released under the GNU General Public License
  --------------------------------------------------------------------------------------- */

$gm_logo_values = gm_get_conf(array('GM_LOGO_FLASH_USE', 'GM_LOGO_SHOP_USE'));

if($gm_logo_values['GM_LOGO_SHOP_USE'] == '1')
{
	$gm_logo = MainFactory::create_object('GMLogoManager', array("gm_logo_shop"));
	$this->set_content_data('gm_logo', '<a href="' . xtc_href_link(FILENAME_DEFAULT, '', 'NONSSL') . '">' . $gm_logo->get_logo() . '</a>');
}
else if($gm_logo_values['GM_LOGO_FLASH_USE'] == '1')
{
	$gm_logo = MainFactory::create_object('GMLogoManager', array("gm_logo_flash"));
	$this->set_content_data('gm_logo', $gm_logo->get_logo());
}