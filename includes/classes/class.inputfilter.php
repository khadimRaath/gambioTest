<?php
/* --------------------------------------------------------------
   class.inputfilter.php 2016-03-07
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio Gambio
   Released under the GNU General Public License
   --------------------------------------------------------------
*/

/**
 *  @class: InputFilter (PHP4 & PHP5, with comments)
 * @project: PHP Input Filter
 * @date: 10-05-2005
 * @version: 1.2.2_php4/php5
 * @author: Daniel Morris
 * @contributors: Gianpaolo Racca, Ghislain Picard, Marco Wandschneider, Chris
 * Tobin and Andrew Eddie.
 * 
 * Modification by Louis Landry
 * 
 * @copyright: Daniel Morris
 * @email: dan@rootcube.com
 * @license: GNU General Public License (GPL)
 */
class InputFilter_ORIGIN {
	var $tagsArray; // default = empty array
	var $attrArray; // default = empty array

	var $tagsMethod; // default = 0
	var $attrMethod; // default = 0

	var $xssAuto; // default = 1
	var $tagBlacklist = array ('applet', 'body', 'bgsound', 'base', 'basefont', 'embed', 'frame', 'frameset', 'head', 'html', 'id', 'iframe', 'ilayer', 'layer', 'link', 'meta', 'name', 'object', 'script', 'style', 'title', 'xml');
	var $attrBlacklist = array ('action', 'background', 'codebase', 'dynsrc', 'lowsrc'); // also will strip ALL event handlers

	var $keyWhitelist = array('gambio_api_xml');
	
	/** 
	  * Constructor for inputFilter class. Only first parameter is required.
	  * @access constructor
	  * @param Array $tagsArray - list of user-defined tags
	  * @param Array $attrArray - list of user-defined attributes
	  * @param int $tagsMethod - 0= allow just user-defined, 1= allow all but user-defined
	  * @param int $attrMethod - 0= allow just user-defined, 1= allow all but user-defined
	  * @param int $xssAuto - 0= only auto clean essentials, 1= allow clean blacklisted tags/attr
	  */
	public function __construct($tagsArray = array (), $attrArray = array (), $tagsMethod = 0, $attrMethod = 0, $xssAuto = 1) {
			// make sure user defined arrays are in lowercase
	for ($i = 0; $i < count($tagsArray); $i ++)
			$tagsArray[$i] = strtolower_wrapper($tagsArray[$i]);
		for ($i = 0; $i < count($attrArray); $i ++)
			$attrArray[$i] = strtolower_wrapper($attrArray[$i]);
		// assign to member vars
		$this->tagsArray = (array) $tagsArray;
		$this->attrArray = (array) $attrArray;
		$this->tagsMethod = $tagsMethod;
		$this->attrMethod = $attrMethod;
		$this->xssAuto = $xssAuto;
	}

	/** 
	  * Method to be called by another php script. Processes for XSS and specified bad code.
	  * @access public
	  * @param Mixed $source - input string/array-of-string to be 'cleaned'
	  * @return String $source - 'cleaned' version of input parameter
	  */
	function process($source, $get=false, $t_exclude_array = array()) {
        // clean all elements in this array 
        if (is_array($source)) { 
            foreach ($source as $key => $value) { 
				//GM_MOD BOF
				if(in_array($key, $t_exclude_array))
				{
					continue;
				}
				
				if (SEARCH_ENGINE_FRIENDLY_URLS == 'true' && $get == true) {
					if((bool) get_magic_quotes_gpc()) $key = addslashes($key);
				}
				//GM_MOD EOF
                // filter element for XSS and other 'bad' code etc. 
                $tmp_key = $key; 
                unset ($source[$key]); 
                $key = $this->remove($this->decode($key)); 
                if ($key == $tmp_key) {
					if (in_array($key, $this->keyWhitelist)) {
						$source[$key] = $this->decode($value);
					} elseif (is_string($value)) {
						//GM_MOD BOF
						
						if (SEARCH_ENGINE_FRIENDLY_URLS == 'true' && $get == true) {
							if((bool) get_magic_quotes_gpc()) $value = htmlspecialchars_wrapper(addslashes($value));
						}
						//GM_MOD EOF
                        $source[$key] = $this->remove($this->decode($value)); 
                    } elseif (is_array($value)) { 
                        $source[$key] = $this->process($value, $get);
                    } 
                } 
            } 
            return $source; 
            // clean this string 
        } else 
        if (is_string($source)) { 
            // filter source for XSS and other 'bad' code etc. 
            return $this->remove($this->decode($source)); 
            // return parameter as given 
        } else 
        return $source; 
    }  

	/** 
	  * Internal method to iteratively remove all unwanted tags and attributes
	  * @access protected
	  * @param String $source - input string to be 'cleaned'
	  * @return String $source - 'cleaned' version of input parameter
	  */
	function remove($source) {
		$loopCounter = 0;
		// provides nested-tag protection
		while ($source != $this->filterTags($source)) {
			$source = $this->filterTags($source);
			$loopCounter ++;
		}
		return $source;
	}

	/** 
	  * Internal method to strip a string of certain tags
	  * @access protected
	  * @param String $source - input string to be 'cleaned'
	  * @return String $source - 'cleaned' version of input parameter
	  */
	function filterTags($source) {
		// filter pass setup
		$preTag = NULL;
		$source = str_replace('<>','',$source);
		$postTag = $source;
		// find initial tag's position
		$tagOpen_start = strpos_wrapper($source, '<');
		// interate through string until no tags left
		while ($tagOpen_start !== FALSE) {
			// process tag interatively
			$preTag .= substr_wrapper($postTag, 0, $tagOpen_start);
			$postTag = substr_wrapper($postTag, $tagOpen_start);
			$fromTagOpen = substr_wrapper($postTag, 1);
			// end of tag
			$tagOpen_end = strpos_wrapper($fromTagOpen, '>');
			if ($tagOpen_end === false)
				break;
			// next start of tag (for nested tag assessment)
			$tagOpen_nested = strpos_wrapper($fromTagOpen, '<');
			if (($tagOpen_nested !== false) && ($tagOpen_nested < $tagOpen_end)) {
				$preTag .= substr_wrapper($postTag, 0, ($tagOpen_nested +1));
				$postTag = substr_wrapper($postTag, ($tagOpen_nested +1));
				$tagOpen_start = strpos_wrapper($postTag, '<');
				continue;
			}
			$tagOpen_nested = (strpos_wrapper($fromTagOpen, '<') + $tagOpen_start +1);
			$currentTag = substr_wrapper($fromTagOpen, 0, $tagOpen_end);
			$tagLength = strlen_wrapper($currentTag);
			if (!$tagOpen_end) {
				$preTag .= $postTag;
				$tagOpen_start = strpos_wrapper($postTag, '<');
			}
			// iterate through tag finding attribute pairs - setup
			$tagLeft = $currentTag;
			$attrSet = array ();
			$currentSpace = strpos_wrapper($tagLeft, ' ');
			// is end tag
			if (substr_wrapper($currentTag, 0, 1) == "/") {
				$isCloseTag = TRUE;
				list ($tagName) = explode(' ', $currentTag);
				$tagName = substr_wrapper($tagName, 1);
				// is start tag
			} else {
				$isCloseTag = FALSE;
				list ($tagName) = explode(' ', $currentTag);
			}
			// excludes all "non-regular" tagnames OR no tagname OR remove if xssauto is on and tag is blacklisted
			if ((!preg_match("/^[a-z][a-z0-9]*$/i", $tagName)) || (!$tagName) || ((in_array(strtolower_wrapper($tagName), $this->tagBlacklist)) && ($this->xssAuto))) {
				$postTag = substr_wrapper($postTag, ($tagLength +2));
				$tagOpen_start = strpos_wrapper($postTag, '<');
				// don't append this tag
				continue;
			}
			// this while is needed to support attribute values with spaces in!
			while ($currentSpace !== FALSE) {
				$fromSpace = substr_wrapper($tagLeft, ($currentSpace +1));
				$nextSpace = strpos_wrapper($fromSpace, ' ');
				$openQuotes = strpos_wrapper($fromSpace, '"');
				$closeQuotes = strpos_wrapper(substr_wrapper($fromSpace, ($openQuotes +1)), '"') + $openQuotes +1;
				// another equals exists
				if (strpos_wrapper($fromSpace, '=') !== FALSE) {
					// opening and closing quotes exists
					if (($openQuotes !== FALSE) && (strpos_wrapper(substr_wrapper($fromSpace, ($openQuotes +1)), '"') !== FALSE))
						$attr = substr_wrapper($fromSpace, 0, ($closeQuotes +1));
					// one or neither exist
					else
						$attr = substr_wrapper($fromSpace, 0, $nextSpace);
					// no more equals exist
				} else
					$attr = substr_wrapper($fromSpace, 0, $nextSpace);
				// last attr pair
				if (!$attr)
					$attr = $fromSpace;
				// add to attribute pairs array
				$attrSet[] = $attr;
				// next inc
				$tagLeft = substr_wrapper($fromSpace, strlen_wrapper($attr));
				$currentSpace = strpos_wrapper($tagLeft, ' ');
			}
			// appears in array specified by user
			$tagFound = in_array(strtolower_wrapper($tagName), $this->tagsArray);
			// remove this tag on condition
			if ((!$tagFound && $this->tagsMethod) || ($tagFound && !$this->tagsMethod)) {
				// reconstruct tag with allowed attributes
				if (!$isCloseTag) {
					$attrSet = $this->filterAttr($attrSet);
					$preTag .= '<'.$tagName;
					for ($i = 0; $i < count($attrSet); $i ++)
						$preTag .= ' '.$attrSet[$i];
					// reformat single tags to XHTML
					if (strpos_wrapper($fromTagOpen, "</".$tagName))
						$preTag .= '>';
					else
						$preTag .= ' />';
					// just the tagname
				} else
					$preTag .= '</'.$tagName.'>';
			}
			// find next tag's start
			$postTag = substr_wrapper($postTag, ($tagLength +2));
			$tagOpen_start = strpos_wrapper($postTag, '<');
		}
		// append any code after end of tags
		$preTag .= $postTag;
		return $preTag;
	}

	/** 
	  * Internal method to strip a tag of certain attributes
	  * @access protected
	  * @param Array $attrSet
	  * @return Array $newSet
	  */
	function filterAttr($attrSet) {
		$newSet = array ();
		// process attributes
		for ($i = 0; $i < count($attrSet); $i ++) {
			// skip blank spaces in tag
			if (!$attrSet[$i])
				continue;
			// split into attr name and value
			$attrSubSet = explode('=', trim($attrSet[$i]));
			list ($attrSubSet[0]) = explode(' ', $attrSubSet[0]);
			// removes all "non-regular" attr names AND also attr blacklisted
			if ((!preg_match('/^[a-z]*$/i', $attrSubSet[0])) || (($this->xssAuto) && ((in_array(strtolower_wrapper($attrSubSet[0]), $this->attrBlacklist)) || (substr_wrapper($attrSubSet[0], 0, 2) == 'on'))))
				continue;
			// xss attr value filtering
			if ($attrSubSet[1]) {
				// strips unicode, hex, etc
				$attrSubSet[1] = str_replace('&#', '', $attrSubSet[1]);
				// strip normal newline within attr value
				$attrSubSet[1] = preg_replace('/\s+/', '', $attrSubSet[1]);
				// strip double quotes
				$attrSubSet[1] = str_replace('"', '', $attrSubSet[1]);
				// [requested feature] convert single quotes from either side to doubles (Single quotes shouldn't be used to pad attr value)
				if ((substr_wrapper($attrSubSet[1], 0, 1) == "'") && (substr_wrapper($attrSubSet[1], (strlen_wrapper($attrSubSet[1]) - 1), 1) == "'"))
					$attrSubSet[1] = substr_wrapper($attrSubSet[1], 1, (strlen_wrapper($attrSubSet[1]) - 2));
				// strip slashes
				$attrSubSet[1] = stripslashes($attrSubSet[1]);
			}
			// auto strip attr's with "javascript:
			if (((strpos_wrapper(strtolower_wrapper($attrSubSet[1]), 'expression') !== false) && (strtolower_wrapper($attrSubSet[0]) == 'style')) || (strpos_wrapper(strtolower_wrapper($attrSubSet[1]), 'javascript:') !== false) || (strpos_wrapper(strtolower_wrapper($attrSubSet[1]), 'behaviour:') !== false) || (strpos_wrapper(strtolower_wrapper($attrSubSet[1]), 'vbscript:') !== false) || (strpos_wrapper(strtolower_wrapper($attrSubSet[1]), 'mocha:') !== false) || (strpos_wrapper(strtolower_wrapper($attrSubSet[1]), 'livescript:') !== false))
				continue;

			// if matches user defined array
			$attrFound = in_array(strtolower_wrapper($attrSubSet[0]), $this->attrArray);
			// keep this attr on condition
			if ((!$attrFound && $this->attrMethod) || ($attrFound && !$this->attrMethod)) {
				// attr has value
				if ($attrSubSet[1])
					$newSet[] = $attrSubSet[0].'="'.$attrSubSet[1].'"';
				// attr has decimal zero as value
				else
					if ($attrSubSet[1] == "0")
						$newSet[] = $attrSubSet[0].'="0"';
				// reformat single attributes to XHTML
				else
					$newSet[] = $attrSubSet[0].'="'.$attrSubSet[0].'"';
			}
		}
		return $newSet;
	}


	/**
	 * Used as a callback function for the preg_replace_callback function.
	 * Returns the replacement string.
	 *
	 * @return string Replacement string.
	 */
	function replaceMatchesWithDecimalNotation()
	{
		return 'chr(\\1)';
	}


	/**
	 * Used as a callback function for the preg_replace_callback function.
	 * Returns the replacement string.
	 *
	 * @return string Replacement string.
	 */
	function replaceMatchesWithHexNotation()
	{
		return 'chr(0x\\1)';
	}


	/**
	  * Try to convert to plaintext
	  * @access protected
	  * @param String $source
	  * @return String $source
	  */
	function decode($source = '') {
		if ($source!='') {
		// url decode
		$source = html_entity_decode_wrapper($source);
		// convert decimal
		$source = preg_replace_callback('/&#(\d+);/m', array($this, 'replaceMatchesWithDecimalNotation'), $source); // decimal notation
		// convert hex
		$source = preg_replace_callback('/&#x([a-f0-9]+);/mi', array($this, 'replaceMatchesWithHexNotation'), $source); // hex notation
		}
		return $source;
	}

	/**
	  * Method to be called by another php script. Processes for SQL injection
	  * @access public
	  * @param Mixed $source - input string/array-of-string to be 'cleaned'
	  * @param Buffer $connection - An open MySQL connection
	  * @return String $source - 'cleaned' version of input parameter
	  */
	function safeSQL($source, $connection = 'db_link') {
			// clean all elements in this array
	if (is_array($source)) {
			foreach ($source as $key => $value)
				// filter element for SQL injection
				if (is_string($value))
					$source[$key] = $this->quoteSmart($this->decode($value), $connection);
			return $source;
			// clean this string
		} else
			if (is_string($source)) {
				// filter source for SQL injection
				if (is_string($source))
					return $this->quoteSmart($this->decode($source), $connection);
				// return parameter as given
			} else
				return $source;
	}

	/** 
	  * @author Chris Tobin
	  * @author Daniel Morris
	  * @access protected
	  * @param String $source
	  * @param Resource $connection - An open MySQL connection
	  * @return String $source
	  */
	function quoteSmart($source, & $connection) {
		// strip slashes
		if (get_magic_quotes_gpc())
			$source = stripslashes($source);
		// quote both numeric and text
		$source = $this->escapeString($source, $connection);
		return $source;
	}

	/** 
	  * @author Chris Tobin
	  * @author Daniel Morris
	  * @access protected
	  * @param String $source
	  * @param Resource $connection - An open MySQL connection
	  * @return String $source
	  */
	function escapeString($string, & $connection) {
		// depreciated function
		if (version_compare(phpversion(), "4.3.0", "<"))
			((isset($GLOBALS["___mysqli_ston"]) && is_object($GLOBALS["___mysqli_ston"])) ? mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $string) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""));
		// current function
		else
			((isset($GLOBALS["___mysqli_ston"]) && is_object($GLOBALS["___mysqli_ston"])) ? mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $string) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""));
		return $string;
	}
}