<?php
/* --------------------------------------------------------------
   AddAQuickieBoxContentView.inc.php 2015-05-29 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------


   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(add_a_quickie.php,v 1.10 2001/12/19); www.oscommerce.com
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: add_a_quickie.php 1262 2005-09-30 10:00:32Z mz $)

   Released under the GNU General Public License
   -----------------------------------------------------------------------------------------
   Third Party contribution:
   Add A Quickie v1.0 Autor  Harald Ponce de Leon

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

class AddAQuickieBoxContentView extends ContentView
{
	public function __construct()
	{
		parent::__construct();
		$this->set_content_template('boxes/box_add_a_quickie.html');
		$this->set_caching_enabled(false);
	}

	public function prepare_data()
	{
		$this->content_array['FORM_ID'] = 'quick_add';
		$this->content_array['FORM_METHOD'] = 'post';
		$this->content_array['FORM_ACTION_URL'] = xtc_href_link(basename(gm_get_env_info('PHP_SELF')), xtc_get_all_get_params(array('action')) . 'action=add_a_quickie', 'NONSSL', true, true, true);
		$this->content_array['INPUT_NAME'] = 'quickie';
	}
}