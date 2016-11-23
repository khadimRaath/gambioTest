<?php
/* --------------------------------------------------------------
   OpenSearchApplicationTopExtender.inc.php 2012-01-16 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2012 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class OpenSearchApplicationTopExtender extends OpenSearchApplicationTopExtender_parent
{
	function proceed()
	{
		parent::proceed();
		
		if((int)gm_get_conf('GM_OPENSEARCH_BOX') == 1 || (int)gm_get_conf('GM_OPENSEARCH_SEARCH') == 1) {
			$opensearch = new GMOpenSearch();
			if((int)gm_get_conf('GM_OPENSEARCH_CHANGED') == 1) {
				require_once (DIR_FS_CATALOG . 'gm/inc/gm_get_language.inc.php');
				$opensearch->create(true);
				gm_set_conf('GM_OPENSEARCH_CHANGED', '0');
				unset($opensearch);
			}
		}		
	}
}
?>