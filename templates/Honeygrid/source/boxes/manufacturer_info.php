<?php
/* --------------------------------------------------------------
  manufacturer_info.php 2014-07-17 gm
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2014 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
*/

$coo_manufacturers_info = MainFactory::create_object('ManufacturersInfoBoxContentView');
$coo_manufacturers_info->set_('coo_product', $this->coo_product);

$coo_manufacturers_info->set_('language_id', $_SESSION['languages_id']);
$t_box_html = $coo_manufacturers_info->get_html();

$gm_box_pos = $GLOBALS['coo_template_control']->get_menubox_position('manufacturers_info');
$this->set_content_data($gm_box_pos, $t_box_html);