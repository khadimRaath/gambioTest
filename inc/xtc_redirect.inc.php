<?php
/* --------------------------------------------------------------
   xtc_redirect.inc.php 2016-02-19
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2019 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------


   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(general.php,v 1.225 2003/05/29); www.oscommerce.com 
   (c) 2003	 nextcommerce (xtc_redirect.inc.php,v 1.5 2003/08/13); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: xtc_redirect.inc.php 1261 2005-09-29 19:01:49Z hhgag $)
   
   Released under the GNU General Public License 
   ---------------------------------------------------------------------------------------*/

// include needed functions
require_once DIR_FS_INC . 'xtc_db_close.inc.php';
require_once DIR_FS_INC . 'xtc_exit.inc.php';

function xtc_redirect($url)
{
	// We are loading an SSL page
	if(ENABLE_SSL == true && (getenv('HTTPS') == 'on' || getenv('HTTPS') == '1'))
	{
		// NONSSL url
		if(substr($url, 0, strlen(HTTP_SERVER)) == HTTP_SERVER)
		{
			$url = HTTPS_SERVER . substr($url, strlen(HTTP_SERVER)); // Change it to SSL
		}
	}

	$url = str_replace('&amp;', '&', $url);

	@xtc_db_close();

	header('Location: ' . preg_replace("/[\r\n]+/i", '', $url));

	xtc_exit();
}