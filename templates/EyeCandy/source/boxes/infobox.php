<?php
/* --------------------------------------------------------------
  infobox.php 2014-07-17 gm
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2014 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------


  based on:
  (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
  (c) 2002-2003 osCommercebased on original files from OSCommerce CVS 2.2 2002/08/28 02:14:35 www.oscommerce.com
  (c) 2003	 nextcommerce (infobox.php,v 1.7 2003/08/13); www.nextcommerce.org
  (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: infobox.php 1262 2005-09-30 10:00:32Z mz $)

  Released under the GNU General Public License
  -----------------------------------------------------------------------------------------
  Third Party contributions:
  Loginbox V1.0        	Aubrey Kilian <aubrey@mycon.co.za>

  Released under the GNU General Public License
  --------------------------------------------------------------------------------------- */

$coo_infobox = MainFactory::create_object('InfoboxBoxContentView');
$t_box_html = $coo_infobox->get_html();

$gm_box_pos = $GLOBALS['coo_template_control']->get_menubox_position('infobox');
$this->set_content_data($gm_box_pos, $t_box_html);