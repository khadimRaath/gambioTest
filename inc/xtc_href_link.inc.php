<?php
/* --------------------------------------------------------------
   xtc_href_link.inc.php 2012-09-26 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2012 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------


   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(html_output.php,v 1.52 2003/03/19); www.oscommerce.com
   (c) 2003	 nextcommerce (xtc_href_link.inc.php,v 1.3 2003/08/13); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: xtc_href_link.inc.php 804 2005-02-26 16:42:03Z mz $)

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

require_once DIR_FS_INC . 'get_href_link.inc.php';

/**
 * Returns a url.
 *
 * @param string $page
 * @param string $queryString
 * @param string $connection
 * @param bool   $allowSessionIdInUrl
 * @param bool   $deprecatedXtcSeoUrl
 * @param bool   $relativeUrl
 * @param bool   $encodeAmpersand
 *
 * @return string
 */
function xtc_href_link($page = '',
                       $queryString = '',
                       $connection = 'NONSSL',
                       $allowSessionIdInUrl = true,
                       $deprecatedXtcSeoUrl = true,
                       $relativeUrl = false,
                       $encodeAmpersand = true)
{
	return get_href_link(HTTP_SERVER, HTTPS_SERVER, DIR_WS_CATALOG, ENABLE_SSL, $page, $queryString, $connection,
	                     $allowSessionIdInUrl, $deprecatedXtcSeoUrl, $relativeUrl, $encodeAmpersand);
}

/**
 * Returns an admin page url.
 *
 * @param string $page
 * @param string $queryString
 * @param string $connection
 * @param bool   $allowSessionIdInUrl
 * @param null   $deprecatedUnusedParam
 *
 * @return string
 */
function xtc_href_link_admin($page = '',
                             $queryString = '',
                             $connection = 'NONSSL',
                             $allowSessionIdInUrl = true,
                             $deprecatedUnusedParam = null)
{
	return xtc_href_link($page, $queryString, $connection, $allowSessionIdInUrl, false);
}
