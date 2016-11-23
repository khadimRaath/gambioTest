<?php
/* --------------------------------------------------------------
   clean_param.inc.php 2016-03-04
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * @param string $queryString
 * @param bool   $isAdminPage
 * @param bool   $encodeAmpersand
 *
 * @return string
 */
function clean_param($queryString, $isAdminPage = false, $encodeAmpersand = true)
{
	if(empty($queryString))
	{
		return $queryString;
	}
	
	$filteredParams = array(
		'action',
		'cID',
		'coID',
		'pID',
	);
	
	$separator = '&amp;';
	
	if(!$encodeAmpersand)
	{
		$separator = '&';
	}
	elseif($isAdminPage)
	{
		$separator = strpos($queryString, '&amp;') !== false ? '&amp;' : '&';
	}
	
	$queryString = str_replace('&amp;', '&', $queryString);
	
	if($separator === '&amp;')
	{
		$queryString = str_replace('&', '&amp;', $queryString);
		
		if(substr($queryString, -6) === '&amp;=')
		{
			$queryString = substr($queryString, 0, -6);
		}
		
		if(substr($queryString, -5) === '&amp;')
		{
			$queryString = substr($queryString, 0, -5);
		}
	}
	
	if(substr($queryString, -2) === '&=')
	{
		$queryString = substr($queryString, 0, -2);
	}
	
	if(substr($queryString, -1) === '&')
	{
		$queryString = substr($queryString, 0, -1);
	}
	
	$queryString = str_replace('#', '%23', $queryString);
	
	$paramPairs = explode($separator, $queryString);
	
	$params = array();
	
	foreach($paramPairs as $paramPair)
	{
		preg_match('/(.*?)=(.*)/', $paramPair, $matches);
		
		// if GET-parameter has a value
		if(isset($matches[2]))
		{
			$params[] = array($matches[1], $matches[2]);
		}
		else
		{
			$params[] = array($paramPair, null);
		}
	}
	
	foreach($params as $key => $param)
	{
		if($param[0] === '' && ($param[1] === '' || $param[1] === null))
		{
			unset($params[$key]);
			
			continue;
		}
		
		if(in_array($param[0], $filteredParams) && preg_match("/[<>\"]/", $param[1]))
		{
			$param[1] = '';
		}
		
		if($param[1] !== null)
		{
			$params[$key] = implode('=', $param);
		}
		else
		{
			$params[$key] = $param[0];
		}
	}
	
	$cleanedQueryString = implode($separator, $params);
	
	return $cleanedQueryString;
}