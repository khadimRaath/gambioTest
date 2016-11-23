<?php
/* --------------------------------------------------------------
   function.menuboxes.php 2015-10-21
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

function smarty_function_menuboxes($params, &$smarty)
{
	$output        = '';
	$assigned_vars = $smarty->getTemplateVars();
	
	$first_i = ($params['first']) ? $params['first'] : 1;
	$last_i  = ($params['last']) ? $params['last'] : 200;
	$html    = ($params['html']) ? $params['html'] : '<div id="gm_box_pos_[COUNTER]" class="gm_box_container">[CONTENT]</div>';
	
	$arrBoxesNo = array();
	if(isset($params['exclude']))
	{
		$arrBoxesNo = explode(',', $params['exclude']);
	}
	$arrBoxesYes = array();
	if(isset($params['only']))
	{
		$arrBoxesYes = explode(',', $params['only']);
	}
	
	for($i = $first_i; $i <= $last_i; $i++)
	{
		$content = $assigned_vars['gm_box_pos_' . $i];
		
		if($_SESSION['style_edit_mode'] == 'edit' && empty($content))
		{
			$content = ' ';
		}
		elseif(empty($content))
		{
			continue;
		}
		
		if(count($arrBoxesNo) > 0)
		{
			$logInList = false;
			foreach($arrBoxesNo as $kBox => $vBox)
			{
				if(strpos($content, 'box-' . $vBox))
				{
					$logInList = true;
				}
			}
			if(!$logInList)
			{
				$html_out = str_replace('[COUNTER]', $i, $html);
				$html_out = str_replace('[CONTENT]', $content, $html_out);
				$output .= $html_out . "\n";
			}
		}
		else
		{
			if(count($arrBoxesYes) > 0)
			{
				foreach($arrBoxesYes as $kBox => $vBox)
				{
					if(strpos($content, 'box-' . $vBox))
					{
						$html_out = str_replace('[COUNTER]', $i, $html);
						$html_out = str_replace('[CONTENT]', $content, $html_out);
						$output .= $html_out . "\n";
					}
				}
			}
			else
			{
				$html_out = str_replace('[COUNTER]', $i, $html);
				$html_out = str_replace('[CONTENT]', $content, $html_out);
				$output .= $html_out . "\n";
			}
		}
	}
	
	return $output;
}
