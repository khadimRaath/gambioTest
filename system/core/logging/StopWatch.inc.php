<?php

/* --------------------------------------------------------------
   StopWatch.inc.php 2014-03-18 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class StopWatch
{
	static protected $instance = null;
	
	protected $time_stamps;
	protected $time_stamp_names;
	
	protected function __construct()
	{
		$this->time_stamps = array();
		$this->time_stamp_names = array();
	}
	
	static public function get_instance()
	{
		if(self::$instance === null)
		{
			self::$instance = new self();
		}
		
		return self::$instance;
	}
	
	public function start($p_time_stamp_name = 'start')
	{
		if(in_array($p_time_stamp_name, $this->time_stamp_names) == false && $p_time_stamp_name != 'start')
		{
			$this->time_stamp_names[] = $p_time_stamp_name;
		}
		
		if($p_time_stamp_name == 'start')
		{
			if(defined('PAGE_PARSE_START_TIME'))
			{
				$this->add_specific_time_stamp($p_time_stamp_name, PAGE_PARSE_START_TIME);
			}
			else
			{
				$this->add_time_stamp($p_time_stamp_name);
			}
		}
		else
		{
			$this->add_time_stamp('start_' . $p_time_stamp_name);
		}
	}
	
	public function stop($p_time_stamp_name = 'stop')
	{
		if(in_array($p_time_stamp_name, $this->time_stamp_names) == false && $p_time_stamp_name != 'stop')
		{
			$this->time_stamp_names[] = $p_time_stamp_name;
		}
		
		if($p_time_stamp_name == 'stop')
		{
			$this->add_time_stamp($p_time_stamp_name);
		}
		else
		{
			$this->add_time_stamp('stop_' . $p_time_stamp_name);
		}
	}
	
	public function add_time_stamp($p_time_stamp_name)
	{
		$this->add_specific_time_stamp($p_time_stamp_name, microtime(true));
	}
	
	public function add_specific_time_stamp($p_time_stamp_name, $p_time_stamp)
	{
		$this->time_stamps[$p_time_stamp_name] = $p_time_stamp;
	}
	
	public function get_duration($p_start_time_name = 'start', $p_stop_time_name = 'stop', $p_formatted = true)
	{
		$t_duration = $this->time_stamps[$p_stop_time_name] - $this->time_stamps[$p_start_time_name];
		if($p_formatted)
		{
			return $this->format($t_duration);
		}
		return $t_duration;
	}
	
	public function get_group_duration($p_time_stamp_name = '', $p_formatted = true)
	{
		$t_duration = 0;
		if(empty($p_time_stamp_name) || isset($this->time_stamps['start_' . $p_time_stamp_name]) == false || isset($this->time_stamps['stop_' . $p_time_stamp_name]) == false)
		{
			return $t_duration;
		}
		
		$t_duration = $this->get_duration('start_' . $p_time_stamp_name, 'stop_' . $p_time_stamp_name, $p_formatted);
		
		return $t_duration;
	}
	
	public function get_duration_array($p_formatted = true)
	{
		$t_duration_array = array();
		
		if(isset($this->time_stamps['start']) && isset($this->time_stamps['stop']))
		{
			$t_duration_array['main'] = $this->get_duration('start', 'stop', $p_formatted);
		}
		
		foreach($this->time_stamp_names as $t_time_stamp_name)
		{
			$t_duration_array[$t_time_stamp_name] = $this->get_group_duration($t_time_stamp_name, $p_formatted);
		}
		
		return $t_duration_array;
	}
	
	public function format($p_time_stamp)
	{
		$t_formatted_time = number_format($p_time_stamp, 6);
		return $t_formatted_time;
	}
	
	public function get_time_stamps()
	{
		return $this->time_stamps;
	}

	public function set_time_stamps($p_time_stamps)
	{
		$this->time_stamps = $p_time_stamps;
	}
}