<?php

/* --------------------------------------------------------------
   EmptyProductImage.inc.php 2016-03-22
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class EmptyProductImage
 *
 * @category   System
 * @package    Product
 * @subpackage Entities
 */
class EmptyProductImage extends ProductImage
{
	// Overriding the parent constructor
	public function __construct()
	{
		$this->filename   = '';
		$this->visibility = false;
	}


	/**
	 * Get Alt Text
	 *
	 * Returns the alternative text of a product image if a name is already set,
	 * otherwise an empty string will be returned.
	 *
	 * @param \LanguageCode $language The language code of the alt text to return.
	 *
	 * @return string The alternative text of the product image.
	 */
	public function getAltText(LanguageCode $language)
	{
		return $this->altTexts[$language->asString()] ? : '';
	}
}