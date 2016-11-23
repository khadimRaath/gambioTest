<?php
/* --------------------------------------------------------------
  GMJSON.php 2015-05-20 mb
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2015 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
 */

class GMJSON_ORIGIN
{
	protected $v_urlencode;
	protected $htmlspecialchars;

	function __construct($p_urlencode = true, $p_htmlspecialchars = false)
	{
		$this->v_urlencode = $p_urlencode;
		$this->htmlspecialchars = $p_htmlspecialchars;
	}

	function is_associative($p_array)
	{
		if(!is_array($p_array))
		{
			return false;
		}

		foreach(array_keys($p_array) AS $t_key => $t_value)
		{
			if($t_key !== $t_value)
			{
				return true;
			}
		}

		return false;
	}

	function create_value($p_value)
	{
		if(is_string($p_value))
		{
			if($this->get_urlencode() == true)
			{
				$p_value = htmlspecialchars_wrapper($p_value);
				$p_value = urlencode($p_value);
			}
			elseif($this->htmlspecialchars === true)
			{
				$p_value = htmlspecialchars_wrapper($p_value);
				$p_value = str_replace('+', '%2B', $p_value);
			}

			$replaceArray = array(
				"\r\n" => '\r\n',
				"\n"   => '\r\n',
				"\r"   => '\r'
			);

			$p_value = str_replace(array_keys($replaceArray), array_values($replaceArray), $p_value);

			$t_value = '"' . $p_value . '"';
		}
		elseif(is_null($p_value))
		{
			$t_value = 'null';
		}
		elseif($p_value === true)
		{
			$t_value = 'true';
		}
		elseif($p_value === false)
		{
			$t_value = 'false';
		}
		else
		{
			$t_value = $p_value;
		}

		return $t_value;
	}

	function encode($p_input)
	{
		$t_output = '';

		if(!is_object($p_input) && !is_array($p_input))
		{
			$t_output = $this->create_value($p_input);
		}
		else
		{
			$t_input_array = (array)$p_input;

			if(!$this->is_associative($t_input_array))
			{
				$t_output .= '[';
			}

			$index_count = 0;
			foreach($t_input_array AS $t_key => $t_value)
			{
				$index_count++;

				if(is_object($t_value) || is_array($t_value))
				{
					if($this->is_associative($t_input_array))
					{
						$t_output .= '"' . $t_key . '":';
					}
					$t_output .= $this->encode($t_value);
				}
				else
				{
					if($this->is_associative($t_input_array))
					{
						$t_output .= '"' . $t_key . '":' . $this->create_value($t_value);
					}
					else
					{
						$t_output .= $this->create_value($t_value);
					}
				}

				if(count($t_input_array) != $index_count)
				{
					$t_output .= ',';
				}
			}

			if(!$this->is_associative($t_input_array))
			{
				$t_output .= ']';
			}
			else
			{
				$t_output = '{' . $t_output . '}';
			}
		}

		$t_output = str_replace("\n", "\\n", $t_output);

		return $t_output;
	}

	function get_urlencode()
	{
		return $this->v_urlencode;
	}
}
MainFactory::load_origin_class('GMJSON');