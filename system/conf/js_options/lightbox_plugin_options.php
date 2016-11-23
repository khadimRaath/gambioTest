<?php
/*
#   --------------------------------------------------------------
#   slider_plugin_options.js 2014-10-05 tb@gambio
#   Gambio GmbH
#   http://www.gambio.de
#   Copyright (c) 2014 Gambio GmbH
#   Released under the GNU General Public License (Version 2)
#   [http://www.gnu.org/licenses/gpl-2.0.html]
#   --------------------------------------------------------------
*/
?><?php

	// initialize class
	$coo_text_manager = MainFactory::create_object('LanguageTextManager', array(false, $_SESSION['languages_id']));
	
	// get messages
	$t_section_array = $coo_text_manager->get_section_array('lightbox_buttons');

    $array["lightbox_plugin"] = array();
    
    $array["lightbox_plugin"]['font_family'] = "Arial,sans-serif"; // [String]
    $array["lightbox_plugin"]['font_size'] = "11px"; // [Integer]px
    $array["lightbox_plugin"]['content_color'] = "#333"; // [Integer]px
    
    $array["lightbox_plugin"]['width'] = "960"; // full || [Integer]
    $array["lightbox_plugin"]['height'] = "auto"; // full || auto || [Integer]
    $array["lightbox_plugin"]['margin_outside'] = 40; // full || auto || [Integer]
    
    $array["lightbox_plugin"]['image_loading_active'] = true; //true || false
    $array["lightbox_plugin"]['image_loading_source'] = "html/assets/images/legacy/ajax-loader.gif";
    $array["lightbox_plugin"]['image_loading_width'] = 31; // [Integer]
    $array["lightbox_plugin"]['image_loading_height'] = 31; // [Integer]
    
    $array["lightbox_plugin"]['headline_position'] = 'top'; // top || bottom || none
    $array["lightbox_plugin"]['navigation_position'] = "top"; // top || bottom || inside || none
    $array["lightbox_plugin"]['close_button_position'] = "none"; // top || bottom || none
    
    $array["lightbox_plugin"]['headline_font_size'] = 13; // [Integer]
    $array["lightbox_plugin"]['headline_color'] = '#333'; // font-color
    $array["lightbox_plugin"]['headline_font_weight'] = 'bold'; // [Integer] || bold || normal
    $array["lightbox_plugin"]['headline_text_decoration'] = 'none'; // none || italic || underline
    $array["lightbox_plugin"]['headline_text_transform'] = 'none'; // none || uppercase || capitalize || lowercase || inherit
    
    $array["lightbox_plugin"]['border_round'] = true; // true || false
    
    $array["lightbox_plugin"]['close_button_width'] = 25; // [Integer]
    $array["lightbox_plugin"]['close_button_height'] = 15; // [Integer]
    $array["lightbox_plugin"]['close_button_image'] = "html/assets/images/legacy/lightbox_icons/lightbox_button_close.png"; // icon_close.gif
    //$array["lightbox_plugin"]['close_button_src_mouseover'] = "html/assets/images/legacy/lightbox_icons/button_close_mouseover.png"; // icon_close.gif
    $array["lightbox_plugin"]['close_button_title'] = $t_section_array['close']; // Schließen
    
    $array["lightbox_plugin"]['background_color'] = "#EFEFEF"; // colors
    
    $array["lightbox_plugin"]['shadow_active'] = true; // true || false
    $array["lightbox_plugin"]['shadow_opacity'] = 85; // 0 to 100
    $array["lightbox_plugin"]['shadow_background_color'] = "#000"; // colors
    $array["lightbox_plugin"]['shadow_close_on_click'] = false; // colors
    
    $array["lightbox_plugin"]['open_close_animate'] = true; // true || false // works not in IE8
    $array["lightbox_plugin"]['open_close_animate_time'] = 500;
    
    $array["lightbox_plugin"]['navigation_button_prev_title'] = $t_section_array['previous'];
    $array["lightbox_plugin"]['navigation_button_prev_image'] = 'html/assets/images/legacy/lightbox_icons/lightbox_navigation_button_prev.png';
    //$array["lightbox_plugin"]['navigation_arrow_prev_image_disabled'] = "html/assets/images/legacy/lightbox_icons/navi_prev_disabled.png";
    //$array["lightbox_plugin"]['navigation_arrow_prev_image_mouseover'] = "html/assets/images/legacy/lightbox_icons/navi_prev_mouseover.png";
	$array["lightbox_plugin"]['navigation_button_prev_width'] = 23; // [Integer]
    $array["lightbox_plugin"]['navigation_button_prev_height'] = 23; // [Integer]
    
    $array["lightbox_plugin"]['navigation_button_next_title'] = $t_section_array['next'];
    $array["lightbox_plugin"]['navigation_button_next_image'] = 'html/assets/images/legacy/lightbox_icons/lightbox_navigation_button_next.png';
    //$array["lightbox_plugin"]['navigation_arrow_next_image_disabled'] = "html/assets/images/legacy/lightbox_icons/navi_next_disabled.png";
    //$array["lightbox_plugin"]['navigation_arrow_next_image_mouseover'] = "html/assets/images/legacy/lightbox_icons/navi_next_mouseover.png";
	$array["lightbox_plugin"]['navigation_button_next_width'] = 23; // [Integer]
    $array["lightbox_plugin"]['navigation_button_next_height'] = 23; // [Integer]
    
    // content_error
    $array["lightbox_plugin"]['content_error_font_color'] = '#d00'; // font-color
    $array["lightbox_plugin"]['content_error_font_size'] = 14; // [Integer]
    $array["lightbox_plugin"]['content_error_font_weight'] = '900'; // [Integer] || bold || normal
    $array["lightbox_plugin"]['content_error_font_text_align'] = 'right'; // left, right, center
    
?>