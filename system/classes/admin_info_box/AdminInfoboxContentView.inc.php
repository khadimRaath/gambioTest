<?php
/* --------------------------------------------------------------
  AdminInfoboxContentView.inc.php 2015-10-07
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2015 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
 */

class AdminInfoboxContentView extends ContentView
{
	public function __construct()
	{
		parent::__construct();
		$this->set_template_dir(DIR_FS_CATALOG . 'admin/html/content/');
		$this->set_content_template('admin_infobox.html');
		$this->set_caching_enabled(false);

		$this->init_smarty();
		$this->set_flat_assigns(false);
	}

	public function prepare_data()
	{
		$coo_admin_infobox_control = MainFactory::create_object('AdminInfoboxControl');
		$t_messages_array = $coo_admin_infobox_control->get_all_messages();

		$this->content_array['messages_array'] = $t_messages_array;
	}
}