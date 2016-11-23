<?php
/* --------------------------------------------------------------
   function.gm_menuboxes.php 2008-08-10 gambio
   Gambio OHG
   http://www.gambio.de
   Copyright (c) 2008 Gambio OHG
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/
?><?php


function smarty_function_gm_menuboxes($params, &$smarty)
{
	$output 				= '';
	$assigned_vars 	= $smarty->getTemplateVars();    
	
	$first_i 	= $params['first'];
	$last_i 	= $params['last'];
	$html 		= $params['html'];
	
	for($i=$first_i; $i<=$last_i; $i++) 
	{
		$content 	= $assigned_vars['gm_box_pos_'.$i];
		
		if($_SESSION['style_edit_mode'] == 'edit') {
			if(strlen($content) == 0) $content = ' ';
		} else {
			if(strlen($content) == 0) continue;
		}
		
		$html_out = str_replace('[COUNTER]', $i, $html);
		$html_out	= str_replace('[CONTENT]', $content, $html_out);
		
		$output .= $html_out."\n";
	}
	return $output;
}

// {gm_menuboxes first=1 last=100 html='<div id="gm_box_pos_[COUNTER]" class="gm_box_container">[CONTENT]</div>'}

/* vim: set expandtab: */

?>