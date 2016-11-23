<?php

/* --------------------------------------------------------------
  $Id: URLEncodeFilter.php 0.1 2010-07-16 $

  brickfox Multichannel eCommerce
  http://www.brickfox.de

  Copyright (c) 2010 brickfox by NETFORMIC GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  -------------------------------------------------------------- */

class Brickfox_Lib_URLEncodeFilter extends php_user_filter
{
	/**
	 * filter()
	 *
	 * urlencode() a given DataStream
	 *
	 * @param $in
	 * @param $out
	 * @param $consumed
	 * @param $closing
	 */
	public function filter($in, $out, &$consumed, $closing)
	{
		while($bucket = stream_bucket_make_writeable($in))
		{
			$bucket->data = urlencode($bucket->data);
			$consumed += $bucket->datalen;
			stream_bucket_append($out, $bucket);
		}
		return PSFS_PASS_ON;
	}
}
?>