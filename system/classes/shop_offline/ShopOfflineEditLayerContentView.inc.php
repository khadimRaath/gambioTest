<?php
/* --------------------------------------------------------------
  ShopOfflineEditLayerContentView.inc.php 2016-06-06
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2016 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
 */

require_once(DIR_FS_CATALOG . 'gm/inc/gm_get_language.inc.php');

class ShopOfflineEditLayerContentView extends LightboxContentView
{

	public function __construct()
	{
		parent::__construct();
		$this->set_template_dir(DIR_FS_CATALOG . 'admin/html/content/shop_offline/');
		$this->set_content_template('shop_topbar_edit_layer.html');
	}

	public function prepare_data()
	{
		$data_src = $this->v_parameters['src'];
		$this->set_content_data('data_src', $data_src);

		$data_id = $this->v_parameters['id'];
		$this->set_content_data('data_id', $data_id);
		
		// Hide configuration to make topbar permanent on Honeygrid based templates
		if(gm_get_env_info('TEMPLATE_VERSION') >= 3)
		{
			$this->set_content_data('hide_topbar_mode', true);
		}
		else
		{
			$this->set_content_data('hide_topbar_mode', false);
		}
		
		// set languages array
		$languagesArray = gm_get_language();
		$this->content_array['languages_array'] = $languagesArray;

		// Set lightbox buttons
		$this->set_lightbox_button('right', 'ok', array('ok', 'green'));
		$this->set_lightbox_button('right', 'close', array('close', 'lightbox_close'));
	}
}