<?php
/*
	--------------------------------------------------------------
	GMLogoManager.php 2015-05-20 gm
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2015 Gambio GmbH
	Released under the GNU General Public License
	--------------------------------------------------------------

*/
	/*
	*	a class to manage the logo files
	*/
	class GMLogoManager_ORIGIN {
		
		var $logo_path;
		var $logo_src;
		var $logo_use;
		var $logo_file;
		var $logo_key;
	
		/*
		* -> constructor
		*/
		function __construct($logo_key) {

			if(!defined(GM_HTTP_SERVER)) {
				define(GM_HTTP_SERVER, HTTP_SERVER);
			}
			$this->logo_src		= DIR_FS_CATALOG . DIR_WS_IMAGES . 'logos/';		
			$this->logo_path	= GM_HTTP_SERVER . DIR_WS_CATALOG . DIR_WS_IMAGES . 'logos/';		
			$this->logo_use		= gm_get_conf(strtoupper($logo_key) . '_USE');	
			$this->logo_file	= gm_get_conf(strtoupper($logo_key));
			$this->logo_key		= $logo_key;

			return;
		}		
		
		/*
		* -> to get the logo html 
		*/
		function get_logo($gm_alt='logo') {			
			
			$_w = gm_get_conf(strtoupper($this->logo_key) . '_SIZE_W');
			$_h = gm_get_conf(strtoupper($this->logo_key) . '_SIZE_H');

			if($this->logo_key == 'gm_logo_flash') {					
				
				$logo_html = '	<object type="application/x-shockwave-flash" data="' . $this->logo_path . $this->logo_file . '" width="' . $_w . '" height="' . $_h . '" codebase="https://fpdownload.adobe.com/pub/shockwave/cabs/flash/swflash.cab#version=9,0,0,0">
									<param name="movie" value="' . $this->logo_path . $this->logo_file . '" />
									<param name="play" value="true" />
									<param name="loop" value="true" />
									<param name="quality" value="high" />
									<param name="wmode" value="transparent" />
								</object>
								';

				return $logo_html;

			} else {					

				if(!empty($_w)) $width='width="' . $_w . '"';
				if(!empty($_h))  $height='height="' . $_h . '"';
				$logo_html = '<img '.$width.' '.$height.' src="' . $this->logo_path . $this->logo_file . '" alt="' . htmlspecialchars_wrapper(STORE_NAME) . '" title="' . htmlspecialchars_wrapper(STORE_NAME) . '" />';

				return $logo_html;
			} 		
		}

		
		/*
		* -> file exist
		*/
		function logo_exist() {			
			if(file_exists($this->logo_src . $this->logo_file) && !empty($this->logo_file)) {	
				
				return true;

			} else {

				return false;

			}
		}

		/*
		* -> check upload by mime-type and file-extension
		*/
		function check_upload($p_filetype, $p_file_extension){ 
			
			if($p_filetype == 'image/x-icon' || $p_filetype == "image/ico") {	
		
				if($p_filetype == 'image/x-icon' && $p_file_extension == 'ico') {	
					return true; 
				} else {					
					return false;
				}					
				
			} else if($this->logo_key == 'gm_logo_flash') {
				
				if($p_filetype == "application/x-shockwave-flash" && ($p_file_extension == 'swf' || $p_file_extension == 'flv')) { 
				
					return true; 
				} else {
					return false;
				}					

			} else if($this->logo_key == 'gm_logo_overlay') {
				
				if($p_filetype == "image/gif" && $p_file_extension == 'gif') { 
				
					return true; 
				} else {
					return false;
				}					

			} else {

				if(($p_filetype == "image/gif" 
						|| $p_filetype == "image/png"  
						|| $p_filetype == "image/x-png"  
						|| $p_filetype == "image/jpg"  
						|| $p_filetype == "image/jpeg"
						|| $p_filetype == "image/pjpeg")
					&& in_array($p_file_extension, array('gif', 'png' ,'jpg', 'jpeg', 'pjpeg')))
				{ 
					return true; 
				} else {
					return false;
				}					
			}
		}
		
		/**
		 * extend logoname with the logo key
		 */
		function prepare_logonames($p_file_parts) {
			// get the filenmae and extension of the new image
			$t_filename = $p_file_parts['filename'];
			$t_file_extension = $p_file_parts['extension'];
			
			// build the name of the new image
			switch($this->logo_key) {
				case 'gm_logo_shop':
					$t_new_filename = gm_prepare_filename($t_filename.'_logo.'.$t_file_extension);
					break;
				case 'gm_logo_flash':
					$t_new_filename = gm_prepare_filename($t_filename.'_flash.'.$t_file_extension);
					break;
				case 'gm_logo_mail':
					$t_new_filename = gm_prepare_filename($t_filename.'_mail.'.$t_file_extension);
					break;
				case 'gm_logo_pdf':
					$t_new_filename = gm_prepare_filename($t_filename.'_pdf.'.$t_file_extension);
					break;
				case 'gm_logo_overlay':
					$t_new_filename = 'overlay.gif';
					break;
				case 'gm_logo_favicon':
					$t_new_filename = 'favicon.ico';
					break;
				case 'gm_logo_favicon_ipad':
					$t_new_filename = 'favicon.png';
					break;
				case 'gm_logo_cat':
					$t_new_filename = gm_prepare_filename($t_filename.'_cat.'.$t_file_extension);
					break;
				default:
					$t_new_filename = gm_prepare_filename($_FILES['GM_LOGO']['name']);
					break;
			}
			
			return $t_new_filename;
		}

		/*
		* -> upload manager
		*/
		function upload() {
			
			$this->manage_logos();

			if($_POST['GM_LOGO_DELETE'] == 'on') {
				
				// delete old file if exists
				if(file_exists($this->logo_src . $this->logo_file)) {
					
					unlink($this->logo_src . $this->logo_file);
					gm_set_conf(strtoupper($this->logo_key), '');				
				}
				
				gm_set_conf(strtoupper($this->logo_key) . '_USE', '0');
				$this->logo_use = '0';
				
				return GM_LOGO_FILE_DELETED;

			} else if(!empty($_FILES['GM_LOGO']['name'])) {

				$myparts = pathinfo($_FILES['GM_LOGO']['name']);
				$myextension = strtolower($myparts['extension']);

				if(($this->check_upload($_FILES['GM_LOGO']['type'], $myextension)	&& $this->logo_key != 'gm_logo_favicon')
				   || ($this->logo_key == 'gm_logo_favicon' && $myextension == 'ico')
				   || ($this->logo_key == 'gm_logo_favicon_ipad' && $myextension == 'png')) {
				
					// prepare filenames
					$t_new_filename = $this->prepare_logonames($myparts);
					
					if(!file_exists($this->logo_src . $t_new_filename) || $this->logo_file == $t_new_filename) {
						
						// delete old file if exists
						if(file_exists($this->logo_src . $this->logo_file)) {
						
							@unlink($this->logo_src . $this->logo_file);
						}
						@copy($_FILES['GM_LOGO']['tmp_name'], $this->logo_src . $t_new_filename);					
						@chmod($this->logo_src . $t_new_filename, 0777);					
						
						// save data to configuration
						gm_set_conf(strtoupper($this->logo_key), $t_new_filename);
						
						// set size once a time
						$imagesize = @getimagesize($this->logo_src . $t_new_filename);
						gm_set_conf(strtoupper($this->logo_key) . '_SIZE_W', $imagesize[0]);
						gm_set_conf(strtoupper($this->logo_key) . '_SIZE_H', $imagesize[1]);						
						if(strtoupper($this->logo_key) == 'GM_LOGO_CAT') {
							gm_set_conf(strtoupper($this->logo_key) . '_USE', '1');
						}
						// save data to configuration
						gm_set_conf(strtoupper($this->logo_key), $t_new_filename);
						
						$this->logo_file = $t_new_filename;

						return GM_LOGO_UPLOAD_SUCCEEDED;
					
					// file in use
					} else {
						
						return GM_LOGO_FILE_EXISTS;
					
					}				
				
				// file not allowed
				} else {

					return GM_LOGO_FILETYP_WRONG;

				}			
			}
		}
		
		/*
		* -> manage logos
		*/
		function manage_logos() {
			
			if($this->logo_key == 'gm_logo_flash' && file_exists($this->logo_src . gm_get_conf('GM_LOGO_FLASH')) && $_POST['GM_LOGO_USE'] == 'on') {
				gm_set_conf('GM_LOGO_FLASH_USE',	'1');
				gm_set_conf('GM_LOGO_SHOP_USE',		'0');
				$this->logo_use = '1';
			} else if($this->logo_key == 'gm_logo_shop' && file_exists($this->logo_src . gm_get_conf('GM_LOGO_SHOP')) && $_POST['GM_LOGO_USE'] == 'on') {
				gm_set_conf('GM_LOGO_FLASH_USE',	'0');
				gm_set_conf('GM_LOGO_SHOP_USE',		'1');
				$this->logo_use = '1';
			} else if(($this->logo_key == 'gm_logo_mail' || $this->logo_key == 'gm_logo_favicon' || $this->logo_key == 'gm_logo_favicon_ipad' || $this->logo_key == 'gm_logo_pdf') && $this->logo_exist() && $_POST['GM_LOGO_USE'] == 'on') {
				gm_set_conf(strtoupper($this->logo_key) . '_USE', '1');
				$this->logo_use = '1';
			} else if(empty($_POST['GM_LOGO_USE'])) {
				gm_set_conf(strtoupper($this->logo_key) . '_USE', '0');
				$this->logo_use = '0';
			}
			return;
		}
	}

MainFactory::load_origin_class('GMLogoManager');