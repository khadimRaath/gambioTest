<?php
/* --------------------------------------------------------------
  DataProcessing.inc.php 2014-02-18 gm
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2014 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
 */

class DataProcessing extends BaseClass
{
	protected $v_data_array = NULL;
	protected $v_redirect_url = NULL;
	protected $v_output_buffer = NULL;

	public function __construct()
	{
		$this->set_validation_rules();
	}

	public function set_data($p_key, $p_value)
	{
		$c_key = trim((string)$p_key);
		if($c_key == '')
		{
			trigger_error('empty key given', E_USER_WARNING);
		}
		$this->v_data_array[$c_key] = $p_value;
	}

	public function proceed()
	{
		return true;
	}

	protected function set_redirect_url($p_url)
	{
		$this->v_redirect_url = $p_url;
	}

	public function get_redirect_url()
	{
		$t_output = $this->v_redirect_url;
		return $t_output;
	}

	public function get_response()
	{
		$t_output = $this->v_output_buffer;
		return $t_output;
	}

	protected function wrapped_db_perform($p_called_from, $p_table, $p_data_array = array(), $p_action = 'insert', $p_parameters = '', $p_link = 'db_link', $p_quoted_values = true)
	{
		return xtc_db_perform($p_table, $p_data_array, $p_action, $p_parameters, $p_link, $p_quoted_values);
	}
}