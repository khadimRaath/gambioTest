<?php
/* --------------------------------------------------------------
  top_menu.php 2015-10-08
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2015 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
 */

$coo_top_navigation = MainFactory::create_object('TopNavigationBoxContentView');
$coo_top_navigation->set_content_template('boxes/box_top_navigation.html');
$coo_top_navigation->setXtcPrice($this->coo_xtc_price);
$t_top_navigation_html = $coo_top_navigation->get_html();
$this->set_content_data('TOP_NAVIGATION', $t_top_navigation_html);

$coo_login_dropdown = MainFactory::create_object('LoginBoxContentView');
$coo_login_dropdown->set_content_template('boxes/box_login_dropdown.html');
$t_login_dropdown_html = $coo_login_dropdown->get_html();
$this->set_content_data('LOGIN_DROPDOWN', $t_login_dropdown_html);

$coo_infobox_dropdown = MainFactory::create_object('InfoboxBoxContentView');
$coo_infobox_dropdown->set_content_template('boxes/box_infobox_dropdown.html');
$t_infobox_dropdown_html = $coo_infobox_dropdown->get_html();
$this->set_content_data('INFOBOX_DROPDOWN', $t_infobox_dropdown_html);

$t_currencies_dropdown_html = '';

if(gm_get_conf('SHOW_TOP_CURRENCY_SELECTION') == 'true')
{
	$coo_currencies_dropdown = MainFactory::create_object('CurrenciesBoxContentView');
	$coo_currencies_dropdown->setXtcPrice($this->coo_xtc_price);
	$coo_currencies_dropdown->setRequestType($this->request_type);
	if(isset($_GET))
	{
		$coo_currencies_dropdown->setGetArray($_GET);
	}
	else
	{
		$coo_currencies_dropdown->setGetArray(array());
	}
	$coo_currencies_dropdown->set_content_template('boxes/box_currencies_dropdown.html');
	$t_currencies_dropdown_html = $coo_currencies_dropdown->get_html();
}

$this->set_content_data('CURRENCIES_DROPDOWN', $t_currencies_dropdown_html);

$t_languages_dropdown_html = '';

if(gm_get_conf('SHOW_TOP_LANGUAGE_SELECTION') == 'true')
{
	if(!isset($lng) && !is_object($lng))
	{
		include_once(DIR_WS_CLASSES . 'language.php');
		$lng = new language;
	}

	$coo_languages_dropdown = MainFactory::create_object('LanguagesBoxContentView');
	$coo_languages_dropdown->set_content_template('boxes/box_languages_dropdown.html');
	$coo_languages_dropdown->set_('coo_language', $lng);
	if(trim($this->request_type) != '')
	{
		$coo_languages_dropdown->set_('request_type', $this->request_type);
	}
	$t_languages_dropdown_html = $coo_languages_dropdown->get_html();
}

$this->set_content_data('LANGUAGES_DROPDOWN', $t_languages_dropdown_html);


$t_countries_dropdown_html = '';

if(gm_get_conf('SHOW_TOP_COUNTRY_SELECTION') === 'true' && !isset($_SESSION['customer_id']))
{
	$coo_countries_dropdown = MainFactory::create_object('CountriesBoxContentView');
	$coo_countries_dropdown->setLanguageId($_SESSION['languages_id']);
	
	if(isset($_SESSION['customer_country_iso']))
	{
		$coo_countries_dropdown->setCustomerCountryIsoCode(MainFactory::create('CustomerCountryIso2', 
		                                                                       $_SESSION['customer_country_iso']));
	}
	
	$coo_countries_dropdown->setLanguageId($_SESSION['languages_id']);
	$t_countries_dropdown_html = $coo_countries_dropdown->get_html();
}

$this->set_content_data('COUNTRIES_DROPDOWN', $t_countries_dropdown_html);
