<?php
/* --------------------------------------------------------------
  currencies.php 2014-10-28 gm
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2014 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------


  based on:
  (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
  (c) 2002-2003 osCommerce(currencies.php,v 1.16 2003/02/12); www.oscommerce.com
  (c) 2003	 nextcommerce (currencies.php,v 1.11 2003/08/17); www.nextcommerce.org
  (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: currencies.php 1262 2005-09-30 10:00:32Z mz $)

  Released under the GNU General Public License
  --------------------------------------------------------------------------------------- */

$t_box_html = '';

if(isset($this->coo_xtc_price) && is_object($this->coo_xtc_price))
{
	$coo_currencies = MainFactory::create_object('CurrenciesBoxContentView');
	$coo_currencies->set_content_template('boxes/box_currencies.html');

	if(isset($_GET))
	{
		$coo_currencies->setGetArray($_GET);
	}
	else
	{
		$coo_currencies->setGetArray(array());
	}

	$coo_currencies->setRequestType($this->request_type);
	$coo_currencies->setXtcPrice($this->coo_xtc_price);
	$t_box_html = $coo_currencies->get_html();
}

$gm_box_pos = $GLOBALS['coo_template_control']->get_menubox_position('currencies');
$this->set_content_data($gm_box_pos, $t_box_html);