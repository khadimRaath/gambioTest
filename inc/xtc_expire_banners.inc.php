<?php
/* -----------------------------------------------------------------------------------------
  $Id: xtc_expire_banners.inc.php 899 2014-03-05 02:40:57Z hhgag $

  XT-Commerce - community made shopping
  http://www.xt-commerce.com

  Copyright (c) 2003 XT-Commerce
  -----------------------------------------------------------------------------------------
  based on:
  (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
  (c) 2002-2003 osCommerce(banner.php,v 1.10 2003/02/11); www.oscommerce.com
  (c) 2003	 nextcommerce (xtc_expire_banners.inc.php,v 1.5 2003/08/1); www.nextcommerce.org

  Released under the GNU General Public License
  --------------------------------------------------------------------------------------- */

require_once(DIR_FS_INC . 'xtc_set_banner_status.inc.php');
// Auto expire banners
function xtc_expire_banners()
{
	$t_query = 'SELECT
					b.banners_id,
					b.expires_date,
					b.expires_impressions,
					sum(bh.banners_shown) as banners_shown 
				FROM
					' . TABLE_BANNERS . ' b,
					' . TABLE_BANNERS_HISTORY . ' bh
				WHERE
					b.status = "1"
					AND b.banners_id = bh.banners_id 
				GROUP BY
					b.banners_id';
	$t_result = xtc_db_query($t_query);
	if(xtc_db_num_rows($t_result))
	{
		while($t_banner = xtc_db_fetch_array($t_result))
		{
			if($t_banner['expires_date'])
			{
				if(date('Y-m-d H:i:s') >= $t_banner['expires_date'])
				{
					xtc_set_banner_status($t_banner['banners_id'], '0');
				}
			}
			elseif($t_banner['expires_impressions'])
			{
				if($t_banner['banners_shown'] >= $t_banner['expires_impressions'])
				{
					xtc_set_banner_status($t_banner['banners_id'], '0');
				}
			}
		}
	}
}