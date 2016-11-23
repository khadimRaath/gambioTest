<?php
/* --------------------------------------------------------------
  AdvancedSearchContentView.inc.php 2015-05-29 gm
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2015 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------


  based on:
  (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
  (c) 2002-2003 osCommerce(advanced_search.php,v 1.49 2003/02/13); www.oscommerce.com
  (c) 2003	 nextcommerce (advanced_search.php,v 1.13 2003/08/21); www.nextcommerce.org
  (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: advanced_search.php 988 2005-06-18 16:42:42Z mz $)

  Released under the GNU General Public License
  --------------------------------------------------------------------------------------- */

// include needed functions
require_once(DIR_FS_INC . 'xtc_get_categories.inc.php');
require_once(DIR_FS_INC . 'xtc_get_manufacturers.inc.php');
require_once(DIR_FS_INC . 'xtc_checkdate.inc.php');

class AdvancedSearchContentView extends ContentView
{
	function __construct()
	{
		parent::__construct();
		$this->set_content_template('module/advanced_search.html');
		$this->set_flat_assigns(true);
	}
	
	function prepare_data()
	{
		$this->content_array['FORM_ID'] = 'advancedsearch';
		$this->content_array['FORM_ACTION_URL'] = xtc_href_link(FILENAME_ADVANCED_SEARCH_RESULT, '', 'NONSSL', false, true, true);
		$this->content_array['FORM_METHOD'] = 'get';

		$this->content_array['INPUT_KEYWORDS_NAME'] = 'keywords';

		$t_categories_array = xtc_get_categories(array(array('id' => '', 'text' => TEXT_ALL_CATEGORIES)));
		$this->content_array['categories_data'] = $t_categories_array;
		$this->content_array['SELECT_CATEGORIES_NAME'] = 'categories_id';

		$this->content_array['INPUT_SUBCAT_NAME'] = 'inc_subcat';
		$this->content_array['INPUT_SUBCAT_VALUE'] = '1';

		$t_manufacturers_array = xtc_get_manufacturers(array(array('id' => '', 'text' => TEXT_ALL_MANUFACTURERS)));
		$this->content_array['manufacturers_data'] = $t_manufacturers_array;
		$this->content_array['SELECT_MANUFACTURERS_NAME'] = 'manufacturers_id';

		$this->content_array['SELECT_PFROM_NAME'] = 'pfrom';

		$this->content_array['SELECT_PTO_NAME'] = 'pto';

		$error = '';
		if(isset($_GET['errorno']))
		{
			if(($_GET['errorno'] & 1) == 1)
			{
				$error .= str_replace('\n', '<br />', JS_AT_LEAST_ONE_INPUT);
			}
			if(($_GET['errorno'] & 10) == 10)
			{
				$error .= str_replace('\n', '<br />', JS_INVALID_FROM_DATE);
			}
			if(($_GET['errorno'] & 100) == 100)
			{
				$error .= str_replace('\n', '<br />', JS_INVALID_TO_DATE);
			}
			if(($_GET['errorno'] & 1000) == 1000)
			{
				$error .= str_replace('\n', '<br />', JS_TO_DATE_LESS_THAN_FROM_DATE);
			}
			if(($_GET['errorno'] & 10000) == 10000)
			{
				$error .= str_replace('\n', '<br />', JS_PRICE_FROM_MUST_BE_NUM);
			}
			if(($_GET['errorno'] & 100000) == 100000)
			{
				$error .= str_replace('\n', '<br />', JS_PRICE_TO_MUST_BE_NUM);
			}
			if(($_GET['errorno'] & 1000000) == 1000000)
			{
				$error .= str_replace('\n', '<br />', JS_PRICE_TO_LESS_THAN_PRICE_FROM);
			}
			if(($_GET['errorno'] & 10000000) == 10000000)
			{
				$error .= str_replace('\n', '<br />', JS_INVALID_KEYWORDS);
			}
		}

		if(gm_get_conf('GM_OPENSEARCH_SEARCH') == 1)
		{
			$this->content_array['gm_opensearch_link_text'] = gm_get_content('GM_OPENSEARCH_LINK', $_SESSION['languages_id']);
			$this->content_array['gm_opensearch_link'] = xtc_href_link('export/opensearch_' . $_SESSION['languages_id'] . '.xml');
		}

		$this->content_array['error'] = $error;
	}
}