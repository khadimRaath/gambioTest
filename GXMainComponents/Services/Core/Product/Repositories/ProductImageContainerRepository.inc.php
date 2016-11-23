<?php
/* --------------------------------------------------------------
   ProductImageContainerRepository.inc.php 2016-06-30
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class ProductImageContainerRepository
 *
 * @category   System
 * @package    Product
 * @subpackage Repositories
 */
class ProductImageContainerRepository implements ProductImageContainerRepositoryInterface
{
	/**
	 * Database connection.
	 *
	 * @var CI_DB_query_builder
	 */
	protected $db;
	
	/**
	 * Used for fetching the language data.
	 *
	 * @var LanguageProviderInterface
	 */
	protected $languageProvider;
	
	
	/**
	 * ProductImageContainerRepository constructor.
	 *
	 * @param CI_DB_query_builder       $db Database connection.
	 * @param LanguageProviderInterface $languageProvider
	 */
	public function __construct(CI_DB_query_builder $db, LanguageProviderInterface $languageProvider)
	{
		$this->db               = $db;
		$this->languageProvider = $languageProvider;
	}


	/**
	 * Stores the product image container.
	 *
	 * @param IdType                         $productId      Product ID.
	 * @param ProductImageContainerInterface $imageContainer Product image container.
	 *
	 * @throws InvalidArgumentException On invalid arguments.
	 *
	 * @return ProductImageContainerRepository Same instance for method chaining.
	 */
	public function store(IdType $productId, ProductImageContainerInterface $imageContainer)
	{
		$this->deleteByProductId($productId);

		// Prepare primary image data.
		$primaryImageData = array(
			'products_image'   => $imageContainer->getPrimary()->getFilename(),
			'products_image_w' => 0,
			'products_image_h' => 0,
			'gm_show_image'    => $imageContainer->getPrimary()->isVisible()
		);

		$this->db->where('products_id', $productId->asInt())->update('products', $primaryImageData);

		$this->_savePrimaryImageAltText($imageContainer, $productId);

		$this->_saveAdditionalImagesAltText($imageContainer, $productId);

		return $this;
	}


	/**
	 * Returns a product image container based on the product ID given.
	 *
	 * @param IdType $productId Product ID.
	 *
	 * @throws InvalidArgumentException On invalid arguments.
	 *
	 * @return ProductImageContainerInterface Product image container.
	 */
	public function getByProductId(IdType $productId)
	{
		$primaryImageQuery = $this->_queryPrimaryImage($productId);

		$additionalImagesQuery = $this->_queryAdditionalImages($productId);

		$imageContainer = MainFactory::create('ProductImageContainer');

		// Create primary image.
		if(!empty($primaryImageQuery['products_image']))
		{
			$primaryImageFilename  = new FilenameStringType($primaryImageQuery['products_image']);
			$primaryImageIsVisible = new BoolType($primaryImageQuery['gm_show_image']);
			$primaryImage          = MainFactory::create('ProductImage', $primaryImageFilename);
			$primaryImage->setVisible($primaryImageIsVisible);

			// Set alternative texts on primary image.
			$this->_getPrimaryImageAltText($primaryImage, $productId);
		}
		else
		{
			$primaryImage = MainFactory::create('EmptyProductImage');
		}

		$imageContainer->setPrimary($primaryImage);

		// Create additional images and add them to image container.
		foreach($additionalImagesQuery as $additionalImageRow)
		{
			// Create additional product image.
			$additionalImageId        = new IdType($additionalImageRow['image_id']);
			$additionalImageFilename  = new FilenameStringType($additionalImageRow['image_name']);
			$additionalImageIsVisible = new BoolType($additionalImageRow['gm_show_image']);
			$additionalImage          = MainFactory::create('ProductImage', $additionalImageFilename);
			$additionalImage->setVisible($additionalImageIsVisible);

			// Set alternative texts on additional image.
			$this->_getAdditionalImagesAltText($additionalImage, $productId, $additionalImageId);

			$imageContainer->addAdditional($additionalImage);
		}

		return $imageContainer;
	}


	/**
	 * Deletes a product image container based on the product ID given.
	 *
	 * @param IdType $productId Product ID.
	 *
	 * @return ProductImageContainerRepository Same instance for method chaining.
	 */
	public function deleteByProductId(IdType $productId)
	{
		$this->db->where('products_id', $productId->asInt())->delete('products_images');

		// Clear primary image fields.
		$updateData = array(
			'products_image'   => '',
			'products_image_w' => 0,
			'products_image_h' => 0
		);
		$this->db->where('products_id', $productId->asInt())->update('products', $updateData);

		// Delete alternative texts entries for additional images.
		$this->db->where('products_id', $productId->asInt())->delete('gm_prd_img_alt');

		return $this;
	}


	/**
	 * Saves alternative texts for additional images.
	 *
	 * @param ProductImageContainerInterface $imageContainer Image container.
	 * @param IdType                         $productId      Product ID.
	 *
	 * @throws InvalidArgumentException on invalid arguments.
	 */
	protected function _saveAdditionalImagesAltText(ProductImageContainerInterface $imageContainer, IdType $productId)
	{
		// Prepare additional images data and insert entries to database.
		foreach($imageContainer->getAdditionals()->getArray() as $index => $additionalImage)
		{
			// Additional image data.
			$additionalImageData = array(
				'products_id'   => $productId->asInt(),
				'image_nr'      => $index + 1,
				'image_name'    => $additionalImage->getFilename(),
				'gm_show_image' => (bool)$additionalImage->isVisible()
			);

			// Insert additional image into database.
			$this->db->insert('products_images', $additionalImageData);
		}

		// Get additional image entries after save.
		$additionalImagesAfterSave = $this->_queryAdditionalImages($productId);

		// Iterate over each additional image fetched from DB.
		foreach($additionalImagesAfterSave as $additionalImageData)
		{
			$imageId    = (int)$additionalImageData['image_id'];
			$imageIndex = (int)$additionalImageData['image_nr'] - 1;

			// Iterate over each language
			foreach($this->languageProvider->getCodes() as $languageCode)
			{
				$languageId = $this->languageProvider->getIdByCode($languageCode);

				// Try to get alternative text of additional image for current language.
				// If no value is set for current language, iterate to next.
				try
				{
					// Get alternative text.
					// Throws InvalidArgumentException if no value is present.
					$altText = $imageContainer->getAdditionals()->getItem($imageIndex)->getAltText($languageCode);

					// Data array for insert into `gm_prd_img_alt` table.
					$productImageAltData = array(
						'image_id'    => $imageId,
						'products_id' => $productId->asInt(),
						'language_id' => $languageId,
						'gm_alt_text' => $altText
					);

					$this->db->insert('gm_prd_img_alt', $productImageAltData);
				}
				catch(Exception $exception)
				{
					continue;
				}
			}
		}
	}


	/**
	 * Saves alternative texts for the primary image.
	 *
	 * @param ProductImageContainerInterface $imageContainer Image container.
	 * @param IdType                         $productId      Product ID.
	 *
	 * @throws InvalidArgumentException on invalid arguments.
	 */
	protected function _savePrimaryImageAltText(ProductImageContainerInterface $imageContainer, IdType $productId)
	{
		// Iterate over each language and save primary image alternative text.
		foreach($this->languageProvider->getCodes() as $languageCode)
		{
			$languageId = $this->languageProvider->getIdByCode($languageCode);

			// Try to get alternative text of primary image for current language.
			// If no value is set for current language, iterate to next.
			try
			{
				// Get alternative text.
				// Throws InvalidArgumentException if no value is present.
				$altText = $imageContainer->getPrimary()->getAltText($languageCode);

				// Data array for `products_description` table.
				$productsDescriptionData = array(
					'gm_alt_text' => $altText
				);

				// Write value to database.
				$this->db->where('products_id', $productId->asInt())
				         ->where('language_id', $languageId)
				         ->update('products_description', $productsDescriptionData);
			}
			catch(Exception $exception)
			{
				continue;
			}
		}
	}


	/**
	 * Sets alternative texts for the primary image provided.
	 *
	 * @param ProductImageInterface $primaryImage Product primary image.
	 * @param IdType                $productId    Product ID.
	 *
	 * @throws InvalidArgumentException on invalid arguments.
	 */
	protected function _getPrimaryImageAltText(ProductImageInterface $primaryImage, IdType $productId)
	{
		foreach($this->languageProvider->getCodes() as $languageCode)
		{
			$languageId = $this->languageProvider->getIdByCode($languageCode);;

			// Get all alternative texts for primary image.
			$alternativeTextsQuery = $this->db->select('language_id, gm_alt_text')
			                                  ->from('products_description')
			                                  ->where('products_id', $productId->asInt())
			                                  ->where('language_id', $languageId)
			                                  ->get()
			                                  ->row_array();

			if(count($alternativeTextsQuery) > 0 && $alternativeTextsQuery['gm_alt_text'] !== null)
			{
				$text = new StringType((string)$alternativeTextsQuery['gm_alt_text']);
			}
			else
			{
				$text = new StringType('');
			}
			$primaryImage->setAltText($text, $languageCode);
		}
	}


	/**
	 * Sets the alternative texts for an additional image.
	 *
	 * @param ProductImageInterface $additionalImage Additional Image.
	 * @param IdType                $productId       Product ID.
	 * @param IdType                $imageId         Image ID.
	 *
	 * @throws InvalidArgumentException on invalid arguments.
	 */
	protected function _getAdditionalImagesAltText(ProductImageInterface $additionalImage,
	                                               IdType $productId,
	                                               IdType $imageId)
	{
		foreach($this->languageProvider->getCodes() as $languageCode)
		{
			$languageId = $this->languageProvider->getIdByCode($languageCode);

			// Get all alternative texts for primary image.
			$alternativeTextsQuery = $this->db->select('*')
			                                  ->from('gm_prd_img_alt')
			                                  ->where('products_id', $productId->asInt())
			                                  ->where('language_id', $languageId)
			                                  ->where('image_id', $imageId->asInt())
			                                  ->get()
			                                  ->row_array();
			
			if(count($alternativeTextsQuery) > 0 && $alternativeTextsQuery['gm_alt_text'] !== null)
			{
				$text = new StringType((string)$alternativeTextsQuery['gm_alt_text']);
			}
			else
			{
				$text = new StringType('');
			}
			$additionalImage->setAltText($text, $languageCode);
		}
	}


	/**
	 * Perform database query to get primary image.
	 *
	 * @param IdType $productId Product ID.
	 *
	 * @return array Result.
	 */
	protected function _queryPrimaryImage(IdType $productId)
	{
		return $this->db->select('products_image, products_image_w, products_image_h, gm_show_image')
		                ->from('products')
		                ->where('products.products_id', $productId->asInt())
		                ->get()
		                ->row_array();
	}


	/**
	 * Perform database query to get additional images.
	 *
	 * @param IdType $productId Product ID.
	 *
	 * @return array Result.
	 */
	protected function _queryAdditionalImages(IdType $productId)
	{
		return $this->db->select('*')
		                ->from('products_images')
		                ->where('products_id', $productId->asInt())
						->order_by('image_nr', 'ASC')
		                ->get()
		                ->result_array();
	}
}