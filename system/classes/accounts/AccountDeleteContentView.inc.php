<?php
/* --------------------------------------------------------------
   AccountDeleteContentView.inc.php 2015-05-29 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------

   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com
   -----------------------------------------------------------------------------------------
   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce www.oscommerce.com
   (c) 2003	 nextcommerce www.nextcommerce.org

   XTC-NEWSLETTER_RECIPIENTS RC1 - Contribution for XT-Commerce http://www.xt-commerce.com
   by Matthias Hinsche http://www.gamesempire.de

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

class AccountDeleteContentView extends ContentView
{
	public function __construct()
	{
		parent::__construct();
		$this->set_content_template('module/gm_account_delete.html');
		$this->set_flat_assigns(true);
		$this->set_caching_enabled(false);
	}
	
	public function prepare_data()
	{
		$this->content_array['FORM_ID'] = 'gm_account_delete';
		$this->content_array['FORM_ACTION_URL'] = xtc_href_link(FILENAME_ACCOUNT, '', 'SSL');
		$this->content_array['FORM_METHOD'] = 'post';
		$this->content_array['HIDDEN_ACTION_FIELD'] = xtc_draw_hidden_field('action', 'gm_delete_account');
		$this->content_array['GM_COMMENTS_NAME'] = 'gm_content';
		$this->content_array['BUTTON_BACK_LINK'] = xtc_href_link(FILENAME_ACCOUNT, '', 'SSL');
	}
}