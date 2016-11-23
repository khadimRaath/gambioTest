<?php
/* --------------------------------------------------------------
  gm_logo.php 2016-02-02
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2016 Gambio GmbH
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

if(gm_get_conf('GM_LOGO_SHOP_USE') === '1')
{
	$logoManager = MainFactory::create('GMLogoManager', 'gm_logo_shop');
	$this->set_content_data('logo_url', $logoManager->logo_path . $logoManager->logo_file);
	$this->set_content_data('logo_link', xtc_href_link(FILENAME_DEFAULT));
}