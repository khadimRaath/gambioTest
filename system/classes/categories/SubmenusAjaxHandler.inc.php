<?php
/* --------------------------------------------------------------
   SubmenusAjaxHandler.inc.php 2014-07-17 mb
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class SubmenusAjaxHandler extends AjaxHandler
{
	function get_permission_status($p_customers_id=NULL)
	{
		return true;
	}

	function proceed()
	{
		$c_parent_categories_id = (int)$this->v_data_array['GET']['id'];

		# Categories Submenus
		$coo_categories_content_view = MainFactory::create_object('CategoriesSubmenusBoxContentView');
		$coo_categories_content_view->setCustomerStatusId($_SESSION['customers_status']['customers_status_id']);
		$coo_categories_content_view->setLanguage($_SESSION['language']);
		$coo_categories_content_view->setLanguageId($_SESSION['languages_id']);
		$coo_categories_content_view->setCurrency($_SESSION['currency']);
		$coo_categories_content_view->setParentCategoriesId($c_parent_categories_id);
		$this->v_output_buffer = $coo_categories_content_view->get_html();

		return true;
	}
}