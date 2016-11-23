<?php
/* --------------------------------------------------------------
   Yoochoose GmbH
   http://www.yoochoose.com
   Copyright (c) 2011 Yoochoose GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   -------------------------------------------------------------- */



    function yooSqlTemplates($prefix, &$resultSet) {
    	$result = array();
    	while ($record = mysqli_fetch_array($resultSet,  MYSQLI_NUM)) {
    		$result[] = $prefix.$record[0];
    	}
    	return $result;
    }
    
    
    function yooProductTemplates() {
	    $p_templates = yooSqlTemplates('/module/product_info/', 
	    		xtc_db_query('SELECT DISTINCT product_template FROM products WHERE product_template <> "default"'));
    	return $p_templates;
    }
    
    
    function yooListingTemplates() {
    	$c_templates = yooSqlTemplates('/module/product_listing/',
    			xtc_db_query('SELECT DISTINCT listing_template FROM categories WHERE listing_template <> "default"'));
    	return $c_templates;
    }
    		

    function printInfoDiv($content, $icon, $style = "info") {
    	echo '<div class="'.$style.'" style=\'background-image:url("includes/modules/yoochoose/images/kurumizawa/'.$icon.'")\'">';
    	echo $content;
    	echo '</div>';
    }
    
    function printQuestionDiv($question, $answer) {
        echo '<div class="$question">';
        echo $question.'<br>';
        echo $answer;
        echo '</div>';
    }

      
    /** Updates a YOOCHOOSE Propery. Creates if not found.
     *  Deletes the property, if it has a default value. */
    function updateProperty($name, $value, $deaultValue = '') {
        
        if ($deaultValue && $name == $deaultValue) {
            $sql = 'DELETE FROM '.TABLE_CONFIGURATION.' WHERE configuration_key=\'%1$s\'';
        } else if (defined($name)) {
            $sql = 'UPDATE '.TABLE_CONFIGURATION.' SET configuration_group_id=24, 
                   configuration_value=\'%2$s\' WHERE configuration_key=\'%1$s\'';
        } else {
            $sql = 'INSERT INTO '.TABLE_CONFIGURATION.' (configuration_key, configuration_group_id, configuration_value ) 
                   VALUES (\'%1$s\', 24, \'%2$s\')';        
        }
        xtc_db_query(sprintf($sql, ((isset($GLOBALS["___mysqli_ston"]) && is_object($GLOBALS["___mysqli_ston"])) ? mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $name) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : "")), ((isset($GLOBALS["___mysqli_ston"]) && is_object($GLOBALS["___mysqli_ston"])) ? mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $value) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""))));
    }
    
    
    /** Returns a post value, if set; a constant value, if set; or derfault value otherwise. */
    function getMaxDisplayValue($name, $default) {
    	$const = getMaxDisplayConstantName($box_name);
    	
        if (isset($_POST[$name])) {
            if (function_exists('do_magic_quotes_gpc')) {
                // Gambio enforces magic_quotes using this function.
                // We need to undo this fraud action
                return stripcslashes($_POST[$name]);
            } else {
                return $_POST[$name];
            }
        } else {
            return getMaxDisplay($name);
        }
    }   
    
        
    /** Returns a post value, if set; a constant value, if set; or derfault value otherwise. */
    function getValue($name, $default) {
        if (isset($_POST[$name])) {
            if (function_exists('do_magic_quotes_gpc')) {
                // Gambio enforces magic_quotes using this function.
                // We need to undo this fraud action
                return stripcslashes($_POST[$name]);
            } else {
                return $_POST[$name];
            }
        } else if ($name && defined($name)) {
            return constant($name);
        } else {
            return $default;
        }
    }    
 
    
    /** Returns a post value, if set; a constant value, if set; or derfault value otherwise. */
    function getPostOrGet($name, $default) {
    	if (isset($_POST[$name])) {
    		if (function_exists('do_magic_quotes_gpc')) {
    			// Gambio enforces magic_quotes using this function.
    			// We need to undo this fraud action
    			return stripcslashes($_POST[$name]);
    		} else {
    			return $_POST[$name];
    		}
    	} else if (isset($_GET[$name])) {
    		if (function_exists('do_magic_quotes_gpc')) {
    			// Gambio enforces magic_quotes using this function.
    			// We need to undo this fraud action
    			return stripcslashes($_GET[$name]);
    		} else {
    			return $_GET[$name];
    		}
    	} else if ($name && defined($name)) {
    		return constant($name);
    	} else {
    		return $default;
    	}
    }
    

    
    
    require_once(DIR_FS_CATALOG.'gm/inc/gm_get_content.inc.php');
    require_once(DIR_FS_CATALOG.'gm/inc/gm_set_content.inc.php');
    
  

    
    
    /** Returns spezified content from the table 'gm_contents' values for 
     *  all languages as array. 
     *  */
    function getContentValue($name) {
    	
    	$result = array();
    	
    	$sql = "SELECT l.languages_id, c.gm_value
                        FROM ".TABLE_LANGUAGES." l LEFT JOIN gm_contents c ON l.languages_id = c.languages_id 
                        WHERE c.gm_key = '" . ((isset($GLOBALS["___mysqli_ston"]) && is_object($GLOBALS["___mysqli_ston"])) ? mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $name) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : "")) . "' ORDER BY l.sort_order";
    	
    	$resultset = xtc_db_query($sql);
    	
    	while ($record = xtc_db_fetch_array($resultset)) {
    		
    		$languageId = $record['languages_id'];
    		$content = $record['gm_value'];
    		
    	    if (isset($_POST[$name]) && isset($_POST[$name][$languageId])) {
    	    	$result[$languageId] = $_POST[$name][$languageId];
    	    } else if ($content != null) {
    	   	    $result[$languageId] = $content;
    	    } else {
    	    	$result[$languageId] = "";
    	    }
    	}
        
        return $result;
    }
    
    
?>