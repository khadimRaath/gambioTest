<?php
/* --------------------------------------------------------------
   xtc_category_link.inc.php 2012-06-15
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2012 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------

(c) 2005 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: xtc_category_link.inc.php 899 2005-04-29 02:40:57Z hhgag $) 

 
   Released under the GNU General Public License 
   ---------------------------------------------------------------------------------------*/

function xtc_category_link($cID,$cName='', $p_keywords_given=false, $p_languages_id = false) {
	
	$c_languages_id = (int)$_SESSION['languages_id'];
	if($p_languages_id !== false)
	{
		$c_languages_id = (int)$p_languages_id;
	}
	
	//GM_MOD BOF
	if($p_keywords_given == false)
	{
		$result = xtc_db_query('
			SELECT gm_url_keywords
			FROM categories_description
			WHERE
				categories_id = "'. (int) $cID 											.'" AND
				language_id		= "'. $c_languages_id	.'"
		');
		$data = xtc_db_fetch_array($result);

		if(strlen($data['gm_url_keywords']) > 0) {
			$cName .= '_'. $data['gm_url_keywords'];
		}
	}
	//GM_MOD EOF

	
	$cName = xtc_cleanName($cName);
	$link = 'cat=c'.$cID.'_'.$cName.'.html';
	return $link;
}
?>