<?php
/* --------------------------------------------------------------
   GMUpload.php  2014-06-21 gm
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
?><?php


	class GMUpload_ORIGIN {
		
		// the actual Element of array $_FILES
		var $file;

		// new name
		var $file_rename;

		// old name of privious image
		var $file_oldname;

		// the file dir
		var $file_dir;


		/*
		* -> constructor
		*/

		function __construct($file, $file_rename, $file_dir, $file_oldname='') {
			
			$this->file				= $file;
			$this->file_dir			= $file_dir;
			$this->file_rename		= $file_rename;
			$this->file_oldname		= $file_oldname;
			return;
		}

		
		/*
		* -> proceed upload
		*/
		function upload($file_new_name) {
			if(@move_uploaded_file($this->file['tmp_name'], $this->file_dir . $file_new_name)) {					
				@chmod($this->file_dir . $file_new_name, 0777);
				return $file_new_name;
			} else {
				return false;
			}			
		}
		
		
		/*
		* -> set file_suffix
		*/
		function set_suffix() {
			
			// case upload
			if(!empty($this->file['type'])) {

				$file_suffix = $this->get_suffix();
			
			// case rename
			} else {

				$array_filename		= explode('.', $this->file_oldname);
				$file_suffix		= array_pop($array_filename);				

			}

			return $file_suffix;
		}

		
		/*
		* -> set file_name
		*/
		function set_filename() {	
			
			$filename = $this->file_rename;
			
			// case rename
			if(!empty($this->file_rename)) { 
					if(strstr($this->file_rename, '.'))
					$filename = substr($this->file_rename, 0, strpos($this->file_rename, '.'));				

			} else {

				$filename = substr($this->file['name'], 0, strpos($this->file['name'],'.'));
			}

			$search	 = "����������������������������&������������������������������������������������������ ";
			$replace = "AaEeIiOoUuCcAaAaAaEeIiOoOoUueSZszYAAAAACEEEEIIIINOOOOOUUUYaaaaaceeeeiiiinooooouuuyy_";			
			$arr = array('�' => 'ae', '�' => 'oe', '�' => 'ue', '�' => 'ss');
			$filename = strtolower(strtr($filename, $search, $replace));			
			$filename = strtr($filename, $arr);			
			$filename = preg_replace("/[^a-zA-Z0-9\\.\\-\\_]/i", '', $filename);
			return $filename;
		}	

		
		/*
		* -> get suffix
		*/
		function get_suffix() {	
			
			switch($this->file['type']) {
				
				case "image/gif":
					$suffix = 'gif';
				break;
				
				case "image/pjpeg":
				case "image/jpeg":
				case "image/jpg":
					$suffix = 'jpg';
				break;				

				case "image/png":
				case "image/x-png":
					$suffix = 'png';
				break;		

			}

			return $suffix;
		}


		/*
		* -> check upload by filetype 
		*/
		function check_upload() { 
			if($this->file_type == "image/gif" 
			|| $this->file_type == "image/png"  
			|| $this->file_type == "image/x-png"  
			|| $this->file_type == "image/jpg"  
			|| $this->file_type == "image/jpeg"  
			|| $this->file_type == "image/gif"  
			|| $this->file_type == "image/pjpeg") { 
			
				return true; 
			} else {
				return false;
			}	
		}
	}	


MainFactory::load_origin_class('GMUpload');
