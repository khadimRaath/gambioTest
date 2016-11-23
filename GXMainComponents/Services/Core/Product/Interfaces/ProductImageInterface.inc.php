<?php
/* --------------------------------------------------------------
   ProductImageInterface.inc.php 2016-03-17
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface ProductImageInterface
 *
 * @category   System
 * @package    Product
 * @subpackage Interfaces
 */
Interface ProductImageInterface
{
	/**
	 * Get Filename
	 *
	 * Returns the filename of the product.
	 *
	 * @return string The filename of the product.
	 */
	public function getFilename();


	/**
	 * Get Alt Text
	 *
	 * Returns the alternative text of a product image.
	 *
	 * @param LanguageCode $language The language code of the alt text to return.
	 *
	 * @return string The alternative text of the product image.
	 */
	public function getAltText(LanguageCode $language);


	/**
	 * Is Visible
	 *
	 * Checks if the product image is set to visible or not.
	 *
	 * @return bool Is the product image visible?
	 */
	public function isVisible();
	
	
	/**
	 * Set Alt Text
	 *
	 * Sets the alternative text of the product image.
	 *
	 * @param StringType   $text     The alternative text for the product image.
	 * @param LanguageCode $language The language code of the alternative text.
	 *
	 * @return string The alternative text of the product image.
	 */
	public function setAltText(StringType $text, LanguageCode $language);


	/**
	 * Set Visible
	 *
	 * Activates or deactivates the products image visibility.
	 *
	 * @param BoolType $visible Should the product image be visible?
	 *
	 * @return ProductImageInterface Same instance for chained method calls.
	 */
	public function setVisible(BoolType $visible);
}