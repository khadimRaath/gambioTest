<?php
/* --------------------------------------------------------------
   function.content_manager.php 2016-01-27
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Helper function that generates the query
 * and executes it on the database
 *
 * @param       {integer}       $group          The content_group id of the desired db entry
 * @param       {integer}       $language       The language id of the desired db entry
 *
 * @return      array|bool|mixed                Returns the first result as array
 */
function _executeContentManagerQuery($group, $language)
{
	if(GROUP_CHECK === 'true')
	{
		$groupCheck = ' AND group_ids LIKE "%c_' . xtc_db_input($_SESSION['customers_status']['customers_status_id'])
		              . '_group%"';
	}

	$queryString = 'SELECT *
					FROM ' . TABLE_CONTENT_MANAGER . '
					WHERE
						`content_group` = ' . (int)$group . '
						AND	`languages_id` = ' . (int)$language . '
						AND `content_status` = 1' .
						$groupCheck;

	$query  = xtc_db_query($queryString);
	$result = array();

	if(xtc_db_num_rows($query))
	{
		$result = xtc_db_fetch_array($query);
	}

	return $result;
}


/**
 * Helper function that calculates the
 * fallback language id if possible
 *
 * @param       {integer}       $source     The fallback language id
 * @param       {integer}       $language   The desired language id
 *
 * @return      integer|null                Returns the fallback id if there is one, otherwise null
 */
function _getContentManagerFallbackId($source, $language)
{
	$fallback = (gettype($source) === 'integer') ? $source : $GLOBALS['coo_lang_file_master']->getDefaultLanguageId();
	$fallback = ((int)$fallback !== (int)$language) ? $fallback : null;

	return $fallback;
}


/**
 * Smarty plugin that gets content manager
 * elements from the database
 *
 * @param   {object}        $params     The passed parameters
 * @param   {object}        $smarty     Smarty object
 *
 * @return  string                      Returns the collected string from the db
 */
function smarty_function_content_manager($params, &$smarty)
{
	$group    = isset($params['group']) ? $params['group'] : null;
	$language = isset($params['lang']) ? $params['lang'] : $_SESSION['languages_id'];
	$fallback = isset($params['fallback']) ? _getContentManagerFallbackId($params['fallback'], $language) : null;
	$output   = isset($params['out']) ? $params['out'] : null;
	$result   = _executeContentManagerQuery($group, $language);

	if(empty($result) && $fallback !== null)
	{
		$result = _executeContentManagerQuery($group, $fallback);
	}

	if($output !== null)
	{
		$smarty->assign($output, $result);
	}
	else
	{
		return (count($result)) ? $result['content_text'] : '';
	}
}