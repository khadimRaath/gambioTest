<?php
/* --------------------------------------------------------------
   WithdrawalFormContentView.inc.php 2014-06-27 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
  
   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

class WithdrawalFormContentView extends ContentView
{
	public function __construct()
	{
		parent::__construct();
		$this->set_content_template('module/form.html'); //TODO
		$this->set_flat_assigns(true);
	}
	
	public function prepare_data()
	{
		$this->content_array['BUTTON_CONTINUE'] = '<a href="'.xtc_href_link(FILENAME_DEFAULT).'">'.xtc_image_button('button_continue.gif', IMAGE_BUTTON_CONTINUE).'</a>';
		$this->content_array['CONTINUE_LINK'] = xtc_href_link(FILENAME_DEFAULT);
	}
}