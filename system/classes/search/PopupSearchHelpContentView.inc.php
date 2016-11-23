<?php
/* --------------------------------------------------------------
  PopupSearchHelpContentView.inc.php 2014-02-28 gm
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2014 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------


  based on:
  (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
  (c) 2002-2003 osCommerce(popup_search_help.php,v 1.3 2003/02/13); www.oscommerce.com
  (c) 2003	 nextcommerce (popup_search_help.php,v 1.6 2003/08/17); www.nextcommerce.org
  (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: http://dev1.gambio-shop.de/2008/shop/gambio/icons/persdaten.png 1238 2005-09-24 10:51:19Z mz $)

  Released under the GNU General Public License
  --------------------------------------------------------------------------------------- */

class PopupSearchHelpContentView extends ContentView 
{
	public function __construct() 
	{
		parent::__construct();
		$this->set_content_template('module/popup_search_help.html');
		$this->set_flat_assigns(true);
	}

	public function prepare_data() 
	{
		$this->content_array['link_close'] = 'javascript:window.close()';
	}
}