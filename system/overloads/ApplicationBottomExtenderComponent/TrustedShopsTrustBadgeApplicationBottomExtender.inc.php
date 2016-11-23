<?php
/* --------------------------------------------------------------
	TrustedShopsTrustBadgeApplicationBottomExtender.inc.php 2015-02-11
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2015 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/

class TrustedShopsTrustBadgeApplicationBottomExtender extends TrustedShopsTrustBadgeApplicationBottomExtender_parent
{
	public function proceed()
	{
		parent::proceed();
		$service = MainFactory::create_object('GMTSService');
		$tsid = $service->findRatingID($_SESSION['language_code']);
		if($tsid !== false)
		{
			$badge_snippet = $service->getBadgeSnippet($tsid);
			if($badge_snippet['enabled'] == true)
			{
				$this->v_output_buffer['TRUST_BADGE'] = $badge_snippet['snippet_code'];
			}
		}
	}
}
