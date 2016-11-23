<?php
/* --------------------------------------------------------------
   function.gm_footer.php 2014-03-07 mb
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

function smarty_function_gm_footer($params, &$smarty)
{
	$coo_footer = MainFactory::create_object('FooterContentView');
	$coo_footer->set_('language_id', $_SESSION['languages_id']);
	$coo_footer->set_('customer_status_id', $_SESSION['customers_status']['customers_status_id']);
	
	$t_view_html = $coo_footer->get_html();
	
	return $t_view_html;
}