<?php
/* --------------------------------------------------------------
   popup_content.php 2014-06-30 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------


   based on:
   (c) 2003	 nextcommerce (content_preview.php,v 1.2 2003/08/25); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: popup_content.php 1169 2005-08-22 16:07:09Z mz $)

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

include ('includes/application_top.php');

// create smarty elements
$smarty = new Smarty;

$coo_popup_content_view = MainFactory::create_object('PopupContentContentView');
if(isset($_GET['coID']))
{
	$coo_popup_content_view->set_('content_group_id', $_GET['coID']);
}
if(isset($_GET['lightbox_mode']))
{
	$coo_popup_content_view->set_('lightbox_mode', $_GET['lightbox_mode']);
}
$coo_popup_content_view->set_('language_id', $_SESSION['languages_id']);
$coo_popup_content_view->set_('customer_status_id', $_SESSION['customers_status']['customers_status_id']);
$t_view_html = $coo_popup_content_view->get_html();

echo $t_view_html;

xtc_db_close();