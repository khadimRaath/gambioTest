<?php
/* --------------------------------------------------------------
   get_href_link.inc.php 2016-03-04
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

require_once DIR_FS_INC . 'clean_param.inc.php';
require_once DIR_FS_INC . 'xtc_get_top_level_domain.inc.php';

/**
 * Returns a url.
 *
 * @param string $httpServer
 * @param string $httpsServer
 * @param string $baseDir
 * @param bool   $isSslEnabled
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
function get_href_link($httpServer,
                       $httpsServer,
                       $baseDir,
                       $isSslEnabled,
                       $page = '',
                       $queryString = '',
                       $connection = 'NONSSL',
                       $allowSessionIdInUrl = true,
                       $deprecatedXtcSeoUrl = true,
                       $relativeUrl = false,
                       $encodeAmpersand = true)
{
	static $searchKeysForcingSslUrl, $httpsDomainIsUnlikeHttpDomain, $truncateSessionId, $currentConnection;
	
	// first call: initialize static vars
	if($searchKeysForcingSslUrl === null)
	{
		$searchKeysForcingSslUrl = array();
		
		$httpDomain                    = xtc_get_top_level_domain($httpServer);
		$httpsDomain                   = xtc_get_top_level_domain($httpsServer);
		$httpsDomainIsUnlikeHttpDomain = $httpDomain !== $httpsDomain;
		$truncateSessionId             = false;
		
		// remove session-ID if user agent is a known spider
		if(CHECK_CLIENT_AGENT === 'True' && xtc_check_agent())
		{
			$truncateSessionId = true;
		}
		
		$currentConnection = 'NONSSL';
		
		if(isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == '1' || strtolower($_SERVER['HTTPS']) === 'on'))
		{
			$currentConnection = 'SSL';
		}
	}
	
	// build array of search keys which are a criterion for building a SSL url
	if(count($searchKeysForcingSslUrl) === 0 && function_exists('xtc_db_query') && function_exists('gm_get_env_info'))
	{
		$seoBoost                  = MainFactory::create_object('GMSEOBoost');
		$searchKeysForcingSslUrl[] = $seoBoost->get_boosted_content_url($seoBoost->get_content_id_by_content_group(14),
		                                                                $_SESSION['languages_id']);
		$searchKeysForcingSslUrl[] = $seoBoost->get_boosted_content_url($seoBoost->get_content_id_by_content_group(7),
		                                                                $_SESSION['languages_id']);
		
		$searchKeysForcingSslUrl[] = 'coID=14'; // callback
		$searchKeysForcingSslUrl[] = 'coID=7'; // contact
		
		$searchKeysForcingSslUrl[] = 'newsletter.php';
		$searchKeysForcingSslUrl[] = 'gm_price_offer.php';
		$searchKeysForcingSslUrl[] = 'product_reviews_write.php';
	}
	
	foreach($searchKeysForcingSslUrl as $searchKey)
	{
		if($connection === 'NONSSL'
		   && (strpos($queryString, $searchKey) !== false || strpos($page, $searchKey) !== false)
		   && strpos(gm_get_env_info('SCRIPT_NAME'), '/admin/') === false
		)
		{
			// force SSL
			$connection = 'SSL';
		}
	}
	
	if(!is_string($page))
	{
		$page = FILENAME_DEFAULT;
	}
	
	$url = $httpServer . $baseDir;
	
	if($relativeUrl === true)
	{
		$url = '';
	}
	elseif($connection === 'SSL' && $isSslEnabled)
	{
		$url = $httpsServer . $baseDir;
	}
	
	$url .= $page;
	$separator = '?';
	
	$queryString = clean_param($queryString, false, $encodeAmpersand);
	if(is_string($queryString) && $queryString !== '')
	{
		$url .= '?' . $queryString;
		$separator = $encodeAmpersand ? '&amp;' : '&';
	}
	
	// The session ID is needed in the url, if the url's domain differs from the current domain. 
	// This happens if the HTTPS server is a proxy.	
	if(!$truncateSessionId && $allowSessionIdInUrl && $httpsDomainIsUnlikeHttpDomain
	   && $currentConnection !== $connection && $isSslEnabled
	)
	{
		$url .= $separator . session_name() . '=' . session_id();
	}
	
	if(SEARCH_ENGINE_FRIENDLY_URLS === 'true' && $deprecatedXtcSeoUrl)
	{
		$url = str_replace(array('?', '&amp;', '&', '='), '/', $url);
	}
	
	return $url;
}