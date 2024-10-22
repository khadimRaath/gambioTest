<?php
/* --------------------------------------------------------------
   xtc_get_categories.inc.php 2011-06-27 gambio
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2011 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------


   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(general.php,v 1.225 2003/05/29); www.oscommerce.com
   (c) 2003	 nextcommerce (xtc_get_categories.inc.php,v 1.3 2003/08/13); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: xtc_get_categories.inc.php 1009 2005-07-11 16:19:29Z mz $)

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

  function xtc_get_categories($categories_array = '', $parent_id = '0', $indent = '') {

    $parent_id = xtc_db_prepare_input($parent_id);

    if (!is_array($categories_array)) $categories_array = array();

	//group check
	$t_group_check = '';
	if(GROUP_CHECK == 'true')
	{
		$t_group_check = " AND c.group_permission_" . (int)$_SESSION['customers_status']['customers_status_id'] . " = '1' ";
	}

    $categories_query = "select
                                      c.categories_id,
                                      cd.categories_name
                                      from " . TABLE_CATEGORIES . " c,
                                       " . TABLE_CATEGORIES_DESCRIPTION . " cd
                                       where parent_id = '" . xtc_db_input($parent_id) . "'
                                       and c.categories_id = cd.categories_id
                                       and c.categories_status != 0
                                       and cd.language_id = '" . $_SESSION['languages_id'] . "'
									   " . $t_group_check . "
                                       order by sort_order, cd.categories_name";

    $categories_query  = xtDBquery($categories_query);

    while ($categories = xtc_db_fetch_array($categories_query,true)) {
      $categories_array[] = array('id' => $categories['categories_id'],
                                  'text' => $indent . htmlspecialchars_wrapper($categories['categories_name']));

      if ($categories['categories_id'] != $parent_id) {
        $categories_array = xtc_get_categories($categories_array, $categories['categories_id'], $indent . '&nbsp;&nbsp;');
      }
    }

    return $categories_array;
  }
 ?>