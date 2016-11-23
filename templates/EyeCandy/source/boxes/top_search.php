<?php
/* --------------------------------------------------------------
  top_menu.php 2014-07-17 gm
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2014 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
 */

$coo_top_search = MainFactory::create_object('SearchBoxContentView');
$coo_top_search->set_content_template('boxes/box_top_search.html');
$t_top_search_html = $coo_top_search->get_html();
$this->set_content_data('TOP_SEARCH', $t_top_search_html);