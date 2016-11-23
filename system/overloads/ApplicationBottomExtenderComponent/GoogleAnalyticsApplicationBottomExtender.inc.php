<?php
/* --------------------------------------------------------------
   GoogleAnalyticsApplicationBottomExtender.inc.php 2012-01-16 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2012 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class GoogleAnalyticsApplicationBottomExtender extends GoogleAnalyticsApplicationBottomExtender_parent
{
	function proceed()
	{
		# print Google Analytics Code
		if(gm_get_conf('GM_ANALYTICS_CODE_USE') == '1') {
			$this->v_output_buffer['GOOGLE_ANALYTICS_CODE'] = gm_get_conf('GM_ANALYTICS_CODE');
		}		
		
		parent::proceed();
	}
}
?>