<?php
/* --------------------------------------------------------------
   generate_withdrawal_link.inc.php 2014-06-23 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

function generate_withdrawal_link($p_order_hash = null)
{
	$t_withdrawal_content_id = gm_get_conf('GM_WITHDRAWAL_CONTENT_ID');
	$t_link = HTTP_SERVER . DIR_WS_CATALOG . 'request_port.php?module=ShopContent&amp;action=download&amp;coID=' . $t_withdrawal_content_id . '&amp;withdrawal_form=1&amp;language=' . $_SESSION['language_code'];
	$t_http_server = HTTP_SERVER;

	if(defined('ENABLE_SSL') && ENABLE_SSL === true)
	{
		$t_http_server = HTTPS_SERVER;
	}
	elseif(defined('ENABLE_SSL_CATALOG') && (ENABLE_SSL_CATALOG === 'true' || ENABLE_SSL_CATALOG === true))
	{
		$t_http_server = HTTPS_CATALOG_SERVER;
	}

	if(gm_get_conf('WITHDRAWAL_WEBFORM_ACTIVE') == '1' && is_string($p_order_hash) && trim($p_order_hash) != '')
	{
		$t_link = $t_http_server . DIR_WS_CATALOG . 'withdrawal.php?order=' . $p_order_hash . '&amp;language=' . $_SESSION['language_code'];
	}
	elseif(gm_get_conf('WITHDRAWAL_WEBFORM_ACTIVE') == '1')
	{
		$t_link = $t_http_server . DIR_WS_CATALOG . 'withdrawal.php?language=' . $_SESSION['language_code'];
	}
	
	return $t_link;
}