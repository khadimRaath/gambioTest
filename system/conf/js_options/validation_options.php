<?php
/*
#   --------------------------------------------------------------
#   validation_options.js 2013-04-24 tb@gambio
#   Gambio GmbH
#   http://www.gambio.de
#   Copyright (c) 2013 Gambio GmbH
#   Released under the GNU General Public License (Version 2)
#   [http://www.gnu.org/licenses/gpl-2.0.html]
#   --------------------------------------------------------------
*/
?><?php

    $array['validation'] = array();
	
	$array['validation']['minlength_1'] = array();
	$array['validation']['minlength_1']['pattern'] = '\S{1,}';
	
	$array['validation']['length_1'] = array();
	$array['validation']['length_1']['pattern'] = '^.{1}$';
	
	$array['validation']['minlength_3'] = array();
	$array['validation']['minlength_3']['pattern'] = '^.{3,}$';
	
	$array['validation']['float'] = array();
	$array['validation']['float']['pattern'] = '^\d+(\.?\d+)?$';
	
	
	/* +++++++++++++++   CSV Validations   +++++++++++++++ */
	
	$array['validation']['csv-import_file_extension'] = array();
	$array['validation']['csv-import_file_extension']['pattern'] = '^.+\.txt|\.csv|\.zip$';
	$array['validation']['csv-import_file_extension']['modifier'] = 'i';
	
	$array['validation']['csv-scheme_name'] = array();
	$array['validation']['csv-scheme_name']['pattern'] = '[^a-zA-Z 0-9\._\(\)-]';
	
	$array['validation']['csv-scheme_filename'] = array();
	$array['validation']['csv-scheme_filename']['pattern'] = '[^a-zA-Z0-9\._\(\)-]';
