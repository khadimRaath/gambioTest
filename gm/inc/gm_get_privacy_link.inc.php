<?php
/*
	--------------------------------------------------------------
	gm_get_privacy_link.inc.php 2016-08-24
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2016 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/

/*
*	-> function to get the privacy link
*/
function gm_get_privacy_link($p_key)
{
	$t_privacy_link = '0';
	
	$keyTextConstantMapping = array(
		'GM_SHOW_PRIVACY_REGISTRATION'          => ENTRY_SHOW_PRIVACY_REGISTRATION,
		'GM_CHECK_PRIVACY_CALLBACK'             => ENTRY_SHOW_PRIVACY_CALLBACK,
		'GM_CHECK_PRIVACY_CONTACT'              => ENTRY_SHOW_PRIVACY_CONTACT,
		'GM_CHECK_PRIVACY_TELL_A_FRIEND'        => ENTRY_SHOW_PRIVACY_ASK_PRODUCT_QUESTION,
		'GM_CHECK_PRIVACY_FOUND_CHEAPER'        => ENTRY_SHOW_PRIVACY_FOUND_CHEAPER,
		'GM_CHECK_PRIVACY_REVIEWS'              => ENTRY_SHOW_PRIVACY_REVIEWS,
		'GM_CHECK_PRIVACY_ACCOUNT_CONTACT'      => ENTRY_SHOW_PRIVACY_ACCOUNT_CONTACT,
		'GM_CHECK_PRIVACY_ACCOUNT_ADDRESS_BOOK' => ENTRY_SHOW_PRIVACY_ADDRESS_BOOK,
		'GM_CHECK_PRIVACY_ACCOUNT_NEWSLETTER'   => ENTRY_SHOW_PRIVACY_NEWSLETTER,
		'GM_CHECK_PRIVACY_CHECKOUT_SHIPPING'    => ENTRY_SHOW_PRIVACY_CHECKOUT_SHIPPING,
		'GM_CHECK_PRIVACY_CHECKOUT_PAYMENT'     => ENTRY_SHOW_PRIVACY_CHECKOUT_PAYMENT,
	);
	
	if(gm_get_conf($p_key) == 1)
	{
		$gm_query = xtc_db_query("
										SELECT
											*
										FROM 
											content_manager
										WHERE 
											languages_id	=	'" . (int)$_SESSION['languages_id'] . "'
										AND 
											content_group	= '2'
			");
		
		$gm_array = xtc_db_fetch_array($gm_query);
		
		$SEF_parameter = '';
		if(SEARCH_ENGINE_FRIENDLY_URLS == 'true')
		{
			$SEF_parameter = '&content=' . xtc_cleanName($gm_array['content_title']);
		}
		$t_privacy_link = xtc_href_link('popup_content.php',
		                                'lightbox_mode=1&coID=' . $gm_array['content_group'] . $SEF_parameter, 'SSL');
		
		if(array_key_exists($p_key, $keyTextConstantMapping))
		{
			$t_privacy_link = sprintf($keyTextConstantMapping[$p_key], $t_privacy_link);
		}
		else
		{
			$t_privacy_link = sprintf(ENTRY_SHOW_PRIVACY, $t_privacy_link);
		}
	}
	
	return $t_privacy_link;
}
