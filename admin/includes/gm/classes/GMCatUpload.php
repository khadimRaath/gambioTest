<?php
/* --------------------------------------------------------------
   GMUpload.php  2014-07-29 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]

   IMPORTANT! THIS FILE IS DEPRECATED AND WILL BE REPLACED IN THE FUTURE. 
   MODIFY IT ONLY FOR FIXES. DO NOT APPEND IT WITH NEW FEATURES, USE THE
   NEW GX-ENGINE LIBRARIES INSTEAD.
   --------------------------------------------------------------
*/

	/*
	* -> class to set the category image
	*/
	MainFactory::load_class('GMUpload');

	class GMCatUpload_ORIGIN extends GMUpload {

		var $sql_id;
		var $file_name;
		var	$file_suffix;
		var	$file_new_name;

		function __construct($file, $file_rename, $file_dir, $file_oldname='', $sql_id='') {
			
			$this->sql_id = $sql_id;

			// call parent class
			parent::__construct($file, $file_rename, $file_dir, $file_oldname);

			// set parts of new filename
			$this->file_dir				= $file_dir;
			$this->file_suffix			= parent::set_suffix();
			$this->file_name			= parent::set_filename();
			$this->file_new_name		= $this->file_name . '.' . $this->file_suffix;

			return;
		}

		
		/*
		* -> proceed upload
		*/
		function upload_file() {	
			if(!$this->get_file_from_table()) {
				return array(FALSE, parent::upload($this->file_new_name));
			} else {
				return array(TRUE, parent::upload($this->file_name . '_' . $this->sql_id . '.' . $this->file_suffix));
			}
		}


		/*
		* -> proceed rename
		*/
		function rename_file($old_image) {	
			if(!$this->get_file_from_table()) {
				$file_name = array(FALSE, $this->file_new_name);
			} else {
				$file_name = array(TRUE, $this->file_name . '_' . $this->sql_id . '.' . $this->file_suffix);
			}

			@rename(
					$this->file_dir . $old_image, 
					$this->file_dir . $file_name[1]
					); 	

			return $file_name;

		}


		/*
		* -> get filename from table categories
		*/
		function get_file_from_table() {			
			
			if(strstr($this->file_dir, 'icons') == false) {
				$gm_query = xtc_db_query("
										SELECT 
											COUNT(*) 
										AS
											count
										FROM 
											categories
										WHERE 
											categories_image  = '" . $this->file_new_name	. "'
										AND 
											categories_id	  != '" . $this->sql_id			. "'
										");
			} else {			
				$gm_query = xtc_db_query("
										SELECT 
											COUNT(*) 
										AS
											count
										FROM 
											categories
										WHERE 
											categories_icon  = '" . $this->file_new_name	. "'
										AND 
											categories_id	 != '" . $this->sql_id			. "'
										");
			}
			
			$gm = xtc_db_fetch_array($gm_query);
			
			if($gm['count'] > 0) {
				return true;
			} else {
				return false;
			}
		}	
	}

MainFactory::load_origin_class('GMCatUpload');
