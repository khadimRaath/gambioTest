<?php
/* --------------------------------------------------------------
   SampleOrderExtender.inc.php 2016-03-07
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class SampleOrderExtender
 *
 * This is a sample overload for the OrderExtenderComponent.
 *
 * @see OrderExtenderComponent
 */
class SampleOrderExtender extends SampleOrderExtender_parent
{
	/**
	 * Overloaded "proceed" method.
	 */
	public function proceed()
	{
		//Position: below_withdrawal
		$this->v_output_buffer['below_withdrawal_heading'] = 'below_withdrawal TITLE';
		$this->v_output_buffer['below_withdrawal']         = '<div style="color: #0000FF">below_withdrawal CONTENT</div>';

		//Position: below_order_info
		$this->v_output_buffer['below_order_info_heading'] = 'below_order_info TITLE';
		$this->v_output_buffer['below_order_info']         = '<div style="color: #0000FF">below_order_info CONTENT</div>';

		//Position: below_history
		$this->v_output_buffer['below_history_heading'] = 'below_history TITLE';
		$this->v_output_buffer['below_history']         = '<div style="color: #0000FF">below_history CONTENT</div>';

		//Position: order_status
		$this->v_output_buffer['order_status'] = '<div style="color: #0000FF">order_status CONTENT</div>';

		//Position: buttons
		$this->v_output_buffer['buttons'] = 'Not yet implemented';
		
		//The following two rows need to be at the end of every overload of the OrderExtender
		$this->addContent();
		parent::proceed();
	}
}