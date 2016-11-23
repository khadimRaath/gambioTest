<?php
/* --------------------------------------------------------------
  DBBackupContentView.inc.php 2015-10-07
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2015 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
 */

class DBBackupContentView extends LightboxContentView
{
	protected $coo_db_backup_control;
	protected $coo_lang_manager;
	protected $pageToken; 
	
	public function __construct()
	{
		parent::__construct();
		$this->coo_db_backup_control = MainFactory::create_object('DBBackupControl');
		$this->coo_lang_manager = MainFactory::create_object('LanguageTextManager', array('db_backup', $_SESSION['languages_id']));

		$this->set_template_dir(DIR_FS_CATALOG . 'admin/html/content/db_backup/');
		//$this->set_caching_enabled(true);
	}

	public function get_html_array($p_get_array = array(), $p_post_array = array())
	{
		$this->v_get_array = $p_get_array;
		$this->v_post_array = $p_post_array;

		$t_html_output = array();

		$c_template = (string)$this->v_get_array['template'];
		if(preg_match('/[^\w\.\-]/', $c_template))
		{
			trigger_error('get_html: unexpected characters in template_name', E_USER_ERROR);
			return false;
		}

		$this->set_content_template($c_template);

		switch($c_template)
		{
			case 'db_backup.html':
				$t_html_output['html'] = $this->get_db_backup();
				break;
			case 'db_backup_restore.html':
				$t_html_output['html'] = $this->get_db_backup_restore();
				break;
			default: trigger_error('get_html: no template selected', E_USER_WARNING);
		}

		
		
		return $t_html_output;
	}

	function get_db_backup()
	{
		$t_files_array = array();
		$t_errors = array();
		
		if(file_exists(DIR_FS_BACKUP))
		{
			if(is_writable(DIR_FS_BACKUP) == false)
			{
				$t_errors[] = $this->coo_lang_manager->get_text('backup_dir_not_writeable') . ': ' . DIR_FS_BACKUP;
			}
			$t_files_array = $this->coo_db_backup_control->get_db_backup_files();
		}
		else
		{
			$t_errors[] = $this->coo_lang_manager->get_text('backup_dir_does_not_exist') . ': ' . DIR_FS_BACKUP;
		}
		
		if(file_exists(DIR_FS_BACKUP . 'temp/'))
		{
			if(is_writable(DIR_FS_BACKUP . 'temp/') == false)
			{
				$t_errors[] = $this->coo_lang_manager->get_text('backup_dir_not_writeable') . ': ' . DIR_FS_BACKUP . 'temp/';
			}
		}
		else
		{
			$t_errors[] = $this->coo_lang_manager->get_text('backup_dir_does_not_exist') . ': ' . DIR_FS_BACKUP . 'temp/';
		}

		$this->set_content_data('files', $t_files_array);
		$this->set_content_data('backup_path', $this->coo_db_backup_control->get_db_backup_path());
		$this->set_content_data('errors', $t_errors);
		$this->set_content_data('page_token', $this->pageToken);

		$this->init_smarty();
		$this->set_flat_assigns(false);

		$t_html_output = $this->build_html();

		return $t_html_output;
	}

	function get_db_backup_restore()
	{
		$t_files_array = $this->coo_db_backup_control->get_db_backup_files("DESC");

		$this->set_content_data('files', $t_files_array);

		$this->init_smarty();
		$this->set_flat_assigns(false);

		$t_html_output = $this->build_html();

		return $t_html_output;
	}
	
	public function setPageToken($p_pageToken)
	{
		$this->pageToken = (string)$p_pageToken; 
	}
}