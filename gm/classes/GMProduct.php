<?php
/* --------------------------------------------------------------
   GMProduct.php 2015-05-20 gambio
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------

GMProduct.php 16.04.2008 pt
	Gambio OHG
	http://www.gambio.de
	Copyright (c) 2008 Gambio OHG
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/
	include(DIR_FS_CATALOG . 'gm/inc/gm_prepare_filename.inc.php');

	/*
	*	-> helper function
	*/
	class GMProduct_ORIGIN {

		/*
		*	-> function to get content values
		*/
		function __construct() {
			
			return;
		}

		function get_filename($filename, $products_id, $products_data) {
		
			if(!$this->filename_exists($filename)) {	
				
				if($this->prd_filename_exists($products_id, $filename) == $products_id) {			
				
					$new_filename = gm_prepare_filename($filename);	

				

				} else {										
					@xtc_del_image_file($products_data['products_previous_image_0']);
					$new_filename = gm_prepare_filename($filename);										
				}

			} else {				
				
				$new_filename = $products_id . '_' . gm_prepare_filename($filename);		
			}		
		
			return $new_filename;
		}

		// Procuct Images Table
		function filename_exists($filename) {

			$dup_check_query = xtDBquery("
										SELECT 
											products_id
										FROM 
											products_images
										WHERE 
											image_name = '" . $filename . "'
										");

			// image already exist
			if(xtc_db_num_rows($dup_check_query) > 0) {
				return true;
			} else {
				return false;
			}
		}	

		// Procuct Table
		function prd_filename_exists($products_id, $filename) {

			$dup_check_query = xtDBquery("
										SELECT 
											products_id
										FROM 
											products_images
										WHERE 
											image_name	= '" . $filename . "'
										AND 
											products_id = '" . (int)$products_id . "'
										");

			// image already exist
			if(xtc_db_num_rows($dup_check_query) > 0) {
				$prd_id = xtc_fetch_array($dup_check_query);
				return $prd_id['products_id'];
			} else {
				return false;
			}
		}	
	}
MainFactory::load_origin_class('GMProduct');