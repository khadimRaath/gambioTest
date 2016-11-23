<?php
/* --------------------------------------------------------------
  last_viewed.php 2014-07-17 gm
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2014 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------


  (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: last_viewed.php 1292 2005-10-07 16:10:55Z mz $)

  Released under the GNU General Public License
  --------------------------------------------------------------------------------------- */

$coo_last_viewed = MainFactory::create_object('LastViewedBoxContentView');
$coo_last_viewed->set_('coo_product', $this->coo_product);
$coo_last_viewed->set_('coo_xtc_price', $this->coo_xtc_price);
$t_box_html = $coo_last_viewed->get_html();

$gm_box_pos = $GLOBALS['coo_template_control']->get_menubox_position('last_viewed');
$this->set_content_data($gm_box_pos, $t_box_html);