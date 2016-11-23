<?php
/* --------------------------------------------------------------
   ScrollerBoxContentView.inc.php 2014-07-17 gambio
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------


   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(languages.php,v 1.14 2003/02/12); www.oscommerce.com
   (c) 2003	 nextcommerce (languages.php,v 1.8 2003/08/17); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: languages.php 1262 2005-09-30 10:00:32Z mz $)

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

// include functions
require_once(DIR_FS_INC . 'xtc_hide_session_id.inc.php');

class ScrollerBoxContentView extends ContentView
{
	protected $language_id = 2;
	
	public function __construct()
	{
		parent::__construct();
		$this->set_content_template('boxes/box_gm_scroller.html');
	}

	public function prepare_data()
	{
		$this->content_array['CONTENT'] = gm_get_content('GM_SCROLLER_CONTENT', $this->language_id);
		$this->content_array['HEIGHT'] = gm_get_conf('GM_SCROLLER_HEIGHT');
	}
	
	protected function set_validation_rules()
	{
		$this->validation_rules_array['language_id'] = array('type' => 'int');
	}
}