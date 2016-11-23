<?php
/* --------------------------------------------------------------
   NewsletterContentView.inc.php 2015-05-29 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------

$Id: newsletter.php,v 1.0

   XTC-NEWSLETTER_RECIPIENTS RC1 - Contribution for XT-Commerce http://www.xt-commerce.com
   by Matthias Hinsche http://www.gamesempire.de

   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com 
   -----------------------------------------------------------------------------------------
   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce www.oscommerce.com
   (c) 2003	 nextcommerce www.nextcommerce.org

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

require_once(DIR_FS_INC . 'xtc_image_submit.inc.php');
require_once(DIR_FS_INC . 'xtc_draw_password_field.inc.php');

class NewsletterBoxContentView extends ContentView
{
	public function __construct()
	{
		parent::__construct();
		$this->set_content_template('boxes/box_newsletter.html');
	}

	public function prepare_data()
	{
		$this->content_array['FORM_ID'] = 'sign_in';
		$this->content_array['FORM_METHOD'] = 'post';
		$this->content_array['FORM_ACTION_URL'] = xtc_href_link(FILENAME_NEWSLETTER, '', 'NONSSL', true, true, true);
		$this->content_array['INPUT_NAME'] = 'email';
	}
}