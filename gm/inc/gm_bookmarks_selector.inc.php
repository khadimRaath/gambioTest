<?php
/* --------------------------------------------------------------
   gm_bookmarks_selector.inc.php 2008-04-07 gambio
   Gambio OHG
   http://www.gambio.de
   Copyright (c) 2008 Gambio OHG
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------

gm_bookmarks_selector.inc.php 2007-11-26 pt@gambio
	Gambio OHG
	http://www.gambio.de
	Copyright (c) 2007 Gambio OHG
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/

	function gm_bookmarks_selector() { 
		
		// get conf for bookmarks
		$boxes = array(
							'GM_BOOKMARKS_START',
							'GM_BOOKMARKS_ARTICLES',
							'GM_BOOKMARKS_CATEGORIES',
							'GM_BOOKMARKS_REST'
							);	

		$gm_values = gm_get_conf($boxes);

		
		if(
			($product->pID !=0 && $gm_values['GM_BOOKMARKS_ARTICLES'] == 1)	||
			(!empty($cPath) && $gm_values['GM_BOOKMARKS_CATEGORIES'] == 1)	||			
			(substr(basename($PHP_SELF), 0, 8) != 'checkout')		
		)
		{
			include(DIR_WS_BOXES . 'gm_bookmarks.php');
		}
		
		return;
	
	}
?>