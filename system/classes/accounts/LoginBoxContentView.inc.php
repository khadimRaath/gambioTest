<?php
/* --------------------------------------------------------------
   LoginBoxContentView.inc.php 2015-05-29 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------


   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommercebased on original files from OSCommerce CVS 2.2 2002/08/28 02:14:35 www.oscommerce.com
   (c) 2003	 nextcommerce (loginbox.php,v 1.10 2003/08/17); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: loginbox.php 1262 2005-09-30 10:00:32Z mz $)

   Released under the GNU General Public License
   -----------------------------------------------------------------------------------------
   Third Party contributions:
   Loginbox V1.0        	Aubrey Kilian <aubrey@mycon.co.za>

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

class LoginBoxContentView extends ContentView
{
	public function __construct()
	{
		parent::__construct();
		$this->set_content_template('boxes/box_login.html');
	}

	public function prepare_data()
	{
		$this->build_html = false;
		
		if(!xtc_session_is_registered('customer_id') || $_SESSION['style_edit_mode'] == 'edit')
		{
			$this->content_array['FORM_ID'] = 'loginbox';
			$this->content_array['FORM_METHOD'] = 'post';
			$this->content_array['FORM_ACTION_URL'] = xtc_href_link(FILENAME_LOGIN, 'action=process', 'SSL');
			$this->content_array['FIELD_EMAIL_NAME'] = 'email_address';
			$this->content_array['FIELD_PWD_NAME'] = 'password';
			$this->content_array['LINK_CREATE_ACCOUNT'] = xtc_href_link(FILENAME_LOGIN, '', 'SSL');
			$this->content_array['LINK_LOST_PASSWORD'] = xtc_href_link(FILENAME_PASSWORD_DOUBLE_OPT, '', 'SSL');
			
			$this->build_html = true;
		}
	}
}