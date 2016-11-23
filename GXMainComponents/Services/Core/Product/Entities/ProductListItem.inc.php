<?php

/* --------------------------------------------------------------
   ProductListItem.inc.php 2015-12-09
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class ProductListItem
 *
 * @category   System
 * @package    Product
 * @subpackage Entities
 */
class ProductListItem
{
	/**
	 * Product repository.
	 *
	 * @var ProductRepositoryInterface
	 */
	protected $productRepo;

	/**
	 * Product ID.
	 *
	 * @var int
	 */
	protected $productId;

	/**
	 * Is product active?
	 *
	 * @var boolean
	 */
	protected $active;

	/**
	 * Product name.
	 *
	 * @var string
	 */
	protected $name;

	/**
	 * URL keywords.
	 *
	 * @var string
	 */
	protected $urlKeywords;

	/**
	 * Image.
	 *
	 * @var string
	 */
	protected $image;

	/**
	 * Image alternative text.
	 *
	 * @var string
	 */
	protected $imageAltText;
	

	/**
	 * ProductListItem constructor.
	 *
	 * @param ProductRepositoryInterface $productRepo  Product Repository.
	 * @param IdType                     $productId    Product ID.
	 * @param BoolType                   $isActive     Is the product active?
	 * @param StringType                 $name         Product name.
	 * @param StringType                 $urlKeywords  URL keywords.
	 * @param StringType                 $image        Product image.
	 * @param StringType                 $imageAltText Product image alternative text.
	 *
	 */
	public function __construct(ProductRepositoryInterface $productRepo,
	                            IdType $productId,
	                            BoolType $isActive,
	                            StringType $name,
	                            StringType $urlKeywords,
	                            StringType $image,
	                            StringType $imageAltText)
	{
		$this->productRepo  = $productRepo;
		$this->productId    = $productId->asInt();
		$this->active       = $isActive->asBool();
		$this->name         = $name->asString();
		$this->urlKeywords  = $urlKeywords->asString();
		$this->image        = $image->asString();
		$this->imageAltText = $imageAltText->asString();
	}


	/**
	 * Returns the product ID.
	 *
	 * @return int
	 */
	public function getProductId()
	{
		return $this->productId;
	}


	/**
	 * Checks if product is active or not.
	 *
	 * @return boolean
	 */
	public function isActive()
	{
		return $this->active;
	}


	/**
	 * Returns the name of the product.
	 *
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}


	/**
	 * Returns the URL keywords.
	 *
	 * @return string
	 */
	public function getUrlKeywords()
	{
		return $this->urlKeywords;
	}


	/**
	 * Returns the image.
	 *
	 * @return string
	 */
	public function getImage()
	{
		return $this->image;
	}


	/**
	 * Returns the alternative image text.
	 *
	 * @return string
	 */
	public function getImageAltText()
	{
		return $this->imageAltText;
	}
	

	/**
	 * Returns the product object.
	 *
	 * @throws InvalidArgumentException if the product ID is not valid.
	 *
	 * @return ProductListItem Same instance for chained method calls.
	 */
	public function getProductObject()
	{
		$id = new IdType($this->getProductId());

		return $this->productRepo->getProductById($id);
	}
}