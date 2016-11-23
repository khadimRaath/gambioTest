<?php
/* --------------------------------------------------------------
   function.categories.php 2015-10-15 mb
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/
function smarty_function_categories($params, &$smarty)
{
	$arrResult['categories'] = getCategories();
	
	$smarty->assign($params['out'], $arrResult);
}

function getCategories()
{
	$arrTemp = array();
	
	if(GROUP_CHECK == 'true')
	{
		$groupCheck = " and c.group_permission_" . (int)$_SESSION['customers_status']['customers_status_id'] . " = 1 ";
	}
	
	$strSql = "select * from categories c, categories_description cd
		where c.categories_id = cd.categories_id
		and c.parent_id = 0
		and c.categories_status = 1
		" . $groupCheck . "
		and cd.language_id = " . $_SESSION['languages_id'] . "
		order by c.sort_order";
	$result         = xtc_db_query($strSql);
	while ($item = xtc_db_fetch_array($result))
	{
		$arrTemp[] = $item;
	};
	return $arrTemp;
}