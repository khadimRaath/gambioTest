<?php
/* --------------------------------------------------------------
   SSLCheckContenView.inc.php 2014-02-25 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------


   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(new_products.php,v 1.33 2003/02/12); www.oscommerce.com
   (c) 2003	 nextcommerce (new_products.php,v 1.9 2003/08/17); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: ssl_check.php 1238 2005-09-24 10:51:19Z mz $)

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

class SSLCheckContentView extends ContentView
{
	public function __construct()
	{
		parent::__construct();
		$this->set_content_template('module/ssl_check.html');
		$this->set_flat_assigns(true);
	}

	public function prepare_data()
	{
		$this->content_array['BUTTON_CONTINUE'] = '<a href="'.xtc_href_link(FILENAME_DEFAULT).'">'.xtc_image_button('button_continue.gif', IMAGE_BUTTON_CONTINUE).'</a>';
	}
}