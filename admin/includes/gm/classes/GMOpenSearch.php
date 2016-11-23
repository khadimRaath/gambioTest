<?php
/* --------------------------------------------------------------
	GMOpenSearch.php 2014-06-21 gm
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]

   IMPORTANT! THIS FILE IS DEPRECATED AND WILL BE REPLACED IN THE FUTURE. 
   MODIFY IT ONLY FOR FIXES. DO NOT APPEND IT WITH NEW FEATURES, USE THE
   NEW GX-ENGINE LIBRARIES INSTEAD.
   --------------------------------------------------------------

	// add on put this to your header.php
	// bof gm opensearch
	if(gm_get_conf('GM_OPENSEARCH') == 1) { 
	require_once(DIR_FS_CATALOG . 'gm/classes/GMLogoManager.php');
	require_once(DIR_FS_CATALOG	. 'admin/includes/gm/classes/GMOpenSearch.php');
	$opensearch = new GMOpenSearch();
	$opensearch->create(true);	
	echo '<link rel="search" type="application/opensearchdescription+xml" href="' . HTTP_SERVER . DIR_WS_CATALOG . $_SESSION['languages_id'] . '_opensearch.xml' . '" title="' . gm_get_content('GM_OPENSEARCH_TITLE', $_SESSION['languages_id']) . '" />';
	} // eof gm opensearch
*/
?><?php

	class GMOpenSearch_ORIGIN {
		
		/*
		*	-> !shortname, must contain 16 or fewer characters of plain text
		*/
		var $shortname;

		/*
		*	-> longname, must contain 48 or fewer characters of plain text
		*/
		var $longname;
		
		/*
		*	-> !tags, must contain 256 or fewer characters of plain text
		*/
		var $tags;
		
		/*
		*	-> !description, must contain 1024 or fewer characters of plain text.
		*/
		var $description;

		/*
		*	-> contact, an valid e-mail-adress
		*/
		var $contact;

		
		/*
		*	-> xmlns spec of the root node of the OpenSearch description document
		*/
		var $xmlns				= 'http://a9.com/-/spec/opensearch/1.1/';
		
		/*
		*	-> kind of xml encoding - e.g. 'UTF-8', 'ISO-8859-1', etc.
		*/
		var $xml_encoding		= 'UTF-8';

		/*
		*	-> kind of the xml version
		*/
		var $xml_version		= '1.0';				

		/*
		*	-> references the current protocol standard
		*/
		var $path				= DIR_FS_CATALOG;
		
		/*
		*	-> references the current protocol standard
		*/
		var $filename			= 'opensearch';

		/*
		*	-> search url
		*/ 
		var $search_url;
		
		/*
		*	-> search url
		*/ 
		var $image;
		
		/*
		*	-> constructor
		*/
		function __construct() {
			
			$this->search_url = HTTP_SERVER . DIR_WS_CATALOG . "advanced_search_result.php?keywords={searchTerms}"; 
			$this->image = MainFactory::create_object('GMLogoManager', array("gm_logo_favicon"));
			return;
		}

		/*
		*	-> create cml doc
		*/
		function create($output=false) {
			
			$_xml  = '<?xml version="' . $this->xml_version . '" encoding="' . $this->xml_encoding . '"?>'	. "\n\t";

			$_xml .= '<OpenSearchDescription xmlns="' . $this->xmlns . '">'									. "\n\t\t";						
			
			$_xml .= '{#CONTENT#}';

			$_xml .= "\n\t" . '</OpenSearchDescription>';

			if($output) {
				$this->output($_xml);
			} else {
				return $_xml;
			}			
		}


		/*
		*	-> get content
		*/
		function get_content($lang_id) {

			$_xml  .= '<ShortName>'		. gm_get_content('GM_OPENSEARCH_SHORTNAME',		$lang_id)	. '</ShortName>'	. "\n\t\t";
			$_xml  .= '<LongName>'		. gm_get_content('GM_OPENSEARCH_LONGNAME',		$lang_id)	. '</LongName>'		. "\n\t\t";
			$_xml  .= '<Description>'	. gm_get_content('GM_OPENSEARCH_DESCRIPTION',	$lang_id)	. '</Description>'	. "\n\t\t";
			$_xml  .= '<Tags>'			. gm_get_content('GM_OPENSEARCH_TAGS',			$lang_id)	. '</Tags>'			. "\n\t\t";
			$_xml  .= '<Contact>'		. gm_get_content('GM_OPENSEARCH_CONTACT',		$lang_id)	. '</Contact>'		. "\n\t\t";

			if($this->image->logo_exist()) {
				$_xml  .= '<Image type="image/x-icon">' .$this->image->logo_path . $this->image->logo_file.'</Image>'	. "\n\t\t";
			}

			$_xml  .= '<Url type="text/html" template="'	. $this->search_url . '"/>'; 

			return $_xml;
		}


		/*
		*	-> create output
		*/
		function output($content) {
			$gm_lang = gm_get_language();

			foreach($gm_lang as $value) {			
				
				$_xml = str_replace('{#CONTENT#}', $this->get_content($value['languages_id']), $content);
				if(is_readable($this->path . 'export/' . $this->filename . '_' . $value['languages_id'] . '.xml'))
				{
					$fp = fopen($this->path . 'export/' . $this->filename . '_' . $value['languages_id'] . '.xml', 'w');
					fwrite($fp, $_xml);
					fclose($fp);
				}
			}
				
			return;
		}	
	}

MainFactory::load_origin_class('GMOpenSearch');
