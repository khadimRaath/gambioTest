<?php
/* --------------------------------------------------------------
  best_sellers.php 2014-10-17 gm
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2014 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------


  based on:
  (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
  (c) 2002-2003 osCommerce(best_sellers.php,v 1.20 2003/02/10); www.oscommerce.com
  (c) 2003	 nextcommerce (best_sellers.php,v 1.10 2003/08/17); www.nextcommerce.org
  (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: best_sellers.php 1292 2005-10-07 16:10:55Z mz $)

  Released under the GNU General Public License
  -----------------------------------------------------------------------------------------
  Third Party contributions:
  Enable_Disable_Categories 1.3        	Autor: Mikel Williams | mikel@ladykatcostumes.com

  Released under the GNU General Public License
  --------------------------------------------------------------------------------------- */

$coo_bestsellers = MainFactory::create_object('BestsellersBoxContentView');
$coo_bestsellers->set_('category_id', (int)$this->category_id);
$t_box_html = $coo_bestsellers->get_html();

$gm_box_pos = $GLOBALS['coo_template_control']->get_menubox_position('bestsellers');
$this->set_content_data($gm_box_pos, $t_box_html);