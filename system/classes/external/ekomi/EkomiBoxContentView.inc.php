<?php
/* --------------------------------------------------------------
   EkomiBoxContentView.inc.php 2014-07-17 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------


   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(languages.php,v 1.14 2003/02/12); www.oscommerce.com
   (c) 2003	 nextcommerce (languages.php,v 1.8 2003/08/17); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: languages.php 1262 2005-09-30 10:00:32Z mz $)

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

class EkomiBoxContentView extends ContentView
{
	function EkomiBoxContentView()
	{
		parent::__construct();
		$this->set_content_template('boxes/box_ekomi.html');
		$this->set_caching_enabled(false);
	}

	function prepare_data()
	{
		$t_widget_code = gm_get_conf('EKOMI_WIDGET_CODE');

		if($_SESSION['style_edit_mode'] == 'edit')
		{
			$t_widget_code = preg_replace('!(.*?)<script.*?</script>(.*?)!is', "$1$2", $t_widget_code);
		}

		$this->content_array['WIDGET_CODE'] = $t_widget_code;
	}
}