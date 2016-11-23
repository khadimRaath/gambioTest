<?php
/* --------------------------------------------------------------
   GPrintDownloadAjaxHandler.inc.php 2013-11-14 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2013 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

require_once(DIR_FS_CATALOG . 'gm/modules/gm_gprint_tables.php');

class GPrintDownloadAjaxHandler extends AjaxHandler
{
	function get_permission_status($p_customers_id=NULL)
	{
		return true;
	}

	function proceed()
	{
		$c_download_key = gm_prepare_string($this->v_data_array['GET']['key']);

		$t_get_file_data = xtc_db_query("SELECT 
											filename,
											encrypted_filename
										FROM " . TABLE_GM_GPRINT_UPLOADS . "
										WHERE download_key = '" . $c_download_key . "'");	
		if(xtc_db_num_rows($t_get_file_data) == 1)
		{
			$t_file_data = xtc_db_fetch_array($t_get_file_data);

			$t_decrypted_filename = basename($t_file_data['filename']);
			$t_encrypted_filename = basename($t_file_data['encrypted_filename']);

			$t_filename = DIR_FS_CATALOG . 'gm/customers_uploads/gprint/' . $t_encrypted_filename;


			if(file_exists($t_filename)){
				header('Content-Description: File Transfer');
				header("Content-Type: application/octet-stream");
				header('Content-Disposition: attachment; filename="' . $t_decrypted_filename . '"');
				header('Content-Transfer-Encoding: binary');
				header('Expires: 0');
				header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
				header('Pragma: public');
				header('Content-Length: ' . filesize($t_filename));
				ob_clean();
				flush();

				readfile($t_filename);
			}	
			else
			{
				$this->v_output_buffer = 'Error: File does not exist!';
			}
		}
		else
		{
			$this->v_output_buffer = 'Error: File does not exist!';
		}

		return true;
	}
}
?>