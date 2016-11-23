<?php
/* --------------------------------------------------------------
   ProductImage.inc.php 2016-03-22
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/


/**
 * Class ProductImage
 *
 * @category   System
 * @package    Product
 * @subpackage Entities
 */
class ProductImage implements ProductImageInterface
{
	/**
	 * Filename of the product image
	 *
	 * @var string
	 */
	protected $filename;

	/**
	 * Alt texts of the image.
	 *
	 * @var array
	 */
	protected $altTexts = array();

	/**
	 * Is the product image visible?
	 *
	 * @var bool
	 */
	protected $visibility = true;


	/**
	 * ProductImage constructor.
	 *
	 * @param FilenameStringType $filename Filename of the image.
	 */
	public function __construct(FilenameStringType $filename)
	{
		$this->filename = $filename->asString();
	}


	/**
	 * Get Filename
	 *
	 * Returns the filename of the product.
	 *
	 * @return string The filename of the product.
	 */
	public function getFilename()
	{
		return $this->filename;
	}


	/**
	 * Get Alt Text
	 *
	 * Returns the alternative text of a product image.
	 *
	 * @param LanguageCode $language The language code of the alt text to return.
	 *
	 * @throws InvalidArgumentException if the language code is not valid.
	 *
	 * @return string The alternative text of the product image.
	 */
	public function getAltText(LanguageCode $language)
	{
		if(!array_key_exists($language->asString(), $this->altTexts))
		{
			throw new InvalidArgumentException($language->asString() . ' is not a valid key.');
		}

		return $this->altTexts[$language->asString()];
	}


	/**
	 * Is Visible
	 *
	 * Checks if the product image is set to visible or not.
	 *
	 * @return bool Is the product image visible?
	 */
	public function isVisible()
	{
		return $this->visibility;
	}


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
	public function setAltText(StringType $text, LanguageCode $language)
	{
		$this->altTexts[$language->asString()] = $text->asString();
	}


	/**
	 * Set Visible
	 *
	 * Activates or deactivates the products image visibility.
	 *
	 * @param BoolType $visible Should the product image be visible?
	 *
	 * @return ProductImageInterface Same instance for chained method calls.
	 */
	public function setVisible(BoolType $visible)
	{
		$this->visibility = $visible->asBool();
	}

}