<?php
/* --------------------------------------------------------------
   JSPageUsermodExtender.inc.php 2012-01-24 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2012 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class JSPageUsermodExtender extends JSPageUsermodExtender_parent
{
	function proceed()
	{
		parent::proceed();
		
		$t_page = 'Global';
		if(isset($this->v_data_array['GET']['page']))
		{
			$t_page = $this->v_data_array['GET']['page'];
		}
		
		$coo_usermod_js = new UsermodJSMaster($t_page);
		$t_files_array = $coo_usermod_js->get_files();
		foreach($t_files_array AS $t_file)
		{
			// print new line avoiding conflicts with comments
			echo "\n";
			
			include_once($t_file);
		}

		// print new line avoiding conflicts with comments
		echo "\n";		
	}
}
?>