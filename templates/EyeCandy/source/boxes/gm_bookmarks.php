<?php
/* --------------------------------------------------------------
  gm_bookmarks.php 2014-09-02 gm
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

$coo_bookmarks = MainFactory::create_object('BookmarksBoxContentView');

if((int)$_GET['manufacturers_id'] != 0)
{
	$coo_bookmarks->set_('manufacturer_id', (int)$_GET['manufacturers_id']);
}

$coo_bookmarks->set_('coo_product', $this->coo_product);
$coo_bookmarks->set_('c_path', $this->c_path);
$t_box_html = $coo_bookmarks->get_html();

$gm_box_pos = $GLOBALS['coo_template_control']->get_menubox_position('gm_bookmarks');
$this->set_content_data($gm_box_pos, $t_box_html);