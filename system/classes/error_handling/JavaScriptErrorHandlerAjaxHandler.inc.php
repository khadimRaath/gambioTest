<?php
/* --------------------------------------------------------------
   JavaScriptErrorHandlerAjaxHandler.inc.php 2014-01-07 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

require_once(DIR_FS_CATALOG . 'gm/classes/JSON.php');

/**
 * Description of JavaScriptErrorHandlerAjaxHandler
 *
 * @author wu
 */
class JavaScriptErrorHandlerAjaxHandler extends AjaxHandler
{
	function get_permission_status($p_customers_id=NULL)
	{
		return true;
	}

	function proceed()
	{
		$coo_json = new Services_JSON(SERVICES_JSON_LOOSE_TYPE);
	    $_POST = $coo_json->decode(stripslashes($coo_json->encode($_POST)), true);
	    $data = $coo_json->decode($_POST['data'], true);
	    $data = str_replace('__-amp-__', '&', $data);
		$error_type = '';
	    $error_file = '';
	    $error_line = '';
		
		if(isset($data['error_type']))
		{
			$error_type = $data['error_type'];
		}
		
		if(isset($data['file']))
		{
			$error_file = $data['file'];
		}
		
		if(isset($data['line']))
		{
			$error_line = $data['line'];
		}
	    
	    $error_msg = 'JavaScript error: ' . $error_type . ' in ' . $error_file . ':' . $error_line;
	    $tmpfile = DIR_FS_CATALOG . 'cache/js.tmp';
	    if (file_exists($tmpfile))
	    {
		$chtime = strtotime(date('F d Y H:i:s.', filemtime($tmpfile)));
	    }
	    else
	    {
		$chtime = 0;
	    }
	    $handle = fopen($tmpfile, 'w+');
	    if (!file_exists($tmpfile) || (filesize($tmpfile) > 0 && time() - $chtime >= 1) || filesize($tmpfile) == 0)
	    {
		trigger_error($error_msg, E_USER_WARNING);
		fwrite($handle, rand(100000, 999999));
	    }
	    fclose($handle);
	    //$this->v_output_buffer = $error_msg;
	    return true;
	}
}
