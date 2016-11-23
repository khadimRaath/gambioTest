<?php
/* --------------------------------------------------------------
  AdminEditProductExtenderComponent.inc.php 2015-04-10 gm
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2015 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
 */

MainFactory::load_class('ExtenderComponent');

class AdminEditProductExtenderComponent extends ExtenderComponent
{
	function __construct() {}
	
	function proceed()
	{
		parent::proceed();
		
		if(is_array($this->v_output_buffer) == false)
		{
			$this->v_output_buffer = array();
		}
		
		$this->v_output_buffer['top'] = array();
		$this->v_output_buffer['bottom'] = array();
		$this->v_output_buffer['left'] = array();
		$this->v_output_buffer['right'] = array();
	}
}