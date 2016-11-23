<?php
/* --------------------------------------------------------------
  BottomContentView.inc.php 2014-04-06 gm
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2014 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
 */

class BottomContentView extends ContentView
{
	protected $parse_time;
	protected $extender_html;

	public function __construct()
	{
		parent::__construct();
		
		$this->set_content_template('module/bottom.html');
	}
	
	public function prepare_data()
	{
		$t_uninitialized_array = $this->get_uninitialized_variables(array('extender_html'));
		if(empty($t_uninitialized_array))
		{
			$t_content_html = '';

			if($this->parse_time !== null)
			{
				$this->content_array['PARSE_TIME'] = $this->parse_time;
			}

			$t_content_html .= $this->get_modules_html();

			$t_content_html .= $this->extender_html;

			$this->content_array['CONTENT'] = $t_content_html;
		}
		else
		{
			trigger_error("Variable(s) " . implode(', ', $t_uninitialized_array) . " do(es) not exist in class " . get_class($this) . " or is/are null", E_USER_ERROR);
		}
	}
	
	function get_modules_html()
	{
		ob_start();

		/* BOF YOOCHOOSE */
		if(defined('YOOCHOOSE_ACTIVE') && YOOCHOOSE_ACTIVE)
		{
			require_once(DIR_WS_INCLUDES . 'yoochoose/tracking.php');
		}
		/* EOF YOOCHOOSE */
		
		$t_html = ob_get_clean();
		
		return $t_html;
	}
	
	protected function set_validation_rules()
	{
		$this->validation_rules_array['parse_time']		= array('type' => 'string', 'strict' => 'true');
		$this->validation_rules_array['extender_html']	= array('type' => 'string', 'strict' => 'true');
	}
}