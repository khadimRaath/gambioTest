<?php
/*
 * Shopgate GmbH
 * http://www.shopgate.com
 * Copyright © 2012-2014 Shopgate GmbH
 *
 * Released under the GNU General Public License (Version 2)
 * [http://www.gnu.org/licenses/gpl-2.0.html]
*/

interface Shopgate_Helper_Redirect_MobileRedirectInterface
{
	/**
	 * @param bool $http
	 * @param bool $javascript
	 */
	public function supressRedirectTechniques($http = false, $javascript = false);
	
	/**
	 * @param string $name One of the Shopgate_Helper_Redirect_TagsGeneratorInterface::SITE_PARAMETER_* constants.
	 * @param string $value
	 */
	public function addSiteParameter($name, $value);
	
	/**
	 * @param string $url
	 * @param bool   $sendVary
	 *
	 * @post ends script execution in case of http redirect
	 */
	public function redirect($url, $sendVary = true);
	
	/**
	 * @return string
	 */
	public function buildScriptDefault();
	
	/**
	 * @return string
	 */
	public function buildScriptShop();
	
	/**
	 * @param string $itemNumber
	 *
	 * @return string
	 */
	public function buildScriptItem($itemNumber);
	
	/**
	 * @param string $itemNumberPublic
	 *
	 * @return string
	 */
	public function buildScriptItemPublic($itemNumberPublic);
	
	/**
	 * @param string $categoryNumber
	 *
	 * @return string
	 */
	public function buildScriptCategory($categoryNumber);
	
	/**
	 * @param string $cmsPage
	 *
	 * @return string
	 */
	public function buildScriptCms($cmsPage);
	
	/**
	 * @param string $manufacturerName
	 *
	 * @return mixed
	 */
	public function buildScriptBrand($manufacturerName);
	
	/**
	 * @param string $searchQuery
	 *
	 * @return string
	 */
	public function buildScriptSearch($searchQuery);
}