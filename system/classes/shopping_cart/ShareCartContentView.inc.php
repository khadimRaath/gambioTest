<?php

/* --------------------------------------------------------------
   ShareCart.inc.php 2016-04-05
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class ShareCartContentView extends ContentView
{
	public function __construct()
	{
		parent::__construct();
		$this->set_content_template('module/share_cart.html');
		$this->set_flat_assigns(true);
	}


	public function prepare_data()
	{
		$this->_setFormData();
		$this->set_content_data('ACTIVATE_SHARED_CART', 'true');
		$this->build_html = true;
	}


	protected function _setFormData()
	{
		/** TODO: Logic for getting the Shared Link */
		$this->set_content_data('SHARE_LINK', 'LINK_HERE');
	}
}