<?php
/*
 * Shopgate GmbH
 * http://www.shopgate.com
 * Copyright © 2012-2014 Shopgate GmbH
 *
 * Released under the GNU General Public License (Version 2)
 * [http://www.gnu.org/licenses/gpl-2.0.html]
*/

interface Shopgate_Helper_Redirect_RedirectorInterface
{
	/**
	 * @post If enabled, a location header to the mobile default/fallback page is sent to the requesting entity.
	 */
	public function redirectDefault();
	
	/**
	 * @post A location header to the mobile home page is sent to the requesting entity.
	 */
	public function redirectHome();
	
	/**
	 * @param string $uid
	 *
	 * @post A location header to the mobile category detail page is sent to the requesting entity.
	 */
	public function redirectCategory($uid);
	
	/**
	 * @param string $uid
	 *
	 * @post A location header to the mobile product page is sent to the requesting entity.
	 */
	public function redirectProduct($uid);
	
	/**
	 * @param string $pageUid
	 *
	 * @post A location header to the mobile CMS page is sent to the requesting entity.
	 */
	public function redirectCms($pageUid);
	
	/**
	 * @param string $brandName
	 *
	 * @post A location header to the mobile brand search is sent to the requesting entity.
	 */
	public function redirectBrand($brandName);
	
	/**
	 * @param string $searchString
	 *
	 * @post A location header to the mobile searchpage is sent to the requesting entity.
	 */
	public function redirectSearch($searchString);
	
	/**
	 * @param string $url      The URL to redirect to.
	 * @param bool   $sendVary True to send the "Vary: User-Agent" header.
	 */
	public function redirect($url, $sendVary = true);

	/**
	 * Checks current browser user agent string
	 * against allowed mobile keywords, e.g. Iphone, Android, etc
	 * 
	 * @return bool
	 */
	public function isMobile();
}