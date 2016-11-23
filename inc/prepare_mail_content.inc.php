<?php
/* --------------------------------------------------------------
   prepare_mail_content.inc.php 2014-02-06 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

function prepare_mail_content($p_mail_content)
{
	$t_mail_content_array = explode("\n", $p_mail_content);
	if ($t_mail_content_array == false)
	{
		$t_mail_content_array = array($p_mail_content);
	}
	$t_mail_content = "";
	$t_line_size_limit = 998;
	
	for ($i = 0; $i < count($t_mail_content_array); $i++)
	{
		if (strlen_wrapper($t_mail_content_array[$i]) > $t_line_size_limit)
		{
			$t_temp_line = substr_wrapper($t_mail_content_array[$i], 0, $t_line_size_limit);
			$t_pos = max((int)strrpos_wrapper($t_temp_line, " "), (int)strrpos_wrapper($t_temp_line, ">"));
			if ($t_pos == 0)
			{
				$t_pos = $t_line_size_limit;
			}
			
			if ($i !== count($t_mail_content_array) - 1)
			{
				$t_mail_content_array[$i + 1] = substr_wrapper($t_mail_content_array[$i], $t_pos + 1) . $t_mail_content_array[$i + 1];
			}
			else
			{
				$t_mail_content_array[] = substr_wrapper($t_mail_content_array[$i], $t_pos + 1);
			}
			$t_mail_content_array[$i] = substr_wrapper($t_temp_line, 0, $t_pos + 1);
		}
		$t_mail_content .= $t_mail_content_array[$i] . "\n";
	}
	
	return $t_mail_content;
}
