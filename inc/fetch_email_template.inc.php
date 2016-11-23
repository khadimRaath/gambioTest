<?php
/* --------------------------------------------------------------
   fetch_email_template.inc.php 2015-04-03 mb
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2013 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * @param Smarty|ContentView $smarty
 * @param string             $p_name
 * @param string             $p_type
 * @param string             $p_folder            is DEPRECATED
 * @param int|null           $p_languageId
 * @param string|null        $p_languageDirectory is DEPRECATED
 *
 * @return bool
 */
function fetch_email_template($smarty,
                              $p_name,
                              $p_type = 'html',
                              $p_folder = '',
                              $p_languageId = null,
                              $p_languageDirectory = null)
{
	$c_languageId = (int)$p_languageId;
	if(empty($p_languageId))
	{
		$c_languageId = (int)$_SESSION['languages_id'];
	}

	/**
	 * @var MailTemplateManager $mailTemplateManager
	 */
	$mailTemplateManager = MainFactory::create_object('MailTemplateManager', array(
		MainFactory::create_object('MailTemplatesCacheBuilder')
	));
	$filePath            = $mailTemplateManager->findPath($p_name, $c_languageId, $p_type);

	if($filePath !== null)
	{
		$output = $smarty->fetch($filePath);

		return $output;
	}

	return false;
}