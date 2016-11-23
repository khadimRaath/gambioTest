<?php
/* --------------------------------------------------------------
  TrustedBoxContentView.inc.php 2014-07-17 gambio
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2014 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------


  based on:
  (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
  (c) 2002-2003 osCommerce(add_a_quickie.php,v 1.10 2001/12/19); www.oscommerce.com
  (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: add_a_quickie.php,v 1.1 2004/04/26 20:26:42 fanta2k Exp $)

  Released under the GNU General Public License
  -----------------------------------------------------------------------------------------
  Third Party contribution:
  Add A Quickie v1.0 Autor  Harald Ponce de Leon

  Released under the GNU General Public License
  --------------------------------------------------------------------------------------- */

class TrustedBoxContentView extends ContentView
{
	public function __construct()
	{
		parent::__construct();
		$this->set_content_template('boxes/box_trusted.html');
	}

	public function prepare_data()
	{
		$this->build_html = false;
		
		if(gm_get_conf('GM_TS_SEAL_ENABLED') == 1)
		{
			$language = $_SESSION['language_code'];
			$service = new GMTSService();
			$tsid = $service->findSealID($language);

			if($tsid !== false || $_SESSION['style_edit_mode'] == 'edit')
			{
				$this->content_array['TSID'] = $tsid;
				$this->content_array['SHOPNAME'] = urlencode(strtolower_wrapper(STORE_NAME));
				$this->build_html = true;
			}
		}
	}
}