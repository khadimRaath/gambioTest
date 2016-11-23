<?php

/* --------------------------------------------------------------
   LanguageProviderInterface.inc.php 2016-05-31
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface LanguageProviderInterface
 *
 * @category   System
 * @package    Shared
 * @subpackage Interfaces
 */
interface LanguageProviderInterface
{
	/**
	 * Returns the language IDs.
	 *
	 * @throws UnexpectedValueException If no ID has been found.
	 * @throws InvalidArgumentException If ID is not valid.
	 *
	 * @return IdCollection
	 */
	public function getIds();
	
	
	/**
	 * Returns the language codes.
	 *
	 * @throws UnexpectedValueException If no code has been found.
	 * @throws InvalidArgumentException If code is not valid.
	 *
	 * @return KeyValueCollection
	 */
	public function getCodes();
	
	
	/**
	 * Returns the language code from a specific language, selected by the language ID.
	 *
	 * @param IdType $id Language ID.
	 *
	 * @throws UnexpectedValueException If no code has been found.
	 * @throws InvalidArgumentException If code is not valid.
	 *
	 * @return LanguageCode
	 */
	public function getCodeById(IdType $id);
	
	
	/**
	 * Returns the directory from the a specific language, selected by the language ID.
	 *
	 * @param IdType $id Language ID.
	 *
	 * @throws UnexpectedValueException If no directory has been found.
	 * @throws InvalidArgumentException If code is not valid.
	 *
	 * @return string
	 */
	public function getDirectoryById(IdType $id);
	
	
	/**
	 * Returns the charset from the a specific language, selected by the language ID.
	 *
	 * @param IdType $id Language ID.
	 *
	 * @throws UnexpectedValueException If no charset has been found.
	 *
	 * @return string
	 */
	public function getCharsetById(IdType $id);
	
	
	/**
	 * Returns the ID from the a specific language, selected by the language code.
	 *
	 * @param LanguageCode $code Language code.
	 *
	 * @throws UnexpectedValueException If no ID has been found.
	 *
	 * @return int
	 */
	public function getIdByCode(LanguageCode $code);
	
	
	/**
	 * Returns the directory from the a specific language, selected by the language code.
	 *
	 * @param LanguageCode $code Language code.
	 *
	 * @throws UnexpectedValueException If no directory has been found.
	 *
	 * @return string
	 */
	public function getDirectoryByCode(LanguageCode $code);
	
	
	/**
	 * Returns the charset from the a specific language, selected by the language code.
	 *
	 * @param LanguageCode $code Language code.
	 *
	 * @throws UnexpectedValueException If no directory has been found.
	 *
	 * @return string
	 */
	public function getCharsetByCode(LanguageCode $code);
	
	
	/**
	 * Returns the active language codes.
	 *
	 * @throws InvalidArgumentException If code is not valid.
	 *
	 * @return KeyValueCollection
	 */
	public function getActiveCodes();
	
	
	/**
	 * Returns the icon for a specific language by a given language code.
	 *
	 * @param LanguageCode $code The given language code
	 *
	 * @throws UnexpectedValueException If no icon has been found.
	 *
	 * @return string
	 */
	public function getIconFilenameByCode(LanguageCode $code);
	
	
	/**
	 * Returns the default language code.
	 *
	 * @throws InvalidArgumentException If no default code exists.
	 *
	 * @return string
	 */
	public function getDefaultLanguageCode();
	
	
	/**
	 * Returns the default language ID.
	 *
	 * @throws InvalidArgumentException If no default code exists.
	 *
	 * @return int
	 */
	public function getDefaultLanguageId();
}