<?php
/* --------------------------------------------------------------
   SearchBoxContentView.inc.php 2015-05-29 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------


   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(search.php,v 1.22 2003/02/10); www.oscommerce.com
   (c) 2003	 nextcommerce (search.php,v 1.9 2003/08/17); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: search.php 1262 2005-09-30 10:00:32Z mz $)

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

require_once(DIR_FS_INC . 'xtc_image_submit.inc.php');
require_once(DIR_FS_INC . 'xtc_hide_session_id.inc.php');

class SearchBoxContentView extends ContentView
{
	public function __construct()
	{
		parent::__construct();
		$this->set_content_template('boxes/box_search.html');
	}

	public function prepare_data()
	{
		$this->content_array['FORM_ID'] = 'quick_find';
		$this->content_array['FORM_METHOD'] = 'get';
		$this->content_array['FORM_ACTION_URL'] = xtc_href_link(FILENAME_ADVANCED_SEARCH_RESULT, '', 'NONSSL', false, true, true);
		$this->content_array['INPUT_NAME'] = 'keywords';
		$this->content_array['LINK_ADVANCED'] = xtc_href_link(FILENAME_ADVANCED_SEARCH);

		if(gm_get_conf('GM_OPENSEARCH_BOX') == '1')
		{
			$this->content_array['GM_OPENSEARCH_TITLE'] = TEXT_OPENSEARCH;
			$this->content_array['GM_OPENSEARCH_TEXT'] = gm_get_content('GM_OPENSEARCH_TEXT', $_SESSION['languages_id']);
		}
	}
}