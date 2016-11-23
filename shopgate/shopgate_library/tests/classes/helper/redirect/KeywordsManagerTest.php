<?php
/*
 * Shopgate GmbH
 * http://www.shopgate.com
 * Copyright © 2012-2014 Shopgate GmbH
 *
 * Released under the GNU General Public License (Version 2)
 * [http://www.gnu.org/licenses/gpl-2.0.html]
*/

class Shopgate_Helper_Redirect_KeywordsManagerTest extends PHPUnit_Framework_TestCase
{
	/** @var ShopgateMerchantApiInterface|PHPUnit_Framework_MockObject_MockObject $merchantApi */
	protected $merchantApi;
	
	/** @var string[] */
	protected $matchingUserAgents;
	
	/** @var string[] */
	protected $nonMatchingUserAgents;
	
	public function setUp()
	{
		/** @var ShopgateMerchantApiInterface|PHPUnit_Framework_MockObject_MockObject $merchantApi */
		$this->merchantApi = $this->getMockForAbstractClass('ShopgateMerchantApiInterface');
		
		$this->merchantApi->method('getMobileRedirectUserAgents')->will($this->returnValue(
			array(
				'keywords'      => array(
					'redirectbot',
					'iphone',
					'ipod',
					'ipad',
					'android',
					'windows phone 8',
				),
				'skip_keywords' => array(
					'shopgate',
					'nexus 7',
				),
			)
		))
		;
		
		$this->matchingUserAgents = array(
			'redirectbot',
			'iphone',
			'ipod',
			'ipad',
			'android',
			'windows phone 8',
			'redirectbotiphoneipodipadandroid',
			'redirectbotiphoneipodipadandroidwindows phone 8',
			'erdirectbotiphoneipodipadandroidwindows phone 8',
			'windows phone 8redirectbotiphoneipodipadandroid',
			'Mozilla/5.0 (Linux; Android 4.3; Nxs 7 Build/JSS15Q) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/48.0.2564.23 Safari/537.36',
			'Mozilla/5.0 (Windows; Windows Phone 8; Windows Phone 8 Build/JSS15Q) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/48.0.2564.23 Safari/537.36',
		);
		
		$this->nonMatchingUserAgents = array(
			'',
			'shopgate',
			'nexus 7',
			'shopgatenexus 7',
			'nexus 7shopgate',
			'shopgaterandom',
			'randomshopgate',
			'randomshopgaterandom',
			'nexus 7random',
			'randomnexus 7',
			'randomnexus 7random',
			'Mozilla/5.0 (Linux; Android 4.3; Nexus 7 Build/JSS15Q) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/48.0.2564.23 Safari/537.36',
			'Mozilla/5.0 (Linux; Nexus 7 Android 4.3; Build/JSS15Q) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/48.0.2564.23 Safari/537.36',
			'Mozilla/5.0 (Linux; Nexus 7 Android 4.3; Nexus 7 Build/JSS15Q) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/48.0.2564.23 Safari/537.36',
			'Mozilla/5.0 (Linux; Build/JSS15Q) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/48.0.2564.23 Safari/537.36',
			'Mozilla/5.0 (Linux; Nexus 7 Build/JSS15Q) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/48.0.2564.23 Safari/537.36',
			'Mozilla/5.0 (Windows; Windows Phone 8; Nexus 7; Windows Phone 8 Build/JSS15Q) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/48.0.2564.23 Safari/537.36',
		);
	}
	
	public function testRegexMatchesWhitelistedUserAgents()
	{
		$keywordsManager = new Shopgate_Helper_Redirect_KeywordsManager(
			$this->merchantApi,
			'/dev/null',
			'/dev/null'
		);
		
		$regEx = $keywordsManager->toRegEx();
		
		foreach ($this->matchingUserAgents as $ua) {
			$this->assertRegExp(
				$regEx,
				$ua
			);
		}
	}
	
	public function testRegexDoesNotMatchBlacklistedUserAgents()
	{
		$keywordsManager = new Shopgate_Helper_Redirect_KeywordsManager(
			$this->merchantApi,
			'/dev/null',
			'/dev/null'
		);
		
		$regEx = $keywordsManager->toRegEx();
		
		foreach ($this->nonMatchingUserAgents as $ua) {
			$this->assertNotRegExp(
				$regEx,
				$ua
			);
		}
	}
}