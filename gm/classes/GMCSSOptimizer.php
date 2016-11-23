<?php
/* --------------------------------------------------------------
   GMCSSOptimizer.php 2015-05-22 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/
?><?php

	class GMCSSOptimizer
	{
		/**
		*	@var string
		*/
		var $v_current_template;
		
		/**
		*	contains the css code
		*	@var string
		*/
		var $v_css;

		/**
		*	debug_mode
		*	@var boolean
		*/
		var $v_debug_mode;

		/**
		*	compression_rate
		*	@var string
		*/
		var $v_compression_rate;

		/**
		*	absolute path to the cache file
		*	@var string
		*/
		var $v_cache_file;

		/**
		*	css tidy object
		*	@var object
		*/
		var $v_css_tidy;

		/**
		*	contains elemnts/selectors which are not in use
		*	@var array
		*/
		var $v_spam = array();

		/**
		*	global combined styles
		*	@var array
		*/
		var $v_global_styles = array(

										/*

										'font-family'
										,
										'font-size'
										,
										'color'
										,
										'font-style'
										,
										'text-decoration'
										,
										'text-transform'
										,
										'font-weight'
										,
										'text-align'

										*/
		);
		
		/**
		 * contains an array with all merged css-styles
		 * 
		 */
		var $v_styles = array();

		/**
		*	contains the css code
		*	@var boolean
		*/
		var $v_style_edit;
		
		/**
		*	constructor
		*	@param boolean $p_debug_mode
		*/
		function __construct($p_debug_mode, $p_style_edit)
		{

			$this->v_current_template = $_GET['current_template'];
			$this->v_debug_mode = $p_debug_mode;
			$this->style_edit = $p_style_edit;

			$this->v_cache_file = DIR_FS_CATALOG . 'cache/__dynamics.css';

			// $this->v_compression_rate = 'low_compression';
			$this->v_compression_rate = 'highest_compression';

			/* SPAM FILTER FOR UNUSED STYLES */

			$this->v_spam[] = 'menubox_gm_addons';

			return;
		}
		
		/**
		*	print css code
		*/
		function create_css()
		{
			if($this->v_debug_mode)
			{
				$this->create_css_plain();
			}
			else
			{
				$this->create_css_optimized();
			}
			return;
		}
		
		
		
		/**
		*	print css code plain
		*/
		function create_css_plain()
		{
			$t_body_styles = array();
			$t_css = '';
			
			$t_result = mysqli_query($GLOBALS["___mysqli_ston"], '
									SELECT 
										style_name		AS name,
										gm_css_style_id AS id
									FROM
										gm_css_style
									WHERE
										template_name	= "' . addslashes(trim($this->v_current_template)) . '"
									ORDER BY
										gm_css_style_id
									');
//			foreach($this->v_styles as $t_style)
			while($t_style = mysqli_fetch_array($t_result))
			{
				$t_result_content = mysqli_query($GLOBALS["___mysqli_ston"], '
												SELECT
													style_attribute AS attribute,
													style_value		AS value
												FROM
													gm_css_style_content
												WHERE
													gm_css_style_id = "' . $t_style['id'] . '"
												ORDER BY
													style_attribute
				');

				while(($t_row_content = mysqli_fetch_array($t_result_content) ))
				{
					$t_value = $t_row_content['value'];

					if($this->v_current_template == 'gambio')
					{
						/* get backgroundcolor for footer */
						if($t_style['name'] == '.wrap_site' && $t_row_content['attribute'] == 'background-color')
						{
							$t_css_background_color = $t_value;
							$t_value = 'transparent';

							$t_css_background_colors_array = array();
							if(strlen($t_css_background_color) == 7)
							{
								$t_css_background_colors_array['r'] = hexdec(substr($t_css_background_color, 1, 2));
								$t_css_background_colors_array['g'] = hexdec(substr($t_css_background_color, 3, 2));
								$t_css_background_colors_array['b'] = hexdec(substr($t_css_background_color, 5, 2));

								// YUV brightness
								$t_css_background_brightness = $t_css_background_colors_array['r'] * 0.299 + $t_css_background_colors_array['g'] * 0.587 + $t_css_background_colors_array['b'] * 0.114;
							}
						}

						/* get background-image for footer */
						if($t_style['name'] == '.wrap_site' && $t_row_content['attribute'] == 'background-image')
						{
							$t_css_background_image = $t_value;
							$t_value = 'none';
						}

						/* get background-repeat for footer */
						if($t_style['name'] == '.wrap_site' && $t_row_content['attribute'] == 'background-repeat')
						{
							$t_css_background_repeat = $t_value;
						}

						/* get font color for footer */
						if($t_style['name'] == '#column_content' && $t_row_content['attribute'] == 'color')
						{
							$t_css_color = $t_value;

							$t_css_colors_array = array();
							if(strlen($t_css_color) == 7)
							{
								$t_css_colors_array['r'] = hexdec(substr($t_css_color, 1, 2));
								$t_css_colors_array['g'] = hexdec(substr($t_css_color, 3, 2));
								$t_css_colors_array['b'] = hexdec(substr($t_css_color, 5, 2));

								// YUV brightness
								$t_css_brightness = $t_css_colors_array['r'] * 0.299 + $t_css_colors_array['g'] * 0.587 + $t_css_colors_array['b'] * 0.114;
							}
						}
					}
					else
					{
						if($this->get_conf('SHOW_FOOTER') == 'true')
						{
							/* get backgroundcolor for footer */
							if($t_style['name'] == '#footer' && $t_row_content['attribute'] == 'background-color')
							{
								$t_css_background_color = $t_value;

								$t_css_background_colors_array = array();
								if(strlen($t_css_background_color) == 7)
								{
									$t_css_background_colors_array['r'] = hexdec(substr($t_css_background_color, 1, 2));
									$t_css_background_colors_array['g'] = hexdec(substr($t_css_background_color, 3, 2));
									$t_css_background_colors_array['b'] = hexdec(substr($t_css_background_color, 5, 2));

									// YUV brightness
									$t_css_background_brightness = $t_css_background_colors_array['r'] * 0.299 + $t_css_background_colors_array['g'] * 0.587 + $t_css_background_colors_array['b'] * 0.114;
								}
							}

							/* get font color for footer */
							if($t_style['name'] == '#footer' && $t_row_content['attribute'] == 'color')
							{
								$t_css_color = $t_value;

								$t_css_colors_array = array();
								if(strlen($t_css_color) == 7)
								{
									$t_css_colors_array['r'] = hexdec(substr($t_css_color, 1, 2));
									$t_css_colors_array['g'] = hexdec(substr($t_css_color, 3, 2));
									$t_css_colors_array['b'] = hexdec(substr($t_css_color, 5, 2));

									// YUV brightness
									$t_css_brightness = $t_css_colors_array['r'] * 0.299 + $t_css_colors_array['g'] * 0.587 + $t_css_colors_array['b'] * 0.114;
								}
							}
						}
						else
						{
							/* get backgroundcolor for footer */
							if($t_style['name'] == '#container_inner' && $t_row_content['attribute'] == 'background-color')
							{
								$t_css_background_color = $t_value;

								$t_css_background_colors_array = array();
								if(strlen($t_css_background_color) == 7)
								{
									$t_css_background_colors_array['r'] = hexdec(substr($t_css_background_color, 1, 2));
									$t_css_background_colors_array['g'] = hexdec(substr($t_css_background_color, 3, 2));
									$t_css_background_colors_array['b'] = hexdec(substr($t_css_background_color, 5, 2));

									// YUV brightness
									$t_css_background_brightness = $t_css_background_colors_array['r'] * 0.299 + $t_css_background_colors_array['g'] * 0.587 + $t_css_background_colors_array['b'] * 0.114;
								}
							}

							/* get font color for footer */
							if($t_style['name'] == '#container_inner' && $t_row_content['attribute'] == 'color')
							{
								$t_css_color = $t_value;

								$t_css_colors_array = array();
								if(strlen($t_css_color) == 7)
								{
									$t_css_colors_array['r'] = hexdec(substr($t_css_color, 1, 2));
									$t_css_colors_array['g'] = hexdec(substr($t_css_color, 3, 2));
									$t_css_colors_array['b'] = hexdec(substr($t_css_color, 5, 2));

									// YUV brightness
									$t_css_brightness = $t_css_colors_array['r'] * 0.299 + $t_css_colors_array['g'] * 0.587 + $t_css_colors_array['b'] * 0.114;
								}
							}
						}
					}
					
					if($t_style['name'] == ".wrap_shop" && stripos($t_row_content['attribute'], "background") !== false)
					{
						$t_body_styles[] = "\n\t" . $t_row_content['attribute'] . ": " . $t_value . ";";
					}
					else
					{
						$t_style_content[$t_style['name']][$t_row_content['attribute']] = $t_value;
					}
				}
			}
			
			
			foreach($t_style_content as $t_style_name => $t_styles)
			{
				$t_css .= $t_style_name . " {";
				foreach($t_styles as $attribute => $value)
				{
					$t_css .= "\n\t" . $attribute . ": " . $value . ";";
				}
				$t_css .= "\n}\n\n";
			}

			
			if(isset($t_css_background_brightness) && isset($t_css_brightness) && isset($t_css_background_colors_array))
			{
				define('GM_COLOR_DIFFERENCE', 80);

				if(max($t_css_background_brightness, $t_css_brightness) - min($t_css_background_brightness, $t_css_brightness) < GM_COLOR_DIFFERENCE)
				{
					if($t_css_background_brightness > 127)
					{
						$t_css_background_colors_array['r'] -= GM_COLOR_DIFFERENCE;
						$t_css_background_colors_array['g'] -= GM_COLOR_DIFFERENCE;
						$t_css_background_colors_array['b'] -= GM_COLOR_DIFFERENCE;

						if($t_css_background_colors_array['r'] < 0)
						{
							$t_css_background_colors_array['r'] = 0;
						}
						if($t_css_background_colors_array['g'] < 0)
						{
							$t_css_background_colors_array['g'] = 0;
						}
						if($t_css_background_colors_array['b'] < 0)
						{
							$t_css_background_colors_array['b'] = 0;
						}

						$t_css_background_colors_array['r'] = dechex(round($t_css_background_colors_array['r']));
						$t_css_background_colors_array['g'] = dechex(round($t_css_background_colors_array['g']));
						$t_css_background_colors_array['b'] = dechex(round($t_css_background_colors_array['b']));

						if(strlen($t_css_background_colors_array['r']) == 1)
						{
							$t_css_background_colors_array['r'] .= 0;
						}
						if(strlen($t_css_background_colors_array['g']) == 1)
						{
							$t_css_background_colors_array['g'] .= 0;
						}
						if(strlen($t_css_background_colors_array['b']) == 1)
						{
							$t_css_background_colors_array['b'] .= 0;
						}

						$t_css_color = '#' . $t_css_background_colors_array['r'] . $t_css_background_colors_array['g'] . $t_css_background_colors_array['b'];
					}
					else
					{
						$t_css_background_colors_array['r'] += GM_COLOR_DIFFERENCE;
						$t_css_background_colors_array['g'] += GM_COLOR_DIFFERENCE;
						$t_css_background_colors_array['b'] += GM_COLOR_DIFFERENCE;

						if($t_css_background_colors_array['r'] > 255)
						{
							$t_css_background_colors_array['r'] = 255;
						}
						if($t_css_background_colors_array['g'] > 255)
						{
							$t_css_background_colors_array['g'] = 255;
						}
						if($t_css_background_colors_array['b'] > 255)
						{
							$t_css_background_colors_array['b'] = 255;
						}

						$t_css_background_colors_array['r'] = dechex(round($t_css_background_colors_array['r']));
						$t_css_background_colors_array['g'] = dechex(round($t_css_background_colors_array['g']));
						$t_css_background_colors_array['b'] = dechex(round($t_css_background_colors_array['b']));

						if(strlen($t_css_background_colors_array['r']) == 1)
						{
							$t_css_background_colors_array['r'] .= 0;
						}
						if(strlen($t_css_background_colors_array['g']) == 1)
						{
							$t_css_background_colors_array['g'] .= 0;
						}
						if(strlen($t_css_background_colors_array['b']) == 1)
						{
							$t_css_background_colors_array['b'] .= 0;
						}

						$t_css_color = '#' . $t_css_background_colors_array['r'] . $t_css_background_colors_array['g'] . $t_css_background_colors_array['b'];
					}
				}
			}

			if($this->v_current_template == 'gambio')
			{
				if($t_css_background_image != 'none' && $t_css_background_image != 'url()')
				{
					/* BODY */
					$t_css .= "body {";
					$t_css .= "\n\t"	. "background-color: "	. $t_css_background_color	. ";";
					$t_css .= "\n\t"	. "background-image: "	. $t_css_background_image	. ";";
					$t_css .= "\n\t"	. "background-repeat: "	. $t_css_background_repeat	. ";";
					$t_css .= "\n"		. "}";
					$t_css .= "\n\n";

					/* HTML */
					$t_css .= "html .copyright, html .copyright a { ";
					$t_css .= "\n\t"	. "background-color: transparent;";
					$t_css .= "\n\t"	. "color: "			. $t_css_color				. ";";
					$t_css .= "\n"		. "}";
				}
				else
				{
					/* BODY */
					$t_css .= "body {";
					$t_css .= "\n\t"	. "background-color: "	. $t_css_background_color	. ";";
					$t_css .= "\n"		. "}";
					$t_css .= "\n\n";

					/* HTML */
					$t_css .= "html .copyright, html .copyright a { ";
					$t_css .= "\n\t"	. "background-color: "	. $t_css_background_color	. ";";
					$t_css .= "\n\t"	. "color: "			. 		  $t_css_color				. ";";
					$t_css .= "\n"		. "}";
				}


				$t_get_underline_setting = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT gm_value
																		FROM gm_configuration
																		WHERE
																			gm_key = 'GM_UNDERLINE_LINKS'
																			AND gm_value = 'true'");
				if(mysqli_num_rows($t_get_underline_setting) > 0)
				{
					$t_css .= 'a, .product_info_link { ';
					$t_css .= "\n\t"	. 'text-decoration: none !important;';
					$t_css .= "\n"		. "}";
					$t_css .= "\n\n";
					$t_css .= 'a:hover, .product_info_link:hover { ';
					$t_css .= "\n\t"	. 'text-decoration: underline !important;';
					$t_css .= "\n"		. "}";
				}
			}
			else
			{
				if(empty($t_body_styles) == false)
				{
					$t_css .= "body { " . implode("", $t_body_styles) . " }";
				}
				
				if($this->get_conf('SHOW_FOOTER') == 'true')
				{
					$t_css .= "body #footer_copyright, body #footer_copyright a { ";
					$t_css .= "\n\t"	. "color: "			. $t_css_color				. ";";
					$t_css .= "\n"		. "}";
				}
				else
				{
					$t_css .= "body #shopsoftware_by_gambio p, body #shopsoftware_by_gambio a { ";
					$t_css .= "\n\t"	. "color: "			. $t_css_color				. ";";
					$t_css .= "\n"		. "}";
				}
			}
			
			$this->v_css = $t_css;

			return;
		}

		/**
		*	print css code optimized
		*/
		function create_css_optimized()
		{
			$t_body_styles = array();
			$t_css = '';

			/* combining */
			if(count($this->v_global_styles) > 0)
			{
				$t_global_styles				= array();
				$t_global_styles				= $this->global_font_style();
				$t_global_styles['SELECTORS']	= $this->combine_selectors($t_global_styles);

				foreach($t_global_styles['SELECTORS'] as $t_key => $t_value)
				{
					$t_combine .= $t_key . "\n,";
				}
				$t_combine = substr($t_combine, 0, strlen($t_combine)-1);
				$t_combine .= "{";

				foreach($this->v_global_styles as $t_key => $t_value)
				{
					$t_combine .= $t_value . ":" . $t_global_styles[$t_value] . ";";
				}

				$t_combine .= "}";
				$t_css = $t_combine;
			}


			$this->v_css_tidy = new csstidy();

			$this->v_css_tidy->set_cfg('remove_bslash',					true);
			$this->v_css_tidy->set_cfg('compress_colors',				true);
			$this->v_css_tidy->set_cfg('compress_font-weight',			true);
			$this->v_css_tidy->set_cfg('lowercase_s',					false);
			$this->v_css_tidy->set_cfg('optimise_shorthands',			2);
			$this->v_css_tidy->set_cfg('remove_last_;',					true);
			$this->v_css_tidy->set_cfg('case_properties',				1);
			$this->v_css_tidy->set_cfg('sort_properties',				true);
			$this->v_css_tidy->set_cfg('sort_selectors',				false);
			$this->v_css_tidy->set_cfg('merge_selectors',				2);
			$this->v_css_tidy->set_cfg('discard_invalid_properties',	false);
			$this->v_css_tidy->set_cfg('css_level',						'CSS1.0');
			$this->v_css_tidy->set_cfg('preserve_css',					false);
			$this->v_css_tidy->set_cfg('timestamp',						false);

			$this->v_css_tidy->load_template($this->v_compression_rate);

			$t_result = mysqli_query($GLOBALS["___mysqli_ston"], '
									SELECT 
										style_name		AS name,
										gm_css_style_id AS id
									FROM
										gm_css_style
									WHERE
										template_name	= "' . addslashes(trim($this->v_current_template)) . '"
									ORDER BY
										gm_css_style_id
									');
			
//			foreach($this->v_styles as $t_style)
			while($t_style = mysqli_fetch_array($t_result))
			{
				if(!$this->inactive_selector($t_style['name']) && $this->is_spam($t_style['name']) == false)
				{
					$t_result_content = mysqli_query($GLOBALS["___mysqli_ston"], '
													SELECT
														style_attribute AS attribute,
														style_value		AS value
													FROM
														gm_css_style_content
													WHERE
														gm_css_style_id = "' . $t_style['id'] . '"
													ORDER BY
														style_attribute
					');

					while($t_row_content = mysqli_fetch_array($t_result_content))
					{
						if(@in_array($t_style['id'], $t_global_styles['SELECTORS']) && $t_global_styles[$t_row_content['attribute']] == $t_row_content['value'])
						{
						}
						else
						{
							$t_value = $t_row_content['value'];

							if($this->v_current_template == 'gambio')
							{
								/* get backgroundcolor for footer */
								if($t_style['name'] == '.wrap_site' && $t_row_content['attribute'] == 'background-color')
								{
									$t_css_background_color = $t_value;
									$t_value = 'transparent';

									$t_css_background_colors_array = array();
									if(strlen($t_css_background_color) == 7)
									{
										$t_css_background_colors_array['r'] = hexdec(substr($t_css_background_color, 1, 2));
										$t_css_background_colors_array['g'] = hexdec(substr($t_css_background_color, 3, 2));
										$t_css_background_colors_array['b'] = hexdec(substr($t_css_background_color, 5, 2));

										// YUV brightness
										$t_css_background_brightness = $t_css_background_colors_array['r'] * 0.299 + $t_css_background_colors_array['g'] * 0.587 + $t_css_background_colors_array['b'] * 0.114;
									}
								}

								/* get background-image for footer */
								if($t_style['name'] == '.wrap_site' && $t_row_content['attribute'] == 'background-image')
								{
									$t_css_background_image = $t_value;
									$t_value = 'none';
								}

								/* get background-repeat for footer */
								if($t_style['name'] == '.wrap_site' && $t_row_content['attribute'] == 'background-repeat')
								{
									$t_css_background_repeat = $t_value;
								}

								/* get font color for footer */
								if($t_style['name'] == '#column_content' && $t_row_content['attribute'] == 'color')
								{
									$t_css_color = $t_value;

									$t_css_colors_array = array();
									if(strlen($t_css_color) == 7)
									{
										$t_css_colors_array['r'] = hexdec(substr($t_css_color, 1, 2));
										$t_css_colors_array['g'] = hexdec(substr($t_css_color, 3, 2));
										$t_css_colors_array['b'] = hexdec(substr($t_css_color, 5, 2));

										// YUV brightness
										$t_css_brightness = $t_css_colors_array['r'] * 0.299 + $t_css_colors_array['g'] * 0.587 + $t_css_colors_array['b'] * 0.114;
									}
								}
							}
							else
							{
								if($this->get_conf('SHOW_FOOTER') == 'true')
								{
									/* get backgroundcolor for footer */
									if($t_style['name'] == '#footer' && $t_row_content['attribute'] == 'background-color')
									{
										$t_css_background_color = $t_value;

										$t_css_background_colors_array = array();
										if(strlen($t_css_background_color) == 7)
										{
											$t_css_background_colors_array['r'] = hexdec(substr($t_css_background_color, 1, 2));
											$t_css_background_colors_array['g'] = hexdec(substr($t_css_background_color, 3, 2));
											$t_css_background_colors_array['b'] = hexdec(substr($t_css_background_color, 5, 2));

											// YUV brightness
											$t_css_background_brightness = $t_css_background_colors_array['r'] * 0.299 + $t_css_background_colors_array['g'] * 0.587 + $t_css_background_colors_array['b'] * 0.114;
										}
									}

									/* get font color for footer */
									if($t_style['name'] == '#footer' && $t_row_content['attribute'] == 'color')
									{
										$t_css_color = $t_value;

										$t_css_colors_array = array();
										if(strlen($t_css_color) == 7)
										{
											$t_css_colors_array['r'] = hexdec(substr($t_css_color, 1, 2));
											$t_css_colors_array['g'] = hexdec(substr($t_css_color, 3, 2));
											$t_css_colors_array['b'] = hexdec(substr($t_css_color, 5, 2));

											// YUV brightness
											$t_css_brightness = $t_css_colors_array['r'] * 0.299 + $t_css_colors_array['g'] * 0.587 + $t_css_colors_array['b'] * 0.114;
										}
									}
								}
								else
								{
									/* get backgroundcolor for footer */
									if($t_style['name'] == '#container_inner' && $t_row_content['attribute'] == 'background-color')
									{
										$t_css_background_color = $t_value;

										$t_css_background_colors_array = array();
										if(strlen($t_css_background_color) == 7)
										{
											$t_css_background_colors_array['r'] = hexdec(substr($t_css_background_color, 1, 2));
											$t_css_background_colors_array['g'] = hexdec(substr($t_css_background_color, 3, 2));
											$t_css_background_colors_array['b'] = hexdec(substr($t_css_background_color, 5, 2));

											// YUV brightness
											$t_css_background_brightness = $t_css_background_colors_array['r'] * 0.299 + $t_css_background_colors_array['g'] * 0.587 + $t_css_background_colors_array['b'] * 0.114;
										}
									}

									/* get font color for footer */
									if($t_style['name'] == '#container_inner' && $t_row_content['attribute'] == 'color')
									{
										$t_css_color = $t_value;

										$t_css_colors_array = array();
										if(strlen($t_css_color) == 7)
										{
											$t_css_colors_array['r'] = hexdec(substr($t_css_color, 1, 2));
											$t_css_colors_array['g'] = hexdec(substr($t_css_color, 3, 2));
											$t_css_colors_array['b'] = hexdec(substr($t_css_color, 5, 2));

											// YUV brightness
											$t_css_brightness = $t_css_colors_array['r'] * 0.299 + $t_css_colors_array['g'] * 0.587 + $t_css_colors_array['b'] * 0.114;
										}
									}
								}
							}

							/* compress colors */
							if(strstr($t_row_content['attribute'], "color"))
							{
								$t_value = $this->v_css_tidy->optimise->cut_color($t_value);
							}
							
							if($t_style['name'] == ".wrap_shop" && stripos($t_row_content['attribute'], "background") !== false)
							{
								$t_body_styles[] = "\n\t" . $t_row_content['attribute'] . ": " . $t_value . ";";
							}
							else
							{
								$t_style_content[$t_style['name']][$t_row_content['attribute']] = $t_value;
							}

						}
					}
				}
			}
			
			if(is_array($t_style_content))
			{
				foreach($t_style_content as $t_style_name => $t_styles)
				{
					$t_css .= $t_style_name . "{";
					foreach($t_styles as $attribute => $value)
					{
						$t_css .= $attribute . ":" . $value . ";";
					}
					$t_css .= "}";
				}
			}

			if(isset($t_css_background_brightness) && isset($t_css_brightness) && isset($t_css_background_colors_array))
			{
				define('GM_COLOR_DIFFERENCE', 80);
				if(max($t_css_background_brightness, $t_css_brightness) - min($t_css_background_brightness, $t_css_brightness) < GM_COLOR_DIFFERENCE)
				{
					if($t_css_background_brightness > 127)
					{
						$t_css_background_colors_array['r'] -= GM_COLOR_DIFFERENCE;
						$t_css_background_colors_array['g'] -= GM_COLOR_DIFFERENCE;
						$t_css_background_colors_array['b'] -= GM_COLOR_DIFFERENCE;

						if($t_css_background_colors_array['r'] < 0)
						{
							$t_css_background_colors_array['r'] = 0;
						}
						if($t_css_background_colors_array['g'] < 0)
						{
							$t_css_background_colors_array['g'] = 0;
						}
						if($t_css_background_colors_array['b'] < 0)
						{
							$t_css_background_colors_array['b'] = 0;
						}

						$t_css_background_colors_array['r'] = dechex(round($t_css_background_colors_array['r']));
						$t_css_background_colors_array['g'] = dechex(round($t_css_background_colors_array['g']));
						$t_css_background_colors_array['b'] = dechex(round($t_css_background_colors_array['b']));

						if(strlen($t_css_background_colors_array['r']) == 1)
						{
							$t_css_background_colors_array['r'] .= 0;
						}
						if(strlen($t_css_background_colors_array['g']) == 1)
						{
							$t_css_background_colors_array['g'] .= 0;
						}
						if(strlen($t_css_background_colors_array['b']) == 1)
						{
							$t_css_background_colors_array['b'] .= 0;
						}

						$t_css_color = '#' . $t_css_background_colors_array['r'] . $t_css_background_colors_array['g'] . $t_css_background_colors_array['b'];
					}
					else
					{
						$t_css_background_colors_array['r'] += GM_COLOR_DIFFERENCE;
						$t_css_background_colors_array['g'] += GM_COLOR_DIFFERENCE;
						$t_css_background_colors_array['b'] += GM_COLOR_DIFFERENCE;

						if($t_css_background_colors_array['r'] > 255)
						{
							$t_css_background_colors_array['r'] = 255;
						}
						if($t_css_background_colors_array['g'] > 255)
						{
							$t_css_background_colors_array['g'] = 255;
						}
						if($t_css_background_colors_array['b'] > 255)
						{
							$t_css_background_colors_array['b'] = 255;
						}

						$t_css_background_colors_array['r'] = dechex(round($t_css_background_colors_array['r']));
						$t_css_background_colors_array['g'] = dechex(round($t_css_background_colors_array['g']));
						$t_css_background_colors_array['b'] = dechex(round($t_css_background_colors_array['b']));

						if(strlen($t_css_background_colors_array['r']) == 1)
						{
							$t_css_background_colors_array['r'] .= 0;
						}
						if(strlen($t_css_background_colors_array['g']) == 1)
						{
							$t_css_background_colors_array['g'] .= 0;
						}
						if(strlen($t_css_background_colors_array['b']) == 1)
						{
							$t_css_background_colors_array['b'] .= 0;
						}

						$t_css_color = '#' . $t_css_background_colors_array['r'] . $t_css_background_colors_array['g'] . $t_css_background_colors_array['b'];
					}
				}
			}

			if($this->v_current_template == 'gambio')
			{
				if($t_css_background_image != 'none' &&
				$t_css_background_image != 'url()')
				{
					/* BODY */
					$t_css .= "body{";
					$t_css .= "background-color:" . 	$t_css_background_color . ";";
					$t_css .= "background-image:" . 	$t_css_background_image . ";";
					$t_css .= "background-repeat:" . $t_css_background_repeat . ";";
					$t_css .= "}";

					/* HTML */
					$t_css .= "html .copyright,html .copyright a{";
					$t_css .= "background-color:transparent;";
					$t_css .= "color:"				. $t_css_color				. ";";
					$t_css .= "}";
				}
				else
				{
					/* BODY */
					$t_css .= "body{";
					$t_css .= "background-color:" . $t_css_background_color . ";";
					$t_css .= "}";


					/* HTML */
					$t_css .= "html .copyright,html .copyright a{";
					$t_css .= "background-color:"	. $t_css_background_color	. ";";
					$t_css .= "color:"				. $t_css_color				. ";";
					$t_css .= "}";
				}

				$t_get_underline_setting = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT gm_value
																		FROM gm_configuration
																		WHERE
																			gm_key = 'GM_UNDERLINE_LINKS'
																			AND gm_value = 'true'");
				if(mysqli_num_rows($t_get_underline_setting) > 0)
				{
					$t_css .= 'a,.product_info_link{text-decoration:none !important}a:hover,.product_info_link:hover{text-decoration:underline !important}';
				}
			}
			else
			{
				if(empty($t_body_styles) == false)
				{
					$t_css .= "body { " . implode("", $t_body_styles) . " }";
				}
				
				if($this->get_conf('SHOW_FOOTER') == 'true')
				{
					$t_css .= "body #footer_copyright,body #footer_copyright a{";
					$t_css .= "color:" . $t_css_color . ";";
					$t_css .= "}";
				}
				else
				{
					$t_css .= "body #shopsoftware_by_gambio p,body #shopsoftware_by_gambio a{";
					$t_css .= "color:" . $t_css_color . ";";
					$t_css .= "}";
				}
			}
			
			

			$this->v_css_tidy->parse($t_css);

			$this->v_css = $this->v_css_tidy->print->plain();

			unset($this->v_css_tidy);

			return;
		}
		
		/**
		*	check if style can be combined
		*	@param array	$p_global_styles
		*	@param int		$p_id
		*	@param string	$p_attribute
		*	@param string	$p_value
		*	@return boolean
		*/
		function is_compatible($p_global_styles, $p_id, $p_attribute, $p_value)
		{

			if(!in_array($p_id, $p_global_styles['SELECTORS']) && $p_global_styles[$p_attribute] != $p_value)
			{
				return false;
			}
			else
			{
				return true;
			}
		}

		/**
		*	@param	array $p_styles
		*	@return array
		*/
		function combine_selectors($p_styles)
		{
			$t_styles		= $p_styles;
			$t_selectors	= array();

			$i = 0;
			foreach($t_styles as $t_key => $t_value)
			{
				if($i > 0)
				{
					$t_sql .= " AND ";
				}
				$t_sql .= "
							gcs.gm_css_style_id
						IN
						(
							SELECT
								gcsc.gm_css_style_id
							FROM
								gm_css_style_content gcsc
							WHERE
								gcsc.style_attribute	= '" . $t_key	. "'
							AND
								gcsc.style_value		= '" . $t_value	. "'
						)";
				$i++;
			}


			$t_result = mysqli_query($GLOBALS["___mysqli_ston"], "
									SELECT
										gcs.style_name		AS name,
										gcs.gm_css_style_id AS id
									FROM
										gm_css_style gcs
									WHERE
										gcs.template_name = '" . addslashes(trim($this->v_current_template)) . "' AND
										" .	$t_sql . "
			");

			if(mysqli_num_rows($t_result) > 0)
			{
				while($t_row = mysqli_fetch_array($t_result))
				{
					if(strlen($t_row['name']) <100)
					{
						$t_selectors['SELECTORS'][$t_row['name']] = $t_row['id'];
					}
				}

				return $t_selectors['SELECTORS'];
			}
			else
			{
				return false;
			}
		}

		/**
		*	search for global font styles defined in array $v_global_styles
		*	@return array
		*/
		function global_font_style()
		{
			$t_global_styles = array();
			foreach($this->v_global_styles as $t_key => $t_value)
			{
				$t_result = mysqli_query($GLOBALS["___mysqli_ston"], 
										"SELECT
											style_value AS value,
											count(*) AS count
										FROM
											gm_css_style_content
										WHERE
											style_attribute = '" . $t_value . "'
										GROUP BY
											style_attribute,
											style_value
										ORDER BY
											count DESC
										LIMIT 1
				");

				if(mysqli_num_rows($t_result) == 1)
				{
					$t_row = mysqli_fetch_array($t_result);

					$t_global_styles[$t_value] = $t_row['value'];
				}
			}
			return $t_global_styles;
		}

		/**
		*	search for menuboxes box_status = 1
		*	@return array
		*/
		function inactive_menuboxes()
		{
			$t_inactive_menuboxes = array();

			if($this->style_edit || is_dir(DIR_FS_CATALOG . 'StyleEdit/') === false)
			{
				return $t_inactive_menuboxes;
			}

			$t_result = mysqli_query($GLOBALS["___mysqli_ston"], 
									"SELECT
										box_name
									AS
										name
									FROM
										gm_boxes
									WHERE
										box_status = '0' AND
										box_name != 'admin' AND
										template_name = '" . addslashes(trim($this->v_current_template)) . "'
									ORDER BY
										box_name
									ASC
			");

			if(mysqli_num_rows($t_result) > 0)
			{
				while($t_row = mysqli_fetch_array($t_result))
				{
					if($t_row['name'] == 'bestsellers')
					{
						$t_row['name'] = 'best_sellers';
					}

					if($t_row['name'] == 'add_quickie')
					{
						$t_row['name'] = 'add_a_quickie';
					}

					$t_inactive_menuboxes[] = $t_row['name'];
				}
			}

			return $t_inactive_menuboxes;
		}

		/**
		*	check if style selector is part of a de/activated menubox
		*	@param	string $p_selector
		*	@return array
		*/
		function inactive_selector($p_selector)
		{
			/* search for inactive menuboxes */
			$t_inactive_menuboxes = $this->inactive_menuboxes();
			if(strstr($p_selector, 'menubox'))
			{
				$t_selector_array = explode(' ', $p_selector);
				if(!empty($t_selector_array))
				{
					$t_key = array_search('menubox', $t_selector_array);
					$p_selector = $t_selector_array[$t_key];
				}

				foreach($t_inactive_menuboxes as $t_key => $t_value)
				{
					if(strstr($p_selector, $t_value))
					{
						if( ($t_value == 'manufacturers' && strpos($p_selector, 'manufacturers_info') !== false)
							|| ($t_value == 'trusted' && strpos($p_selector, 'gm_trusted_shops_widget') !== false) )
						{
							continue;
						}
						else
						{
							return true;
						}
					}
				}
			}
			return false;
		}


		/**
		*	check if selector is spam
		*	@return boolean
		*/

		function is_spam($p_selector)

		{

			foreach($this->v_spam as $t_key => $t_value)
			{
				if(strstr($p_selector, $t_value))
				{
					return true;
				}
			}
			return false;
		}

		/**
		*	save css code to file
		*/
		function save_css()
		{
			$t_fopen = fopen($this->v_cache_file, 'w+');
			fwrite($t_fopen, $this->v_css);
			fclose($t_fopen);
			return;
		}

		/**
		*	get method
		*	@return string
		*/
		function get_css()
		{
			return $this->v_css;
		}

		/**
		*	get method
		*	@return string
		*/
		function get_conf($p_key)
		{
			$c_key = ((isset($GLOBALS["___mysqli_ston"]) && is_object($GLOBALS["___mysqli_ston"])) ? mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $p_key) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""));
			$t_value = '';

			$t_query = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT gm_value
									FROM gm_configuration
									WHERE
										gm_key = '" . $c_key . "'
										LIMIT 1");
			if(mysqli_num_rows($t_query) == 1)
			{
				$t_result_array = mysqli_fetch_array($t_query);
				$t_value = $t_result_array['gm_value'];
			}

			return $t_value;
		}
	}