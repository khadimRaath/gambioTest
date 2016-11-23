<?php
/* --------------------------------------------------------------
   gm_create_corner.inc.php 2009-11-23 pt
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2009 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------

*
	*	function to install the corner in db/css
	*/
	function gm_install_corner() 
	{
		
		$t_gm_bg = xtc_db_query("
											SELECT 
													gm_css_style_id 
												AS 
													id
											FROM
												gm_css_style gcs
											WHERE
												gcs.style_name	= '#topmenu_block'
											LIMIT 1
						");

		if(xtc_db_num_rows($t_gm_bg) == 1)
		{

			$t_row = xtc_db_fetch_array($t_gm_bg);

			$t_gm_bg_query = xtc_db_query("
											SELECT
												*
											FROM
												gm_css_style_content	
											WHERE
												gm_css_style_id = '" . $t_row['id'] . "'
											AND 
												style_attribute = 'background-image'
											LIMIT 1
						");

			if(xtc_db_num_rows($t_gm_bg_query) == 0)
			{
				xtc_db_query("
											INSERT
											INTO
												gm_css_style_content
											SET
												style_attribute = 'background-image',
												style_value		= 'url(" . DIR_WS_CATALOG . DIR_WS_IMAGES . "logos/gm_corner.gif)',
												gm_css_style_id = '" . $t_row['id'] . "'
							");

				xtc_db_query("
											INSERT
											INTO
												gm_css_style_content
											SET
												style_attribute = 'background-repeat',
												style_value		= 'no-repeat',
												gm_css_style_id = '" . $t_row['id'] . "'
							");
			}
			else 
			{

				/* delete existing file */
				$t_row_old_img		= xtc_db_fetch_array($t_gm_bg_query);
				$t_old_file_array	= @explode('/', $t_row_old_img['style_value']);
				$t_old_file			= $t_old_file_array[sizeof($t_old_file_array) - 1];
				$t_old_file			= trim(substr($t_old_file, 0, strlen($t_old_file) - 1));

				if(!empty($t_old_file))
				{				
					@unlink(DIR_FS_CATALOG . 'templates/' . CURRENT_TEMPLATE . '/backgrounds/' . $t_old_file);						
				}

				xtc_db_query("
											UPDATE 
												gm_css_style_content
											SET
												style_value = 'url(" . DIR_WS_CATALOG . DIR_WS_IMAGES . "logos/gm_corner.gif)'
											WHERE
												gm_css_style_id = '" . $t_row['id'] . "'
											AND 
												style_attribute = 'background-image'
											LIMIT 1
				");

				xtc_db_query("
											UPDATE 
												gm_css_style_content
											SET
												style_value = 'no-repeat'
											WHERE
												gm_css_style_id = '" . $t_row['id'] . "'
											AND 
												style_attribute = 'background-repeat'
											LIMIT 1
				");
			}

			@unlink(DIR_FS_CATALOG . 'cache/__dynamics.css');
		}

		return;
	}


	/**
	*	function to remove the corner in db/css
	*/
	function gm_delete_corner() 
	{
	
		$t_gm_bg = xtc_db_query("
											SELECT 
													gm_css_style_id 
												AS 
													id
											FROM
												gm_css_style gcs
											WHERE
												gcs.style_name	= '#topmenu_block'
											LIMIT 1
						");

		if(xtc_db_num_rows($t_gm_bg) == 1)
		{
			$t_row = xtc_db_fetch_array($t_gm_bg);

			xtc_db_query("
											UPDATE
												gm_css_style_content
											SET
												style_value = 'none'
											WHERE
												gm_css_style_id = '" . $t_row['id'] . "'
											AND style_attribute = 'background-image'
						");
			
			xtc_db_query("
											UPDATE
												gm_css_style_content
											SET
												style_value = 'repeat'
											WHERE
												gm_css_style_id = '" . $t_row['id'] . "'
											AND style_attribute = 'background-repeat'
						");
			
			@unlink(DIR_FS_CATALOG . 'cache/__dynamics.css');
		}

		return;
	}


	/**
	*	helper function to convert hex to rgb 
	*/
	function gm_corner_get_rgb($p_style_name)
	{	
		$t_gm_color = xtc_db_query("
											SELECT 
													gcsc.style_value 
												AS 
													color
											FROM
												gm_css_style gcs,
												gm_css_style_content gcsc
											WHERE
												gcs.gm_css_style_id			= gcsc.gm_css_style_id
											AND
												gcs.style_name				= '" . $p_style_name . "'
											AND
												gcsc.style_attribute		= 'background-color'
											LIMIT 1
		");

		if(xtc_db_num_rows($t_gm_color) == 1)
		{
			$t_row = xtc_db_fetch_array($t_gm_color);

			$t_gm_rgb		= array();

			$t_gm_hex		= str_replace('#', '', $t_row['color']);

			$t_gm_rgb['r']	= hexdec(substr($t_gm_hex, 0, 2));
			$t_gm_rgb['g']	= hexdec(substr($t_gm_hex, 2, 2));
			$t_gm_rgb['b']	= hexdec(substr($t_gm_hex, 4, 2));

			return $t_gm_rgb;
		} 
		else
		{
			return false;		
		}
	}


	/**
	*	function to create the corner file
	*/
	function gm_create_corner()
	{
		gm_install_corner();

		$t_gm_corner = @imagecreate(10, 10);

		$t_gm_fg_color_rgb = array();
		$t_gm_bg_color_rgb = array();

		$t_gm_fg_color_rgb = gm_corner_get_rgb('#topmenu_block');
		$t_gm_bg_color_rgb = gm_corner_get_rgb('.wrap_shop');

		$t_gm_bg_color = imagecolorallocate($t_gm_corner, $t_gm_bg_color_rgb['r'], $t_gm_bg_color_rgb['g'], $t_gm_bg_color_rgb['b']);
		$t_gm_fg_color = imagecolorallocate($t_gm_corner, $t_gm_fg_color_rgb['r'], $t_gm_fg_color_rgb['g'], $t_gm_fg_color_rgb['b']);
		
		$t_gm_coord = array(10, 0, 10, 10, 0, 10);

		@imagefilledpolygon($t_gm_corner, $t_gm_coord, 3, $t_gm_fg_color);        
		
		@fopen(DIR_FS_CATALOG . DIR_WS_IMAGES . 'logos/gm_corner.gif', "w+");
		@chmod(DIR_FS_CATALOG . DIR_WS_IMAGES . 'logos/gm_corner.gif', 0777);

        @imagegif($t_gm_corner, DIR_FS_CATALOG . DIR_WS_IMAGES . 'logos/gm_corner.gif');
        
		@imagedestroy($t_gm_corner);

		return;
	}
?>