<?php
/* --------------------------------------------------------------
  reviews.php 2014-07-17 gm
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2014 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------


  based on:
  (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
  (c) 2002-2003 osCommerce(reviews.php,v 1.36 2003/02/12); www.oscommerce.com
  (c) 2003	 nextcommerce (reviews.php,v 1.9 2003/08/17 22:40:08); www.nextcommerce.org
  (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: reviews.php 1262 2005-09-30 10:00:32Z mz $)

  Released under the GNU General Public License
  --------------------------------------------------------------------------------------- */

$coo_reviews = MainFactory::create_object('ReviewsBoxContentView');
$coo_reviews->set_('coo_product', $this->coo_product);
$coo_reviews->set_('language_id', $_SESSION['languages_id']);
if(isset($_SESSION['style_edit_mode']))
{
	$coo_reviews->set_('style_edit_mode', $_SESSION['style_edit_mode']);
}
$coo_reviews->set_('customers_fsk18_display', $_SESSION['customers_status']['customers_fsk18_display']);
$t_box_html = $coo_reviews->get_html();

$gm_box_pos = $GLOBALS['coo_template_control']->get_menubox_position('reviews');
$this->set_content_data($gm_box_pos, $t_box_html);