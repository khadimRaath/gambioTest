<?php
/* --------------------------------------------------------------
   gm_get_google_changefreq.inc.php 2008-05-16 gambio
   Gambio OHG
   http://www.gambio.de
   Copyright (c) 2008 Gambio OHG
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------

gm_get_google_changefreq.inc.php 2008-01-30 pt
	Gambio OHG
	http://www.gambio.de
	Copyright (c) 2008 Gambio OHG
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/
	
	/*
	*	-> function to get a dropdown list
	*/	
	function gm_get_google_changefreq() {
				
		return '<select name="GM_SITEMAP_GOOGLE_CHANGEFREQ">
					<option value="always">always</option>
					<option value="hourly">hourly</option>
					<option value="daily">daily</option>
					<option selected value="weekly">weekly</option>
					<option value="monthly">monthly</option>
					<option value="yearly">yearly</option>
					<option value="never">never</option>
				</select>';
	}


	/*
	*	-> function to get a dropdown list
	*/	
	function gm_get_google_priority() {
				
		return '<select name="GM_SITEMAP_GOOGLE_PRIORITY">
					<option value="0.0">0.0</option>
					<option value="0.1">0.1</option>
					<option value="0.2">0.2</option>
					<option value="0.3">0.3</option>
					<option value="0.4">0.4</option>
					<option selected value="0.5">0.5</option>
					<option value="0.6">0.6</option>
					<option value="0.7">0.7</option>
					<option value="0.8">0.8</option>
					<option value="0.9">0.9</option>
					<option value="1.0">1.0</option>
				</select>';
	}
?>