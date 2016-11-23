<?php
/* --------------------------------------------------------------
   GMProductUpload.php  2014-12-18 tt
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]

   IMPORTANT! THIS FILE IS DEPRECATED AND WILL BE REPLACED IN THE FUTURE. 
   MODIFY IT ONLY FOR FIXES. DO NOT APPEND IT WITH NEW FEATURES, USE THE
   NEW GX-ENGINE LIBRARIES INSTEAD.
   --------------------------------------------------------------

*	-> class to upload product images
	*/
	class GMProductUpload_ORIGIN {
		
		var $file_tmp_name;
		var $suffix;
		var $file_name;
		var $file_type;
		var $prd_id;
		var $more_image_nr;
		var $more_image_prev;

		function __construct($file, $new_file_name='', $prd_id, $more_image_prev='', $more_image_nr='', $first_image='', $more_image=false) {
			
			
			$this->more_image		= $more_image;
			$this->first_image		= $first_image;
			$this->tmp_name			= (isset($file['tmp_name'])) ? $file['tmp_name'] : '';
			$this->file_type		= (isset($file['type'])) ? $file['type'] : '';
			$this->more_image_prev	= $more_image_prev;
			$this->more_image_nr	= $more_image_nr;
			$this->prd_id			= $prd_id;
			
			if(empty($new_file_name)) {
				$this->suffix			= $this->get_suffix($file['name']);
				$this->file_name		= $this->get_filename($file['name']);
			} else {
				$this->suffix			= $this->get_suffix($new_file_name);
				$this->file_name		= $this->get_filename($new_file_name);
			}

			return;
		}
		

		/*
		* -> get suffix
		*/
		function upload() {		

			$file_name = $this->get_file();			
			if($this->check_upload()) {				
				if( (defined( 'SUPPRESS_UPLOAD_CHECKS' ) && rename($this->tmp_name, DIR_FS_CATALOG_ORIGINAL_IMAGES . $file_name)) || @move_uploaded_file($this->tmp_name, DIR_FS_CATALOG_ORIGINAL_IMAGES . $file_name)) {
					@chmod(DIR_FS_CATALOG_ORIGINAL_IMAGES . $file_name, 0777);
					return $file_name;
				} else {
					return false;
				}				
			} else {
				return false;
			}
		}

		
		/*
		* -> rename file
		*/
		function re_name($old_file) {
		
			$this->suffix			= $this->get_suffix($old_file);	
			$new_filename			= $this->get_file();
			

			@rename(DIR_FS_CATALOG_INFO_IMAGES		. $old_file, DIR_FS_CATALOG_INFO_IMAGES			. $new_filename);
			@rename(DIR_FS_CATALOG_POPUP_IMAGES		. $old_file, DIR_FS_CATALOG_POPUP_IMAGES		. $new_filename);
			@rename(DIR_FS_CATALOG_THUMBNAIL_IMAGES	. $old_file, DIR_FS_CATALOG_THUMBNAIL_IMAGES	. $new_filename);
			@rename(DIR_FS_CATALOG_ORIGINAL_IMAGES	. $old_file, DIR_FS_CATALOG_ORIGINAL_IMAGES		. $new_filename);
			@rename(DIR_FS_CATALOG_IMAGES . 'product_images/gallery_images/' . $old_file, DIR_FS_CATALOG_IMAGES . 'product_images/gallery_images/' . $new_filename);
			
			if(file_exists(DIR_FS_CATALOG_IMAGES . 'product_images/gm_gmotion_images/' . basename($old_file)))
			{
				@rename(DIR_FS_CATALOG_IMAGES . 'product_images/gm_gmotion_images/' . basename($old_file), DIR_FS_CATALOG_IMAGES . 'product_images/gm_gmotion_images/' . basename($new_filename));
			}

			return $new_filename;
		}

		
		/*
		* -> get file_owner
		*/
		function get_file() {			
			
			// check suffix
			$cntrl_suffix = $this->set_suffix();
			if($cntrl_suffix != $this->suffix && !empty($cntrl_suffix)) {
				$this->suffix = $cntrl_suffix;
			} 
			
			$new_file_name = $this->file_name . '.' . $this->suffix;
			
			if($this->more_image) {	
				$dup_image = '_' . $this->prd_id . '_' . ($this->more_image_nr + 1);				
				if($this->tbl_prd_mo_img($new_file_name) || $this->tbl_prd_mo($new_file_name) || $this->first_image==$new_file_name) {
					$_SESSION['gm_redirect'] += 1;
					$new_file_name = $this->file_name . $dup_image . '.' . $this->suffix;
				} 

			} else {
				
				$dup_image = '_' . $this->prd_id . '_0';
				if($this->tbl_prd_img($new_file_name) || $this->tbl_prd($new_file_name)) {
					$_SESSION['gm_redirect'] += 1;
					$new_file_name = $this->file_name . $dup_image . '.' . $this->suffix;
				} 
			}

			return $new_file_name;
		}


		/*
		* -> see if file exists in table products image
		*/
		function tbl_prd_img($new_file_name) {
			
			$gm_query = xtc_db_query("
									SELECT 
										COUNT(*) 
									AS
										count
									FROM 
										products_images
									WHERE 
										image_name = '" . $new_file_name . "'
									");
			
			$gm = xtc_db_fetch_array($gm_query);
			
			if($gm['count'] > 0) {
				return true;
			} else {
				return false;
			}
		}


		/*
		* -> case more pics - see if file exists in table products image
		*/
		function tbl_prd_mo_img($new_file_name) {

			
			$gm_query = xtc_db_query("
									SELECT 
										image_id
									AS
										id
									FROM 
										products_images
									WHERE 
										image_name	 = '" . $this->more_image_prev . "'
									LIMIT 1 
									");

			$gm = xtc_db_fetch_array($gm_query);
			
			$gm_query = xtc_db_query("
									SELECT 
										COUNT(*) 
									AS
										count
									FROM 
										products_images
									WHERE 
										image_name	 = '" . $new_file_name . "'
									AND 
										image_id	!= '" . $gm['id'] . "'
									");

			$gm = xtc_db_fetch_array($gm_query);

			if($gm['count'] > 0) {
				return true;
			} else {
				return false;
			}
		}


		/*
		* -> see if file exists in table products
		*/
		function tbl_prd($new_file_name) {
			
			$gm_query = xtc_db_query("
									SELECT 
										COUNT(*) 
									AS
										count
									FROM 
										products
									WHERE 
										products_image	= '" . $new_file_name . "'
									AND
										products_id		!= '" . $this->prd_id . "'
									");

			$gm = xtc_db_fetch_array($gm_query);
			if($gm['count'] > 0) {
				return true;
			} else {
				return false;
			}
		}


		/*
		* -> see if "more" file exists in table products
		*/
		function tbl_prd_mo($new_file_name) {
			
			$gm_query = xtc_db_query("
									SELECT 
										COUNT(*) 
									AS
										count
									FROM 
										products
									WHERE 
										products_image	= '" . $new_file_name . "'
									");

			$gm = xtc_db_fetch_array($gm_query);
			if($gm['count'] > 0) {
				return true;
			} else {
				return false;
			}
		}


		/*
		* -> get suffix
		*/
		function get_suffix($file) {	
			if(strstr($file, '.')) {

				$array_filename = explode('.', $file);
				$suffix			= array_pop($array_filename);				

			} else {
				$suffix = '';
			}
			return $suffix;
		}

		
		/*
		* -> get absolut filename 
		*/
		function get_filename($file) {	
			
			if(!empty($this->suffix)) {
				$filename	= str_replace('.' . $this->suffix, '', $file);
			} else {
				$filename	= $file;
			}
			$search	 = "ÁáÉéÍíÓóÚúÇçÃãÀàÂâÊêÎîÔôÕõÛû&ŠŽšžŸÀÁÂÃÅÇÈÉÊËÌÍÎÏÑÒÓÔÕØÙÚÛÝàáâãåçèéêëìíîïñòóôõøùúûýÿ ";
			$replace = "AaEeIiOoUuCcAaAaAaEeIiOoOoUueSZszYAAAAACEEEEIIIINOOOOOUUUYaaaaaceeeeiiiinooooouuuyy_";			
			$arr = array('ä' => 'ae', 'ö' => 'oe', 'Ü' => 'ue', 'ß' => 'ss');
			$filename = strtolower(strtr($filename, $search, $replace));			
			$filename = strtr($filename, $arr);			
			$filename = preg_replace("/[^a-zA-Z0-9\\.\\-\\_]/i", '', $filename);
			return $filename;
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


		/*
		* -> set suffix
		*/
		function set_suffix() {	
			
			switch($this->file_type) {
				
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
	}

MainFactory::load_origin_class('GMProductUpload');
