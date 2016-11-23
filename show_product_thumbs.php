<?php
/* --------------------------------------------------------------
   show_product_thumbs.php 2014-02-25 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------


   based on: 
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(popup_image.php,v 1.12 2001/12/12); www.oscommerce.com
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: show_product_thumbs.php 831 2005-03-13 10:16:09Z mz $)

   Third Party contributions:
   Modified by BIA Solutions (www.biasolutions.com) to create a bordered look to the image

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

require ('includes/application_top.php');

$coo_product_thumbs_view = MainFactory::create_object('ShowProductThumbsContentView');
$coo_product_thumbs_view->set_('products_id', $_GET['pID']);
$coo_product_thumbs_view->set_('languages_id', $_SESSION['languages_id']);
$coo_product_thumbs_view->set_('image_id', $_GET['imgID']);
echo $t_view_html = $coo_product_thumbs_view->get_html();

xtc_db_close();
