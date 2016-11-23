<?php
/* --------------------------------------------------------------
   LanguageHelperInterface.inc.php 2015-12-09 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class LanguageHelperInterface
 *
 * @category   System
 * @package    Extensions
 * @subpackage Helpers
 */
interface LanguageHelperInterface
{
	/**
	 * Gets the corresponding language code for the given ID
	 * 
	 * @param IdType $id
	 *
	 * @return LanguageCode
	 */
	public function getLanguageCodeById(IdType $id);
	
	
	/**
	 * Gets all language codes in a KeyValueCollection with language ID as key
	 *
	 * @param BoolType $onlyActiveLanguages
	 * 
	 * @return KeyValueCollection
	 */
	public function getLanguageCodes(BoolType $onlyActiveLanguages = null);
	
	
	/**
	 * Gets the language codes of all active languages in a KeyValueCollection
	 * 
	 * @return KeyValueCollection
	 */
	public function getActiveLanguageCodes();
}
