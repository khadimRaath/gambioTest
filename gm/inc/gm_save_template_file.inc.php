<?php
/* --------------------------------------------------------------
   gm_save_template_file.inc.php 2014-06-19 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/
	
function gm_save_template_file($id, $content, $lang, $type) {
	$gm_get_template = xtc_db_query("SELECT 
																			filename,
																			folder
																		FROM gm_emails
																		WHERE gm_email_id = '" . (int)$id . "'
																		LIMIT 1");
	$template = xtc_db_fetch_array($gm_get_template);
	@clearstatcache();	
	if(@is_writable(DIR_FS_CATALOG . 'templates/' . CURRENT_TEMPLATE . '/' . $template['folder'] . '/mail/' . $lang . '/' . $template['filename'] . '.' . $type))
    {
        $fp = fopen(DIR_FS_CATALOG . 'templates/' . CURRENT_TEMPLATE . '/' . $template['folder'] . '/mail/' . $lang . '/' . $template['filename'] . '.' . $type, "w+");
        fwrite($fp, stripslashes($content));
        fclose($fp);
        return true;
    }
    else return false;
}

function gm_correct_config_tag($content)
{
	$t_original_array = array();
	$t_modified_array = array();

	$t_result_array = array();

	preg_match_all('/{.*?}/', $content, $t_result_array);
	$t_original_array = array_merge($t_original_array, $t_result_array);

	foreach($t_original_array as $t_original)
	{
		$t_new = $t_original;
		$t_new = str_replace("&quot;", '"', $t_new);
		$t_new = str_replace("&#39;", "'", $t_new);
		$t_modified_array[] = $t_new;
	}
	
	for($i=0; $i < sizeof($t_original_array); $i++)
	{
		$content = str_replace($t_original_array[$i], $t_modified_array[$i], $content);
	}
	
	return trim($content);
}