<?php
/* --------------------------------------------------------------
   SampleHeaderExtender.inc.php 2016-03-07
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class SampleHeaderExtender
 * 
 * This is a sample overload for the HeaderExtenderComponent.
 * 
 * @see HeaderExtenderComponent
 */
class SampleHeaderExtender extends SampleHeaderExtender_parent
{
	/**
	 * Overloaded "proceed" method. 
	 */
	public function proceed()
	{
		parent::proceed();
		
		echo '<meta http-equiv="Content-Script-Type" content="text/javascript">';
	}
}