<?php
/* --------------------------------------------------------------
  trusted.php 2014-07-17 gm
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2014 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------


  based on:
  (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
  (c) 2002-2003 osCommerce(add_a_quickie.php,v 1.10 2001/12/19); www.oscommerce.com
  (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: add_a_quickie.php,v 1.1 2004/04/26 20:26:42 fanta2k Exp $)

  Released under the GNU General Public License
  -----------------------------------------------------------------------------------------
  Third Party contribution:
  Add A Quickie v1.0 Autor  Harald Ponce de Leon

  Released under the GNU General Public License
  --------------------------------------------------------------------------------------- */

$coo_trusted = MainFactory::create_object('TrustedBoxContentView');
$t_box_html = $coo_trusted->get_html();

$gm_box_pos = $GLOBALS['coo_template_control']->get_menubox_position('trusted');
$this->set_content_data($gm_box_pos, $t_box_html);