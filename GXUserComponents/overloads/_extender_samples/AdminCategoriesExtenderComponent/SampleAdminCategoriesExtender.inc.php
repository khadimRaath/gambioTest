<?php
/* --------------------------------------------------------------
   SampleAdminCategoriesExtender.inc.php 2016-03-07
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class SampleAdminCategoriesExtender
 * 
 * This is a sample overload for the AdminCategoriesExtenderComponent.
 * 
 * @see AdminCategoriesExtenderComponent
 */
class SampleAdminCategoriesExtender extends SampleAdminCategoriesExtender_parent
{
	/**
	 * Overloaded "proceed" method.
	 */
	public function proceed()
	{
		parent::proceed();
		
		// Update the product status.
		if(isset($this->v_data_array['POST']['save']) || isset($this->v_data_array['POST']['gm_update']))
		{
			$productId = $this->v_data_array['GET']['pID']; // Product ID is always available (insert & update).
			$productStatus = $this->v_data_array['POST']['products_status'];
			$db = StaticGXCoreLoader::getDatabaseQueryBuilder(); 
			$db->update('products', array('products_status' => $productStatus), array('products_id' => $productId));
		}		
	}
}