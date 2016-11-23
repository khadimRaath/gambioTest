<?php

/* --------------------------------------------------------------
  ExtenderComponent.inc.php 2015-04-29 gm
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2015 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
 */

class ExtenderComponent
{
	var $v_output_buffer;
	var $v_data_array;

	
	public function set_data($p_key, $p_value)
	{
		$c_key = trim((string) $p_key);
		if($c_key != '')
		{
			$this->v_data_array[$c_key] = $p_value;

			return true;
		}

		return false;
	}


	public function proceed()
	{
		
	}

	
	public function get_output($p_position)
	{
		$t_output_array = '';

		if(isset($this->v_output_buffer[$p_position]))
		{
			$t_output_array = $this->v_output_buffer[$p_position];
		}

		return $t_output_array;
	}
	

	public function get_response()
	{
		return $this->v_output_buffer;
	}
}