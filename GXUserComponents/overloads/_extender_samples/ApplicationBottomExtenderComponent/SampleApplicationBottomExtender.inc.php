<?php
/* --------------------------------------------------------------
   SampleApplicationBottomExtender.inc.php 2016-03-07
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class SampleApplicationBottomExtender
 *
 * This is a sample overload for the ApplicationBottomExtenderComponent.
 *
 * @see ApplicationBottomExtenderComponent
 */
class SampleApplicationBottomExtender extends SampleApplicationBottomExtender_parent
{
   /**
    * Overloaded "proceed" method.
    */
	public function proceed()
	{
		$this->v_output_buffer[] = '<span id="my_span">This is my span.</span>';
		$this->v_output_buffer[] = '<style type="text/css">.green { color: green; }</style>';
        $this->v_output_buffer[] = '<script type="text/javascript">$("#my_span").addClass("green");</script>';

		parent::proceed();
	}
}