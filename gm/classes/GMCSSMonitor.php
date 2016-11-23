<?php
/* --------------------------------------------------------------
   GMCSSMonitor.php 2015-05-22 gambio
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

	class GmImportCSS
	{
		var $full_css_code;

		/*
		*	@var string
		*/
		var $v_current_template;
	
		// constructor
		function __construct($css_code, $p_current_template) 
		{
			$this->v_current_template	= $p_current_template;
			$this->full_css_code		= $css_code;
		}
		
		/* 
		* update
		*/
		function run_away() {

			$code 	= $this->clean_code();					//remove newlines
			$styles = $this->get_style_list($code);	//get styles as array

			foreach($styles as $s) {
				$s_key 		= key($styles);
				$s_value	= current($styles);

				$gm_query = xtc_db_query('
										SELECT 
											gm_css_style_id 
										AS
											id
										FROM
											gm_css_style
										WHERE 
											style_name = "'. $s_key .'"
										AND
											template_name	= "' . addslashes($this->v_current_template) . '"
										');
				

				if(xtc_db_num_rows($gm_query) > 0){
					
					$gm_row = xtc_db_fetch_array($gm_query);
					
					xtc_db_query('
									DELETE
										FROM
											gm_css_style_content
										WHERE
											gm_css_style_id = "' . $gm_row['id'] . '"
								');

					$attributes = $this->get_attribute_list($s_value);
					foreach($attributes as $key => $value)  {

						xtc_db_query('
							INSERT INTO gm_css_style_content
							SET
								gm_css_style_id = "'. $gm_row['id']	.'",
								style_attribute	= "'. $key		.'",
								style_value		= "'. $value		.'"
						');
					next($attributes);
					}
				next($styles);
				}

				echo "Eintrag gespeichert";
				
			}			
		}


		/* 
		* -> check if style exits
		*/
		function style_exists($styles) {

			foreach($styles as $key => $value) {
				
				$gm_query = xtc_db_query("
					SELECT
						*
					FROM
						gm_css_style
					WHERE
						style_name		= '" . $key										. "'
					AND
						template_name	= '" . addslashes($this->v_current_template)	. "'
					");
				
				if(xtc_db_num_rows($gm_query) > 0) {					
					return true;
				}			
			}
			return false;
		}


		/* 
		* insert 
		*/
		function run() 
		{
			$code 	= $this->clean_code();			//remove newlines
			$styles = $this->get_style_list($code);	//get styles as array

			
			if(!$this->style_exists($styles) && $code != 'empty') {		
				foreach($styles as $s)
				{
					$s_key 		= key($styles);
					$s_value	= current($styles);
					
					mysqli_query($GLOBALS["___mysqli_ston"], '
						INSERT INTO gm_css_style
						SET style_name = "'. $s_key .'",
							template_name	= "' . addslashes($this->v_current_template)	. '",
							selectors_specificity = "' . ((isset($GLOBALS["___mysqli_ston"]) && is_object($GLOBALS["___mysqli_ston"])) ? mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $this->get_specificity($s_key)) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : "")) . '"

					');
					$gm_css_style_id = ((is_null($___mysqli_res = mysqli_insert_id($GLOBALS["___mysqli_ston"]))) ? false : $___mysqli_res);
					
					$attributes = $this->get_attribute_list($s_value);
					
					foreach($attributes as $a)
					{
						$a_key 		= key($attributes);
						$a_value	= current($attributes);
						
						mysqli_query($GLOBALS["___mysqli_ston"], '
							INSERT INTO gm_css_style_content
							SET
								gm_css_style_id = "'. $gm_css_style_id 	.'",
								style_attribute	= "'. $a_key						.'",
								style_value			= "'.	$a_value					.'"
						');
					next($attributes);
					}
				next($styles);
				}	

				echo '<pre>'.$this->clean_code() .'</pre>';
				echo '<br /><div class="error">Styles wurden in der Datenbank gespeichert. <a href="gm_css_monitor.php">weiter zur &Uuml;bersicht</a></div>';


			} elseif($code == 'empty') {
				echo xtc_draw_textarea_field("css_input", '', '', 20);
				echo  '<br /><input id="gm_save" type="submit" value="speichern" onclick="insert_styles()">&nbsp;<span class="error">Sie haben nichts eingegeben.</span>';

			} else {
				echo xtc_draw_textarea_field("css_input", '', '', 20, $this->clean_code());
				echo  '<br /><input id="gm_save" type="submit" value="speichern" onclick="insert_styles()">&nbsp;<span class="error">Style existiert bereits - kontrollieren Sie Ihre Eingabe!</span>';
			}			
		}
		
		function get_attribute_list($id_values) 
		{
			/* fix invalid syntax */
			$id_values = preg_replace('/([^;\s])(\s+\S+)\s*:/', '$1;$2:', $id_values);
			$id_values = preg_replace('/[^\S ]+/', '', $id_values);
			$id_values = preg_replace('/\s\s+/', ' ', $id_values);

			$attr_list 	= explode(';', $id_values);
			$attr_pairs = array();
			
			foreach($attr_list as $attribute) 
			{
				$pair 	= explode(':', $attribute);
				$key		= trim($pair[0]);
				$value	= trim($pair[1]);
				
				$attr_pairs = array_merge($attr_pairs, array("$key" => "$value"));
			}

			if(empty($attr_pairs[count($attr_pairs)-1]))
			{
				array_pop($attr_pairs);
			}

			return $attr_pairs;
		}
		
		function get_style_list($cleaned_code = false)
		{
			if($cleaned_code === false) $cleaned_code = $this->full_css_code;
			
			$style_lines = explode('}', $cleaned_code);
			$style_pairs = array();
			
			foreach($style_lines as $line) 
			{
				$pair 	= explode('{', $line);
				$key		= trim($pair[0]);
				$value	= trim($pair[1]);
				
				$style_pairs = array_merge($style_pairs, array("$key" => "$value"));
			}
			array_pop($style_pairs);
			//print_r($style_pairs);
			return $style_pairs;
		}
		
		function clean_code($code = false)
		{
			if($code === false) $code = $this->full_css_code;
			
			//$code = str_replace("\n", '', $code);						//remove newlines
			//$code = str_replace("\r", '', $code);						//remove newlines
			//$code = preg_replace("/\r|\n/s", "", $code);
			$code = preg_replace('(/\*.*\*/)', '', $code);	//remove comments
			
			return $code;
		}

		function get_specificity($p_style_name)
		{
			$t_specificity = '';
			$t_selector = trim($p_style_name);
			$t_specificity .= substr_count($t_selector, '#') . '-';
			$t_specificity .= substr_count($t_selector, '.') . '-';

			$t_tag_count = 0;
			if(substr($t_selector, 0, 1) != '#' && substr($t_selector, 0, 1) != '.')
			{
				$t_tag_count++;
			}

			preg_match_all('/\s+[^.#\s]{1}/', $t_selector, $t_matches_array);
			if(isset($t_matches_array[0]))
			{
				$t_tag_count += count($t_matches_array[0]);
			}

			$t_specificity .= $t_tag_count;

			return $t_specificity;
		}
	}


	/*
	* a simple class to monitor css styles
	*/
	class GMCSSMonitor
	{
		/*
		*	@var string
		*/
		var $v_current_template;

		function __construct($p_current_template) 
		{			
			$this->v_current_template = $p_current_template;
			return;
		}


		/*
		* -> mark elements
		*/
		function get_needle($style) {
			if(strpos($style, ' ') == 0) {
				
				$array = explode('_', $style);
				//if(count($array) > 2) {		
				if(count($array) > 1) {		
					//return strlen($array[0]) + strlen($array[1])  + strlen($array[2]) + 2;
					return strlen($array[0]);
				} else {
					return;
				}				
			} else {				
				return strpos($style, ' ');
			}
		}

		
		/*
		* -> load parents 
		*/
		function get_parent()	 {
			
			$actual; 
			$nodes .= '<option selected value="default" style="font-weight:bold">Hauptebene</option>'; 

			$gm_query = xtc_db_query("
									SELECT
										*
									FROM
										gm_css_style
									WHERE
										template_name	= '" . addslashes($this->v_current_template) . "'								
									ORDER by
										style_name
									");

			while($row = xtc_db_fetch_array($gm_query)) {
				
				$pos = $this->get_needle($row['style_name']);
				if($pos > 0) {
					$element = substr($row['style_name'], 0, $pos);
				} else {
					$element = $row['style_name'];
				}
				if(!strstr($element, $actual)) {
					$actual = $element;
					$nodes .= '<option value="' . $actual . '">' . $actual . '</option>';
				} 
			}

			return $nodes;
		}	


		/*
		* -> load childs 
		*/
		function get_child($parent)	 {
			
			$nodes .= '<option selected value="default" style="font-weight:bold">' . $parent . '</option>'; 
			$nodes .= '<option value="root" style="font-weight:bold">..</option>'; 

			$gm_query = xtc_db_query("
									SELECT
										*
									FROM
										gm_css_style
									WHERE
										style_name LIKE '%" . $parent . "%'
									AND
										template_name	= '" . addslashes($this->v_current_template) . "'								
									ORDER by
										style_name
									");

			while($row = xtc_db_fetch_array($gm_query)) {
				$nodes .= '<option value="' . $row['gm_css_style_id']. '">' . $row['style_name'] . '</option>';
			}

			return $nodes;
		}	


		/*
		* -> load styles
		*/
		function get_style_by_id($element) {
			$i = 0;
			$gm_query = xtc_db_query("
									SELECT
										c.style_value,
										c.style_attribute,
										s.style_name
									FROM
										gm_css_style_content c
									LEFT JOIN
										gm_css_style s
									ON 
										c.gm_css_style_id = s.gm_css_style_id
									WHERE
										c.gm_css_style_id = '" . $element . "'
									AND
										s.template_name	= '" . addslashes($this->v_current_template)	. "'								
									ORDER by
										c.style_attribute
									");
			if(xtc_db_num_rows($gm_query) > 0){
				
				while($row = xtc_db_fetch_array($gm_query)) {
					$i++;
					$name = $row['style_name'];
					$style .= $row['style_attribute'] . ": " . $row['style_value'] . ";\n";
				}
				$style .= "}". "\n";

				$style = $name . " { \n". $style;
			}
			return xtc_draw_textarea_field("css_input", '', '', $i+4, $style);
		}	


		/*
		* -> load styles
		*/
		function get_style_by_name($element) {
			$i = 0;
			$gm_query = xtc_db_query("
									SELECT
										c.style_value,
										c.style_attribute,
										s.style_name
									FROM
										gm_css_style_content c
									LEFT JOIN
										gm_css_style s
									ON 
										c.gm_css_style_id = s.gm_css_style_id
									WHERE
										s.style_name	= '" . trim($element)							. "'
									AND
										s.template_name	= '" . addslashes($this->v_current_template)	. "'								
									ORDER by
										c.style_attribute
									");
			if(xtc_db_num_rows($gm_query) > 0){
				
				while($row = xtc_db_fetch_array($gm_query)) {
					$i++;
					$name		= $row['style_name'];
					$style .= $row['style_attribute'] . ": " . $row['style_value'] . ";\n";
				}

				$style .= "}". "\n";

				$style = $name . " { \n". $style;

				return xtc_draw_textarea_field("css_input", '', '', $i+6, $style) . '<br /><input id="gm_save" type="submit" value="speichern" onclick="save_styles()">';
			

			/*
			* -> search for alternates
			*/
			} else {
				
				$return_dlev = false;

				$nodes .= '<select id="gm_searcher" onchange="load_searched_styles()">';
				$nodes .= '<option selected value="default" style="font-weight:bold">Suchvorschl&auml;ge</option>'; 

				$gm_query = xtc_db_query("
										SELECT
											*
										FROM
											gm_css_style
										WHERE
											template_name = '" . addslashes($this->v_current_template)		. "'
										ORDER by
											style_name
										");

				while($row = xtc_db_fetch_array($gm_query)) {
				
					if($this->gm_dlev(trim($element), $row['style_name']) < 7) {
						$return_dlev = true;
						$nodes .= '<option value="' . $row['gm_css_style_id']. '">' . $row['style_name'] . '</option>';
					} 
				}	
				
				if($return_dlev) {
					return $nodes . '</select>&nbsp;<span id="gm_close" onclick="gm_close()">close</span>';
				} else {
					echo 'Kein entsprechender Eintrag vorhanden.&nbsp;<span id="gm_close" onclick="gm_close()">close</span>';
				}
			}
		}	

			
		/*
		* -> explode/split str 
		*/
		function gm_split_str($str) {
			
			$array = array();
			for($i = 0; $i < strlen($str); $i++) {
				$array[] = substr($str, $i, 1);
			}
		
			return $array;
		}


		/*
		* -> damerau-levenshtein
		*/
		function gm_dlev($str_1, $str_2) {	
			

			$array_1 = $this->gm_split_str($str_1);
			$array_2 = $this->gm_split_str($str_2);

			/* 
			* -> build default distance y
			*/
			for($i = 0; $i <= count($array_1); $i++) {
				$distance[$i][0] = $i;
			}
			
			/* 
			* -> build default distance x
			*/
			for($j = 0; $j <= count($array_2); $j++) {
				$distance[0][$j] = $j;
			}

			/* 
			* -> build the levenshtein-distance
			*/
			for($i = 1; $i <= count($array_1); $i++) {
				for($j = 1; $j <= count($array_2); $j++) {
					if($array_1[$i-1] == $array_2[$j-1]) {
						$cost = 0;
					} else {
						$cost = 1;
					}	
					
					$distance[$i][$j] = min(
											$distance[$i-1][$j] + 1,			// top insert
											$distance[$i][$j-1] + 1,			// left	del
											$distance[$i-1][$j-1] + $cost		// top-left sub
											);
											
					/*
					* -> damerau-extension
					*/
					if($i > 1 && $j > 1 && $array_1[$i-1] == $array_2[$j-2] && $array_1[$i-2] ==$array_2[$j-1]){
						$distance[$i][$j] = min($distance[$i][$j], $distance[$i-2][$j-2] + $cost);
					}
				}
			}
			return $distance[count($array_1)][count($array_2)];	
		}
	}