<?php
/* --------------------------------------------------------------
   GambioUpdateControl.inc.php 2016-09-19
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

require_once(DIR_FS_CATALOG . 'gambio_updater/classes/GambioUpdateModel.inc.php');
require_once(DIR_FS_CATALOG . 'gambio_updater/classes/FTPManager.inc.php');
require_once(DIR_FS_CATALOG . 'system/core/caching/CacheControl.inc.php');
require_once(DIR_FS_CATALOG . 'system/classes/security/SecurityCheck.inc.php');

class GambioUpdateControl
{
    public $current_db_version;
    public $gambio_update_array            = array();
    public $section_file_delete_info_array = array();

    protected $customer_id = 0;
    protected $db_host;
    protected $db_user;
    protected $db_password;
    protected $db_name;
    protected $db_persistent;
    protected $rerun_step;

    protected $deleteOperationsSuccess = 1;


    /**
     * Creates a new GambioUpdateControl instance and loads and sorts all available updates
     *
     * @param string $p_db_host       The host for the DB connection
     * @param string $p_db_user       The user for the DB connection
     * @param string $p_db_password   The password for the DB connection
     * @param string $p_db_name       The selected DB name
     * @param bool   $p_db_persistent Persistent DB connection?
     */
    public function __construct($p_db_host = '',
                                $p_db_user = '',
                                $p_db_password = '',
                                $p_db_name = '',
                                $p_db_persistent = null)
    {
        $this->db_host            = $p_db_host;
        $this->db_user            = $p_db_user;
        $this->db_password        = $p_db_password;
        $this->db_name            = $p_db_name;
        $this->db_persistent      = $p_db_persistent;
        $this->current_db_version = $this->get_current_db_version();
        $this->rerun_step         = false;

        $this->load_updates();
        $this->sort_updates();
    }


    private function get_current_db_version()
    {
        $t_sql          = "SELECT * FROM `version_history` WHERE `type` IN ('master_update', 'service_pack') ORDER BY `installation_date` DESC LIMIT 1";
        $coo_db         = new DatabaseModel();
        $t_version_data = $coo_db->query($t_sql);

        if(count($t_version_data) > 0)
        {
            return $t_version_data[0]['version'];
        }
        else
        {
            return false;
        }
    }


    /**
     * Returns an array of versions for the "force shop version dropdown"
     *
     * @return array
     */
    public function get_versions()
    {
        if(file_exists(DIR_FS_CATALOG . 'gambio_updater/updates/versions.ini'))
        {
            $versions = parse_ini_file('updates/versions.ini');
        }
        else
        {
            $versions    = array();
            $update_dirs = glob(DIR_FS_CATALOG . 'gambio_updater/updates/v*', GLOB_ONLYDIR);

            foreach($update_dirs as $update_dir)
            {
                $version = substr(basename($update_dir), 1);

                if(version_compare($version, '2.1.3.0', '<'))
                {
                    continue;
                }

                $versions[$version] = 'v' . $version;
            }
        }
	
	    arsort($versions);
	    
	    return $versions;
    }


    /**
     * Reads all available updates from the 'update'-directory, that match the requirements
     *
     * @return bool Indicates if all updates have been successfully loaded
     */
    public function load_updates()
    {
        $t_success    = true;
        $t_dir_handle = opendir(DIR_FS_CATALOG . 'gambio_updater/updates');
        while($t_update_path = readdir($t_dir_handle))
        {
            if(is_dir(DIR_FS_CATALOG . 'gambio_updater/updates/' . $t_update_path)
               && $t_update_path != '.'
               && $t_update_path != '..'
               && file_exists(DIR_FS_CATALOG . 'gambio_updater/updates/' . $t_update_path . '/configuration.ini')
            )
            {
                $coo_update = new GambioUpdateModel($t_update_path, $this->db_host, $this->db_user, $this->db_password,
                                                    $this->db_name, $this->db_persistent, $this->customer_id);
                if($this->current_db_version === false || is_null($this->current_db_version))
                {
                    $this->current_db_version = $coo_update->get_shop_db_version();
                }
                $t_matches_requirements = $coo_update->check_environment_requirements();
                if($t_matches_requirements)
                {
                    $this->gambio_update_array[] = $coo_update;
                }
                $t_success &= $t_matches_requirements;
            }
        }
        closedir($t_dir_handle);

        return $t_success;
    }


    public function sort_updates()
    {
        $this->sort_update_versions();
        $previous_update_list = array();
        while($previous_update_list != $this->gambio_update_array)
        {
            $previous_update_list = $this->gambio_update_array;
            $this->sort_out_old_updates();
            $this->sort_out_disconnected_updates();
            $this->sort_out_implicit_updates();
            $this->sort_out_installed_updates();
        }
    }


    public function insert_version_history_entry($p_db_version = false)
    {
        $t_db_version = $this->current_db_version;
        if($p_db_version !== false)
        {
            $t_db_version = $p_db_version;
        }
        $coo_db = new DatabaseModel($this->db_host, $this->db_user, $this->db_password, $this->db_name);

        $t_php_version = PHP_VERSION;

        $t_sql = "	INSERT INTO
						`version_history` (`version`, `name`, `type`, `revision`, `is_full_version`, `installation_date`, `php_version`, `mysql_version`, `installed`)
					VALUES
						('" . $coo_db->real_escape_string($t_db_version) . "', 
						'" . $coo_db->real_escape_string($t_db_version) . "', 
						'service_pack', 
						0, 
						1, 
						NOW(), 
						'" . $coo_db->real_escape_string($t_php_version) . "', 
						VERSION(),
						0)";
        $coo_db->query($t_sql);
    }


    public function get_chmod_array()
    {
        $t_chmod_array = SecurityCheck::getWrongPermittedUpdaterFiles();

        return $t_chmod_array;
    }


    public function get_move_array()
    {
        $t_move_array = array();
        foreach($this->gambio_update_array as $coo_update)
        {
            $coo_update->set_charset($coo_update->get_charset());
            $coo_update->load_move_array();
            $t_move_array = array_merge_recursive($t_move_array, $coo_update->get_move_array());
        }

        return $t_move_array;
    }


    protected function chmod_array_unique($p_chmod_array = array())
    {
        $t_chmod_array_flat = array();
        $t_chmod_array      = array();

        foreach($p_chmod_array as $t_chmod_data)
        {
            $t_chmod_array_flat[$t_chmod_data['PATH']] = $t_chmod_data['IS_DIR'];
        }

        foreach($t_chmod_array_flat as $t_path => $t_is_dir)
        {
            $t_chmod_array[] = array('PATH' => $t_path, 'IS_DIR' => $t_is_dir);
        }

        return $t_chmod_array;
    }


    protected function get_save_filelist_array($p_action, $p_ftp_sleeptime = 1000000)
    {
        for($i = 0; $i < 5; $i++)
        {
            switch($p_action)
            {
                case 'delete':
                    $t_ftp_action_array = $this->get_delete_list();
                    break;
                case 'move':
                    $t_ftp_action_array = $this->get_move_array();
                    break;
                case 'chmod':
                    $t_ftp_action_array = $this->get_chmod_array();
                    break;
            }

            if(empty($t_ftp_action_array))
            {
                break;
            }
            usleep($p_ftp_sleeptime);
        }

        if($p_action !== 'move')
        {
            sort($t_ftp_action_array);
        }

        return $t_ftp_action_array;
    }


    public function get_delete_form($p_second_try = false)
    {

        $t_delete_array = $this->get_save_filelist_array('delete');

        $t_html = '';

        /**
         * Creates an HTML form showing wrong file or directory permissions
         */
        if(empty($t_delete_array) === false)
        {
            $t_html .= $this->get_ftp_form('delete', $t_delete_array, $p_second_try);
        }
        else
        {
            if(isset($_POST['dir']) && empty($_POST['dir']) == false)
            {
                $t_dir_confirmed = 'true';
            }
            else
            {
                $t_dir_confirmed = 'false';
            }

            $t_html .= '<div>' . TEXT_DELETE_FILES_OK . '</div>' . '<br/><br/>'
                       . '<input type="hidden" name="FTP_HOST" value="' . str_replace('"', '&quot;', $_POST['FTP_HOST'])
                       . '" />' . '<input type="hidden" name="FTP_USER" value="' . str_replace('"', '&quot;',
                                                                                               $_POST['FTP_USER'])
                       . '" />' . '<input type="hidden" name="FTP_PASSWORD" value="' . str_replace('"', '&quot;',
                                                                                                   $_POST['FTP_PASSWORD'])
                       . '" />' . '<input type="hidden" name="FTP_PASV" value="' . str_replace('"', '&quot;',
                                                                                               $_POST['FTP_PASV'])
                       . '" />' . '<input type="hidden" name="dir" value="' . str_replace('"', '&quot;', $_POST['dir'])
                       . '" />' . '<input type="hidden" name="dir_confirmed" value="' . $t_dir_confirmed . '" />'
                       . '<input type="submit" name="go" value="' . BUTTON_CONTINUE
                       . '" class="button gradient green" />';
        }

        return $t_html;
    }


    public function get_move_form($p_second_try = false, $p_ftp_sleeptime = 1000000)
    {
        $t_move_array = $this->get_save_filelist_array('move');

        $t_html = '';

        /**
         * Creates an HTML form
         */
        if(empty($t_move_array) === false)
        {
            $t_html .= $this->get_ftp_form('move', $t_move_array, $p_second_try);
        }
        else
        {
            if(isset($_POST['dir']) && empty($_POST['dir']) == false)
            {
                $t_dir_confirmed = 'true';
            }
            else
            {
                $t_dir_confirmed = 'false';
            }

            $t_html .= '<div>' . TEXT_MOVE_OK . '</div>' . '<br/><br/>' . '<input type="hidden" name="FTP_HOST" value="'
                       . str_replace('"', '&quot;', $_POST['FTP_HOST']) . '" />'
                       . '<input type="hidden" name="FTP_USER" value="' . str_replace('"', '&quot;', $_POST['FTP_USER'])
                       . '" />' . '<input type="hidden" name="FTP_PASSWORD" value="' . str_replace('"', '&quot;',
                                                                                                   $_POST['FTP_PASSWORD'])
                       . '" />' . '<input type="hidden" name="FTP_PASV" value="' . str_replace('"', '&quot;',
                                                                                               $_POST['FTP_PASV'])
                       . '" />' . '<input type="hidden" name="dir" value="' . str_replace('"', '&quot;', $_POST['dir'])
                       . '" />' . '<input type="hidden" name="dir_confirmed" value="' . $t_dir_confirmed . '" />'
                       . '<input type="submit" name="go" value="' . BUTTON_CONTINUE
                       . '" class="button gradient green" />';
        }

        return $t_html;
    }


    public function get_ftp_html($p_coo_ftp_manager, $p_dir, $p_content)
    {
        $t_html = '';

        if(is_object($p_coo_ftp_manager))
        {
            if($p_coo_ftp_manager->error != '')
            {
                $t_html .= '<div class="error">' . $p_coo_ftp_manager->error . '</div>';
            }
            else
            {
                if($p_coo_ftp_manager->is_shop($p_dir))
                {
                    $t_is_correct_shop = $this->check_correct_shop($p_dir);

                    if($t_is_correct_shop)
                    {
                        $t_html .= '<input type="hidden" name="dir" value="' . $p_dir . '" />';

                        switch($p_content)
                        {
                            case 'move':
                                $t_html .= '<input type="submit" name="move" value="' . BUTTON_MOVE
                                           . '" class="button gradient green" /><br /><br />';

                                break;
                            case 'delete':
                                $t_html .= '<input type="submit" name="delete_files" value="' . BUTTON_DELETE_FILES
                                           . '" class="button gradient red" /><br /><br />';

                                break;
                            case 'chmod':
                                $t_html .= '<input type="submit" name="chmod_777" value="' . BUTTON_SET_PERMISSIONS
                                           . '" class="button gradient green" /><br /><br />';

                                break;
                        }
                    }
                }

                $t_html .= $this->get_folders_html($p_coo_ftp_manager, $p_dir);
            }
        }

        return $t_html;
    }


    public function get_ftp_form($p_content, $p_files_array, $p_second_try)
    {
        $t_html = '';

        switch($p_content)
        {
            case 'move':
                $t_files_array = array();

                $moveFilesSuccess = $this->_moveFiles($p_files_array);

                foreach($p_files_array as $t_data_array)
                {
                    if(dirname($t_data_array['old']) !== dirname($t_data_array['new']))
                    {
                        $t_files_to_move_array[] = $t_data_array['old'] . ' => ' . $t_data_array['new'];
                    }
                    else
                    {
                        $t_files_to_rename_array[] = $t_data_array['old'] . ' => ' . $t_data_array['new'];
                    }
                }

                if($moveFilesSuccess)
                {
                    $t_html .= '<div id="move_errors_report">';

                    if(empty($t_files_to_move_array) === false)
                    {
                        $t_html .= '<p><strong>' . HEADING_MOVED . '</strong></p>
								<div class="error_field">' . implode("<br />\n", $t_files_to_move_array) . '</div>';
                    }

                    if(empty($t_files_to_rename_array) === false)
                    {
                        if(empty($t_files_to_move_array) === false)
                        {
                            $t_html .= '<br />';
                        }

                        $t_html .= '<p><strong>' . HEADING_RENAMED . '</strong></p>
								<div class="error_field">' . implode("<br />\n", $t_files_to_rename_array) . '</div>';
                    }

                    $t_html .= '<br /><br />
								<a class="button_reload button gradient green" href="#">' . BUTTON_CONTINUE . '</a>
							</div>';

                    return $t_html;
                }

                $t_html .= '<div id="move_errors_report">';

                if(empty($t_files_to_move_array) === false)
                {
                    $t_html .= '<p><strong>' . HEADING_MOVE . '</strong></p>
								<div class="error_field">' . implode("<br />\n", $t_files_to_move_array) . '</div>';
                }

                if(empty($t_files_to_rename_array) === false)
                {
                    if(empty($t_files_to_move_array) === false)
                    {
                        $t_html .= '<br />';
                    }

                    $t_html .= '<p><strong>' . HEADING_RENAME . '</strong></p>
								<div class="error_field">' . implode("<br />\n", $t_files_to_rename_array) . '</div>';
                }

                $t_html .= '<p>' . TEXT_MOVE . '</p>
								<a class="button_reload button gradient" href="#">' . BUTTON_CHECK_MOVE . '</a>
								<br /><br />
								<br /><br />';

                break;
            case 'delete':

                $t_files_array = $p_files_array;
                foreach($t_files_array as $t_key => $t_path)
                {
                    $t_path = trim($t_path);
                    if(strlen($t_path) > 2 && substr($t_path, -2) == '/*')
                    {
                        $t_files_array[$t_key] = substr($t_path, 0, -2);
                    }
                }

                // Create Backup START *********************************************
                require_once 'classes/zip_creator/ZipCreator.inc.php';

                $fileListToZip = ZipCreator::prepareFileListFromShop($t_files_array);

                $zipDirFromShopRoot = 'export/';
                $zipFileName        = null;
                $zipCreator         = @new ZipCreator(DIR_FS_CATALOG, $zipDirFromShopRoot, $zipFileName);
                @$zipCreator->createZip($fileListToZip, DIR_FS_CATALOG);

                $t_files_json = urlencode(json_encode($t_files_array));

                $t_html_deletepart = '<div id="delete_errors_report">
							<p><strong>' . HEADING_NEED_TO_DELETE . '</strong></p>
							<div class="error_field">' . implode("<br />\n", $t_files_array) . '</div>
							<p>' . TEXT_DELETE_FILES . '</p>
							<a class="button_reload button gradient smaller" data-filelist_to_delete="' . $t_files_json
                                     . '" href="#">' . BUTTON_DOWNLOAD_FILELIST_TO_DELETE . '</a>
							<a class="button_reload button gradient smaller" href="#">' . BUTTON_CHECK_DELETE_FILES . '</a>
							###downloadbutton###
							<br /><br />
							<br /><br />';

                $t_buttonTemplate = '';

                $zipFileShowName = @$zipCreator->getZipFileName();

                $zipFilePath = DIR_FS_CATALOG . $zipDirFromShopRoot . $zipFileShowName;

                if($zipFileShowName && file_exists($zipFilePath))
                {
                    $t_buttonTemplate = '<a class="button_create_backup button gradient smaller" href="../export/'
                                        . $zipFileShowName . '" target="_blank">' . BUTTON_CREATE_BACKUP . '</a>';

                    $this->_deleteFiles($t_files_array);

                    if($this->deleteOperationsSuccess)
                    {
                        $t_html_deletepart = '<div id="delete_errors_report">
							<p><strong>' . HEADING_DELETED_FILES . '</strong></p>
							<div class="error_field">' . implode("<br />\n", $t_files_array) . '</div>
							<p>' . TEXT_DELETED_FILES . '</p>
							<a class="button_reload button gradient smaller" data-filelist_to_delete="' . $t_files_json
                                             . '" href="#">' . BUTTON_DOWNLOAD_FILELIST_TO_DELETE . '</a>
							###downloadbutton###
							<br /><br /><br />
							<a class="button_reload button gradient smaller green" href="#">' . BUTTON_CONTINUE . '</a>
						</div>';

                        $t_html_deletepart = str_replace('###downloadbutton###', $t_buttonTemplate, $t_html_deletepart);
                        $t_html .= $t_html_deletepart;

                        return $t_html;
                    }
                }

                $t_html_deletepart = str_replace('###downloadbutton###', $t_buttonTemplate, $t_html_deletepart);
                $t_html .= $t_html_deletepart;

                // Create Backup END *****************************************************
                break;
            case 'chmod':
                $t_files_array = array();

                $chmodSuccess = $this->_chmod($p_files_array);

                foreach($p_files_array as &$t_data_array)
                {
                    $t_data_array['PATH'] = str_replace(DIR_FS_CATALOG, '', $t_data_array['PATH']);
                    $t_files_array[]      = $t_data_array['PATH'];
                }

                if($chmodSuccess)
                {
                    $t_html .= '<div id="chmod_errors_report">
								<p><strong>' . HEADING_PERMISSIONS_SET . '</strong></p>
								<div class="error_field">' . implode("<br />\n", $t_files_array) . '</div>
								<p>' . TEXT_PERMISSIONS_SET . '</p>
								<a class="button_reload button gradient green" href="#">' . BUTTON_CONTINUE . '</a>
							</div>';

                    return $t_html;
                }

                $t_html .= '<div id="chmod_errors_report">
								<p><strong>' . HEADING_WRONG_PERMISSIONS . '</strong></p>
								<div class="error_field">' . implode("<br />\n", $t_files_array) . '</div>
								<p>' . TEXT_SET_PERMISSIONS . '</p>
								<a class="button_reload button gradient" href="#">' . BUTTON_CHECK_PERMISSIONS . '</a>
								<br /><br />
								<br /><br />';

                break;
        }

        if(isset($_POST['FTP_PASV']) == false || empty($_POST['FTP_PASV']) || $_POST['FTP_PASV'] == true)
        {
            $t_is_passive_html = ' checked="checked"';
        }
        else
        {
            $t_is_passive_html = '';
        }

        $t_html .= '<table class="block_head" width="100%" border="0" cellspacing="0" cellpadding="0">
						<tr>
							<td width="4%"><img src="images/icon-path.png" alt="" /></td>
							<td width="96%"><strong>' . HEADING_FTP_DATA . '</strong></td>
						</tr>
					</table>

					<table width="620" border="0" cellspacing="5" cellpadding="0">
						<tr>
							<td width="120">' . LABEL_FTP_SERVER . '</td>
							<td width="500"><input type="text" class="input_field" name="FTP_HOST" size="35" value="'
                   . str_replace('"', '&quot;', $_POST['FTP_HOST']) . '" /></td>
						</tr>
						<tr>
							<td width="120">' . LABEL_FTP_USER . '</td>
							<td width="500"><input type="text" class="input_field" name="FTP_USER" size="35" value="'
                   . str_replace('"', '&quot;', $_POST['FTP_USER']) . '" /></td>
						</tr>
						<tr>
							<td width="120">' . LABEL_FTP_PASSWORD . '</td>
							<td width="500"><input type="password" class="input_field" name="FTP_PASSWORD" size="35" value="'
                   . str_replace('"', '&quot;', $_POST['FTP_PASSWORD']) . '" /></td>
						</tr>
						<tr>
							<td><label for="pasv">' . LABEL_FTP_PASV . '</label></td>
							<td><input type="checkbox" id="pasv" name="FTP_PASV" value="true" style="margin-left: 0"'
                   . $t_is_passive_html . ' /></td>
						</tr>
					</table>';
        if(isset($_POST['FTP_HOST']) == false
           || ($_POST['FTP_HOST'] === '' && $_POST['FTP_USER'] === ''
               && $_POST['FTP_PASSWORD'] === '')
        )
        {
            $t_html .= '<br /><input type="hidden" name="dir" value="/" />
						<input type="submit" name="go" value="' . BUTTON_CONNECT
                       . '" class="button gradient green" /><br/><br/>';
        }
        else
        {
            $t_html .= '<br />
						<fieldset>
							<legend>' . HEADING_REMOTE_CONSOLE . '</legend>
							<div id="ftp_content">';
            $coo_ftp_manager = FTPManager::get_instance(true, $_POST['FTP_HOST'], $_POST['FTP_USER'],
                                                        $_POST['FTP_PASSWORD'], $_POST['FTP_PASV']);

            if((is_object($coo_ftp_manager)) && $coo_ftp_manager->error != '')
            {
                $t_html .= $coo_ftp_manager->error;

                $t_html .= '</div>
						</fieldset>
						<br />
						<input type="submit" name="go" value="' . BUTTON_CONNECT_NEW . '" class="button gradient" />
					</div>';

                return $t_html;
            }

            if(isset($_POST['dir']) && $_POST['dir'] !== '/')
            {
                $t_dir = $_POST['dir'];
            }
            else
            {
                $t_dir = $coo_ftp_manager->find_shop_dir('/');
            }

            if(isset($_POST['move'])
               && empty($_POST['move']) === false
               && is_object($coo_ftp_manager)
               && $coo_ftp_manager->is_shop($t_dir)
               && $p_second_try === false
            )
            {
                $coo_ftp_manager->move($t_dir, $p_files_array);

                return $this->get_move_form(true);
            }

            if(isset($_POST['delete_files'])
               && empty($_POST['delete_files']) === false
               && is_object($coo_ftp_manager)
               && $coo_ftp_manager->is_shop($t_dir)
               && $p_second_try === false
            )
            {
                $coo_ftp_manager->delete_files($t_dir, $p_files_array);

                return $this->get_delete_form(true);
            }

            if(isset($_POST['chmod_777'])
               && empty($_POST['chmod_777']) === false
               && is_object($coo_ftp_manager)
               && $coo_ftp_manager->is_shop($t_dir)
               && $p_second_try === false
            )
            {
                $coo_ftp_manager->chmod_777($t_dir, $p_files_array);

                return $this->get_chmod_form(true);
            }

            $t_html .= $this->get_ftp_html($coo_ftp_manager, $t_dir, $p_content);

            $t_html .= '</div></fieldset>';

            if($coo_ftp_manager->error != '' || (is_object($coo_ftp_manager)))
            {
                $t_html .= '<br />
				<input type="submit" name="go" value="' . BUTTON_CONNECT_NEW . '" class="button gradient" />';
            }
        }

        if($p_content === 'chmod')
        {
            $t_html .= '<div class="warning">
							' . TEXT_SKIP . '<br />
							<br />
							<a class="button_skip button gradient" href="#">' . BUTTON_SKIP . '</a>
						</div>';
        }

        $t_html .= '</div>';

        return $t_html;
    }


    public function get_chmod_form($p_second_try = false, $p_ftp_sleeptime = 1000000)
    {

        $t_chmod_array = $this->get_save_filelist_array('chmod');

        $t_html = '';

        /**
         * Creates an HTML form showing wrong file or directory permissions
         */
        if(empty($t_chmod_array) == false)
        {
            $t_html .= $this->get_ftp_form('chmod', $t_chmod_array, $p_second_try);
        }
        else
        {
            $t_html .= '<div>' . TEXT_PERMISSIONS_OK . '</div>' . '<br/><br/>'
                       . '<input type="submit" name="go" value="' . BUTTON_CONTINUE
                       . '" class="button gradient green" />';
        }

        return $t_html;
    }


    public function get_folders_html($p_coo_ftp_manager, $p_dir)
    {
        $t_html = TEXT_CURRENT_DIR . $p_dir . '<br /><br />';

        if(strrpos($p_dir, '/') !== false && $p_dir != '/')
        {
            if(strrpos($p_dir, '/') === 0)
            {
                $t_absolute_dir = '/';
            }
            else
            {
                $t_absolute_dir = substr($p_dir, 0, strrpos($p_dir, '/'));
            }

            $t_html .= '<div class="folder" title="' . LABEL_DIR_UP . '">
							<img src="' . DIR_WS_CATALOG . 'gambio_updater/images/folder.png" width="16" height="16" /> ..		
							<span class="absolute_dir">' . $t_absolute_dir . '</span>
						</div>';
        }

        $t_list_array = $p_coo_ftp_manager->get_directories($p_dir);

        for($i = 0; $i < count($t_list_array); $i++)
        {
            $t_html .= '<div class="folder">
							<img src="' . DIR_WS_CATALOG
                       . 'gambio_updater/images/folder.png" width="16" height="16" /> ' . basename($t_list_array[$i]) . ' 
							<span class="absolute_dir">' . $t_list_array[$i] . '</span>
						</div>';
        }

        return $t_html;
    }


    /**
     * Returns an array of all update forms
     *
     * @return array An array of all update forms
     */
    public function get_update_forms()
    {
        $t_forms_array     = array();
        $t_conflicts_array = array();
        $t_html            = '';

        foreach($this->gambio_update_array as $coo_update)
        {
            $coo_update->set_charset($coo_update->get_charset());
            $t_forms_array[]   = $coo_update->get_update_form();
            $t_conflicts_array = array_merge_recursive($t_conflicts_array, $coo_update->get_section_conflicts());
        }

        /**
         * Creates an HTML form to ask the user which text changes should be applied
         */
        if(!empty($t_conflicts_array))
        {
            $t_html .= '<b>' . TEXTCONFLICTS_TEXT . '</b><fieldset id="conflict_fieldset"><legend>'
                       . TEXTCONFLICTS_LABEL . '</legend><table id="section_conflicts">';

            foreach($t_conflicts_array as $t_language_name => $t_section_data)
            {
                $t_html .= '<tbody>
								<tr class="section_conflicts_language_row">
									<td colspan="4">
										<b>' . ucfirst($t_language_name) . '</b>
									</td>
								</tr>
							</tbody>';
                foreach($t_section_data as $t_section_name => $t_phrase_data)
                {
                    $t_section_path = $t_section_name;
                    if(strpos(trim($t_section_name), 'lang__') === 0)
                    {
                        $t_section_path = str_replace('__', '/', $t_section_path);
                        $t_section_path = str_replace('___', '.', $t_section_path);
                    }
                    else
                    {
                        $t_section_path = 'lang/' . $t_language_name . '/sections/' . $t_section_name . '.lang.inc.php';
                    }

                    $t_alt_counter = 0;
                    $t_html .= '<tbody id="' . $t_section_path . '" class="section_body">
									<tr class="section_conflicts_section_row">
										<td colspan="4">
											<i>' . $t_section_name . '</i>
											<input type="hidden" name="keep_list[' . $t_language_name . '_'
                               . $t_section_name . ']" value=""/>
										</td>
									</tr>
									<tr>
										<th colspan="2" class="section_conflicts_left_column conflict_header">'
                               . TEXTCONFLICTS_OLD . '</th>
										<th colspan="2" class="conflict_header">' . TEXTCONFLICTS_NEW . '</th>
									</tr>';
                    foreach($t_phrase_data as $t_phrase_name => $t_phrase_text_data)
                    {
                        $t_alt_class = '';
                        if($t_alt_counter % 2 == 1)
                        {
                            $t_alt_class = '_alt';
                        }
                        if(is_array($t_phrase_text_data['old']))
                        {
                            $t_phrase_text_data['old'] = end($t_phrase_text_data['old']);
                        }
                        if(is_array($t_phrase_text_data['new']))
                        {
                            $t_phrase_text_data['new'] = end($t_phrase_text_data['new']);
                        }
                        if(is_array($t_phrase_text_data['from_file']))
                        {
                            $t_phrase_text_data['from_file'] = end($t_phrase_text_data['from_file']);
                        }

                        $t_begin_file_span = '';
                        $t_end_file_span   = '';
                        $t_bound_radio     = '';
                        if($t_phrase_text_data['from_file'] == 1)
                        {
                            $t_begin_file_span = '<span class="from_file">';
                            $t_end_file_span   = '</span>';
                            $t_bound_radio     = 'bound_radio ';
                        }

                        $t_html .= '<tr class="section_conflicts_phrase_row' . $t_alt_class . '">
										<td class="section_conflict section_conflict_radio">
											<input id="' . $t_language_name . '_' . $t_section_name . '_'
                                   . $t_phrase_name . '_old" class="' . $t_bound_radio . 'section_' . $t_language_name
                                   . '_' . $t_section_name . '" type="radio" name="section_phrase[' . $t_language_name
                                   . '][' . $t_section_name . '][' . $t_phrase_name . '][refuse]" value="1" /> 
										</td>
										<td class="section_conflicts_left_column section_conflict">
											<label for="' . $t_language_name . '_' . $t_section_name . '_'
                                   . $t_phrase_name . '_old">' . $t_begin_file_span . '&quot;'
                                   . $this->htmlentities($t_phrase_text_data['old']) . '&quot;' . $t_end_file_span . '</label>
										</td>
										<td class="section_conflict section_conflict_radio">
											<input id="' . $t_language_name . '_' . $t_section_name . '_'
                                   . $t_phrase_name . '_new" class="' . $t_bound_radio . 'section_' . $t_language_name
                                   . '_' . $t_section_name . '" type="radio" name="section_phrase[' . $t_language_name
                                   . '][' . $t_section_name . '][' . $t_phrase_name . '][refuse]" value="0" checked="checked" /> 
										</td>
										<td class="section_conflict">
											<label for="' . $t_language_name . '_' . $t_section_name . '_'
                                   . $t_phrase_name . '_new">' . $t_begin_file_span . '&quot;'
                                   . $this->htmlentities($t_phrase_text_data['new']) . '&quot;' . $t_end_file_span . '</label>
											<input type="hidden" name="section_phrase[' . $t_language_name . ']['
                                   . $t_section_name . '][' . $t_phrase_name . '][from_file]" value="'
                                   . $t_phrase_text_data['from_file'] . '" />
										</td>
									</tr>';
                        $t_alt_counter++;
                    }
                    $t_html .= '<tr>
									<td colspan="4">&nbsp;</td>
								</tr>';
                }
                $t_html .= '	<tr>
									<td colspan="4">&nbsp;</td>
								</tr>
							</tbody>';
            }

            $t_html .= '</table></fieldset>';
        }
        elseif(empty($t_html))
        {
            $t_empty = true;
            foreach($t_forms_array as $t_content)
            {
                if($t_content != '')
                {
                    $t_empty = false;
                    break;
                }
            }

            if($t_empty)
            {
                $t_html .= '<p>' . TEXT_NO_CONFIGURATION . '</p>';
            }
        }

        $t_forms_array[] = $t_html;

        return $t_forms_array;
    }


    protected function htmlentities($p_string)
    {
        $t_string = $p_string;
        if(preg_match('/(?:[\xC2-\xDF][\x80-\xBF]|\xE0[\xA0-\xBF][\x80-\xBF]|[\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}|\xED[\x80-\x9F][\x80-\xBF]|\xF0[\x90-\xBF][\x80-\xBF]{2}|[\xF1-\xF3][\x80-\xBF]{3}|\xF4[\x80-\x8F][\x80-\xBF]{2})+/xs',
                      $t_string) === 0
        )
        {
            return htmlentities($t_string, ENT_COMPAT, 'ISO-8859-15');
        }

        return htmlentities($t_string, ENT_COMPAT, 'UTF-8');
    }


    /**
     * Gathers all delete lists of all updates and combines intersections
     *
     * @return An array of all files that need to be deleted
     */
    public function get_delete_list()
    {
        clearstatcache();

        $t_delete_list = array();

        foreach($this->gambio_update_array as $coo_update)
        {
            $t_delete_list = array_merge($t_delete_list, $coo_update->get_delete_list());
        }

        if(file_exists(DIR_FS_CATALOG . 'cache/additional_delete_list.pdc'))
        {
            $t_additional_delete_list = unserialize(file_get_contents(DIR_FS_CATALOG
                                                                      . 'cache/additional_delete_list.pdc'));
            foreach($t_additional_delete_list as $t_delete_file)
            {
                $t_delete_list[] = $t_delete_file;
            }
        }

        $t_delete_list = array_unique($t_delete_list);

        foreach($t_delete_list as $t_key => $t_file_path)
        {
            $t_file_path = trim($t_file_path);

            if($t_file_path === '')
            {
                unset($t_delete_list[$t_key]);
                continue;
            }

            if(strlen($t_file_path) > 2 && substr($t_file_path, -2) == '/*')
            {
                $t_file_path = substr($t_file_path, 0, -2);
            }
            elseif(is_dir(DIR_FS_CATALOG . $t_file_path) && glob(DIR_FS_CATALOG . $t_file_path . '/*') !== false
                   && $this->dir_has_files_in_list($t_delete_list, $t_file_path) == false
            )
            {
                unset($t_delete_list[$t_key]);
            }

            if(!file_exists(DIR_FS_CATALOG . $t_file_path))
            {
                unset($t_delete_list[$t_key]);
            }
            else // Windows cannot differentiate between lower- und uppercase -> double check
            {
                $t_realpath = realpath(DIR_FS_CATALOG . $t_file_path);
                $t_realpath = str_replace('\\', '/', $t_realpath);

                if(strpos($t_realpath, $t_file_path) === false)
                {
                    unset($t_delete_list[$t_key]);
                }
            }
        }

        rsort($t_delete_list);

        return $t_delete_list;
    }


    protected function dir_has_files_in_list(array $p_list, $p_dir)
    {
        foreach($p_list as $t_entry)
        {
            if(strpos(trim($t_entry), $p_dir . '/') !== false && $t_entry != $p_dir)
            {
                return true;
            }
        }

        return false;
    }


    /**
     * Executes all updates from gambio_update_array
     *
     * @param string $p_current_update_name         Name of update to install
     * @param array  $p_refusion_array              Refused phrases by the user
     * @param bool   $p_execute_independent_queries Indicates if independent updates should be executed
     * @param bool   $p_execute_dependent_queries   Indicates if dependent updates should be executed
     * @param bool   $p_update_css                  Indicates if CSS should be updated
     * @param bool   $p_update_sections             Indicates if sections should be updated
     *
     * @return bool Indicates if all updates were executed successfully
     */
    public function update($p_current_update_name,
                           $p_refusion_array = array(),
                           $p_execute_dependent_queries = true,
                           $p_execute_independent_queries = true,
                           $p_update_css = true,
                           $p_update_sections = true,
                           $p_update_version_history = true)
    {
        $t_success = true;

        foreach($this->gambio_update_array as $coo_update)
        {
            if($coo_update->get_update_name() == $p_current_update_name)
            {
                $coo_update->set_charset($coo_update->get_charset());
                if($p_execute_dependent_queries)
                {
                    $t_success &= $coo_update->update_dependent_data();
                }
                if($p_execute_independent_queries)
                {
                    $t_success &= $coo_update->update_independent_data();
                }
                if($p_update_sections)
                {
                    $check = $coo_update->query('SHOW TABLES LIKE "language_sections"', true);
                    if($check->num_rows === 1)
                    {
                        $t_section_file_delete_info_array = $coo_update->update_sections($p_refusion_array);
                        if($t_section_file_delete_info_array === false)
                        {
                            $t_success = false;
                        }
                        else
                        {
                            $this->section_file_delete_info_array = $t_section_file_delete_info_array;
                        }
                    }
                }
                if($p_update_css)
                {
                    $t_success &= $coo_update->update_css();
                }
                if($p_update_version_history)
                {
                    $t_success &= $coo_update->update_version_history();
                }

                $this->rerun_step = $coo_update->get_rerun_step();
                break;
            }
        }

        return $t_success;
    }


    /**
     * Sorts all GambioUpdate instances in the corect installation order
     */
    protected function sort_update_versions()
    {
        $t_sorted_updates = array();
        foreach($this->gambio_update_array as $coo_update)
        {
            if(empty($t_sorted_updates))
            {
                $t_sorted_updates[] = $coo_update;
            }
            else
            {
                $t_actual_sort_value = $coo_update->convert_version($coo_update->get_version_sort_value());
                $t_previous_size     = count($t_sorted_updates);
                for($i = 0; $i < count($t_sorted_updates); $i++)
                {
                    $t_ref_sort_value = $t_sorted_updates[$i]->convert_version($t_sorted_updates[$i]->get_version_sort_value());
                    if(version_compare($t_actual_sort_value, $t_ref_sort_value, '<'))
                    {
                        $t_sorted_updates = array_merge(array_slice($t_sorted_updates, 0, $i), array($coo_update),
                                                        array_slice($t_sorted_updates, $i));
                        break;
                    }
                }

                if($t_previous_size == count($t_sorted_updates))
                {
                    $t_sorted_updates[] = $coo_update;
                }
            }
        }
        $this->gambio_update_array = $t_sorted_updates;
    }


    /**
     * Sorts out updates that are lower than the actual version
     */
    protected function sort_out_old_updates()
    {
        $t_sorted_updates = array();
        foreach($this->gambio_update_array as $coo_update)
        {
            if(!$coo_update->is_lower_than_installed())
            {
                $t_sorted_updates[] = $coo_update;
            }
        }
        $this->gambio_update_array = $t_sorted_updates;
    }


    /**
     * Sorts out updates that can't be installed because none of the required shopversions can be reached through any
     * other updates
     */
    protected function sort_out_disconnected_updates()
    {
        $t_sorted_updates = array();
        if(count($this->gambio_update_array) > 0 && $this->gambio_update_array[0]->is_appliable())
        {
            $t_sorted_updates[] = $this->gambio_update_array[0];
            for($i = 0; $i < count($this->gambio_update_array) - 1; $i++)
            {
                if($this->gambio_update_array[$i + 1]->is_compatible_to($this->get_predicted_shop_version($i)))
                {
                    $t_sorted_updates[] = $this->gambio_update_array[$i + 1];
                }
            }
        }
        elseif(count($this->gambio_update_array) > 0)
        {
            array_shift($this->gambio_update_array);
            $this->sort_out_disconnected_updates();

            return;
        }
        $this->gambio_update_array = $t_sorted_updates;
    }


    protected function get_predicted_shop_version($p_index)
    {
        if(!isset($this->gambio_update_array[$p_index]))
        {
            return $this->current_db_version;
        }
        if($this->gambio_update_array[$p_index]->get_update_type() == 'update')
        {
            return $this->get_predicted_shop_version($p_index - 1);
        }

        return $this->gambio_update_array[$p_index]->get_update_version();
    }


    /**
     * Sorts out updates that are included within another update
     */
    protected function sort_out_implicit_updates()
    {
        $t_sorted_updates = array();
        foreach($this->gambio_update_array as $coo_update)
        {
            $t_update_is_implicit = false;
            foreach($this->gambio_update_array as $coo_implying_update)
            {
                if($coo_implying_update->implies_update($coo_update->get_update_key()))
                {
                    $t_update_is_implicit = true;
                    break;
                }
            }
            if(!$t_update_is_implicit)
            {
                $t_sorted_updates[] = $coo_update;
            }
        }
        $this->gambio_update_array = $t_sorted_updates;
    }


    protected function sort_out_installed_updates()
    {
        $coo_database_model = new DatabaseModel($this->db_host, $this->db_user, $this->db_password, $this->db_name,
                                                $this->db_persistent);
        $t_sql              = '';

        $t_sorted_updates = array();
        foreach($this->gambio_update_array as $coo_update)
        {
            if($coo_update->get_update_type() == 'update')
            {
                $t_query  = 'SELECT
								`history_id`
							FROM
								`version_history`
							WHERE
								`name` = "' . $coo_update->get_name() . '"
								AND `version` >= "' . $coo_update->get_update_version() . '"';
                $t_result = $coo_database_model->query($t_query);
                if(empty($t_result))
                {
                    $t_sorted_updates[] = $coo_update;
                }
            }
            else
            {
                $t_sorted_updates[] = $coo_update;
            }
        }
        $this->gambio_update_array = $t_sorted_updates;
    }


    /**
     * Returns SQL-errors-array of all updates
     * @return array
     */
    public function get_sql_errors_array()
    {
        $t_sql_errors_array = array();
        foreach($this->gambio_update_array AS $coo_update)
        {
            $t_sql_errors_array = array_merge($t_sql_errors_array, $coo_update->get_sql_errors());
        }

        return $t_sql_errors_array;
    }


    public function login($p_email, $p_password, $p_check_latin1 = true)
    {
        $coo_database_model = new DatabaseModel($this->db_host, $this->db_user, $this->db_password, $this->db_name,
                                                $this->db_persistent);

        $c_email    = $coo_database_model->real_escape_string(trim(stripslashes($p_email)));
        $c_password = md5(trim(stripslashes($p_password)));

        $t_sql    = "SELECT `customers_id` FROM `customers` 
					WHERE 
						`customers_email_address` = '" . $c_email . "' AND 
						`customers_password` = '" . $c_password . "' AND
						`customers_status` = 0";
        $t_result = $coo_database_model->query($t_sql, true);
        if($t_result->num_rows == 1)
        {
            $t_result_array    = $t_result->fetch_assoc();
            $this->customer_id = (int)$t_result_array['customers_id'];

            foreach($this->gambio_update_array as $coo_update)
            {
                $coo_update->set_customer_id($this->customer_id);
            }

            return true;
        }
        
        if($p_check_latin1 && $this->login($p_email, utf8_decode($p_password), false))
        {
	        $c_password = md5(trim(stripslashes($p_password)));
        	
	        $t_sql    = "UPDATE `customers` 
					        SET `customers_password` = '" . $c_password . "' 
							WHERE 
								`customers_email_address` = '" . $c_email . "' AND 
								`customers_status` = 0";
	        $coo_database_model->query($t_sql);
        	
        	return true;
        }

        return false;
    }


    public function clear_cache()
    {
        include_once(DIR_FS_INC . 'xtc_db_connect.inc.php');
        include_once(DIR_FS_INC . 'xtc_db_query.inc.php');
        include_once(DIR_FS_INC . 'xtc_db_input.inc.php');
        include_once(DIR_FS_INC . 'xtc_db_fetch_array.inc.php');
        include_once(DIR_FS_INC . 'xtc_db_num_rows.inc.php');
        include_once(DIR_FS_CATALOG . 'gm/inc/check_data_type.inc.php');
        include_once(DIR_FS_CATALOG . 'system/gngp_layer_init.inc.php');
        include_once(DIR_FS_CATALOG . 'system/core/caching/CacheControl.inc.php');

        $t_db_link = xtc_db_connect();

        $coo_cache_control = new CacheControl();
        $coo_cache_control->clear_data_cache();
        $coo_cache_control->clear_content_view_cache();
        $coo_cache_control->clear_templates_c();
        $coo_cache_control->clear_css_cache();

        $coo_phrase_cache_builder = MainFactory::create_object('PhraseCacheBuilder');
        $coo_phrase_cache_builder->build();

        $coo_mail_templates_cache_builder = MainFactory::create_object('MailTemplatesCacheBuilder');
        $coo_mail_templates_cache_builder->build();

        $this->set_no_error_output(false);

        ((is_null($___mysqli_res = mysqli_close($t_db_link))) ? false : $___mysqli_res);

        debug_notice('cache cleared');
    }


    public function rebuild_cache()
    {
        $coo_cache_control = new CacheControl();
        $coo_cache_control->rebuild_feature_index();
        $coo_cache_control->rebuild_categories_submenus_cache();
        $coo_cache_control->rebuild_products_categories_index();
        $coo_cache_control->rebuild_products_properties_index();

        debug_notice('cache rebuilded');
    }


    public function get_current_shop_version()
    {
        $coo_database_model = new DatabaseModel($this->db_host, $this->db_user, $this->db_password, $this->db_name,
                                                $this->db_persistent);
        $current_version    = $coo_database_model->query("SELECT `gm_value` FROM `gm_configuration` WHERE `gm_key` = 'CURRENT_SHOP_VERSION'");
        
        return $current_version[0]['gm_value'];
    }


    public function set_current_shop_version()
    {
        $coo_database_model = new DatabaseModel($this->db_host, $this->db_user, $this->db_password, $this->db_name,
                                                $this->db_persistent);
        $coo_database_model->query("
            REPLACE INTO `gm_configuration` 
            SET `gm_key` = 'CURRENT_SHOP_VERSION', `gm_value` = '" . $this->current_db_version . "'
        ");
    }


    public function reset_current_shop_version()
    {
        $coo_database_model = new DatabaseModel($this->db_host, $this->db_user, $this->db_password, $this->db_name,
                                                $this->db_persistent);
        $coo_database_model->query("
            UPDATE gm_configuration 
            SET gm_value = '0' WHERE gm_key = 'CURRENT_SHOP_VERSION'
        ");
    }


    public function set_installed_version()
    {
        $coo_database_model = new DatabaseModel($this->db_host, $this->db_user, $this->db_password, $this->db_name,
                                                $this->db_persistent);

        include(DIR_FS_CATALOG . 'release_info.php');
        $coo_database_model->query("
			REPLACE INTO
				`gm_configuration`
			SET
				`gm_key`		= 'INSTALLED_VERSION',
				`gm_value`	= '" . $coo_database_model->real_escape_string($gx_version) . "'
		");
    }


    public function is_update_mandatory()
    {
        $coo_database_model = new DatabaseModel($this->db_host, $this->db_user, $this->db_password, $this->db_name,
                                                $this->db_persistent);

        include(DIR_FS_CATALOG . 'release_info.php');
        $result = $coo_database_model->query("
			SELECT
				`gm_value`
			FROM
				`gm_configuration`
			WHERE
				`gm_key`		= 'INSTALLED_VERSION'
		");

        if(!empty($result) && $result[0]['gm_value'] == $gx_version)
        {
            return false;
        }

        return true;
    }


    protected function check_correct_shop($p_dir)
    {
        $t_source_file = 'cache/source.test';
        $t_target_file = 'cache/target.test';

        $t_handle = @fopen(DIR_FS_CATALOG . $t_source_file, 'w+');
        if($t_handle === false)
        {
            return false;
        }

        $coo_ftp_manager = FTPManager::get_instance(true, $_POST['FTP_HOST'], $_POST['FTP_USER'],
                                                    $_POST['FTP_PASSWORD'], $_POST['FTP_PASV']);
        $t_success       = $coo_ftp_manager->put_file($p_dir, $t_handle, $t_target_file);
        fclose($t_handle);
        if($t_success == false)
        {
            unlink(DIR_FS_CATALOG . $t_source_file);

            return false;
        }

        $t_success = file_exists(DIR_FS_CATALOG . $t_target_file);

        unlink(DIR_FS_CATALOG . $t_source_file);
        $coo_ftp_manager->delete_file($p_dir, $t_target_file);

        return $t_success;
    }


    public function rebuild_gambio_update_array(array $p_update_dir_array)
    {
        $this->gambio_update_array = array();
        foreach($p_update_dir_array as $t_update_path)
        {
            $coo_update = new GambioUpdateModel($t_update_path, $this->db_host, $this->db_user, $this->db_password,
                                                $this->db_name, $this->db_persistent, $this->customer_id);

            if($coo_update->check_environment_requirements())
            {
                $this->gambio_update_array[] = $coo_update;
            }
        }
    }


    public function get_rerun_step()
    {
        return $this->rerun_step;
    }


    /**
     * Try to delete files/directories via PHP
     *
     * @param array $files
     */
    protected function _deleteFiles(array $files)
    {
        foreach($files as $file)
        {
            $file = trim($file);
            $file = DIR_FS_CATALOG . $file;

            if($file !== DIR_FS_CATALOG
               && substr($file, -1) !== '.'
               && substr($file, -2) !== './'
               && strpos($file, '..') === false
            )
            {
                if($file && file_exists($file))
                {
                    if(is_dir($file))
                    {
                        $this->_deleteDir($file);
                    }
                    else
                    {
                        $this->deleteOperationsSuccess &= @unlink($file);
                    }
                }
            }
        }
    }


    /**
     * @param string $p_dir
     */
    protected function _deleteDir($p_dir)
    {
        $dirContent = @scandir($p_dir);

        if(is_array($dirContent))
        {
            $files = array_diff($dirContent, array('.', '..'));

            foreach($files as $file)
            {
                if(is_dir("$p_dir/$file"))
                {
                    $this->_deleteDir("$p_dir/$file");
                }
                else
                {
                    $this->deleteOperationsSuccess &= @unlink("$p_dir/$file");
                }
            }

            $this->deleteOperationsSuccess &= @rmdir($p_dir);
        }
    }


    /**
     * @param array $files
     *
     * @return int success 1 failure 0
     */
    protected function _moveFiles(array $files)
    {
        $success = 1;

        foreach($files as $move)
        {
            $old = DIR_FS_CATALOG . $move['old'];
            $new = DIR_FS_CATALOG . $move['new'];

            if(file_exists($old) && !file_exists($new))
            {
                $success &= @rename($old, $new);
            }
            else
            {
                $success = 0;
            }
        }

        return $success;
    }


    /**
     * @param array $files
     *
     * @return int success 1 failure 0
     */
    protected function _chmod(array $files)
    {
        $success = 1;

        foreach($files as $file)
        {
            $success &= @chmod($file['PATH'], 0777);
        }

        return $success;
    }


    public function set_no_error_output($p_noErrorOutput)
    {
        if($p_noErrorOutput)
        {
            file_put_contents(DIR_FS_CATALOG . 'cache/no_error_output.php', 'no_error_output');
        }
        elseif(file_exists(DIR_FS_CATALOG . 'cache/no_error_output.php'))
        {
            unlink(DIR_FS_CATALOG . 'cache/no_error_output.php');
        }
    }
}
