<?php
/*
#   --------------------------------------------------------------
#   slider_plugin_options.js 2014-10-11 tb@gambio
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
	$array['error_handling'] = array();
	$array['error_handling'] = $coo_text_manager->get_section_array('messages');
    
	/* +++++++++++++++   CSV Errors   +++++++++++++++ */
	
	$array['error_handling']['csv'] = array();
	$array['error_handling']['csv'] = $coo_text_manager->get_section_array('messages_csv');
	
	/* +++++++++++++++   Filter Errors   +++++++++++++++ */
	
	$array['error_handling']['filter'] = array();
	$array['error_handling']['filter'] = $coo_text_manager->get_section_array('messages_filter');

	/* +++++++++++++++   FormEditor Errors   +++++++++++++++ */
	
	$array['error_handling']['form_section_no_valid_section_id'] = 'Fehler: Keine gültige Section-ID übergeben.';