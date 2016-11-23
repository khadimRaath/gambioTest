<?php
/* --------------------------------------------------------------
   SampleAdminEditProductExtender.inc.php 2016-03-07
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class SampleAdminEditProductExtender
 * 
 * This is a sample overload for the AdminEditProductExtenderComponent.
 * 
 * @see AdminEditProductExtenderComponent
 */
class SampleAdminEditProductExtender extends SampleAdminEditProductExtender_parent
{
	/**
	 * Overloaded "proceed" method. 
	 */
	public function proceed()
	{
		parent::proceed();
		
		$this->v_output_buffer['top']['sample'] = array('title' => 'TOP Headline', 'content' => 'Top content');
		$this->v_output_buffer['bottom']['sample'] = array('title' => 'BOTTOM Headline', 'content' => 'Bottom content');
	}
}