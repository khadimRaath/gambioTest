<?php
/* --------------------------------------------------------------
   LightboxGalleryAjaxHandler.inc.php 2014-03-06 mb
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class LightboxGalleryAjaxHandler extends AjaxHandler
{
	function get_permission_status($p_customers_id=NULL)
	{
		return true;
	}

	function proceed()
	{
		$c_products_id = (int)$this->v_data_array['GET']['id'];

		$coo_lightbox_gallery = MainFactory::create_object('LightboxGalleryContentView');
		$coo_lightbox_gallery->set_('products_id', $c_products_id);
		$this->v_output_buffer = $coo_lightbox_gallery->get_html();

		return true;
	}
}