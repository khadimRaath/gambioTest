<?php
/* --------------------------------------------------------------
   SampleLoginExtender.inc.php 2016-03-07
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class SampleLoginExtender
 *
 * This is a sample overload for the LoginExtenderComponentTableExtenderComponent.
 * 
 * @see LoginExtenderComponentTableExtenderComponent
 */
class SampleLoginExtender extends SampleLoginExtenderr_parent
{
	/**
	 * Overloaded "proceed" method. 
	 */
	public function proceed()
	{
		parent::proceed();
		
		$gmCustomers = $this->get_customer();
		
		if($gmCustomers->get_data_value('customers_status') === '0')
		{
			// Clear admin cache. 
			$dataCache = DataCache::get_instance();
			$dataCache->clear_cache_by_tag('ADMIN');
		}
	}
}