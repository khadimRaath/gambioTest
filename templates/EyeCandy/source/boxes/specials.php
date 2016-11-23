<?php
/* --------------------------------------------------------------
  specials.php 2014-07-17 gm
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2014 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------


  based on:
  (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
  (c) 2002-2003 osCommerce(specials.php,v 1.30 2003/02/10); www.oscommerce.com
  (c) 2003	 nextcommerce (specials.php,v 1.10 2003/08/17); www.nextcommerce.org
  (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: specials.php 1292 2005-10-07 16:10:55Z mz $)

  Released under the GNU General Public License
  --------------------------------------------------------------------------------------- */

$coo_specials = MainFactory::create_object('SpecialsBoxContentView');
$coo_specials->set_('coo_product', $this->coo_product);
$t_box_html = $coo_specials->get_html();

$gm_box_pos = $GLOBALS['coo_template_control']->get_menubox_position('specials');
$this->set_content_data($gm_box_pos, $t_box_html);