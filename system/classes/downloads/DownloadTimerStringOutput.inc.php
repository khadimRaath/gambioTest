<?php
/* --------------------------------------------------------------
   DownloadTimerStringOutput.php 2014-07-15 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/


/**
 * Class DownloadTimerStringOutput
 */
class DownloadTimerStringOutput
{

	protected $text_str_elements_array = array('days'		=> array('lang_key_plural' => 'days',		'counts' => ''),
											   'hours'		=> array('lang_key_plural' => 'hours',		'counts' => ''),
											   'minutes'	=> array('lang_key_plural' => 'minutes',	'counts' => ''),
											   'seconds'	=> array('lang_key_plural' => 'seconds',	'counts' => '')
	);

	/**
	 * @var LanguageTextManager
	 */
	protected $coo_text_mgr;


	/**
	 * @param                     $p_days_remaining
	 * @param                     $p_hours_remaining
	 * @param                     $p_minutes_remaining
	 * @param                     $p_seconds_remaining
	 * @param LanguageTextManager $p_coo_text_mgr
	 */
	public function __construct($p_days_remaining,
								$p_hours_remaining,
								$p_minutes_remaining,
								$p_seconds_remaining,
								LanguageTextManager $p_coo_text_mgr
	)
	{
		foreach($this->text_str_elements_array as $t_element_name => $t_element_data)
		{
			$t_count = (int)${'p_' . $t_element_name . '_remaining'};
			$this->text_str_elements_array[$t_element_name]['counts'] = $t_count;
		}
		
		$this->coo_text_mgr = $p_coo_text_mgr;
	}


	/**
	 * @return string
	 */
	public function get_msg()
	{

		$t_output = $this->coo_text_mgr->get_text('download_abandonment_time_forbidden') . '<br/>';

		foreach($this->text_str_elements_array as $t_element_name)
		{
			$t_output .= $this->build_textstr_for_element($t_element_name);
		}

		$t_output .= '.';

		return $t_output;
	}


	/**
	 * @param $p_element_name_array
	 *
	 * @return string
	 */
	protected function build_textstr_for_element(array $p_element_name_array)
	{
		$t_output_element = '';

		if(empty($p_element_name_array['counts']) === false)
		{
			$t_output_element = ' ' . (string)$p_element_name_array['counts'] . ' ';
			$t_lang_key = $this->build_language_key($p_element_name_array);
			$t_output_element .= $this->coo_text_mgr->get_text($t_lang_key);
		}

		return $t_output_element;

	}


	/**
	 * @param array $p_elementName_array
	 *
	 * @return string
	 */
	protected function build_language_key(array $p_elementName_array)
	{
		$t_lang_key = $p_elementName_array['lang_key_plural'];


		switch($t_lang_key)
		{
			case 'days':
				if($p_elementName_array['counts'] === 1)
				{
					$t_lang_key = 'day';
				}
				else
				{
					$t_lang_key = 'days';
				}
				break;
			case 'hours':
				if($p_elementName_array['counts'] === 1)
				{
					$t_lang_key = 'hour';
				}
				else
				{
					$t_lang_key = 'hours';
				}
				break;
			case 'minutes':
				if($p_elementName_array['counts'] === 1)
				{
					$t_lang_key = 'minute';
				}
				else
				{
					$t_lang_key = 'minutes';
				}
				break;
			case 'seconds':
				if($p_elementName_array['counts'] === 1)
				{
					$t_lang_key = 'second';
				}
				else
				{
					$t_lang_key = 'seconds';
				}
				break;
			default:
				$t_lang_key = 'none';
				break;
		}

		return $t_lang_key;
	}

}