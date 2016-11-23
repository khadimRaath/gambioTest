<?php

/* --------------------------------------------------------------
  RedirectProcess.inc.php 2013-10-11 gm
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2013 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------


  based on:
  (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
  (c) 2002-2003 osCommerce(redirect.php,v 1.9 2003/02/13); www.oscommerce.com
  (c) 2003	 nextcommerce (redirect.php,v 1.7 2003/08/17); www.nextcommerce.org
  (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: redirect.php 1060 2005-07-21 18:32:58Z mz $)

  Released under the GNU General Public License
  --------------------------------------------------------------------------------------- */

// include needed functions
require_once(DIR_FS_INC . 'xtc_update_banner_click_count.inc.php');

// include needed classes
MainFactory::load_class('DataProcessing');

class RedirectProcess extends DataProcessing
{
	public function proceed()
	{
		if(isset($this->v_data_array['GET']['action']) == false)
		{
			return true;
		}
		
		switch($this->v_data_array['GET']['action'])
		{
			case 'banner' :
				if(isset($this->v_data_array['GET']['goto']))
				{
					$t_banner_query = xtc_db_query("SELECT banners_url FROM " . TABLE_BANNERS . " WHERE banners_id = '" . (int)$this->v_data_array['GET']['goto'] . "'");

					if(xtc_db_num_rows($t_banner_query))
					{
						$t_banner_array = xtc_db_fetch_array($t_banner_query);
						xtc_update_banner_click_count($this->v_data_array['GET']['goto']);

						$this->set_redirect_url($t_banner_array['banners_url']);
					}
					else
					{
						$this->set_redirect_url(xtc_href_link(FILENAME_DEFAULT));
					}
				}
				else
				{
					$this->set_redirect_url(xtc_href_link(FILENAME_DEFAULT));
				}
				break;

			case 'product' :
				if(isset($this->v_data_array['GET']['id']))
				{
					$t_product_query = xtc_db_query("SELECT products_url 
													FROM " . TABLE_PRODUCTS_DESCRIPTION . " 
													WHERE 
														products_id = '" . (int) $this->v_data_array['GET']['id'] . "' AND 
														language_id='" . (int) $_SESSION['languages_id'] . "'");

					if(xtc_db_num_rows($t_product_query))
					{
						$t_product_array = xtc_db_fetch_array($t_product_query);
						
						if(substr($t_product_array['products_url'], 0, 4) == 'http')
						{
							$this->set_redirect_url($t_product_array['products_url']);
						}
						else
						{
							$this->set_redirect_url('http://' . $t_product_array['products_url']);
						}
					}
					else
					{
						$this->set_redirect_url(xtc_href_link(FILENAME_DEFAULT));
					}
				}
				else
				{
					$this->set_redirect_url(xtc_href_link(FILENAME_DEFAULT));
				}
				break;

			case 'manufacturer' :
				if(isset($this->v_data_array['GET']['manufacturers_id']))
				{
					$t_manufacturer_query = xtc_db_query("SELECT manufacturers_url 
															FROM " . TABLE_MANUFACTURERS_INFO . " 
															WHERE 
																manufacturers_id = '" . (int)$this->v_data_array['GET']['manufacturers_id'] . "' AND 
																languages_id = '" . (int)$_SESSION['languages_id'] . "'");
					if(xtc_db_num_rows($t_manufacturer_query) == false)
					{
						// no url exists for the selected language, lets use the default language then
						$t_manufacturer_query = xtc_db_query("SELECT 
																mi.languages_id, 
																mi.manufacturers_url 
															FROM 
																" . TABLE_MANUFACTURERS_INFO . " mi, 
																" . TABLE_LANGUAGES . " l 
															WHERE 
																mi.manufacturers_id = '" . (int)$this->v_data_array['GET']['manufacturers_id'] . "' AND 
																mi.languages_id = l.languages_id AND 
																l.code = '" . DEFAULT_LANGUAGE . "'");
						if(xtc_db_num_rows($t_manufacturer_query) == false)
						{
							// no url exists, return to the site
							$this->set_redirect_url(xtc_href_link(FILENAME_DEFAULT));
						}
						else
						{
							$t_manufacturer_array = xtc_db_fetch_array($t_manufacturer_query);
							xtc_db_query("UPDATE " . TABLE_MANUFACTURERS_INFO . " 
											SET 
												url_clicked = url_clicked+1, 
												date_last_click = now() 
											WHERE 
												manufacturers_id = '" . (int)$this->v_data_array['GET']['manufacturers_id'] . "' AND 
												languages_id = '" . $t_manufacturer_array['languages_id'] . "'");
						}
					}
					else
					{
						// url exists in selected language
						$t_manufacturer_array = xtc_db_fetch_array($t_manufacturer_query);
						xtc_db_query("UPDATE " . TABLE_MANUFACTURERS_INFO . " 
										SET 
											url_clicked = url_clicked+1, 
											date_last_click = now() 
										WHERE 
											manufacturers_id = '" . (int)$this->v_data_array['GET']['manufacturers_id'] . "' AND 
											languages_id = '" . (int)$_SESSION['languages_id'] . "'");
					}

					$this->set_redirect_url($t_manufacturer_array['manufacturers_url']);
				}
				else
				{
					$this->set_redirect_url(xtc_href_link(FILENAME_DEFAULT));
				}
				break;

			default :
				$this->set_redirect_url(xtc_href_link(FILENAME_DEFAULT));
		}
		
		return true;
	}
}
