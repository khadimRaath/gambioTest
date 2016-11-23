<?php
/* --------------------------------------------------------------
   xtc_get_tax_description.inc.php 2016-08-22
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------


   based on: 
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(general.php,v 1.225 2003/05/29); www.oscommerce.com 
   (c) 2003	 nextcommerce (xtc_get_tax_description.inc.php); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: xtc_get_tax_description.inc.php 1166 2014-02-06 00:52:02Z mz $)

   Released under the GNU General Public License 
   ---------------------------------------------------------------------------------------*/

function xtc_get_tax_description($p_class_id, $p_country_id = -1, $p_zone_id = -1, $p_customer_b2b = -1)
{
	$c_class_id   = (int)$p_class_id;
	$c_country_id = (int)$p_country_id;
	$c_zone_id    = (int)$p_zone_id;

	if(($c_country_id === -1 || $c_country_id === 0) && ($c_zone_id === -1 || $c_zone_id === 0))
	{
		$c_country_id = (isset($_SESSION['customer_country_id'])) ? (int)$_SESSION['customer_country_id'] : (int)STORE_COUNTRY;

		if(isset($_SESSION['customer_zone_id']))
		{
			$c_zone_id = (int)$_SESSION['customer_country_id'];
		}
		elseif(!isset($_SESSION['customer_zone_id']) &&
			   (!isset($_SESSION['customer_country_id']) || $_SESSION['customer_country_id'] == STORE_COUNTRY)
		)
		{
			$c_zone_id = (int)STORE_ZONE;
		}
		else
		{
			$c_zone_id = 0;
		}
	}

	if($_SESSION['customers_status']['customers_status_id'] === '0')
	{
		$c_country_id = (int)STORE_COUNTRY;
		$c_zone_id    = (int)STORE_ZONE;
	}
	elseif(country_eu_status_by_country_id($c_country_id) == true)
	{
		if($p_customer_b2b != -1)
		{
			$t_customer_b2b = $p_customer_b2b;
		}
		elseif(isset($_SESSION['customer_b2b_status']) == true)
		{
			$t_customer_b2b = $_SESSION['customer_b2b_status'];
		}
		else
		{
			$t_customer_b2b = false;
		}

		if($t_customer_b2b == true)
		{
			// OVERWRITE country and zone, if customer is B2B in EU
			$c_country_id = (int)STORE_COUNTRY;
			$c_zone_id    = (int)STORE_ZONE;
		}
	}

	$tax_query = xtDBquery("select tax_rate from " . TABLE_TAX_RATES . " tr left join " .
						   TABLE_ZONES_TO_GEO_ZONES . " za on (tr.tax_zone_id = za.geo_zone_id) left join " .
						   TABLE_GEO_ZONES .
						   " tz on (tz.geo_zone_id = tr.tax_zone_id) where (za.zone_country_id is null or za.zone_country_id = '0' or za.zone_country_id = '" .
						   $c_country_id . "') and (za.zone_id is null or za.zone_id = '0' or za.zone_id = '" .
						   $c_zone_id . "') and tr.tax_class_id = '" . $c_class_id .
						   "' order by tr.tax_priority");
	if(xtc_db_num_rows($tax_query, true))
	{
		$tax_description = '';
		while($tax = xtc_db_fetch_array($tax_query, true))
		{
			if($_SESSION['customers_status']['customers_status_show_price_tax'] == '1')
			{
				$tax_description .= sprintf(strstr(TAX_INFO_INCL, '%s'), (double)$tax['tax_rate'] . '%') . ' + ';
			}
			else
			{
				$tax_description .= sprintf(strstr(TAX_INFO_ADD, '%s'), (double)$tax['tax_rate'] . '%') . ' + ';
			}
		}
		$tax_description = substr_wrapper($tax_description, 0, -3);

		return $tax_description;
	}
	else
	{
		return TEXT_UNKNOWN_TAX_RATE;
	}
}
