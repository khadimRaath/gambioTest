<?php
/* --------------------------------------------------------------
   LogoffContentView.inc.php 2014-03-02 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------


   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(logoff.php,v 1.12 2003/02/13); www.oscommerce.com
   (c) 2003	 nextcommerce (logoff.php,v 1.16 2003/08/17); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: logoff.php 1071 2005-07-22 16:36:53Z mz $)

   Released under the GNU General Public License
   -----------------------------------------------------------------------------------------
   Third Party contribution:

   Credit Class/Gift Vouchers/Discount Coupons (Version 5.10)
   http://www.oscommerce.com/community/contributions,282
   Copyright (c) Strider | Strider@oscworks.com
   Copyright (c  Nick Stanko of UkiDev.com, nick@ukidev.com
   Copyright (c) Andre ambidex@gmx.net
   Copyright (c) 2001,2002 Ian C Wilson http://www.phesis.org


   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

class LogoffContentView extends ContentView
{
	public function __construct()
	{
		parent::__construct();
		$this->set_content_template('module/logoff.html');
		$this->set_flat_assigns(true);
	}
	
	public function prepare_data()
	{
		$this->content_array['BUTTON_CONTINUE'] = '<a href="'.xtc_href_link(FILENAME_DEFAULT).'">'.xtc_image_button('button_continue.gif', IMAGE_BUTTON_CONTINUE).'</a>';
		$this->content_array['CONTINUE_LINK'] = xtc_href_link(FILENAME_DEFAULT);
	}
}