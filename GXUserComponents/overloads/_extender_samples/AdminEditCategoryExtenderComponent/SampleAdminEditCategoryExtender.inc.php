<?php
/* --------------------------------------------------------------
   SampleAdminEditCategoryExtender.inc.php 2016-03-07
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class SampleAdminEditCategoryExtender
 * 
 * This is a sample overload for the AdminEditCategoryExtenderComponent.
 * 
 * @see AdminEditCategoryExtenderComponent
 */
class SampleAdminEditCategoryExtender extends SampleAdminEditCategoryExtender_parent
{
	/**
	 * Overloaded "proceed" method.
	 */
	public function proceed()
	{
		parent::proceed();
		
		$this->v_output_buffer['top']['sample'] = array('title' => 'TOP Headline', 'content' => 'Top content');
		$this->v_output_buffer['bottom']['sample'] = array('title' => 'BOTTOM Headline', 'content' => 'Bottom content');
		$this->v_output_buffer['left']['sample'] = array('title' => 'LEFT Headline', 'content' => 'Left content');
		$this->v_output_buffer['right']['sample'] = array('title' => 'RIGHT Headline', 'content' => 'Right content');
	}
}